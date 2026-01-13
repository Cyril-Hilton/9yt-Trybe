<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\MnotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOTPController extends Controller
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

        $admin = Admin::where('phone', $validated['phone'])->first();

        if (!$admin) {
            return back()->withErrors(['phone' => 'Phone number not found.']);
        }

        if (!$admin->is_active) {
            return back()->withErrors(['phone' => 'Your account has been deactivated. Please contact support.']);
        }

        // Generate and save OTP
        $otp = $admin->generateOTP();

        // Send OTP via mNotify SMS
        $message = "Your 9yt !Trybe Admin verification code is: {$otp}. Valid for 10 minutes. Do not share this code.";
        $smsResult = $this->mnotify->sendSMS($admin->phone, $message);

        if ($smsResult['success']) {
            \Log::info("OTP sent via SMS to {$admin->phone}: {$otp}");

            return back()->with([
                'otp_sent' => true,
                'phone' => $validated['phone'],
                'message' => "Verification code sent to {$validated['phone']}"
            ]);
        }

        // If SMS fails, log OTP for testing but still proceed
        \Log::warning("SMS failed for {$admin->phone}, OTP: {$otp}. Error: " . $smsResult['message']);

        return back()->with([
            'otp_sent' => true,
            'phone' => $validated['phone'],
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

        $admin = Admin::where('phone', $validated['phone'])->first();

        if (!$admin) {
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        if (!$admin->verifyOTP($validated['otp'])) {
            return back()->withErrors(['otp' => 'Invalid or expired verification code.']);
        }

        // Login admin
        Auth::guard('admin')->login($admin);

        return redirect()->route('admin.dashboard')->with('success', 'Logged in successfully!');
    }
}
