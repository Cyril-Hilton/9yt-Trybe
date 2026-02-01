<?php

namespace App\Services\Blog;

use App\Services\AI\AIClient;
use Illuminate\Support\Str;

class AiBlogService
{
    public function __construct(private readonly AIClient $client)
    {
    }

    public function generateHowTo(string $topic, array $context = []): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $payload = [
            'topic' => $topic,
            'platform_name' => '9yt !Trybe',
            'features' => $context['features'] ?? [],
            'audience' => $context['audience'] ?? 'event organizers, creators, and promoters',
        ];

        $system = 'You are a content strategist for a modern events platform. '
            . 'Write clear, helpful, non-hype content. Return ONLY valid JSON.';

        $user = "Write a how-to blog post.\n\n"
            . "CONTEXT:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- title: 50-80 chars\n"
            . "- summary: 1-2 sentences\n"
            . "- content: 5-8 short sections, plain text with line breaks\n"
            . "- include a short CTA mentioning 9yt !Trybe\n"
            . "- do not invent stats or quotes\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"title\":\"...\",\"summary\":\"...\",\"content\":\"...\",\"meta_title\":\"...\",\"meta_description\":\"...\",\"category\":\"how-to\"}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 900,
            'temperature' => 0.5,
        ]);

        return $this->normalizeResult($result, 'how-to');
    }

    public function generateWhatsOn(string $region, array $events): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $eventLines = collect($events)->map(function ($event) {
            return [
                'title' => $event['title'] ?? '',
                'date' => $event['date'] ?? '',
                'venue' => $event['venue'] ?? '',
                'url' => $event['url'] ?? '',
            ];
        })->values()->all();

        $payload = [
            'region' => $region,
            'events' => $eventLines,
            'platform_name' => '9yt !Trybe',
        ];

        $system = 'You are a local culture editor. '
            . 'Summarize upcoming events accurately. Return ONLY valid JSON.';

        $user = "Write a whats-on roundup.\n\n"
            . "CONTEXT:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- title: include region and timeframe\n"
            . "- summary: 1-2 sentences\n"
            . "- content: short intro + list events (title, date, venue) in plain text\n"
            . "- include a CTA to book on 9yt !Trybe\n"
            . "- do not invent events\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"title\":\"...\",\"summary\":\"...\",\"content\":\"...\",\"meta_title\":\"...\",\"meta_description\":\"...\",\"category\":\"whats-on\"}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 900,
            'temperature' => 0.4,
        ]);

        return $this->normalizeResult($result, 'whats-on');
    }

    private function normalizeResult(?array $result, string $category): ?array
    {
        if (!$result) {
            return null;
        }

        $title = trim((string) ($result['title'] ?? ''));
        $summary = trim((string) ($result['summary'] ?? ''));
        $content = trim((string) ($result['content'] ?? ''));
        $metaTitle = trim((string) ($result['meta_title'] ?? ''));
        $metaDescription = trim((string) ($result['meta_description'] ?? ''));

        if ($title === '' || $summary === '' || $content === '') {
            return null;
        }

        return [
            'title' => Str::limit($title, 80, ''),
            'summary' => Str::limit($summary, 300, ''),
            'content' => $content,
            'meta_title' => Str::limit($metaTitle !== '' ? $metaTitle : $title, 60, ''),
            'meta_description' => Str::limit($metaDescription !== '' ? $metaDescription : $summary, 155, ''),
            'category' => $category,
        ];
    }
}
