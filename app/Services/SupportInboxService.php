<?php

namespace App\Services;

use App\Models\SupportInboxMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SupportInboxService
{
    public function process(bool $bootstrap = false): array
    {
        if ((!config('services.support_inbox.enabled', false) && !$bootstrap) || !function_exists('imap_open')) {
            return ['processed' => 0, 'replied' => 0, 'escalated' => 0, 'baselined' => 0, 'reason' => 'Inbox monitoring is unavailable.'];
        }

        imap_timeout(IMAP_OPENTIMEOUT, 15);
        imap_timeout(IMAP_READTIMEOUT, 20);
        imap_timeout(IMAP_WRITETIMEOUT, 20);
        imap_timeout(IMAP_CLOSETIMEOUT, 10);

        $mailbox = sprintf('{%s:%d/imap/ssl}INBOX', config('services.support_inbox.host'), config('services.support_inbox.port'));
        $inbox = @imap_open($mailbox, config('services.support_inbox.username'), config('services.support_inbox.password'), OP_READONLY);
        if ($inbox === false) {
            Log::warning('Support inbox login failed.');
            return ['processed' => 0, 'replied' => 0, 'escalated' => 0, 'baselined' => 0, 'reason' => 'Inbox login failed.'];
        }

        // A mail client can mark a message as read before the next scheduler pass.
        // Search the recent mailbox window and rely on message-ID deduplication instead.
        $uids = array_slice(imap_search($inbox, 'ALL', SE_UID) ?: [], -100);
        $result = ['processed' => 0, 'replied' => 0, 'escalated' => 0, 'baselined' => 0];

        foreach ($uids as $uid) {
            $overview = imap_fetch_overview($inbox, (string) $uid, FT_UID)[0] ?? null;
            if (!$overview) continue;

            $from = $this->address($overview->from ?? '');
            if (!$this->isCustomerSender($from['email'])) continue;

            $messageId = trim((string) ($overview->message_id ?? 'imap-' . $uid . '-' . ($overview->udate ?? time())));
            if (SupportInboxMessage::where('message_id', $messageId)->exists()) continue;

            $subject = $this->decode((string) ($overview->subject ?? ''));
            $body = $this->messageText($inbox, $uid);
            $classification = $this->classify($subject . "\n" . $body);
            $message = SupportInboxMessage::create([
                'message_id' => $messageId,
                'from_email' => $from['email'],
                'from_name' => $from['name'],
                'subject' => $subject,
                'body' => $body,
                'classification' => $classification,
                'status' => $classification === 'escalated' ? 'needs_review' : 'received',
                'received_at' => isset($overview->udate) ? now()->setTimestamp((int) $overview->udate) : now(),
            ]);
            $result['processed']++;

            if ($bootstrap) {
                $message->update(['status' => 'baselined']);
                $result['baselined']++;
                continue;
            }

            if ($classification === 'escalated') {
                $result['escalated']++;
                continue;
            }

            $reply = $this->replyFor($classification, $from['name'], $body);
            try {
                Mail::raw($reply, function ($mail) use ($from, $subject, $messageId) {
                    $mail->to($from['email'])->subject($this->replySubject($subject));
                    $mail->getSymfonyMessage()->getHeaders()->addTextHeader('In-Reply-To', $messageId);
                    $mail->getSymfonyMessage()->getHeaders()->addTextHeader('References', $messageId);
                });
                $message->update(['status' => 'replied', 'reply_body' => $reply, 'replied_at' => now()]);
                $result['replied']++;
            } catch (\Throwable $exception) {
                Log::warning('Support inbox reply failed.', ['message_id' => $messageId, 'error' => $exception->getMessage()]);
                $message->update(['status' => 'reply_failed']);
            }
        }

        imap_close($inbox);
        return $result;
    }

    private function classify(string $text): string
    {
        $text = Str::lower($text);
        if (preg_match('/refund|chargeback|payout|payment.*dispute|invoice|contract|agreement|lawsuit|legal|privacy|delete my data|hack|breach|fraud|abuse|harass/', $text)) return 'escalated';
        if (preg_match('/demo|walkthrough|learn more|how.*work/', $text)) return 'demo';
        if (preg_match('/price|pricing|charge|commission|fee|service rate|rate\b|cost|how much/', $text)) return 'pricing';
        if (preg_match('/free event|complimentary|rsvp/', $text)) return 'free_event';
        if (preg_match('/qr|check.?in|ticket/', $text)) return 'ticketing';
        return 'general';
    }

    private function replyFor(string $type, ?string $name, string $message): string
    {
        $greeting = 'Hello' . ($name ? ' ' . trim($name) : '') . ',';
        $body = match ($type) {
            'demo' => "Thank you for your interest. We would be happy to give you a short 9yt !Trybe walkthrough and help set up your first event. Please share the type of event you are planning and a convenient time, and our team will arrange the next step.",
            'pricing' => "Our service rate for paid ticket sales is a 4% platform commission. There is no separate setup charge. Free events, complimentary tickets, RSVP registrations and free transport reservations are GHS 0, with no payment gateway step for attendees.",
            'free_event' => "Yes—free events, complimentary tickets and RSVP registrations can be set up at GHS 0, without a payment gateway step for attendees.",
            'ticketing' => "9yt !Trybe lets organisers create ticket types, issue QR tickets, track registrations and scan attendees at entry. We can also help you set up your first event.",
            default => $this->generalReply($message),
        };
        return "$greeting\n\n$body\n\nKind regards,\n9yt !Trybe Team\nsupport@9yttrybe.com\nhttps://9yttrybe.com";
    }

    private function generalReply(string $message): string
    {
        $text = Str::lower($message);
        if (preg_match('/service|offer|about/', $text)) {
            return "9yt !Trybe helps organisers publish events, sell paid tickets, manage free RSVP and complimentary registrations, issue QR tickets, and check guests in at the venue. We also support attendee records and organiser event management in one place. What type of event are you planning?";
        }

        return "Thank you for your message. To give you the right answer, please tell us whether your event is paid or free, and whether you need ticketing, RSVP registration, or QR check-in.";
    }

    private function messageText($inbox, int $uid): string
    {
        $structure = imap_fetchstructure($inbox, (string) $uid, FT_UID);
        $part = $this->findTextPart($structure);
        $section = $part['section'] ?? '';
        $body = $section === ''
            ? imap_body($inbox, (string) $uid, FT_UID)
            : imap_fetchbody($inbox, (string) $uid, $section, FT_UID);
        $encoding = $part['encoding'] ?? ($structure->encoding ?? 0);
        $body = match ($encoding) {
            3 => base64_decode($body, true) ?: '',
            4 => quoted_printable_decode($body),
            default => $body,
        };
        $body = html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $body = preg_split('/\n\s*(On .+ wrote:|From:|>)/i', $body, 2)[0] ?? $body;
        return Str::limit(trim($body), 6000, '');
    }

    private function findTextPart(object $structure, string $prefix = ''): array
    {
        if (($structure->type ?? null) === 0 && strtolower($structure->subtype ?? 'plain') === 'plain') {
            return ['section' => $prefix, 'encoding' => $structure->encoding ?? 0];
        }
        foreach ($structure->parts ?? [] as $index => $part) {
            $section = $prefix === '' ? (string) ($index + 1) : $prefix . '.' . ($index + 1);
            $found = $this->findTextPart($part, $section);
            if ($found) return $found;
        }
        return [];
    }

    private function replySubject(string $subject): string { return Str::startsWith(Str::lower($subject), 're:') ? $subject : 'Re: ' . $subject; }
    private function decode(string $value): string { $decoded = imap_mime_header_decode($value); return collect($decoded)->pluck('text')->implode(''); }
    private function isCustomerSender(string $email): bool
    {
        $email = Str::lower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
        if (strcasecmp($email, (string) config('mail.from.address')) === 0) return false;
        if (Str::endsWith($email, '@9yttrybe.com')) return false;
        return !in_array(Str::before($email, '@'), ['mailer-daemon', 'postmaster', 'cpanel'], true);
    }

    private function address(string $value): array { $item = imap_rfc822_parse_adrlist($value, ''); $first = $item[0] ?? null; $email = $first ? (($first->mailbox ?? '') . '@' . ($first->host ?? '')) : ''; return ['email' => filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '', 'name' => $first?->personal ?? null]; }
}
