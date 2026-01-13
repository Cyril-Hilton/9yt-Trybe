<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\OrganizationStaff;
use App\Services\MnotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffAuthController extends Controller
{
    protected $mnotify;

    public function __construct(MnotifyService $mnotify)
    {
        $this->mnotify = $mnotify;
    }

    /**
     * Show phone login form
     */
    public function showLoginForm()
    {
        return view('staff.auth.login');
    }

    /**
     * Send OTP to phone
     */
    public function sendOTP(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
        ]);

        $staff = OrganizationStaff::where('phone', $validated['phone'])->first();

        if (!$staff) {
            return back()->withErrors(['phone' => 'Phone number not found.']);
        }

        if ($staff->status !== 'active') {
            return back()->withErrors(['phone' => 'Your account has been suspended. Contact your organizer.']);
        }

        // Generate and save OTP
        $otp = $staff->generateOTP();

        // Send OTP via mNotify SMS
        $message = "Your 9yt !Trybe Scanner verification code is: {$otp}. Valid for 10 minutes. Do not share this code.";
        $smsResult = $this->mnotify->sendSMS($staff->phone, $message);

        if ($smsResult['success']) {
            \Log::info("OTP sent via SMS to {$staff->phone}: {$otp}");

            return back()->with([
                'otp_sent' => true,
                'phone' => $validated['phone'],
                'message' => "Verification code sent to {$validated['phone']}"
            ]);
        }

        // If SMS fails, log OTP for testing but still proceed
        \Log::warning("SMS failed for {$staff->phone}, OTP: {$otp}. Error: " . $smsResult['message']);

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

        $staff = OrganizationStaff::where('phone', $validated['phone'])->first();

        if (!$staff) {
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        if (!$staff->verifyOTP($validated['otp'])) {
            return back()->withErrors(['otp' => 'Invalid or expired verification code.']);
        }

        // Login staff
        Auth::guard('staff')->login($staff);

        return redirect()->route('staff.scanner');
    }

    /**
     * Logout staff
     */
    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login')->with('message', 'Logged out successfully.');
    }
}
