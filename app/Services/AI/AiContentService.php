<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AiContentService
{
    public function __construct(private readonly AIClient $client)
    {
    }

    public function generateEventCopy(array $data): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            return null;
        }

        $payload = [
            'title' => $title,
            'summary' => trim((string) ($data['summary'] ?? '')),
            'overview' => trim((string) ($data['overview'] ?? '')),
            'event_type' => (string) ($data['event_type'] ?? ''),
            'start_date' => (string) ($data['start_date'] ?? ''),
            'end_date' => (string) ($data['end_date'] ?? ''),
            'location_type' => (string) ($data['location_type'] ?? ''),
            'venue_name' => (string) ($data['venue_name'] ?? ''),
            'region' => (string) ($data['region'] ?? ''),
            'online_platform' => (string) ($data['online_platform'] ?? ''),
            'audience' => (string) ($data['audience'] ?? ''),
            'tone' => (string) ($data['tone'] ?? 'inspiring, premium, and clear'),
            'key_points' => (string) ($data['key_points'] ?? ''),
            'categories' => $data['categories'] ?? [],
        ];

        $system = 'You are a senior event copywriter for a premium event platform. '
            . 'Write crisp, compelling copy without inventing facts. '
            . 'Return ONLY valid JSON.';

        $user = "Create event copy using the details below.\n\n"
            . "DETAILS:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- summary: 1-2 sentences, max 500 characters.\n"
            . "- overview: 2-4 short paragraphs (avoid bullet lists), max 900 characters.\n"
            . "- style: {tone}.\n"
            . "- do not add ticket prices or dates not provided.\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"summary\": \"...\", \"overview\": \"...\"}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 500,
            'temperature' => 0.5,
        ]);

        if (!$result || (!isset($result['summary']) && !isset($result['overview']))) {
            return null;
        }

        $summary = trim((string) ($result['summary'] ?? ''));
        $overview = trim((string) ($result['overview'] ?? ''));

        if ($summary !== '') {
            $summary = Str::limit($summary, 500, '');
        }
        if ($overview !== '') {
            $overview = Str::limit($overview, 900, '');
        }

        return [
            'summary' => $summary,
            'overview' => $overview,
        ];
    }

    public function generateSmsMessage(array $data): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $purpose = trim((string) ($data['purpose'] ?? ''));
        $details = trim((string) ($data['details'] ?? ''));
        $cta = trim((string) ($data['cta'] ?? ''));
        $tone = trim((string) ($data['tone'] ?? 'friendly and professional'));
        $maxLength = (int) ($data['max_length'] ?? 160);
        if ($maxLength <= 0) {
            $maxLength = 160;
        }

        $payload = [
            'purpose' => $purpose,
            'details' => $details,
            'cta' => $cta,
            'tone' => $tone,
            'max_length' => $maxLength,
        ];

        $system = 'You are an expert SMS copywriter. '
            . 'Write short, high-converting SMS messages. '
            . 'Return ONLY valid JSON.';

        $user = "Create one SMS message.\n\n"
            . "DETAILS:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- Must be under {$maxLength} characters.\n"
            . "- Include CTA if provided.\n"
            . "- Avoid emojis unless explicitly requested in details.\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"message\": \"...\"}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 200,
            'temperature' => 0.4,
        ]);

        if (!$result || empty($result['message'])) {
            return null;
        }

        $message = trim((string) $result['message']);
        if (Str::length($message) > $maxLength) {
            $message = Str::limit($message, $maxLength, '');
        }

        return [
            'message' => $message,
        ];
    }

    public function generateNewsDigest(array $articles, ?string $query = null): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $articles = array_values(array_filter($articles, function ($article) {
            return !empty($article['title']);
        }));

        if (empty($articles)) {
            return null;
        }

        $slice = array_slice($articles, 0, 8);
        $hashBase = collect($slice)
            ->map(fn ($item) => ($item['title'] ?? '') . '|' . ($item['published_at'] ?? '') . '|' . ($item['source'] ?? ''))
            ->implode('|');
        $cacheKey = 'ai:news_digest:' . md5(($query ?? '') . '|' . $hashBase);

        return Cache::remember($cacheKey, now()->addMinutes(45), function () use ($slice, $query) {
            $lines = collect($slice)->map(function ($article) {
                $title = trim((string) ($article['title'] ?? ''));
                $source = trim((string) ($article['source'] ?? ''));
                $published = trim((string) ($article['published_at'] ?? ''));
                $desc = trim((string) ($article['description'] ?? ''));

                $parts = [
                    $title,
                    $source !== '' ? "source: {$source}" : null,
                    $published !== '' ? "published: {$published}" : null,
                ];

                $line = implode(' | ', array_filter($parts));
                if ($desc !== '') {
                    $line .= " | summary: " . Str::limit($desc, 140, '');
                }

                return '- ' . $line;
            })->implode("\n");

            $system = 'You are a news editor for a lifestyle and entertainment site. '
                . 'Summarize headlines without inventing facts. '
                . 'Return ONLY valid JSON.';

            $user = "Create a short digest from these headlines.\n\n"
                . "QUERY: " . ($query ?: 'general') . "\n\n"
                . "HEADLINES:\n{$lines}\n\n"
                . "REQUIREMENTS:\n"
                . "- headline: 1 short sentence.\n"
                . "- bullets: 3 concise bullets.\n"
                . "- topics: 3-5 short topic labels.\n\n"
                . "OUTPUT JSON SHAPE:\n"
                . "{\"headline\": \"...\", \"bullets\": [\"...\"], \"topics\": [\"...\"]}";

            $result = $this->client->generateJson($system, $user, [
                'max_tokens' => 260,
                'temperature' => 0.35,
            ]);

            if (!$result || empty($result['headline'])) {
                return null;
            }

            $bullets = collect($result['bullets'] ?? [])
                ->filter(fn ($item) => is_string($item) && trim($item) !== '')
                ->map(fn ($item) => trim($item))
                ->take(3)
                ->values()
                ->all();

            $topics = collect($result['topics'] ?? [])
                ->filter(fn ($item) => is_string($item) && trim($item) !== '')
                ->map(fn ($item) => trim($item))
                ->take(5)
                ->values()
                ->all();

            return [
                'headline' => trim((string) $result['headline']),
                'bullets' => $bullets,
                'topics' => $topics,
            ];
        });
    }
}
