<?php

namespace App\Services\SEO;

use Illuminate\Support\Facades\Http;

class IndexNowService
{
    public function submitUrls(array $urls): bool
    {
        $key = trim((string) config('services.indexnow.key'));
        if ($key === '') {
            return false;
        }

        $host = trim((string) config('services.indexnow.host'));
        if ($host === '') {
            $host = parse_url(config('app.url'), PHP_URL_HOST) ?: '';
        }

        if ($host === '') {
            return false;
        }

        $payload = [
            'host' => $host,
            'key' => $key,
            'urlList' => array_values(array_unique($urls)),
        ];

        $keyLocation = trim((string) config('services.indexnow.key_location'));
        if ($keyLocation !== '') {
            $payload['keyLocation'] = $keyLocation;
        }

        try {
            $response = Http::timeout(8)
                ->retry(1, 200)
                ->acceptJson()
                ->post('https://api.indexnow.org/indexnow', $payload);

            if ($response->successful()) {
                return true;
            }

            \Log::warning('IndexNow submission failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('IndexNow submission error.', [
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }
}
