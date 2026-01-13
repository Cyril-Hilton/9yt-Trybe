<?php

namespace App\Services;

use App\Models\EventAttendee;
use Illuminate\Support\Str;

class TicketGeneratorService
{
    /**
     * Generate unique 6-digit ticket code
     *
     * @return string
     */
    public function generateTicketCode(): string
    {
        do {
            // Generate 6-digit alphanumeric code
            $code = strtoupper(Str::random(6));
        } while (EventAttendee::where('ticket_code', $code)->exists());

        return $code;
    }

    /**
     * Generate multiple unique ticket codes
     *
     * @param int $count
     * @return array
     */
    public function generateMultipleCodes(int $count): array
    {
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = $this->generateTicketCode();
        }

        return $codes;
    }

    /**
     * Validate ticket code format
     *
     * @param string $code
     * @return bool
     */
    public function isValidFormat(string $code): bool
    {
        return preg_match('/^[A-Z0-9]{6}$/', $code) === 1;
    }
}
