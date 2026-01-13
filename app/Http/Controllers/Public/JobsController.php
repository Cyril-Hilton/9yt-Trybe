<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JobPortfolio;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function index()
    {
        $portfolios = JobPortfolio::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('public.jobs.index', compact('portfolios'));
    }

    public function create()
    {
        return view('public.jobs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'job_type' => 'required|string|max:255',
            'portfolio_link' => 'required|url',
            'profile_picture' => 'nullable|image',
        ]);

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('portfolios', 'public');
        }

        JobPortfolio::create($validated);

        return redirect()->route('jobs.index')->with('success', 'Your portfolio has been submitted for review!');
    }
}
