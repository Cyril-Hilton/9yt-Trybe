<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MnotifyService;
use App\Support\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserOTPController extends Controller
{
    protected $mnotify;

    public function __construct(MnotifyService $mnotify)
    {
        $this->mnotify = $mnotify;
    }

    /**
     * Send OTP to phone
     */
    public function sendOTP(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
        ]);

        $rawPhone = $validated['phone'];
        $normalizedPhone = PhoneNumber::normalize($rawPhone) ?? $rawPhone;

        $user = User::where('phone', $normalizedPhone)
            ->orWhere('phone', $rawPhone)
            ->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'Phone number not found.']);
        }

        // Generate and save OTP
        $otp = $user->generateOTP();

        // Send OTP via mNotify SMS
        $message = "Your 9yt !Trybe verification code is: {$otp}. Valid for 10 minutes. Do not share this code.";
        $smsResult = $this->mnotify->sendSMS($normalizedPhone, $message);

        if ($smsResult['success']) {
            \Log::info("OTP sent via SMS to {$user->phone}: {$otp}");

            return back()->with([
                'otp_sent' => true,
                'phone' => $rawPhone,
                'message' => "Verification code sent to {$rawPhone}"
            ]);
        }

        // If SMS fails, log OTP for testing but still proceed
        \Log::warning("SMS failed for {$user->phone}, OTP: {$otp}. Error: " . $smsResult['message']);

        return back()->with([
            'otp_sent' => true,
            'phone' => $rawPhone,
            'message' => "OTP sent. For testing (SMS failed), check logs: {$otp}"
        ]);
    }

    /**
     * Verify OTP and login
     */
    public function verifyOTP(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        $rawPhone = $validated['phone'];
        $normalizedPhone = PhoneNumber::normalize($rawPhone) ?? $rawPhone;

        $user = User::where('phone', $normalizedPhone)
            ->orWhere('phone', $rawPhone)
            ->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        if (!$user->verifyOTP($validated['otp'])) {
            return back()->withErrors(['otp' => 'Invalid or expired verification code.']);
        }

        if (!$user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        // Login user
        Auth::guard('web')->login($user);

        // Redirect to intended page or dashboard
        return redirect()->intended(route('home'))->with('success', 'Logged in successfully!');
    }
}
