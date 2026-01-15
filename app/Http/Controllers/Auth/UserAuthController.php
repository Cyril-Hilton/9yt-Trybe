<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class UserAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('user.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'full_phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $request->input('full_phone') ?? $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        // Trigger email verification
        event(new Registered($user));

        Auth::login($user);

        // Merge any cart items from session
        $this->mergeCartItems();

        // Check if there's a redirect parameter or use home as default
        // Security: Validate redirect URL to prevent open redirect vulnerability
        $redirect = $request->input('redirect', route('home'));
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $redirectHost = parse_url($redirect, PHP_URL_HOST);

        // Only allow redirects to same host or relative URLs
        if ($redirectHost !== null && $redirectHost !== $appHost) {
            $redirect = route('home');
        }

        return redirect($redirect)
            ->with('success', 'Welcome! Your account has been created successfully. Please check your email to verify your account.');
    }

    public function showLoginForm()
    {
        return view('user.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Merge cart items from session to user
            $this->mergeCartItems();

            // Check if there's a redirect parameter or use home as default
            // Security: Validate redirect URL to prevent open redirect vulnerability
            $redirect = $request->input('redirect', route('home'));
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);
            $redirectHost = parse_url($redirect, PHP_URL_HOST);

            // Only allow redirects to same host or relative URLs
            if ($redirectHost !== null && $redirectHost !== $appHost) {
                $redirect = route('home');
            }

            return redirect($redirect)
                ->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Merge cart items from session to authenticated user
     */
    protected function mergeCartItems()
    {
        $sessionId = session()->getId();
        $userId = auth()->id();

        if ($userId) {
            \App\Models\CartItem::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->update(['user_id' => $userId, 'session_id' => null]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }

    public function showForgotPasswordForm()
    {
        return view('user.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        return view('user.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('user.login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
