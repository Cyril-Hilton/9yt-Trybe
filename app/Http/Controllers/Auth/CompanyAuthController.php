<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class CompanyAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('company.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:companies'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $company = Company::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'website' => $validated['website'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        Auth::guard('company')->login($company);

        return redirect()->route('organization.dashboard')
            ->with('success', 'Welcome! Your account has been created successfully.');
    }

    public function showLoginForm()
    {
        return view('company.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('company')->attempt($credentials, $request->boolean('remember'))) {
            $company = Auth::guard('company')->user();

            // Check if company is suspended
            if ($company->is_suspended) {
                Auth::guard('company')->logout();

                $message = 'Your account has been suspended. ';
                $message .= $company->suspension_reason
                    ? $company->suspension_reason . ' '
                    : '';
                $message .= 'Please contact the administrator for more information.';

                return back()->withErrors([
                    'email' => $message,
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('organization.dashboard'))
                ->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('company')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('organization.login')
            ->with('success', 'You have been logged out successfully.');
    }

    public function showForgotPasswordForm()
    {
        return view('company.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::broker('companies')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        return view('company.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $status = Password::broker('companies')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($company, $password) {
                $company->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $company->save();

                event(new PasswordReset($company));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('organization.login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
