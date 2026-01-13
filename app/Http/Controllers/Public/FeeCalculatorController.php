<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\FeeCalculatorService;
use Illuminate\Http\Request;

/**
 * Public Fee Calculator
 *
 * COMPETITIVE ADVANTAGE: Eventbrite hides fees until checkout!
 * We show fees UPFRONT = builds trust = higher conversions
 *
 * Transparency > Hidden costs
 */
class FeeCalculatorController extends Controller
{
    protected FeeCalculatorService $feeCalculator;

    public function __construct(FeeCalculatorService $feeCalculator)
    {
        $this->feeCalculator = $feeCalculator;
    }

    /**
     * Show fee calculator tool
     */
    public function index()
    {
        // Get current platform fees
        $feeSummary = $this->feeCalculator->getFeeSummary();

        return view('public.fee-calculator', compact('feeSummary'));
    }

    /**
     * Calculate fees via AJAX
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'subtotal' => 'required|numeric|min:0',
            'ticket_count' => 'required|integer|min:1',
            'fee_bearer' => 'required|in:organizer,attendee,split',
        ]);

        $calculation = $this->feeCalculator->calculateFees(
            $validated['subtotal'],
            $validated['ticket_count'],
            $validated['fee_bearer']
        );

        // Add comparison with competitors
        $calculation['eventbrite_fees'] = $this->calculateEventbriteFees($validated['subtotal']);
        $calculation['ticketmaster_fees'] = $this->calculateTicketmasterFees($validated['subtotal']);
        $calculation['our_savings'] = max(0, $calculation['eventbrite_fees']['total_fees'] - $calculation['total_fees']);

        return response()->json($calculation);
    }

    /**
     * Calculate Eventbrite's fees for comparison
     * Eventbrite: 3.5% + 1.59% payment processing + GH₵0.99 per ticket
     */
    protected function calculateEventbriteFees(float $subtotal): array
    {
        $serviceFee = $subtotal * 0.035; // 3.5% service fee
        $processingFee = $subtotal * 0.0159; // 1.59% payment processing
        $perTicketFee = 0.99; // GH₵0.99 per ticket (assuming 1 ticket for simplicity)

        $totalFees = $serviceFee + $processingFee + $perTicketFee;

        return [
            'service_fee' => round($serviceFee, 2),
            'processing_fee' => round($processingFee, 2),
            'per_ticket_fee' => $perTicketFee,
            'total_fees' => round($totalFees, 2),
            'customer_pays' => round($subtotal + $totalFees, 2),
        ];
    }

    /**
     * Calculate Ticketmaster's fees for comparison
     * Ticketmaster: ~20-25% in fees (notoriously high!)
     */
    protected function calculateTicketmasterFees(float $subtotal): array
    {
        $totalFees = $subtotal * 0.225; // Average 22.5% in fees

        return [
            'total_fees' => round($totalFees, 2),
            'customer_pays' => round($subtotal + $totalFees, 2),
        ];
    }

    /**
     * Get fee comparison data for organizers
     */
    public function comparison()
    {
        $testAmounts = [10, 50, 100, 500, 1000];
        $comparisons = [];

        foreach ($testAmounts as $amount) {
            $ourFees = $this->feeCalculator->calculateFees($amount, 1, 'attendee');
            $eventbriteFees = $this->calculateEventbriteFees($amount);
            $ticketmasterFees = $this->calculateTicketmasterFees($amount);

            $comparisons[] = [
                'ticket_price' => $amount,
                '9yt_trybe' => [
                    'fees' => $ourFees['total_fees'],
                    'customer_pays' => $ourFees['attendee_pays'],
                ],
                'eventbrite' => [
                    'fees' => $eventbriteFees['total_fees'],
                    'customer_pays' => $eventbriteFees['customer_pays'],
                ],
                'ticketmaster' => [
                    'fees' => $ticketmasterFees['total_fees'],
                    'customer_pays' => $ticketmasterFees['customer_pays'],
                ],
                'savings_vs_eventbrite' => round($eventbriteFees['total_fees'] - $ourFees['total_fees'], 2),
                'savings_vs_ticketmaster' => round($ticketmasterFees['total_fees'] - $ourFees['total_fees'], 2),
            ];
        }

        return view('public.fee-comparison', compact('comparisons'));
    }
}
