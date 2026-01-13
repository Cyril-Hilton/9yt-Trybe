<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSwitchController extends Controller
{
    public function switchToOrganizer(Request $request)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('user.login')
                ->with('error', 'Please login first.');
        }

        $user = Auth::user();

        // Check if there's an organization account with the same email
        $company = Company::where('email', $user->email)->first();

        if (!$company) {
            // No organization account found - redirect to registration
            return redirect()->route('organization.register')
                ->with('info', 'You don\'t have an organization account yet. Create one to manage events!');
        }

        // Check if organization is suspended
        if ($company->is_suspended) {
            return redirect()->back()
                ->with('error', 'Your organization account has been suspended. Please contact support.');
        }

        // Logout from user guard
        Auth::logout();

        // Login to company guard
        Auth::guard('company')->login($company);

        // Regenerate session for security
        $request->session()->regenerate();

        return redirect()->route('organization.dashboard')
            ->with('success', 'Switched to organization account successfully!');
    }

    public function switchToUser(Request $request)
    {
        // Ensure company is authenticated
        if (!Auth::guard('company')->check()) {
            return redirect()->route('organization.login')
                ->with('error', 'Please login first.');
        }

        $company = Auth::guard('company')->user();

        // Check if there's a user account with the same email
        $user = \App\Models\User::where('email', $company->email)->first();

        if (!$user) {
            // No user account found - redirect to registration
            return redirect()->route('user.register')
                ->with('info', 'You don\'t have a user account yet. Create one to purchase tickets!');
        }

        // Logout from company guard
        Auth::guard('company')->logout();

        // Login to user guard
        Auth::login($user);

        // Regenerate session for security
        $request->session()->regenerate();

        return redirect()->route('user.dashboard')
            ->with('success', 'Switched to user account successfully!');
    }
}
