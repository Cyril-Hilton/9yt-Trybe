<?php

namespace App\Services\SEO;

use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EventSeoAutomation
{
    public function prepare(Event $event): void
    {
        if ($event->status !== 'approved') {
            return;
        }

        $updates = [];
        $region = trim((string) $event->region) ?: 'Ghana';
        $date = $event->start_date?->format('F j, Y');
        $venue = trim((string) $event->venue_name)
            ?: ($event->location_type === 'online' ? 'online' : $region);

        if (blank($event->meta_title)) {
            $updates['meta_title'] = Str::limit(
                "{$event->title} in {$region} | Tickets & Details",
                60,
                ''
            );
        }

        if (blank($event->meta_description)) {
            $fallback = "Discover {$event->title} in {$region}"
                . ($date ? " on {$date}" : '')
                . ". View event details, venue information and ticket options on 9yt !Trybe.";

            $updates['meta_description'] = Str::limit(
                strip_tags((string) ($event->summary ?: $event->overview ?: $fallback)),
                155,
                ''
            );
        }

        if (empty($event->ai_tags)) {
            $titleTags = collect(preg_split('/[^\pL\pN]+/u', Str::lower($event->title)))
                ->filter(fn ($word) => mb_strlen($word) >= 3)
                ->take(8);
            $categoryTags = $event->categories()->pluck('name');

            $updates['ai_tags'] = $titleTags
                ->merge($categoryTags)
                ->merge([$region, $event->event_type, 'events in Ghana', 'event tickets'])
                ->filter()
                ->map(fn ($tag) => trim((string) $tag))
                ->unique(fn ($tag) => Str::lower($tag))
                ->values()
                ->take(15)
                ->all();
        }

        if (empty($event->ai_faqs)) {
            $updates['ai_faqs'] = array_values(array_filter([
                $date ? [
                    'question' => "When is {$event->title}?",
                    'answer' => "{$event->title} is scheduled for {$date}. Check the event page for any organizer updates.",
                ] : null,
                [
                    'question' => "Where is {$event->title} happening?",
                    'answer' => "{$event->title} will take place at {$venue}. Full location details are shown on the event page.",
                ],
                [
                    'question' => "How can I get tickets for {$event->title}?",
                    'answer' => "Use the ticket options on this 9yt !Trybe event page to check availability and complete your booking.",
                ],
            ]));
        }

        if ($updates !== []) {
            $event->forceFill($updates)->saveQuietly();
        }

        foreach (array_filter([
            route('home'),
            route('events.index'),
            route('sitemap'),
            $event->public_url,
        ]) as $url) {
            Cache::forget('route_cache:' . sha1($url));
        }
    }
}
