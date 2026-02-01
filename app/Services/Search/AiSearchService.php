<?php

namespace App\Services\Search;

use App\Services\AI\AIClient;
use Illuminate\Support\Facades\Cache;

class AiSearchService
{
    public function __construct(private readonly AIClient $client)
    {
    }

    public function rewrite(string $query): ?array
    {
        $query = trim($query);
        if ($query === '') {
            return null;
        }

        if (!config('services.ai.search.enabled', true)) {
            return null;
        }

        $minLen = (int) config('services.ai.search.min_len', 3);
        if (mb_strlen($query) < $minLen) {
            return null;
        }

        if (!$this->client->isAvailable()) {
            return null;
        }

        $cacheMinutes = (int) config('services.ai.search.cache_minutes', 1440);
        $cacheKey = 'ai:search:rewrite:' . md5($query);

        return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($query) {
            $system = 'You are a search assistant. '
                . 'Fix spelling, extract synonyms, and detect user intent. '
                . 'Return ONLY valid JSON.';

            $user = "Rewrite this search query for an events platform.\n\n"
                . "QUERY: {$query}\n\n"
                . "REQUIREMENTS:\n"
                . "- corrected_query: spelling-fixed, concise\n"
                . "- synonyms: 2-8 short terms, no duplicates\n"
                . "- intents: choose from [events, organizers, blogs, products, polls, surveys, conferences, categories, general]\n"
                . "- locations: list any city/region detected (if none, empty array)\n\n"
                . "OUTPUT JSON SHAPE:\n"
                . "{\"corrected_query\":\"...\",\"synonyms\":[\"...\"],\"intents\":[\"events\"],\"locations\":[\"Accra\"]}";

            $result = $this->client->generateJson($system, $user, [
                'max_tokens' => 240,
                'temperature' => 0.2,
            ]);

            return $this->normalizeResult($result, $query);
        });
    }

    private function normalizeResult(?array $result, string $fallback): ?array
    {
        if (!$result) {
            return null;
        }

        $corrected = trim((string) ($result['corrected_query'] ?? ''));
        if ($corrected === '') {
            $corrected = $fallback;
        }

        $synonyms = $result['synonyms'] ?? [];
        if (!is_array($synonyms)) {
            $synonyms = [];
        }

        $synonyms = array_values(array_filter(array_map(function ($item) {
            $item = trim((string) $item);
            return $item !== '' ? $item : null;
        }, $synonyms)));

        $maxSynonyms = (int) config('services.ai.search.max_synonyms', 6);
        $synonyms = array_slice(array_unique($synonyms), 0, $maxSynonyms);

        $intents = $result['intents'] ?? [];
        if (!is_array($intents)) {
            $intents = [];
        }

        $intents = array_values(array_filter(array_map(fn ($item) => strtolower(trim((string) $item)), $intents)));

        $locations = $result['locations'] ?? [];
        if (!is_array($locations)) {
            $locations = [];
        }

        $locations = array_values(array_filter(array_map(function ($item) {
            $item = trim((string) $item);
            return $item !== '' ? $item : null;
        }, $locations)));

        return [
            'corrected_query' => $corrected,
            'synonyms' => $synonyms,
            'intents' => $intents,
            'locations' => $locations,
        ];
    }
}
