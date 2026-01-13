<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\SmsPlan;
use App\Models\SmsTransaction;
use App\Services\PaystackService;
use App\Services\Sms\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsWalletController extends Controller
{
    protected SmsService $smsService;
    protected PaystackService $paystackService;

    public function __construct(SmsService $smsService, PaystackService $paystackService)
    {
        $this->smsService = $smsService;
        $this->paystackService = $paystackService;
    }

    /**
     * Display wallet and SMS plans
     */
    public function index()
    {
        $company = Auth::guard('company')->user();

        // Get SMS credit balance
        $creditBalance = $this->smsService->getCreditBalance($company);

        // Get active SMS plans
        $plans = SmsPlan::active()->get();

        // Get recent transactions
        $transactions = SmsTransaction::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->with('plan', 'creditedByAdmin')
            ->latest()
            ->paginate(10);

        // Get Paystack public key for frontend
        $paystackPublicKey = $this->paystackService->getPublicKey();

        return view('company.sms.wallet', compact(
            'creditBalance',
            'plans',
            'transactions',
            'paystackPublicKey'
        ));
    }

    /**
     * Initialize payment for SMS credit purchase
     */
    public function initializePayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:sms_plans,id',
        ]);

        $company = Auth::guard('company')->user();
        $plan = SmsPlan::findOrFail($request->plan_id);

        if (!$plan->is_active) {
            return back()->with('error', 'This plan is no longer available.');
        }

        // Initialize payment
        $result = $this->paystackService->initializePayment(
            $company,
            $plan,
            $company->email,
            route('organization.sms.payment.callback')
        );

        if ($result['success']) {
            // Redirect to Paystack payment page
            return redirect($result['authorization_url']);
        }

        return back()->with('error', $result['error'] ?? 'Failed to initialize payment. Please try again.');
    }

    /**
     * Handle Paystack payment callback
     */
    public function handlePaymentCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('organization.sms.wallet.index')
                ->with('error', 'Invalid payment reference.');
        }

        // Process the payment
        $result = $this->paystackService->processSuccessfulPayment($reference);

        if ($result['success']) {
            return redirect()->route('organization.sms.wallet.index')
                ->with('success', "Payment successful! {$result['credits_added']} SMS credits have been added to your account.");
        }

        return redirect()->route('organization.sms.wallet.index')
            ->with('error', $result['error'] ?? 'Payment verification failed. Please contact support if you were charged.');
    }

    /**
     * View transaction details
     */
    public function showTransaction($id)
    {
        $company = Auth::guard('company')->user();

        $transaction = SmsTransaction::where('owner_id', $company->id)->where('owner_type', get_class($company))
            ->with('plan', 'creditedByAdmin')
            ->findOrFail($id);

        // Get Paystack public key for retry payment button
        $paystackPublicKey = $this->paystackService->getPublicKey();

        return view('company.sms.transaction', compact('transaction', 'paystackPublicKey'));
    }
}
