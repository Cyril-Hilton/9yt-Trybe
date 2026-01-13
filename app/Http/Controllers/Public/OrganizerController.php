<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Company;

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

    public function show(string $slug)
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

        return view('public.organizers.show', compact(
            'organizer',
            'upcomingEvents',
            'pastEvents',
            'approvedEventsCount',
            'followersCount'
        ));
    }
}
