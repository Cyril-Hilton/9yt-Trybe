<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SmsSenderId;
use App\Services\PaystackService;
use App\Services\Sms\MnotifyProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSmsController extends Controller
{
    protected PaystackService $paystackService;
    protected MnotifyProvider $mnotifyProvider;

    public function __construct(PaystackService $paystackService, MnotifyProvider $mnotifyProvider)
    {
        $this->paystackService = $paystackService;
        $this->mnotifyProvider = $mnotifyProvider;
    }

    /**
     * Display pending sender ID requests
     */
    public function senderIdRequests(Request $request)
    {
        $query = SmsSenderId::with(['owner', 'reviewer']);

        // Filter by status if requested
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $senderIds = $query->latest()->paginate(20);

        // Get counts for statistics
        $pendingCount = SmsSenderId::where('status', 'pending')->count();
        $approvedCount = SmsSenderId::where('status', 'approved')->count();
        $rejectedCount = SmsSenderId::where('status', 'rejected')->count();

        return view('admin.sms.sender-ids', compact('senderIds', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    /**
     * Approve a sender ID request
     */
    public function approveSenderId(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();
        $senderId = SmsSenderId::findOrFail($id);

        // Automatically register with Mnotify
        $mnotifyResult = $this->mnotifyProvider->registerSenderId(
            $senderId->sender_id,
            $senderId->purpose ?? 'For sending SMS messages via Conference Portal'
        );

        // Log the Mnotify registration result
        \Log::info('Sender ID auto-registration with Mnotify', [
            'sender_id' => $senderId->sender_id,
            'mnotify_success' => $mnotifyResult['success'],
            'mnotify_status' => $mnotifyResult['status'] ?? null,
            'mnotify_error' => $mnotifyResult['error'] ?? null,
        ]);

        // Approve on our platform regardless of Mnotify result
        $senderId->approve($admin);

        // Add Mnotify registration info to success message
        if ($mnotifyResult['success']) {
            $message = "Sender ID approved successfully! Automatically registered with Mnotify (Status: {$mnotifyResult['status']}).";
        } else {
            $message = "Sender ID approved on platform. Note: Mnotify registration " .
                      ($mnotifyResult['error'] ?? 'may require manual registration. Contact Mnotify support.');
        }

        return back()->with('success', $message);
    }

    /**
     * Reject a sender ID request
     */
    public function rejectSenderId(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $admin = Auth::guard('admin')->user();
        $senderId = SmsSenderId::findOrFail($id);

        $senderId->reject($admin, $request->rejection_reason);

        return back()->with('success', 'Sender ID rejected successfully!');
    }

    /**
     * Show form to add manual credits
     */
    public function showAddCredits()
    {
        $companies = Company::where('is_suspended', false)
            ->with('smsCredit')
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(function ($company) {
                $company->sms_balance = $company->smsCredit ? $company->smsCredit->balance : 0;
                return $company;
            });

        // Get recent manual credit additions
        $recentCredits = \App\Models\SmsTransaction::where('type', 'manual_credit')
            ->with('owner')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.sms.add-credits', compact('companies', 'recentCredits'));
    }

    /**
     * Add manual credits to a company
     */
    public function addManualCredits(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'sms_credits' => 'required_without:credits|integer|min:1',
            'credits' => 'required_without:sms_credits|integer|min:1',
            'reason' => 'required_without:notes|string|max:500',
            'notes' => 'required_without:reason|string|max:500',
        ]);

        $admin = Auth::guard('admin')->user();
        $company = Company::findOrFail($request->company_id);
        $credits = (int) ($request->sms_credits ?? $request->credits);
        $notes = $request->reason ?? $request->notes;

        $result = $this->paystackService->addManualCredits(
            $company,
            $credits,
            $admin->id,
            $notes
        );

        if ($result['success']) {
            return back()->with('success', "{$credits} SMS credits added to {$company->name} successfully! New balance: {$result['new_balance']}");
        }

        return back()->with('error', $result['error'] ?? 'Failed to add credits.');
    }
}
