<?php

namespace App\Services\SEO;

use App\Services\AI\AIClient;
use Illuminate\Support\Facades\Cache;

class AiTranslationService
{
    public function __construct(private readonly AIClient $client)
    {
    }

    public function resolveLanguage(?string $lang): string
    {
        $lang = strtolower(trim((string) $lang));
        $allowed = config('services.ai.seo.languages', ['en']);
        $allowed = array_map(fn ($item) => strtolower(trim((string) $item)), $allowed);

        if ($lang === '' || !in_array($lang, $allowed, true)) {
            return 'en';
        }

        return $lang;
    }

    public function translateText(string $text, string $lang): string
    {
        $lang = $this->resolveLanguage($lang);
        $text = trim($text);

        if ($lang === 'en' || $text === '' || !$this->client->isAvailable()) {
            return $text;
        }

        $cacheKey = 'ai:translate:text:' . $lang . ':' . md5($text);
        $ttl = now()->addDays((int) config('services.ai.translation.cache_days', 30));

        return Cache::remember($cacheKey, $ttl, function () use ($text, $lang) {
            $system = 'You are a professional translator. '
                . 'Return ONLY the translated text, no quotes or extra punctuation.';

            $user = "Translate the following text to {$this->languageLabel($lang)} ({$lang}). "
                . "Keep tone and length similar.\n\nTEXT:\n{$text}";

            $result = $this->client->generateText($system, $user, [
                'max_tokens' => 260,
                'temperature' => 0.2,
            ]);

            return $result ? trim($result) : $text;
        });
    }

    public function translateMeta(string $title, string $description, string $lang): array
    {
        $lang = $this->resolveLanguage($lang);
        $title = trim($title);
        $description = trim($description);

        if ($lang === 'en' || (!$this->client->isAvailable())) {
            return [
                'meta_title' => $title,
                'meta_description' => $description,
            ];
        }

        $cacheKey = 'ai:translate:meta:' . $lang . ':' . md5($title . '|' . $description);
        $ttl = now()->addDays((int) config('services.ai.translation.cache_days', 30));

        return Cache::remember($cacheKey, $ttl, function () use ($title, $description, $lang) {
            $system = 'You are a professional translator for SEO metadata. '
                . 'Return ONLY valid JSON.';

            $user = "Translate this SEO metadata to {$this->languageLabel($lang)} ({$lang}).\n\n"
                . "REQUIREMENTS:\n"
                . "- Keep length similar.\n"
                . "- Preserve brand names.\n"
                . "- No extra text.\n\n"
                . "INPUT JSON:\n"
                . json_encode([
                    'meta_title' => $title,
                    'meta_description' => $description,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
                . "OUTPUT JSON SHAPE:\n"
                . "{\"meta_title\":\"...\",\"meta_description\":\"...\"}";

            $result = $this->client->generateJson($system, $user, [
                'max_tokens' => 260,
                'temperature' => 0.2,
            ]);

            if (!$result || (!isset($result['meta_title']) && !isset($result['meta_description']))) {
                return [
                    'meta_title' => $title,
                    'meta_description' => $description,
                ];
            }

            return [
                'meta_title' => trim((string) ($result['meta_title'] ?? $title)),
                'meta_description' => trim((string) ($result['meta_description'] ?? $description)),
            ];
        });
    }

    private function languageLabel(string $lang): string
    {
        return match ($lang) {
            'fr' => 'French',
            'es' => 'Spanish',
            'pt' => 'Portuguese',
            'de' => 'German',
            'it' => 'Italian',
            default => 'English',
        };
    }
}
