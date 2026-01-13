<?php

namespace App\Mail;

use App\Models\EventOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class TicketConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public EventOrder $order;

    public function __construct(EventOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message and embed QR codes as raw data, avoiding temporary file storage.
     *
     * @return $this
     */
    public function build()
    {
        // Stores attendee IDs mapped to the Content ID (CID) used for embedding.
        $embeddedCids = [];
        $inlineParts = [];

        foreach ($this->order->attendees as $attendee) {
            if ($attendee->ticket_code) {
                $qrCodeData = null;
                if (extension_loaded('imagick')) {
                    try {
                        // 1. Generate the QR code as raw PNG binary data
                        $qrCodeData = QrCode::format('png')
                            ->size(200)
                            ->margin(1)
                            ->errorCorrection('M')
                            ->generate($attendee->ticket_code);
                    } catch (\Exception $e) {
                        Log::error('QR code generation failed for attendee ' . $attendee->id . ': ' . $e->getMessage());
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
                        Log::warning('QR code fallback failed for attendee ' . $attendee->id . ': ' . $e->getMessage());
                    }
                }

                if ($qrCodeData) {
                    $contentId = 'qr-' . $attendee->id . '@conference-portal';
                    $embeddedCids[$attendee->id] = 'cid:' . $contentId;
                    $inlineParts[] = [
                        'content_id' => $contentId,
                        'name' => 'qr-' . $attendee->id . '.png',
                        'data' => $qrCodeData,
                    ];
                } else {
                    $embeddedCids[$attendee->id] = null;
                }
            }
        }

        $embeddedBannerCid = null;
        $bannerPath = $this->order->event?->flier_path;
        if ($bannerPath && Storage::disk('public')->exists($bannerPath)) {
            $bannerData = Storage::disk('public')->get($bannerPath);
            $bannerMime = Storage::disk('public')->mimeType($bannerPath) ?: 'image/jpeg';
            $embeddedBannerCid = 'cid:event-banner@conference-portal';
            $inlineParts[] = [
                'content_id' => 'event-banner@conference-portal',
                'name' => basename($bannerPath),
                'data' => $bannerData,
                'mime' => $bannerMime,
            ];
        }

        if (!empty($inlineParts)) {
            $this->withSymfonyMessage(function (Email $message) use ($inlineParts) {
                foreach ($inlineParts as $part) {
                    $dataPart = new DataPart($part['data'], $part['name'], $part['mime'] ?? 'image/png');
                    $dataPart->asInline();
                    $dataPart->setContentId($part['content_id']);
                    $message->addPart($dataPart);
                }
            });
        }

        return $this->view('emails.ticket-confirmation')
            ->subject('Your Tickets for ' . $this->order->event->title)
            ->with([
                'order' => $this->order,
                // Pass the CIDs, NOT temporary file paths
                'embeddedCids' => $embeddedCids, 
                'embeddedBannerCid' => $embeddedBannerCid,
            ]);
    }
}
