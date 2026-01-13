<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to OAuth provider
     *
     * @param string $provider (google, microsoft, yahoo)
     * @param string $guard (web, company, admin)
     */
    public function redirect($provider, $guard = 'web')
    {
        // Validate provider
        if (!in_array($provider, ['google', 'microsoft', 'yahoo'])) {
            abort(404);
        }

        // Store guard in session to use after callback
        session(['oauth_guard' => $guard]);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle OAuth callback
     *
     * @param string $provider (google, microsoft, yahoo)
     */
    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            $guard = session('oauth_guard', 'web');

            // Determine which model and redirect to use based on guard
            switch ($guard) {
                case 'company':
                    $user = $this->findOrCreateCompany($socialUser, $provider);
                    Auth::guard('company')->login($user);
                    return redirect()->route('organization.dashboard');

                case 'admin':
                    $user = $this->findOrCreateAdmin($socialUser, $provider);
                    Auth::guard('admin')->login($user);
                    return redirect()->route('admin.dashboard');

                default: // web guard (attendees)
                    $user = $this->findOrCreateUser($socialUser, $provider);
                    Auth::guard('web')->login($user);
                    return redirect()->route('home');
            }
        } catch (\Exception $e) {
            \Log::error("OAuth {$provider} callback error: " . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'Authentication failed. Please try again.']);
        }
    }

    /**
     * Find or create a User (attendee)
     */
    private function findOrCreateUser($socialUser, $provider)
    {
        // Try to find by email first
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(32)), // Random password
                'email_verified_at' => now(),
                'oauth_provider' => $provider,
                'oauth_id' => $socialUser->getId(),
            ]);
        } else {
            // Update OAuth info if not set
            if (!$user->oauth_provider) {
                $user->update([
                    'oauth_provider' => $provider,
                    'oauth_id' => $socialUser->getId(),
                ]);
            }
        }

        return $user;
    }

    /**
     * Find or create a Company (organizer)
     */
    private function findOrCreateCompany($socialUser, $provider)
    {
        // Try to find by email first
        $company = Company::where('email', $socialUser->getEmail())->first();

        if (!$company) {
            // Create new company
            $company = Company::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(32)), // Random password
                'email_verified_at' => now(),
                'oauth_provider' => $provider,
                'oauth_id' => $socialUser->getId(),
            ]);
        } else {
            // Update OAuth info if not set
            if (!$company->oauth_provider) {
                $company->update([
                    'oauth_provider' => $provider,
                    'oauth_id' => $socialUser->getId(),
                ]);
            }
        }

        return $company;
    }

    /**
     * Find or create an Admin
     */
    private function findOrCreateAdmin($socialUser, $provider)
    {
        // Try to find by email first
        $admin = Admin::where('email', $socialUser->getEmail())->first();

        if (!$admin) {
            // For admins, we should NOT auto-create accounts
            // Only existing admin emails should be able to use OAuth
            throw new \Exception('Admin account not found. Please contact system administrator.');
        }

        // Update OAuth info if not set
        if (!$admin->oauth_provider) {
            $admin->update([
                'oauth_provider' => $provider,
                'oauth_id' => $socialUser->getId(),
            ]);
        }

        return $admin;
    }
}
