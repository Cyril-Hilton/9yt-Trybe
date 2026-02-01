<?php

namespace App\Services\Growth;

use App\Models\AiInsight;
use App\Models\Category;
use App\Models\Company;
use App\Models\Event;
use App\Models\Poll;
use App\Services\AI\AIClient;

class AiGrowthService
{
    public function __construct(private readonly AIClient $client)
    {
    }

    public function generateWeeklyDigest(): ?AiInsight
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $topEvents = Event::approved()
            ->upcoming()
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get(['title', 'start_date', 'region', 'views_count'])
            ->map(fn ($event) => [
                'title' => $event->title,
                'date' => $event->start_date?->format('M d, Y'),
                'region' => $event->region,
                'views' => $event->views_count,
            ])
            ->toArray();

        $topCategories = Category::active()
            ->withCount('events')
            ->orderBy('events_count', 'desc')
            ->take(5)
            ->get(['name'])
            ->map(fn ($category) => [
                'name' => $category->name,
                'events_count' => $category->events_count,
            ])
            ->toArray();

        $topPolls = Poll::where('status', 'active')
            ->orderBy('total_votes', 'desc')
            ->take(5)
            ->get(['title', 'total_votes'])
            ->map(fn ($poll) => [
                'title' => $poll->title,
                'votes' => $poll->total_votes,
            ])
            ->toArray();

        $payload = [
            'top_events' => $topEvents,
            'top_categories' => $topCategories,
            'top_polls' => $topPolls,
        ];

        $system = 'You are a growth analyst for an events platform. '
            . 'Write a weekly insight summary. Return ONLY valid JSON.';

        $user = "Create a weekly trends digest.\n\n"
            . "DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- summary: 3-4 sentences\n"
            . "- trends: 3 bullet insights\n"
            . "- recommendations: 3 action items\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"summary\":\"...\",\"trends\":[\"...\"],\"recommendations\":[\"...\"]}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 380,
            'temperature' => 0.4,
        ]);

        if (!$result) {
            return null;
        }

        return AiInsight::create([
            'type' => 'weekly_trends',
            'payload' => $result,
        ]);
    }

    public function generateOrganizerTips(Company $company): ?AiInsight
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $events = $company->events()
            ->approved()
            ->latest('start_date')
            ->take(5)
            ->get(['title', 'start_date', 'views_count', 'tickets_sold'])
            ->map(fn ($event) => [
                'title' => $event->title,
                'date' => $event->start_date?->format('M d, Y'),
                'views' => $event->views_count,
                'tickets_sold' => $event->tickets_sold,
            ])
            ->toArray();

        $payload = [
            'organizer' => $company->name,
            'events' => $events,
            'total_events' => $company->events()->approved()->count(),
        ];

        $system = 'You are a growth coach for event organizers. '
            . 'Give practical, non-generic tips. Return ONLY valid JSON.';

        $user = "Create optimization tips for this organizer.\n\n"
            . "DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- headline: short title\n"
            . "- tips: 3-5 actionable items\n"
            . "- quick_wins: 2 short ideas\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"headline\":\"...\",\"tips\":[\"...\"],\"quick_wins\":[\"...\"]}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 320,
            'temperature' => 0.5,
        ]);

        if (!$result) {
            return null;
        }

        return AiInsight::create([
            'type' => 'organizer_tips',
            'subject_type' => Company::class,
            'subject_id' => $company->id,
            'payload' => $result,
        ]);
    }

    public function generateSocialSnippets(Event $event): ?AiInsight
    {
        if (!$this->client->isAvailable()) {
            return null;
        }

        $payload = [
            'title' => $event->title,
            'summary' => $event->summary,
            'date' => $event->start_date?->format('M d, Y'),
            'venue' => $event->venue_name,
            'region' => $event->region,
            'url' => $event->public_url ?? url('/events/' . $event->slug),
        ];

        $system = 'You are a social media copywriter for events. '
            . 'Write short, energetic posts. Return ONLY valid JSON.';

        $user = "Create social snippets for this event.\n\n"
            . "DATA:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n"
            . "REQUIREMENTS:\n"
            . "- snippets: 3 short posts (Twitter/IG style)\n"
            . "- hashtags: 3-6 hashtags per post\n"
            . "- include CTA to book\n\n"
            . "OUTPUT JSON SHAPE:\n"
            . "{\"snippets\":[{\"text\":\"...\",\"hashtags\":[\"...\"]}]}";

        $result = $this->client->generateJson($system, $user, [
            'max_tokens' => 380,
            'temperature' => 0.6,
        ]);

        if (!$result) {
            return null;
        }

        return AiInsight::create([
            'type' => 'social_snippets',
            'subject_type' => Event::class,
            'subject_id' => $event->id,
            'payload' => $result,
        ]);
    }
}
