<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\Contestant;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PollController extends Controller
{
    /**
     * Display a listing of polls
     */
    public function index()
    {
        $company = auth('company')->user();

        $polls = Poll::where('company_id', $company->id)
            ->with(['event', 'contestants'])
            ->withCount('contestants')
            ->latest()
            ->paginate(15);

        return view('organization.polls.index', compact('polls'));
    }

    /**
     * Show the form for creating a new poll
     */
    public function create()
    {
        $company = auth('company')->user();
        $events = Event::where('company_id', $company->id)
            ->where('status', 'approved')
            ->orderBy('start_date', 'desc')
            ->get();

        return view('organization.polls.create', compact('events'));
    }

    /**
     * Store a newly created poll
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_id' => 'nullable|exists:events,id',
            'banner_image' => 'nullable|image',
            'poll_type' => 'required|in:pageant,voting,survey',
            'voting_type' => 'required|in:free,paid',
            'vote_price' => 'required_if:voting_type,paid|nullable|numeric|min:0',
            'votes_per_transaction' => 'nullable|integer|min:1',
            'allow_multiple_votes' => 'boolean',
            'max_votes_per_user' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'show_results' => 'boolean',
            'require_login' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $validated['company_id'] = auth('company')->id();
        $validated['status'] = 'draft';

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('polls/banners', 'public');
        }

        $poll = Poll::create($validated);

        return redirect()->route('organization.polls.show', $poll)
            ->with('success', 'Poll created successfully! Add contestants to get started.');
    }

    /**
     * Display the specified poll
     */
    public function show(Poll $poll)
    {
        // Check if poll belongs to this company
        if ((int) $poll->company_id !== (int) auth('company')->id()) {
            abort(403);
        }

        $poll->load(['contestants' => function ($query) {
            $query->orderBy('order')->orderBy('contestant_number');
        }, 'event']);

        $analytics = [
            'total_votes' => $poll->total_votes,
            'total_revenue' => $poll->total_revenue,
            'unique_voters' => $poll->unique_voters,
            'views_count' => $poll->views_count,
            'contestants_count' => $poll->contestants()->count(),
            'leader' => $poll->getLeader(),
        ];

        return view('organization.polls.show', compact('poll', 'analytics'));
    }

    /**
     * Show the form for editing the specified poll
     */
    public function edit(Poll $poll)
    {
        // Check if poll belongs to this company
        if ((int) $poll->company_id !== (int) auth('company')->id()) {
            abort(403);
        }

        $company = auth('company')->user();
        $events = Event::where('company_id', $company->id)
            ->where('status', 'approved')
            ->orderBy('start_date', 'desc')
            ->get();

        return view('organization.polls.edit', compact('poll', 'events'));
    }

    /**
     * Update the specified poll
     */
    public function update(Request $request, Poll $poll)
    {
        // Check if poll belongs to this company
        if ((int) $poll->company_id !== (int) auth('company')->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_id' => 'nullable|exists:events,id',
            'banner_image' => 'nullable|image',
            'poll_type' => 'required|in:pageant,voting,survey',
            'voting_type' => 'required|in:free,paid',
            'vote_price' => 'required_if:voting_type,paid|nullable|numeric|min:0',
            'votes_per_transaction' => 'nullable|integer|min:1',
            'allow_multiple_votes' => 'boolean',
            'max_votes_per_user' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'show_results' => 'boolean',
            'require_login' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            if ($poll->banner_image) {
                Storage::disk('public')->delete($poll->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('polls/banners', 'public');
        }

        $poll->update($validated);

        return redirect()->route('organization.polls.show', $poll)
            ->with('success', 'Poll updated successfully!');
    }

    /**
     * Remove the specified poll
     */
    public function destroy(Poll $poll)
    {
        // Check if poll belongs to this company
        if ((int) $poll->company_id !== (int) auth('company')->id()) {
            abort(403);
        }

        if ($poll->banner_image) {
            Storage::disk('public')->delete($poll->banner_image);
        }

        $poll->delete();

        return redirect()->route('organization.polls.index')
            ->with('success', 'Poll deleted successfully!');
    }

    /**
     * Publish a poll
     */
    public function publish(Poll $poll)
    {
        // Check if poll belongs to this company
        if ((int) $poll->company_id !== (int) auth('company')->id()) {
            abort(403);
        }

        if ($poll->contestants()->count() < 2) {
            return back()->with('error', 'You need at least 2 contestants to publish a poll.');
        }

        $poll->update(['status' => 'active']);

        return back()->with('success', 'Poll published successfully!');
    }

    /**
     * Close a poll
     */
    public function close(Poll $poll)
    {
        // Check if poll belongs to this company
        if ((int) $poll->company_id !== (int) auth('company')->id()) {
            abort(403);
        }

        $poll->update(['status' => 'closed']);

        return back()->with('success', 'Poll closed successfully!');
    }

    /**
     * Add a contestant to the poll
     */
    public function addContestant(Request $request, Poll $poll)
    {
        // Check if poll belongs to this company
        if ((int) $poll->company_id !== (int) auth('company')->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'contestant_number' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'required|image',
            'video_url' => 'nullable|url',
            'social_media' => 'nullable|array',
        ]);

        $validated['poll_id'] = $poll->id;
        $validated['status'] = 'active';
        $validated['order'] = $poll->contestants()->count() + 1;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('contestants', 'public');
        }

        Contestant::create($validated);

        return back()->with('success', 'Contestant added successfully!');
    }

    /**
     * Remove a contestant
     */
    public function removeContestant(Poll $poll, Contestant $contestant)
    {
        // Check if poll belongs to this company
        if ((int) $poll->company_id !== (int) auth('company')->id()) {
            abort(403);
        }

        if ($contestant->poll_id !== $poll->id) {
            abort(404);
        }

        if ($contestant->photo) {
            Storage::disk('public')->delete($contestant->photo);
        }

        $contestant->delete();

        return back()->with('success', 'Contestant removed successfully!');
    }
}

