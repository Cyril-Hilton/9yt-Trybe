<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\SEO\AiTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganizerController extends Controller
{
    public function index()
    {
        $organizers = Company::where(function ($q) {
                $q->where('is_suspended', false)->orWhereNull('is_suspended');
            })
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->withCount([
                'events as approved_events_count' => function ($q) {
                    $q->approved();
                },
            ])
            ->orderByDesc('approved_events_count')
            ->orderBy('name')
            ->paginate(12);

        return view('public.organizers.index', compact('organizers'));
    }

    public function show(Request $request, string $slug)
    {
        $organizer = Company::where('slug', $slug)
            ->where(function ($q) {
                $q->where('is_suspended', false)->orWhereNull('is_suspended');
            })
            ->firstOrFail();

        $upcomingEvents = $organizer->events()
            ->approved()
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->take(12)
            ->get();

        $pastEvents = $organizer->events()
            ->approved()
            ->past()
            ->orderBy('start_date', 'desc')
            ->take(12)
            ->get();

        $approvedEventsCount = $organizer->events()->approved()->count();
        $followersCount = $organizer->followers()->count();

        $metaOverrides = null;
        $translator = app(AiTranslationService::class);
        $lang = $translator->resolveLanguage($request->query('lang'));
        if ($lang !== 'en') {
            $baseTitle = $organizer->meta_title ?: ($organizer->name . ' - Organizer');
            $baseDescription = $organizer->meta_description ?: ($organizer->description ? Str::limit($organizer->description, 150) : 'Organizer profile and events on 9yt !Trybe.');
            $metaOverrides = $translator->translateMeta($baseTitle, $baseDescription, $lang);
        }

        return view('public.organizers.show', compact(
            'organizer',
            'upcomingEvents',
            'pastEvents',
            'approvedEventsCount',
            'followersCount',
            'metaOverrides'
        ));
    }
}
