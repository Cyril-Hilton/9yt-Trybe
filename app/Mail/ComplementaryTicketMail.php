<?php

namespace App\Mail;

use App\Models\EventAttendee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class ComplementaryTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public EventAttendee $attendee;

    public function __construct(EventAttendee $attendee)
    {
        $this->attendee = $attendee;
    }

    public function build()
    {
        $attendee = $this->attendee->load(['event', 'ticket']);
        $embeddedCid = null;
        $qrCodeUrl = $attendee->ticket_code
            ? 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($attendee->ticket_code)
            : null;

        if ($attendee->ticket_code) {
            $qrCodeData = null;
            if (extension_loaded('imagick')) {
                try {
                    $qrCodeData = QrCode::format('png')
                        ->size(200)
                        ->margin(1)
                        ->errorCorrection('M')
                        ->generate($attendee->ticket_code);
                } catch (\Exception $e) {
                    Log::error('Complementary ticket QR generation failed for attendee ' . $attendee->id . ': ' . $e->getMessage());
                }
            }

            if (!$qrCodeData) {
                try {
                    $response = Http::timeout(5)->get('https://api.qrserver.com/v1/create-qr-code/', [
                        'size' => '200x200',
                        'data' => $attendee->ticket_code,
                    ]);
                    if ($response->successful()) {
                        $qrCodeData = $response->body();
                    }
                } catch (\Exception $e) {
                    Log::warning('Complementary ticket QR fallback failed for attendee ' . $attendee->id . ': ' . $e->getMessage());
                }
            }

            if ($qrCodeData) {
                $contentId = 'qr-' . $attendee->id . '@conference-portal';
                $embeddedCid = 'cid:' . $contentId;

                $this->withSymfonyMessage(function (Email $message) use ($qrCodeData, $contentId, $attendee) {
                    $part = new DataPart($qrCodeData, 'qr-' . $attendee->id . '.png', 'image/png');
                    $part->asInline();
                    $part->setContentId($contentId);
                    $message->addPart($part);
                });
            }
        }

        $embeddedBannerCid = null;
        $bannerPath = $event->flier_path;
        if ($bannerPath && Storage::disk('public')->exists($bannerPath)) {
            $bannerData = Storage::disk('public')->get($bannerPath);
            $bannerMime = Storage::disk('public')->mimeType($bannerPath) ?: 'image/jpeg';
            $bannerContentId = 'event-banner-' . $attendee->id . '@conference-portal';
            $embeddedBannerCid = 'cid:' . $bannerContentId;
            $this->withSymfonyMessage(function (Email $message) use ($bannerData, $bannerMime, $bannerContentId, $bannerPath) {
                $part = new DataPart($bannerData, basename($bannerPath), $bannerMime);
                $part->asInline();
                $part->setContentId($bannerContentId);
                $message->addPart($part);
            });
        }

        return $this->subject('Complementary Ticket for ' . $attendee->event->title)
            ->view('emails.complementary-ticket')
            ->with([
                'attendee' => $attendee,
                'event' => $attendee->event,
                'embeddedCid' => $embeddedCid,
                'embeddedBannerCid' => $embeddedBannerCid,
                'qrCodeUrl' => $qrCodeUrl,
            ]);
    }
}
