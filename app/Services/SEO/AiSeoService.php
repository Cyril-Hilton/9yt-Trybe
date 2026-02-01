<?php

namespace App\Services\SEO;

use App\Models\Article;
use App\Models\Category;
use App\Models\Company;
use App\Models\Conference;
use App\Models\Event;
use App\Models\Poll;
use App\Models\ShopProduct;
use App\Models\Survey;
use App\Services\AI\AIClient;
use Illuminate\Support\Str;

class AiSeoService
{
    public function __construct(private readonly AIClient $client)
    {
    }

    public function generateEventMeta(Event $event): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $payload = [
            'title' => $event->title,
            'summary' => $event->summary,
            'overview' => $event->overview,
            'venue_name' => $event->venue_name,
            'region' => $event->region,
            'start_date' => $event->start_date?->toDateTimeString(),
            'end_date' => $event->end_date?->toDateTimeString(),
            'location_type' => $event->location_type,
            'event_type' => $event->event_type,
            'categories' => $event->categories?->pluck('name')->values()->all(),
        ];

        $system = 'You are an SEO expert for an events marketplace. '
            . 'Write metadata that is accurate, natural, and clickable. '
            . 'Return ONLY valid JSON.';

        $user = "Create SEO metadata for this event.\n\n"
            . "EVENT DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- meta_title: 50-60 chars.\n"
            . "- meta_description: 140-155 chars.\n"
            . "- No pricing unless provided.\n"
            . "- No invented facts.\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"meta_title\": \"...\", \"meta_description\": \"...\"}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 220,
            'temperature' => 0.4,
        ]);

        return $this->normalizeMeta($result);
    }

    public function generatePollMeta(Poll $poll): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $payload = [
            'title' => $poll->title,
            'description' => $poll->description,
            'poll_type' => $poll->poll_type,
            'voting_type' => $poll->voting_type,
            'vote_price' => $poll->vote_price,
            'start_date' => $poll->start_date?->toDateTimeString(),
            'end_date' => $poll->end_date?->toDateTimeString(),
        ];

        $system = 'You are an SEO expert for a voting and polls platform. '
            . 'Write metadata that is accurate, natural, and clickable. '
            . 'Return ONLY valid JSON.';

        $user = "Create SEO metadata for this poll.\n\n"
            . "POLL DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- meta_title: 50-60 chars.\n"
            . "- meta_description: 140-155 chars.\n"
            . "- No invented facts.\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"meta_title\": \"...\", \"meta_description\": \"...\"}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 220,
            'temperature' => 0.4,
        ]);

        return $this->normalizeMeta($result);
    }

    public function generateArticleMeta(Article $article): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $payload = [
            'title' => $article->title,
            'summary' => $article->description,
            'content' => Str::limit(strip_tags((string) $article->content), 400, ''),
            'category' => $article->category,
            'type' => $article->type ?? 'blog',
        ];

        $system = 'You are an SEO editor for a blog. '
            . 'Write metadata that is accurate, natural, and clickable. '
            . 'Return ONLY valid JSON.';

        $user = "Create SEO metadata for this blog article.\n\n"
            . "ARTICLE DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- meta_title: 50-60 chars.\n"
            . "- meta_description: 140-155 chars.\n"
            . "- No invented facts.\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"meta_title\": \"...\", \"meta_description\": \"...\"}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 220,
            'temperature' => 0.4,
        ]);

        return $this->normalizeMeta($result);
    }

    public function generateCompanyMeta(Company $company): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $eventSample = $company->events()
            ->approved()
            ->latest('start_date')
            ->take(5)
            ->pluck('title')
            ->values()
            ->all();

        $payload = [
            'name' => $company->name,
            'description' => $company->description,
            'website' => $company->website,
            'event_count' => $company->events()->approved()->count(),
            'recent_events' => $eventSample,
        ];

        $system = 'You are an SEO expert for an events organizer marketplace. '
            . 'Write metadata that is accurate, natural, and clickable. '
            . 'Return ONLY valid JSON.';

        $user = "Create SEO metadata for this organizer profile.\n\n"
            . "ORGANIZER DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- meta_title: 50-60 chars.\n"
            . "- meta_description: 140-155 chars.\n"
            . "- No invented facts.\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"meta_title\": \"...\", \"meta_description\": \"...\"}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 220,
            'temperature' => 0.4,
        ]);

        return $this->normalizeMeta($result);
    }

    public function generateProductMeta(ShopProduct $product): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $payload = [
            'name' => $product->name,
            'description' => $product->description,
            'category' => $product->category,
            'price' => $product->price,
            'status' => $product->status,
        ];

        $system = 'You are an SEO expert for an ecommerce marketplace. '
            . 'Write metadata that is accurate, natural, and clickable. '
            . 'Return ONLY valid JSON.';

        $user = "Create SEO metadata for this product.\n\n"
            . "PRODUCT DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- meta_title: 50-60 chars.\n"
            . "- meta_description: 140-155 chars.\n"
            . "- No invented facts.\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"meta_title\": \"...\", \"meta_description\": \"...\"}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 220,
            'temperature' => 0.4,
        ]);

        return $this->normalizeMeta($result);
    }

    public function generateSurveyMeta(Survey $survey): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $payload = [
            'title' => $survey->title,
            'description' => $survey->description,
            'status' => $survey->status,
            'start_date' => $survey->start_date?->toDateTimeString(),
            'end_date' => $survey->end_date?->toDateTimeString(),
        ];

        $system = 'You are an SEO expert for surveys and feedback forms. '
            . 'Write metadata that is accurate, natural, and clickable. '
            . 'Return ONLY valid JSON.';

        $user = "Create SEO metadata for this survey.\n\n"
            . "SURVEY DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- meta_title: 50-60 chars.\n"
            . "- meta_description: 140-155 chars.\n"
            . "- No invented facts.\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"meta_title\": \"...\", \"meta_description\": \"...\"}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 220,
            'temperature' => 0.4,
        ]);

        return $this->normalizeMeta($result);
    }

    public function generateConferenceMeta(Conference $conference): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $payload = [
            'title' => $conference->title,
            'description' => $conference->description,
            'venue' => $conference->venue,
            'start_date' => $conference->start_date?->toDateTimeString(),
            'end_date' => $conference->end_date?->toDateTimeString(),
            'status' => $conference->status,
        ];

        $system = 'You are an SEO expert for conferences and registrations. '
            . 'Write metadata that is accurate, natural, and clickable. '
            . 'Return ONLY valid JSON.';

        $user = "Create SEO metadata for this conference.\n\n"
            . "CONFERENCE DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- meta_title: 50-60 chars.\n"
            . "- meta_description: 140-155 chars.\n"
            . "- No invented facts.\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"meta_title\": \"...\", \"meta_description\": \"...\"}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 220,
            'temperature' => 0.4,
        ]);

        return $this->normalizeMeta($result);
    }

    public function generateCategoryMeta(Category $category): ?array
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $payload = [
            'name' => $category->name,
            'description' => $category->description,
            'events_count' => $category->events()->count(),
        ];

        $system = 'You are an SEO editor for event categories. '
            . 'Write metadata that is accurate, natural, and clickable. '
            . 'Return ONLY valid JSON.';

        $user = "Create SEO metadata for this event category.\n\n"
            . "CATEGORY DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- meta_title: 50-60 chars.\n"
            . "- meta_description: 140-155 chars.\n"
            . "- No invented facts.\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"meta_title\": \"...\", \"meta_description\": \"...\"}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 220,
            'temperature' => 0.4,
        ]);

        return $this->normalizeMeta($result);
    }

    private function normalizeMeta(?array $result): ?array
    {
        if (!$result) {
            return null;
        }

        $title = trim((string) ($result['meta_title'] ?? ''));
        $description = trim((string) ($result['meta_description'] ?? ''));

        if ($title === '' && $description === '') {
            return null;
        }

        if ($title !== '') {
            $title = Str::limit($title, 60, '');
        }

        if ($description !== '') {
            $description = Str::limit($description, 155, '');
        }

        return [
            'meta_title' => $title,
            'meta_description' => $description,
        ];
    }
}
