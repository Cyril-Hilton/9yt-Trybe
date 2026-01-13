<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contestant;
use App\Models\Event;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdminPollController extends Controller
{
    /**
     * Display a listing of all polls
     */
    public function index(Request $request)
    {
        $query = Poll::with(['company', 'event'])
            ->withCount('contestants');

        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->type && $request->type !== 'all') {
            $query->where('poll_type', $request->type);
        }

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('company', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $polls = $query->latest()->paginate(20);

        // Stats
        $stats = [
            'total' => Poll::count(),
            'active' => Poll::where('status', 'active')->count(),
            'draft' => Poll::where('status', 'draft')->count(),
            'closed' => Poll::where('status', 'closed')->count(),
            'total_revenue' => Poll::sum('total_revenue'),
        ];

        return view('admin.polls.index', compact('polls', 'stats'));
    }

    /**
     * Show the form for creating a new poll
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $events = Event::where('status', 'approved')
            ->orderBy('start_date', 'desc')
            ->get();

        return view('organization.polls.create', [
            'companies' => $companies,
            'events' => $events,
            'isAdmin' => true,
            'layout' => 'layouts.admin',
            'pollRoutePrefix' => 'admin.polls',
        ]);
    }

    /**
     * Store a newly created poll
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
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

        $validated = $this->normalizeOwnership($validated);
        $validated['status'] = 'active';
        $validated['allow_multiple_votes'] = $request->boolean('allow_multiple_votes');
        $validated['show_results'] = $request->boolean('show_results');
        $validated['require_login'] = $request->boolean('require_login');

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('polls/banners', 'public');
        }

        $poll = Poll::create($validated);

        return redirect()->route('admin.polls.show', $poll)
            ->with('success', 'Poll created successfully!');
    }

    /**
     * Display the specified poll
     */
    public function show(Poll $poll)
    {
        $poll->load(['company', 'event', 'contestants' => function ($query) {
            $query->orderBy('order')->orderBy('contestant_number');
        }]);

        $analytics = [
            'total_votes' => $poll->total_votes,
            'total_revenue' => $poll->total_revenue,
            'unique_voters' => $poll->unique_voters,
            'views_count' => $poll->views_count,
            'contestants_count' => $poll->contestants()->count(),
            'leader' => $poll->getLeader(),
        ];

        return view('organization.polls.show', [
            'poll' => $poll,
            'analytics' => $analytics,
            'isAdmin' => true,
            'layout' => 'layouts.admin',
            'pollRoutePrefix' => 'admin.polls',
        ]);
    }

    /**
     * Show the form for editing the specified poll
     */
    public function edit(Poll $poll)
    {
        $companies = Company::orderBy('name')->get();
        $events = Event::where('status', 'approved')
            ->orderBy('start_date', 'desc')
            ->get();

        return view('organization.polls.edit', [
            'poll' => $poll,
            'companies' => $companies,
            'events' => $events,
            'isAdmin' => true,
            'layout' => 'layouts.admin',
            'pollRoutePrefix' => 'admin.polls',
        ]);
    }

    /**
     * Update the specified poll
     */
    public function update(Request $request, Poll $poll)
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
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

        $validated = $this->normalizeOwnership($validated);
        $validated['allow_multiple_votes'] = $request->boolean('allow_multiple_votes');
        $validated['show_results'] = $request->boolean('show_results');
        $validated['require_login'] = $request->boolean('require_login');

        if ($request->hasFile('banner_image')) {
            if ($poll->banner_image) {
                Storage::disk('public')->delete($poll->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('polls/banners', 'public');
        }

        $poll->update($validated);

        return redirect()->route('admin.polls.show', $poll)
            ->with('success', 'Poll updated successfully!');
    }

    /**
     * Approve a poll (set to active)
     */
    public function approve(Poll $poll)
    {
        $poll->update(['status' => 'active']);

        return back()->with('success', 'Poll approved and is now active.');
    }

    /**
     * Suspend/Close a poll
     */
    public function suspend(Poll $poll)
    {
        $poll->update(['status' => 'suspended']);

        return back()->with('success', 'Poll has been suspended.');
    }

    /**
     * Reactivate a poll
     */
    public function reactivate(Poll $poll)
    {
        $poll->update(['status' => 'active']);

        return back()->with('success', 'Poll has been reactivated.');
    }

    /**
     * Publish a poll
     */
    public function publish(Poll $poll)
    {
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
        $poll->update(['status' => 'closed']);

        return back()->with('success', 'Poll closed successfully!');
    }

    /**
     * Add a contestant to the poll
     */
    public function addContestant(Request $request, Poll $poll)
    {
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
        if ($contestant->poll_id !== $poll->id) {
            abort(404);
        }

        if ($contestant->photo) {
            Storage::disk('public')->delete($contestant->photo);
        }

        $contestant->delete();

        return back()->with('success', 'Contestant removed successfully!');
    }

    /**
     * Delete a poll
     */
    public function destroy(Poll $poll)
    {
        if ($poll->banner_image) {
            Storage::disk('public')->delete($poll->banner_image);
        }

        $poll->delete();

        return redirect()->route('admin.polls.index')
            ->with('success', 'Poll deleted successfully.');
    }

    private function normalizeOwnership(array $validated): array
    {
        $companyId = $validated['company_id'] ?? null;
        $eventId = $validated['event_id'] ?? null;

        if (empty($companyId)) {
            if (!empty($eventId)) {
                throw ValidationException::withMessages([
                    'event_id' => 'Select a company that owns the chosen event.',
                ]);
            }

            $validated['company_id'] = null;
            $validated['event_id'] = null;
            return $validated;
        }

        if (!empty($eventId)) {
            $event = Event::find($eventId);
            if (!$event || $event->company_id !== (int) $companyId) {
                throw ValidationException::withMessages([
                    'event_id' => 'Selected event does not belong to the selected company.',
                ]);
            }
        }

        return $validated;
    }
}
