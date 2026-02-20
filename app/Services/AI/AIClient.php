<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AIClient
{
    public function isAvailable(): bool
    {
        return $this->resolveProvider() !== null;
    }

    public function generateText(string $system, string $user, array $options = []): ?string
    {
        $providers = $this->resolveProviders();
        if (empty($providers)) {
            return null;
        }

        $temperature = isset($options['temperature']) ? (float) $options['temperature'] : 0.4;
        $maxTokens = isset($options['max_tokens']) ? (int) $options['max_tokens'] : 400;

        foreach ($providers as $provider) {
            try {
                if ($provider === 'openai') {
                    $result = $this->callOpenAI($system, $user, $temperature, $maxTokens, $options);
                } elseif ($provider === 'gemini') {
                    $result = $this->callGemini($system, $user, $temperature, $maxTokens, $options);
                } else {
                    $result = null;
                }

                if ($result !== null && $result !== '') {
                    return $result;
                }
            } catch (\Throwable $e) {
                \Log::warning('AI request failed.', [
                    'provider' => $provider,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }

    public function generateJson(string $system, string $user, array $options = []): ?array
    {
        $text = $this->generateText($system, $user, $options);
        if (!$text) {
            return null;
        }

        return $this->extractJson($text);
    }

    private function resolveProvider(): ?string
    {
        $preferred = strtolower((string) config('services.ai.provider', 'auto'));
        $hasOpenAi = !empty(config('services.openai.api_key'));
        $hasGemini = !empty(config('services.gemini.api_key'));

        if ($preferred === 'auto') {
            if ($hasOpenAi) {
                return 'openai';
            }
            if ($hasGemini) {
                return 'gemini';
            }
            return null;
        }

        if ($preferred === 'openai') {
            if ($hasOpenAi) {
                return 'openai';
            }
            return $hasGemini ? 'gemini' : null;
        }

        if ($preferred === 'gemini') {
            if ($hasGemini) {
                return 'gemini';
            }
            return $hasOpenAi ? 'openai' : null;
        }

        return null;
    }

    private function resolveProviders(): array
    {
        $primary = $this->resolveProvider();
        if (!$primary) {
            return [];
        }

        $providers = [$primary];
        $fallback = $this->resolveFallbackProvider($primary);
        if ($fallback && !in_array($fallback, $providers, true)) {
            $providers[] = $fallback;
        }

        return $providers;
    }

    private function resolveFallbackProvider(string $primary): ?string
    {
        $hasOpenAi = !empty(config('services.openai.api_key'));
        $hasGemini = !empty(config('services.gemini.api_key'));

        if ($primary === 'openai') {
            return $hasGemini ? 'gemini' : null;
        }

        if ($primary === 'gemini') {
            return $hasOpenAi ? 'openai' : null;
        }

        return null;
    }

    private function callOpenAI(string $system, string $user, float $temperature, int $maxTokens, array $options): ?string
    {
        $apiKey = (string) config('services.openai.api_key');
        if ($apiKey === '') {
            return null;
        }

        $baseUrl = rtrim((string) config('services.openai.api_base_url', 'https://api.openai.com'), '/');
        $endpoint = $baseUrl . '/v1/chat/completions';
        $model = $options['model'] ?? config('services.openai.model', config('services.ai.model', 'gpt-4o-mini'));

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ];

        // Check if we are in a backoff period
        if (Cache::has('ai_backoff_openai')) {
            return null;
        }

        // Add a small jitter, but cap it to 1s max so we don't block the UI
        usleep(rand(100000, 1000000));

        $response = Http::timeout(4)
            ->retry(1, 100)
            ->when(config('services.ai.insecure', app()->environment('local')), fn($h) => $h->withoutVerifying())
            ->withToken($apiKey)
            ->acceptJson()
            ->post($endpoint, $payload);

        if (!$response->successful()) {
            if ($response->status() === 429) {
                Cache::put('ai_backoff_openai', true, now()->addMinutes(2));
                \Log::warning('Rate limited by OpenAI. Backing off for 2 minutes.');
            } else {
                \Log::warning('OpenAI response failed.', [
                    'status' => $response->status(),
                    'provider' => 'openai',
                ]);
            }
            return null;
        }

        $content = $response->json('choices.0.message.content');

        return is_string($content) ? trim($content) : null;
    }

    private function callGemini(string $system, string $user, float $temperature, int $maxTokens, array $options): ?string
    {
        $apiKey = (string) config('services.gemini.api_key');
        if ($apiKey === '') {
            return null;
        }

        $baseUrl = rtrim((string) config('services.gemini.api_base_url', 'https://generativelanguage.googleapis.com'), '/');
        $model = $options['model'] ?? config('services.gemini.model', config('services.ai.model', 'gemini-1.5-flash'));
        $endpoint = $baseUrl . '/v1beta/models/' . $model . ':generateContent?key=' . $apiKey;

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $system],
                ],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $user],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => $maxTokens,
            ],
        ];

        // Check if we are in a backoff period
        if (Cache::has('ai_backoff_gemini')) {
            return null;
        }

        // Add a small jitter, but cap it to 1s max so we don't block the UI
        usleep(rand(100000, 1000000));

        $response = Http::timeout(4)
            ->retry(1, 100)
            ->when(config('services.ai.insecure', app()->environment('local')), fn($h) => $h->withoutVerifying())
            ->acceptJson()
            ->post($endpoint, $payload);

        if (!$response->successful()) {
            if ($response->status() === 429) {
                Cache::put('ai_backoff_gemini', true, now()->addMinutes(2));
                \Log::warning('Rate limited by Gemini. Backing off for 2 minutes.');
            } else {
                \Log::warning('Gemini response failed.', [
                    'status' => $response->status(),
                    'provider' => 'gemini',
                    'model' => $model,
                    'endpoint_mask' => Str::before($endpoint, '?'),
                ]);
            }
            return null;
        }

        $content = $response->json('candidates.0.content.parts.0.text');
        if (!is_string($content)) {
            $content = $response->json('candidates.0.content.parts');
            if (is_array($content)) {
                $content = collect($content)
                    ->pluck('text')
                    ->filter()
                    ->implode("\n");
            }
        }

        if (is_string($content) && trim($content) !== '') {
            return trim($content);
        }

        $finishReason = $response->json('candidates.0.finishReason');
        if ($finishReason === 'MAX_TOKENS' && is_string($content)) {
            \Log::warning('Gemini reached MAX_TOKENS but returned partial content.', [
                'model' => $model
            ]);
            return trim($content);
        }

        \Log::debug('Gemini returned success but no text content found.', [
            'raw_body' => Str::limit($response->body(), 1000),
            'finish_reason' => $finishReason
        ]);

        return null;
    }

    private function extractJson(string $text): ?array
    {
        $clean = trim($text);
        
        // 1. Try direct decode (cleanest case)
        $decoded = json_decode($clean, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 2. Greedy search for the outermost { ... }
        $start = strpos($clean, '{');
        $end = strrpos($clean, '}');

        if ($start !== false && $end !== false && $end > $start) {
            $snippet = substr($clean, $start, $end - $start + 1);
            
            // Clean up common LLM JSON junk
            // Remove trailing commas before closing braces/brackets
            $snippet = preg_replace('/,\s*([\}\]])/', '$1', $snippet);
            // Remove potential control characters that break json_decode
            $snippet = preg_replace('/[\x00-\x1F\x7F]/u', '', $snippet);

            $decoded = json_decode($snippet, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            
            // Log if we found a snippet but it still failed (helpful for debugging)
            \Log::debug('AI JSON snippet decode failed.', [
                'error' => json_last_error_msg(),
                'snippet_start' => substr($snippet, 0, 50),
            ]);
        }

        // 3. Fallback for arrays [ ... ]
        if (Str::startsWith($clean, '[') || strpos($clean, '[') !== false) {
            $startArr = strpos($clean, '[');
            $endArr = strrpos($clean, ']');
            if ($startArr !== false && $endArr !== false && $endArr > $startArr) {
                $snippet = substr($clean, $startArr, $endArr - $startArr + 1);
                $decoded = json_decode($snippet, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
            }
        }
        
        \Log::debug('AI JSON extraction failed completely.', ['raw_len' => strlen($clean)]);
        return null;
    }
}
