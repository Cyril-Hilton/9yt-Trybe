<?php

namespace App\Services\SEO;

use App\Services\AI\AIClient;
use Illuminate\Support\Facades\Cache;

class AiLandingService
{
    public function __construct(private readonly AIClient $client)
    {
    }

    public function generateRegionLanding(string $region, array $eventTitles): array
    {
        $key = 'ai:landing:region:' . md5($region . '|' . implode('|', $eventTitles));

        return $this->remember($key, function () use ($region, $eventTitles) {
            $payload = [
                'region' => $region,
                'event_titles' => array_values($eventTitles),
            ];

            $system = 'You are an SEO editor for an events marketplace. '
                . 'Write concise intros and metadata. Return ONLY valid JSON.';

            $user = "Create a landing page intro for events in this region.\n\n"
                . "DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
                . "REQUIREMENTS:\n"
                . "- headline: short and energetic\n"
                . "- intro: 2-3 sentences, helpful and local\n"
                . "- meta_title: 50-60 chars\n"
                . "- meta_description: 140-155 chars\n"
                . "- No invented facts or prices\n\n"
                . "OUTPUT JSON SHAPE:\n"
                . "{\"headline\":\"...\",\"intro\":\"...\",\"meta_title\":\"...\",\"meta_description\":\"...\"}";

            $result = $this->client->generateJson($system, $user, [
                'max_tokens' => 260,
                'temperature' => 0.4,
            ]);

            return $this->normalizeLanding($result, "Events in {$region}");
        });
    }

    public function generateCategoryLanding(string $category, array $eventTitles): array
    {
        $key = 'ai:landing:category:' . md5($category . '|' . implode('|', $eventTitles));

        return $this->remember($key, function () use ($category, $eventTitles) {
            $payload = [
                'category' => $category,
                'event_titles' => array_values($eventTitles),
            ];

            $system = 'You are an SEO editor for event categories. '
                . 'Write concise intros and metadata. Return ONLY valid JSON.';

            $user = "Create a landing page intro for the {$category} category.\n\n"
                . "DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
                . "REQUIREMENTS:\n"
                . "- headline: short and energetic\n"
                . "- intro: 2-3 sentences, helpful and on-theme\n"
                . "- meta_title: 50-60 chars\n"
                . "- meta_description: 140-155 chars\n"
                . "- No invented facts or prices\n\n"
                . "OUTPUT JSON SHAPE:\n"
                . "{\"headline\":\"...\",\"intro\":\"...\",\"meta_title\":\"...\",\"meta_description\":\"...\"}";

            $result = $this->client->generateJson($system, $user, [
                'max_tokens' => 260,
                'temperature' => 0.4,
            ]);

            return $this->normalizeLanding($result, "{$category} events");
        });
    }

    public function generateTimeLanding(string $label, array $eventTitles): array
    {
        $key = 'ai:landing:time:' . md5($label . '|' . implode('|', $eventTitles));

        return $this->remember($key, function () use ($label, $eventTitles) {
            $payload = [
                'timeframe' => $label,
                'event_titles' => array_values($eventTitles),
            ];

            $system = 'You are an SEO editor for events happening soon. '
                . 'Write concise intros and metadata. Return ONLY valid JSON.';

            $user = "Create a landing page intro for {$label} events.\n\n"
                . "DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
                . "REQUIREMENTS:\n"
                . "- headline: short and energetic\n"
                . "- intro: 2-3 sentences, helpful and time-based\n"
                . "- meta_title: 50-60 chars\n"
                . "- meta_description: 140-155 chars\n"
                . "- No invented facts or prices\n\n"
                . "OUTPUT JSON SHAPE:\n"
                . "{\"headline\":\"...\",\"intro\":\"...\",\"meta_title\":\"...\",\"meta_description\":\"...\"}";

            $result = $this->client->generateJson($system, $user, [
                'max_tokens' => 260,
                'temperature' => 0.4,
            ]);

            return $this->normalizeLanding($result, "{$label} events");
        });
    }

    private function remember(string $key, callable $callback): array
    {
        $cacheDays = (int) config('services.ai.landing.cache_days', 7);
        $ttl = now()->addDays($cacheDays);

        return Cache::remember($key, $ttl, function () use ($callback) {
            if (!$this->client->isAvailable()) {
                return [];
            }

            return $callback() ?: [];
        });
    }

    private function normalizeLanding(?array $result, string $fallbackTitle): array
    {
        if (!$result) {
            return [
                'headline' => $fallbackTitle,
                'intro' => '',
                'meta_title' => $fallbackTitle,
                'meta_description' => '',
            ];
        }

        return [
            'headline' => trim((string) ($result['headline'] ?? $fallbackTitle)),
            'intro' => trim((string) ($result['intro'] ?? '')),
            'meta_title' => trim((string) ($result['meta_title'] ?? $fallbackTitle)),
            'meta_description' => trim((string) ($result['meta_description'] ?? '')),
        ];
    }
}
