<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
    public function index()
    {
        $company = Auth::guard('company')->user();

        // Conference Stats
        $conferenceStats = [
            'total_conferences' => $company->conferences()->count(),
            'active_conferences' => $company->conferences()->where('status', 'active')->count(),
            'total_registrations' => $company->registrations()->count(),
            'total_views' => $company->conferences()->sum('views_count'),
        ];

        // Survey Stats
        $surveyStats = [
            'total_surveys' => $company->surveys()->count(),
            'active_surveys' => $company->surveys()->where('status', 'active')->count(),
            'total_responses' => \App\Models\SurveyResponse::whereHas('survey', function($q) use ($company) {
                $q->where('company_id', $company->id);
            })->count(),
        ];

        // SMS Stats
        $smsStats = [
            'sms_credits' => $company->smsCredit ? $company->smsCredit->balance : 0,
            'total_campaigns' => $company->smsCampaigns()->count(),
            'total_sent' => $company->smsCampaigns()->sum('total_sent'),
        ];

        // Poll Stats
        $pollStats = [
            'total_polls' => $company->polls()->count(),
            'active_polls' => $company->polls()->where('status', 'active')->count(),
            'total_votes' => $company->polls()->sum('total_votes'),
            'poll_revenue' => $company->polls()->sum('total_revenue'),
        ];

        $stats = array_merge($conferenceStats, $surveyStats, $smsStats, $pollStats);

        $recentConferences = $company->conferences()
            ->latest()
            ->take(5)
            ->withCount('registrations')
            ->get();

        $recentRegistrations = $company->registrations()
            ->with(['conference'])
            ->latest()
            ->take(5)
            ->get();

        $upcomingConferences = $company->conferences()
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        // Recent Surveys
        $recentSurveys = $company->surveys()
            ->latest()
            ->take(5)
            ->withCount('responses')
            ->get();

        // Recent SMS Campaigns
        $recentSmsCampaigns = $company->smsCampaigns()
            ->latest()
            ->take(5)
            ->get();

        // Recent Polls
        $recentPolls = $company->polls()
            ->latest()
            ->take(5)
            ->withCount('contestants')
            ->get();

        return view('company.dashboard', compact(
            'stats',
            'recentConferences',
            'recentRegistrations',
            'upcomingConferences',
            'recentSurveys',
            'recentSmsCampaigns',
            'recentPolls'
        ));
    }

    public function editProfile()
    {
        $company = Auth::guard('company')->user();
        return view('company.profile.edit', compact('company'));
    }

    public function updateProfile(Request $request)
    {
        $company = Auth::guard('company')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:companies,email,' . $company->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('company-logos', 'public');
        }

        // Handle password update
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $company->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
            $validated['password'] = Hash::make($request->new_password);
        }

        // Remove password fields from validated data if not changing password
        unset($validated['current_password'], $validated['new_password'], $validated['new_password_confirmation']);

        $company->update($validated);

        return redirect()->route('organization.profile.edit')
            ->with('success', 'Profile updated successfully!');
    }
}
