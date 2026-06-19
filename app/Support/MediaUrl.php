<?php

namespace App\Support;

use Illuminate\Support\Str;

class MediaUrl
{
    public static function fromPath(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'data:'])) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (Str::startsWith($path, ['storage/', 'ui/', 'images/', 'build/'])) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }
}
