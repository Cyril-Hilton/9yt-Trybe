<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teamMembers = TeamMember::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('role');

        // Flag for SEO - prevent indexing empty pages
        $isEmpty = $teamMembers->isEmpty();

        return view('public.team.index', compact('teamMembers', 'isEmpty'));
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
        ]);

        TeamMember::create($validated);

        return redirect()->route('team.index')->with('success', 'Your application has been submitted for review!');
    }
}
