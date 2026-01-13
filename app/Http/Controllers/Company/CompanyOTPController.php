<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\MnotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyOTPController extends Controller
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

        $company = Company::where('phone', $validated['phone'])->first();

        if (!$company) {
            return back()->withErrors(['phone' => 'Phone number not found.']);
        }

        if ($company->is_suspended) {
            return back()->withErrors(['phone' => 'Your account has been suspended. Please contact support.']);
        }

        // Generate and save OTP
        $otp = $company->generateOTP();

        // Send OTP via mNotify SMS
        $message = "Your 9yt !Trybe verification code is: {$otp}. Valid for 10 minutes. Do not share this code.";
        $smsResult = $this->mnotify->sendSMS($company->phone, $message);

        if ($smsResult['success']) {
            \Log::info("OTP sent via SMS to {$company->phone}: {$otp}");

            return back()->with([
                'otp_sent' => true,
                'phone' => $validated['phone'],
                'message' => "Verification code sent to {$validated['phone']}"
            ]);
        }

        // If SMS fails, log OTP for testing but still proceed
        \Log::warning("SMS failed for {$company->phone}, OTP: {$otp}. Error: " . $smsResult['message']);

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

        $company = Company::where('phone', $validated['phone'])->first();

        if (!$company) {
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        if (!$company->verifyOTP($validated['otp'])) {
            return back()->withErrors(['otp' => 'Invalid or expired verification code.']);
        }

        // Login company
        Auth::guard('company')->login($company);

        return redirect()->route('organization.dashboard')->with('success', 'Logged in successfully!');
    }
}
