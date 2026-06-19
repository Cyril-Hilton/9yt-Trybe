<?php

namespace App\Services\SEO;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class IndexNowService
{
    private const QUEUE_KEY = 'seo:indexnow:pending_urls';

    public function queueUrls(array $urls): void
    {
        $urls = array_values(array_filter(array_unique($urls)));
        if ($urls === []) {
            return;
        }

        $pending = Cache::get(self::QUEUE_KEY, []);
        Cache::put(
            self::QUEUE_KEY,
            array_values(array_unique(array_merge(is_array($pending) ? $pending : [], $urls))),
            now()->addDays(2)
        );
    }

    public function flushQueued(): int
    {
        $urls = Cache::get(self::QUEUE_KEY, []);
        if (!is_array($urls) || $urls === []) {
            return 0;
        }

        if (!$this->submitUrls($urls)) {
            return 0;
        }

        Cache::forget(self::QUEUE_KEY);

        return count($urls);
    }

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
                ->when(app()->environment('local'), fn($h) => $h->withoutVerifying())
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
