<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\EventOrder;
use App\Models\EventPayout;
use App\Models\OrganizationPaymentAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function payouts()
    {
        $company = Auth::guard('company')->user();

        $payouts = $company->payouts()
            ->with(['event', 'paymentAccount'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total_earned' => $company->events()->sum('total_revenue'),
            'pending_payout' => $payouts->where('status', 'pending')->sum('net_amount'),
            'completed_payout' => $payouts->where('status', 'completed')->sum('net_amount'),
        ];

        return view('company.finance.payouts', compact('payouts', 'stats'));
    }

    public function invoices()
    {
        $company = Auth::guard('company')->user();

        $orders = EventOrder::whereHas('event', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->where('payment_status', 'completed')
            ->with(['event', 'attendees'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total_orders' => $orders->total(),
            'total_revenue' => $company->events()->sum('total_revenue'),
            'this_month_revenue' => EventOrder::whereHas('event', function ($q) use ($company) {
                    $q->where('company_id', $company->id);
                })
                ->where('payment_status', 'completed')
                ->whereMonth('paid_at', now()->month)
                ->sum('subtotal'),
        ];

        return view('company.finance.invoices', compact('orders', 'stats'));
    }

    public function bankAccounts()
    {
        $company = Auth::guard('company')->user();

        $accounts = $company->paymentAccounts()->latest()->get();

        return view('company.finance.bank-accounts', compact('accounts'));
    }

    public function storeBankAccount(Request $request)
    {
        $company = Auth::guard('company')->user();

        $validated = $request->validate([
            'account_type' => 'required|in:bank,mobile_money',
            'bank_name' => 'required_if:account_type,bank|nullable|string',
            'account_name' => 'required_if:account_type,bank|nullable|string',
            'account_number' => 'required_if:account_type,bank|nullable|string',
            'branch' => 'nullable|string',
            'mobile_money_network' => 'required_if:account_type,mobile_money|nullable|in:MTN,Vodafone,AirtelTigo',
            'mobile_money_number' => 'required_if:account_type,mobile_money|nullable|string',
            'mobile_money_name' => 'required_if:account_type,mobile_money|nullable|string',
        ]);

        $account = $company->paymentAccounts()->create($validated);

        // If this is the first account, make it default
        if ($company->paymentAccounts()->count() === 1) {
            $account->update(['is_default' => true]);
        }

        return back()->with('success', 'Payment account added successfully!');
    }

    public function updateBankAccount(Request $request, OrganizationPaymentAccount $account)
    {
        $company = Auth::guard('company')->user();

        if ($account->company_id !== $company->id) {
            abort(403);
        }

        $validated = $request->validate([
            'account_type' => 'required|in:bank,mobile_money',
            'bank_name' => 'required_if:account_type,bank|nullable|string',
            'account_name' => 'required_if:account_type,bank|nullable|string',
            'account_number' => 'required_if:account_type,bank|nullable|string',
            'branch' => 'nullable|string',
            'mobile_money_network' => 'required_if:account_type,mobile_money|nullable|in:MTN,Vodafone,AirtelTigo',
            'mobile_money_number' => 'required_if:account_type,mobile_money|nullable|string',
            'mobile_money_name' => 'required_if:account_type,mobile_money|nullable|string',
        ]);

        $account->update($validated);

        return back()->with('success', 'Payment account updated successfully!');
    }

    public function deleteBankAccount(OrganizationPaymentAccount $account)
    {
        $company = Auth::guard('company')->user();

        if ($account->company_id !== $company->id) {
            abort(403);
        }

        // Cannot delete if there are pending payouts
        if ($account->payouts()->where('status', 'pending')->exists()) {
            return back()->with('error', 'Cannot delete account with pending payouts.');
        }

        $account->delete();

        return back()->with('success', 'Payment account deleted successfully!');
    }

    public function setDefaultAccount(OrganizationPaymentAccount $account)
    {
        $company = Auth::guard('company')->user();

        if ($account->company_id !== $company->id) {
            abort(403);
        }

        $account->update(['is_default' => true]);

        return back()->with('success', 'Default payment account updated!');
    }
}
