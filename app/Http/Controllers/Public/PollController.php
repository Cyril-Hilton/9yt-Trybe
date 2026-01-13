<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Poll;

class PollController extends Controller
{
    public function index()
    {
        $polls = Poll::active()
            ->with(['company:id,name,slug'])
            ->withCount(['contestants' => function ($q) {
                $q->where('status', 'active');
            }])
            ->orderByDesc('start_date')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('public.polls.index', compact('polls'));
    }

    public function show(string $slug)
    {
        $poll = Poll::where('slug', $slug)
            ->whereIn('status', ['active', 'closed'])
            ->with([
                'company:id,name,slug',
                'contestants' => function ($q) {
                    $q->where('status', 'active')->orderByDesc('total_votes');
                },
            ])
            ->firstOrFail();

        $poll->incrementViews();

        $leader = $poll->getLeader();
        $isActive = $poll->isActive();
        $isClosed = $poll->isClosed();

        return view('public.polls.show', compact('poll', 'leader', 'isActive', 'isClosed'));
    }
}
