<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class AdminTeamController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $role = $request->get('role', 'all');

        $query = TeamMember::query()->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($role !== 'all' && $role !== '') {
            $query->where('role', $role);
        }

        $members = $query->paginate(20);

        return view('admin.team.index', compact('members', 'status', 'role'));
    }

    public function create()
    {
        return view('admin.team.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'job_description' => 'required|string',
            'portfolio_link' => 'nullable|url',
            'contact_number' => 'required|string|max:20',
            'socials' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ]);

        TeamMember::create($validated);

        return redirect()->route('admin.team.index')
            ->with('success', 'Team member added successfully.');
    }

    public function approve(TeamMember $member)
    {
        $member->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Team member approved successfully!');
    }

    public function reject(Request $request, TeamMember $member)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $member->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->back()->with('success', 'Application rejected.');
    }

    public function destroy(TeamMember $member)
    {
        $member->delete();

        return redirect()->back()->with('success', 'Application deleted successfully.');
    }
}
