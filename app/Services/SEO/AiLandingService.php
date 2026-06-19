<?php

namespace App\Services\SEO;

use Illuminate\Support\Facades\Cache;

class AiLandingService
{
    public function generateRegionLanding(string $region, array $eventTitles): array
    {
        $key = 'ai:landing:region:' . md5($region . '|' . implode('|', $eventTitles));

        return $this->cachedOrFallback($key, [
            'headline' => "Events in {$region}",
            'intro' => "Discover upcoming concerts, nightlife, business events, festivals, and community experiences in {$region}. Compare dates, venues, and ticket options on 9yt !Trybe.",
            'meta_title' => "Events in {$region} & Tickets | 9yt !Trybe",
            'meta_description' => "Find upcoming events in {$region}, Ghana. Explore concerts, nightlife, festivals, business events, venues, and tickets on 9yt !Trybe.",
        ]);
    }

    public function generateCategoryLanding(string $category, array $eventTitles): array
    {
        $key = 'ai:landing:category:' . md5($category . '|' . implode('|', $eventTitles));

        return $this->cachedOrFallback($key, [
            'headline' => "{$category} Events",
            'intro' => "Explore upcoming {$category} events, compare venues and dates, and book tickets through 9yt !Trybe.",
            'meta_title' => "{$category} Events & Tickets | 9yt !Trybe",
            'meta_description' => "Discover upcoming {$category} events in Ghana. Browse dates, venues, organizers, and ticket options on 9yt !Trybe.",
        ]);
    }

    public function generateTimeLanding(string $label, array $eventTitles): array
    {
        $key = 'ai:landing:time:' . md5($label . '|' . implode('|', $eventTitles));

        return $this->cachedOrFallback($key, [
            'headline' => "{$label} Events",
            'intro' => "See what is happening {$label} across Ghana. Browse live event listings, venues, organizers, and ticket options on 9yt !Trybe.",
            'meta_title' => "{$label} Events in Ghana | 9yt !Trybe",
            'meta_description' => "Find {$label} events in Ghana. Explore concerts, nightlife, festivals, business events, venues, and tickets on 9yt !Trybe.",
        ]);
    }

    private function cachedOrFallback(string $key, array $fallback): array
    {
        $cached = Cache::get($key);

        return is_array($cached) && !empty($cached)
            ? array_merge($fallback, $cached)
            : $fallback;
    }
}
