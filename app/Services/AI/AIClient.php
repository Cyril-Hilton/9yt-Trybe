<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
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

        $response = Http::timeout(25)
            ->retry(1, 200)
            ->when(app()->environment('local'), fn($h) => $h->withoutVerifying())
            ->withToken($apiKey)
            ->acceptJson()
            ->post($endpoint, $payload);

        if (!$response->successful()) {
            \Log::warning('OpenAI response failed.', [
                'status' => $response->status(),
                'provider' => 'openai',
            ]);
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

        $response = Http::timeout(25)
            ->retry(1, 200)
            ->when(app()->environment('local'), fn($h) => $h->withoutVerifying())
            ->acceptJson()
            ->post($endpoint, $payload);

        if (!$response->successful()) {
            \Log::warning('Gemini response failed.', [
                'status' => $response->status(),
                'provider' => 'gemini',
            ]);
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

        return is_string($content) ? trim($content) : null;
    }

    private function extractJson(string $text): ?array
    {
        $clean = trim($text);
        $clean = preg_replace('/^```(?:json)?/i', '', $clean);
        $clean = preg_replace('/```$/', '', $clean);
        $clean = trim($clean);

        $decoded = json_decode($clean, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        $start = strpos($clean, '{');
        $end = strrpos($clean, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $snippet = substr($clean, $start, $end - $start + 1);
            $decoded = json_decode($snippet, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        if (Str::startsWith($clean, '[') && Str::endsWith($clean, ']')) {
            $decoded = json_decode($clean, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
