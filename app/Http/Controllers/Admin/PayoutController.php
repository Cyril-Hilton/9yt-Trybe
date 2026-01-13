<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventPayout;
use App\Mail\PaymentConfirmationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    /**
     * Display a listing of all payouts
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = EventPayout::with(['company', 'event', 'paymentAccount'])
                           ->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $payouts = $query->paginate(20);

        // Get counts for each status
        $statusCounts = [
            'all' => EventPayout::count(),
            'pending' => EventPayout::where('status', 'pending')->count(),
            'processing' => EventPayout::where('status', 'processing')->count(),
            'completed' => EventPayout::where('status', 'completed')->count(),
            'failed' => EventPayout::where('status', 'failed')->count(),
        ];

        return view('admin.payouts.index', compact('payouts', 'status', 'statusCounts'));
    }

    /**
     * Display the specified payout
     */
    public function show(EventPayout $payout)
    {
        $payout->load(['company', 'event', 'paymentAccount']);

        return view('admin.payouts.show', compact('payout'));
    }

    /**
     * Mark payout as processing
     */
    public function process(EventPayout $payout)
    {
        if (!$payout->canBeProcessed()) {
            return back()->with('error', 'This payout cannot be processed. Payment account may not be set up.');
        }

        $payout->update([
            'status' => 'processing',
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Payout marked as processing.');
    }

    /**
     * Mark payout as completed and send confirmation email
     */
    public function complete(Request $request, EventPayout $payout)
    {
        $validated = $request->validate([
            'payout_reference' => 'required|string|max:255',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Update payout status
            $payout->update([
                'status' => 'completed',
                'payout_reference' => $validated['payout_reference'],
                'admin_notes' => $validated['admin_notes'] ?? null,
                'completed_at' => now(),
            ]);

            // Send payment confirmation email
            if (!$payout->paymentConfirmationEmailSent()) {
                Mail::to($payout->company->email)
                    ->send(new PaymentConfirmationEmail($payout));

                $payout->markPaymentConfirmationEmailSent();
            }

            DB::commit();

            return redirect()->route('admin.payouts.index')
                           ->with('success', "Payout completed successfully! Confirmation email sent to {$payout->company->name}.");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Failed to complete payout: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Mark payout as failed
     */
    public function fail(Request $request, EventPayout $payout)
    {
        $validated = $request->validate([
            'failure_reason' => 'required|string|max:1000',
        ]);

        $payout->update([
            'status' => 'failed',
            'failure_reason' => $validated['failure_reason'],
        ]);

        return back()->with('success', 'Payout marked as failed.');
    }
}
