<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\SEO\AiLandingService;
use App\Services\SEO\AiTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeoLandingController extends Controller
{
    public function region(Request $request, string $region)
    {
        $regionName = $this->resolveRegionName($region);

        $events = Event::approved()
            ->upcoming()
            ->where('region', $regionName)
            ->orderBy('start_date')
            ->limit((int) config('services.ai.landing.max_events', 16))
            ->get();

        $landing = app(AiLandingService::class)
            ->generateRegionLanding($regionName, $events->pluck('title')->take(8)->all());

        $landing = $this->applyTranslations($request, $landing, "Events in {$regionName}", $regionName);

        return view('public.seo.landing', [
            'landing' => $landing,
            'events' => $events,
            'type' => 'region',
            'context' => $regionName,
        ]);
    }

    public function today(Request $request)
    {
        $start = now()->startOfDay();
        $end = now()->endOfDay();

        $events = Event::approved()
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->orderBy('start_date')
            ->limit((int) config('services.ai.landing.max_events', 16))
            ->get();

        $landing = app(AiLandingService::class)
            ->generateTimeLanding('Today', $events->pluck('title')->take(8)->all());

        $landing = $this->applyTranslations($request, $landing, 'Events Today', 'Today');

        return view('public.seo.landing', [
            'landing' => $landing,
            'events' => $events,
            'type' => 'today',
            'context' => 'Today',
        ]);
    }

    public function weekend(Request $request)
    {
        $now = now();
        $start = $now->copy()->next('Saturday')->startOfDay();
        $end = $now->copy()->next('Sunday')->endOfDay();

        $events = Event::approved()
            ->whereBetween('start_date', [$start, $end])
            ->orderBy('start_date')
            ->limit((int) config('services.ai.landing.max_events', 16))
            ->get();

        $landing = app(AiLandingService::class)
            ->generateTimeLanding('This Weekend', $events->pluck('title')->take(8)->all());

        $landing = $this->applyTranslations($request, $landing, 'This Weekend Events', 'This Weekend');

        return view('public.seo.landing', [
            'landing' => $landing,
            'events' => $events,
            'type' => 'weekend',
            'context' => 'This Weekend',
        ]);
    }

    private function resolveRegionName(string $slug): string
    {
        $slug = strtolower(trim($slug));
        if ($slug === '') {
            return 'Ghana';
        }

        $regions = Event::query()
            ->whereNotNull('region')
            ->where('region', '!=', '')
            ->distinct()
            ->pluck('region')
            ->toArray();

        foreach ($regions as $region) {
            if (Str::slug($region) === $slug) {
                return $region;
            }
        }

        return Str::title(str_replace('-', ' ', $slug));
    }

    private function applyTranslations(Request $request, array $landing, string $fallbackTitle, string $label): array
    {
        $title = $landing['meta_title'] ?? $fallbackTitle;
        $description = $landing['meta_description'] ?? ("Explore {$label} events, tickets, and experiences on 9yt !Trybe.");
        $headline = $landing['headline'] ?? $fallbackTitle;
        $intro = $landing['intro'] ?? '';

        $translator = app(AiTranslationService::class);
        $lang = $translator->resolveLanguage($request->query('lang'));
        if ($lang !== 'en') {
            $translated = $translator->translateMeta($title, $description, $lang);
            $title = $translated['meta_title'];
            $description = $translated['meta_description'];
            $headline = $translator->translateText($headline, $lang);
            $intro = $translator->translateText($intro, $lang);
        }

        return [
            'meta_title' => $title,
            'meta_description' => $description,
            'headline' => $headline,
            'intro' => $intro,
        ];
    }
}
