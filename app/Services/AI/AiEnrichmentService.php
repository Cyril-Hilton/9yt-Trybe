<?php

namespace App\Services\AI;

use App\Models\Company;
use App\Models\Event;
use App\Models\ShopProduct;
use Illuminate\Support\Str;

class AiEnrichmentService
{
    public function __construct(private readonly AIClient $client)
    {
    }

    public function generateEventTagsFaqs(Event $event): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $payload = [
            'title' => $event->title,
            'summary' => $event->summary,
            'overview' => Str::limit(strip_tags((string) $event->overview), 400, ''),
            'venue' => $event->venue_name,
            'region' => $event->region,
            'start_date' => $event->start_date?->toDateTimeString(),
            'end_date' => $event->end_date?->toDateTimeString(),
            'categories' => $event->categories?->pluck('name')->values()->all(),
        ];

        return $this->generateTagsFaqs('event', $payload);
    }

    public function generateOrganizerTagsFaqs(Company $company): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $payload = [
            'name' => $company->name,
            'description' => $company->description,
            'website' => $company->website,
            'events' => $company->events()->approved()->latest('start_date')->take(5)->pluck('title')->values()->all(),
        ];

        return $this->generateTagsFaqs('organizer', $payload);
    }

    public function generateProductTagsFaqs(ShopProduct $product): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $payload = [
            'name' => $product->name,
            'description' => $product->description,
            'category' => $product->category,
            'price' => $product->price,
            'stock' => $product->stock,
        ];

        return $this->generateTagsFaqs('product', $payload);
    }

    private function generateTagsFaqs(string $type, array $payload): ?array
    {
        $tagCount = (int) config('services.ai.enrichment.tag_count', 10);
        $faqCount = (int) config('services.ai.enrichment.faq_count', 5);

        $system = 'You are an assistant that generates SEO tags and FAQs. '
            . 'Return ONLY valid JSON. Do not invent facts.';

        $user = "Generate tags and FAQs for this {$type}.\n\n"
            . "DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- tags: {$tagCount} short keywords (lowercase ok)\n"
            . "- faqs: {$faqCount} helpful Q&A items\n"
            . "- No invented facts or prices\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"tags\":[\"...\"],\"faqs\":[{\"question\":\"...\",\"answer\":\"...\"}]}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 450,
            'temperature' => 0.4,
        ]);

        if (!$result) {
            return null;
        }

        $tags = $result['tags'] ?? [];
        if (!is_array($tags)) {
            $tags = [];
        }
        $tags = array_values(array_filter(array_map(function ($item) {
            $item = trim((string) $item);
            return $item !== '' ? $item : null;
        }, $tags)));
        $tags = array_slice(array_unique($tags), 0, $tagCount);

        $faqs = $result['faqs'] ?? [];
        if (!is_array($faqs)) {
            $faqs = [];
        }
        $faqs = array_values(array_filter(array_map(function ($item) {
            if (!is_array($item)) {
                return null;
            }
            $question = trim((string) ($item['question'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? ''));
            if ($question === '' || $answer === '') {
                return null;
            }
            return [
                'question' => $question,
                'answer' => $answer,
            ];
        }, $faqs)));
        $faqs = array_slice($faqs, 0, $faqCount);

        return [
            'ai_tags' => $tags,
            'ai_faqs' => $faqs,
        ];
    }
}
