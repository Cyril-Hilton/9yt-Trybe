<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Conference;
use App\Models\Registration;
use App\Models\Survey;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_companies' => Company::count(),
            'active_companies' => Company::where('is_suspended', false)->count(),
            'suspended_companies' => Company::where('is_suspended', true)->count(),
            'total_conferences' => Conference::count(),
            'total_registrations' => Registration::count(),
            'total_surveys' => Survey::count(),
            'total_admins' => Admin::where('is_active', true)->count(),
        ];

        // Recent companies
        $recentCompanies = Company::latest()->take(5)->get();

        // Recent registrations
        $recentRegistrations = Registration::with(['conference', 'conference.company'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentCompanies', 'recentRegistrations'));
    }
}
