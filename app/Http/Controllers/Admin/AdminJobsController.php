<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPortfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminJobsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = JobPortfolio::query()->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $portfolios = $query->paginate(20);

        return view('admin.jobs.index', compact('portfolios', 'status'));
    }

    public function create()
    {
        return view('admin.jobs.create');
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
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ]);

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('portfolios', 'public');
        }

        JobPortfolio::create($validated);

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Portfolio added successfully.');
    }

    public function approve(JobPortfolio $portfolio)
    {
        $portfolio->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Portfolio approved successfully!');
    }

    public function reject(Request $request, JobPortfolio $portfolio)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $portfolio->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->back()->with('success', 'Portfolio rejected.');
    }

    public function destroy(JobPortfolio $portfolio)
    {
        // Delete profile picture file if it exists
        if ($portfolio->profile_picture) {
            Storage::disk('public')->delete($portfolio->profile_picture);
        }

        $portfolio->delete();

        return redirect()->back()->with('success', 'Portfolio deleted successfully.');
    }
}
