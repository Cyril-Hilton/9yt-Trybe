<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\EventPayout;
use App\Models\OrganizationPaymentAccount;
use App\Models\Admin;
use App\Mail\PayoutRequestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PayoutController extends Controller
{
    /**
     * Display a listing of payouts for the authenticated company
     */
    public function index()
    {
        $company = Auth::guard('company')->user();

        $payouts = EventPayout::where('company_id', $company->id)
                              ->with(['event', 'paymentAccount'])
                              ->orderBy('created_at', 'desc')
                              ->paginate(15);

        return view('company.payouts.index', compact('payouts'));
    }

    /**
     * Display the specified payout
     */
    public function show(EventPayout $payout)
    {
        $company = Auth::guard('company')->user();

        // Ensure payout belongs to authenticated company
        if ($payout->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this payout.');
        }

        $payout->load(['event', 'paymentAccount']);

        return view('company.payouts.show', compact('payout'));
    }

    /**
     * Show the payment account setup form
     */
    public function setup(EventPayout $payout)
    {
        $company = Auth::guard('company')->user();

        // Ensure payout belongs to authenticated company
        if ($payout->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this payout.');
        }

        // Check if payout already has payment account set up
        if ($payout->payment_account_id) {
            return redirect()->route('organization.payouts.show', $payout)
                           ->with('info', 'Payment account is already set up for this payout.');
        }

        // Get existing payment accounts for this company
        $existingAccounts = OrganizationPaymentAccount::where('company_id', $company->id)
                                                      ->orderBy('is_default', 'desc')
                                                      ->get();

        $payout->load('event');

        return view('company.payouts.setup', compact('payout', 'existingAccounts'));
    }

    /**
     * Store payment account for payout
     */
    public function storePaymentAccount(Request $request, EventPayout $payout)
    {
        $company = Auth::guard('company')->user();

        // Ensure payout belongs to authenticated company
        if ($payout->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this payout.');
        }

        // Check if payout already has payment account
        if ($payout->payment_account_id) {
            return redirect()->route('organization.payouts.show', $payout)
                           ->with('error', 'Payment account is already set up for this payout.');
        }

        try {
            DB::beginTransaction();

            // Determine if using existing account or creating new one
            if ($request->has('use_existing_account') && $request->existing_account_id) {
                // Use existing payment account
                $paymentAccount = OrganizationPaymentAccount::where('id', $request->existing_account_id)
                                                            ->where('company_id', $company->id)
                                                            ->firstOrFail();
            } else {
                // Validate based on account type
                $validated = $request->validate([
                    'account_type' => 'required|in:bank,mobile_money',

                    // Bank fields
                    'bank_name' => 'required_if:account_type,bank|nullable|string|max:255',
                    'account_name' => 'required_if:account_type,bank|nullable|string|max:255',
                    'account_number' => 'required_if:account_type,bank|nullable|string|max:255',
                    'branch' => 'nullable|string|max:255',

                    // Mobile Money fields
                    'mobile_money_network' => 'required_if:account_type,mobile_money|nullable|in:MTN,Airtel,Telecel',
                    'mobile_money_number' => 'required_if:account_type,mobile_money|nullable|string|max:20',
                    'mobile_money_name' => 'required_if:account_type,mobile_money|nullable|string|max:255',

                    'set_as_default' => 'nullable|boolean',
                ]);

                // Create new payment account
                $paymentAccount = OrganizationPaymentAccount::create([
                    'company_id' => $company->id,
                    'account_type' => $validated['account_type'],
                    'bank_name' => $validated['bank_name'] ?? null,
                    'account_name' => $validated['account_name'] ?? null,
                    'account_number' => $validated['account_number'] ?? null,
                    'branch' => $validated['branch'] ?? null,
                    'mobile_money_network' => $validated['mobile_money_network'] ?? null,
                    'mobile_money_number' => $validated['mobile_money_number'] ?? null,
                    'mobile_money_name' => $validated['mobile_money_name'] ?? null,
                    'is_verified' => false,
                    'is_default' => $request->has('set_as_default'),
                ]);
            }

            // Link payment account to payout
            $payout->update([
                'payment_account_id' => $paymentAccount->id,
                'payout_method' => $paymentAccount->account_type,
            ]);

            DB::commit();

            return redirect()->route('organization.payouts.show', $payout)
                           ->with('success', 'Payment account set up successfully! Our team will process your payout within 2-3 business days.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Failed to set up payment account: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Request payout - sends email to admin with payment details
     */
    public function requestPayout(EventPayout $payout)
    {
        $company = Auth::guard('company')->user();

        // Ensure payout belongs to authenticated company
        if ($payout->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this payout.');
        }

        // Check if payout is in pending status
        if (!$payout->isPending()) {
            return back()->with('error', 'This payout has already been requested or processed.');
        }

        // Check if payment account is set up
        if (!$payout->payment_account_id) {
            return redirect()->route('organization.payouts.setup', $payout)
                           ->with('error', 'Please set up your payment account first before requesting payout.');
        }

        try {
            // Update payout status to 'requested'
            $payout->update([
                'status' => 'requested',
                'processed_at' => now(),
            ]);

            // Get admin email (first super admin or fallback to env)
            $adminEmail = Admin::where('role', 'super_admin')
                              ->where('is_active', true)
                              ->first()
                              ?->email ?? config('mail.from.address');

            // Send email to admin
            Mail::to($adminEmail)->send(new PayoutRequestEmail($payout));

            return redirect()->route('organization.payouts.show', $payout)
                           ->with('success', 'Payout request submitted successfully! Admin will process your payment within 2-3 business days.');

        } catch (\Exception $e) {
            // Rollback status if email fails
            $payout->update(['status' => 'pending']);

            return back()->with('error', 'Failed to submit payout request: ' . $e->getMessage());
        }
    }
}
