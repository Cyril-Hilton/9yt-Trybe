<?php

namespace App\Services;

use App\Models\Registration;

class UniqueIdGenerator
{
    public static function generate(): string
    {
        $maxAttempts = 100;
        $attempt = 0;

        do {
            $uniqueId = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $exists = Registration::where('unique_id', $uniqueId)->exists();
            $attempt++;

            if ($attempt >= $maxAttempts) {
                $uniqueId = self::generateWithPrefix();
                $exists = Registration::where('unique_id', $uniqueId)->exists();
            }
        } while ($exists);

        return $uniqueId;
    }

    private static function generateWithPrefix(): string
    {
        $timestamp = substr((string) time(), -4);
        $random = random_int(0, 99);
        return substr($timestamp . $random, 0, 4);
    }

    public static function validate(string $uniqueId): bool
    {
        return preg_match('/^\d{4}$/', $uniqueId);
    }

    public static function generateBatch(int $count): array
    {
        $ids = [];
        for ($i = 0; $i < $count; $i++) {
            $ids[] = self::generate();
        }
        return $ids;
    }
}