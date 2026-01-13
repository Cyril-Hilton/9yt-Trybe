<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate QR code for ticket
     * Uses SVG format (works without imagick extension)
     *
     * @param string $ticketCode
     * @param int $attendeeId
     * @return string Path to stored QR code
     */
    public function generateTicketQrCode(string $ticketCode, int $attendeeId): string
    {
        try {
            $qrCodeContent = $ticketCode;

            // Generate QR code as SVG (doesn't require imagick!)
            $qrCode = QrCode::format('svg')
                ->size(300)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($qrCodeContent);

            // Store QR code
            $filename = "qr-codes/ticket-{$attendeeId}-{$ticketCode}.svg";
            Storage::disk('public')->put($filename, $qrCode);

            return $filename;
        } catch (\Exception $e) {
            \Log::error('QR code generation failed', [
                'ticket_code' => $ticketCode,
                'attendee_id' => $attendeeId,
                'error' => $e->getMessage(),
            ]);

            // Return empty string if fails
            return '';
        }
    }

    /**
     * Generate QR code from content
     * Returns SVG as base64 (works without imagick!)
     *
     * @param string $content
     * @return string Base64 encoded QR code
     */
    public function generateBase64(string $content): string
    {
        try {
            return base64_encode(
                QrCode::format('svg')
                    ->size(300)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate($content)
            );
        } catch (\Exception $e) {
            \Log::error('QR code base64 generation failed', [
                'content_length' => strlen($content),
                'error' => $e->getMessage(),
            ]);

            // Return empty string if fails
            return '';
        }
    }
}
