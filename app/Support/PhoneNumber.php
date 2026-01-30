<?php

namespace App\Support;

class PhoneNumber
{
    public static function normalize(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', $phone);
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '233' . substr($digits, 1);
        }

        if (!str_starts_with($digits, '233')) {
            $digits = '233' . $digits;
        }

        return $digits;
    }
}
