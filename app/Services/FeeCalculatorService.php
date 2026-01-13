<?php

namespace App\Services;

use App\Models\PlatformSetting;

class FeeCalculatorService
{
    /**
     * Calculate all fees for an order
     *
     * NEW COMPETITIVE MODEL:
     * - Organizer pays: 4% commission (lowest in Ghana market!)
     * - Ticket buyer pays: Paystack gateway fees (1.95% + GH₵0.10) + VAT on gateway fees
     * - Platform appears cheaper to organizers while covering all costs
     *
     * @param float $subtotal Ticket price before fees
     * @param int $ticketCount Number of tickets
     * @param string $feeBearer 'organizer' or 'attendee' (always 'attendee' for new model)
     * @return array
     */
    public function calculateFees(float $subtotal, int $ticketCount = 1, string $feeBearer = 'attendee'): array
    {
        // Platform commission rate (charged to organizer from ticket revenue)
        // Default 4% - lowest in Ghana market (competitors charge 5-7.5%)
        $platformCommissionRate = PlatformSetting::get('platform_commission_rate', 4.0);

        // Paystack payment gateway fees (passed to buyer)
        // Paystack charges: 1.95% + GH₵0.10 per transaction
        $paystackPercentage = PlatformSetting::get('paystack_fee_percentage', 1.95);
        $paystackFixed = PlatformSetting::get('paystack_fee_fixed', 0.10);

        // VAT on services (12.5% in Ghana) - applied to gateway fees only
        $vatRate = PlatformSetting::get('vat_rate', 12.5);

        // Legacy support: Check if old service fee model is still enabled
        $useLegacyModel = PlatformSetting::get('use_legacy_fee_model', false);

        if ($useLegacyModel) {
            return $this->calculateLegacyFees($subtotal, $ticketCount, $feeBearer);
        }

        // === NEW COMPETITIVE MODEL ===

        // 1. Platform commission (what organizer pays from revenue)
        $platformCommission = ($subtotal * $platformCommissionRate) / 100;

        // 2. Payment gateway fee (what buyer pays)
        // Paystack: 1.95% of subtotal + GH₵0.10 fixed fee
        $paymentGatewayFee = (($subtotal * $paystackPercentage) / 100) + $paystackFixed;

        // 3. VAT on gateway fee (12.5% of gateway fee)
        $vatOnGatewayFee = ($paymentGatewayFee * $vatRate) / 100;

        // 4. Total fees paid by buyer (gateway + VAT)
        $buyerFees = $paymentGatewayFee + $vatOnGatewayFee;

        // 5. Total amount buyer pays
        $totalBuyerPays = $subtotal + $buyerFees;

        // 6. Organizer receives the subtotal, then pays platform commission during payout
        $organizerReceives = $subtotal;
        $organizerPaysCommission = $platformCommission;
        $organizerNetPayout = $organizerReceives - $organizerPaysCommission;

        return [
            // Ticket pricing
            'subtotal' => round($subtotal, 2),
            'ticket_count' => $ticketCount,

            // Buyer fees (transparent pass-through)
            'payment_gateway_fee' => round($paymentGatewayFee, 2),
            'vat' => round($vatOnGatewayFee, 2),
            'buyer_fees_total' => round($buyerFees, 2),
            'total' => round($totalBuyerPays, 2),

            // Organizer commission (deducted during payout)
            'platform_commission' => round($platformCommission, 2),
            'platform_commission_rate' => $platformCommissionRate,

            // Payout calculation
            'organizer_gross_revenue' => round($organizerReceives, 2),
            'organizer_net_payout' => round($organizerNetPayout, 2),

            // Backward compatibility
            'attendee_pays' => round($totalBuyerPays, 2),
            'organizer_fees' => round($organizerPaysCommission, 2),
            'platform_fee' => round($platformCommission, 2),
            'service_fee' => 0, // Not used in new model
            'processing_fee' => round($paymentGatewayFee, 2),

            // Fee breakdown for display
            'fee_breakdown' => [
                'model' => 'competitive_4_percent',
                'platform_commission_rate' => $platformCommissionRate . '%',
                'paystack_percentage' => $paystackPercentage . '%',
                'paystack_fixed' => 'GH₵' . number_format($paystackFixed, 2),
                'vat_rate' => $vatRate . '%',
                'buyer_pays_gateway_fees' => true,
                'organizer_commission_rate' => $platformCommissionRate . '% (Lowest in Ghana!)',
            ],
        ];
    }

    /**
     * Legacy fee calculation (backward compatibility)
     * Used when use_legacy_fee_model is enabled
     */
    protected function calculateLegacyFees(float $subtotal, int $ticketCount, string $feeBearer): array
    {
        $platformFeePercentage = PlatformSetting::get('platform_fee_percentage', 2.8);
        $serviceFeePercentage = PlatformSetting::get('service_fee_percentage', 3.7);
        $serviceFeeFixed = PlatformSetting::get('service_fee_fixed', 7.16);
        $processingFeePercentage = PlatformSetting::get('payment_processing_fee', 2.9);
        $serviceFeeEnabled = PlatformSetting::get('service_fee_enabled', true);

        $platformFee = ($subtotal * $platformFeePercentage) / 100;

        $serviceFee = 0;
        if ($serviceFeeEnabled) {
            $serviceFee = (($subtotal * $serviceFeePercentage) / 100) + ($serviceFeeFixed * $ticketCount);
        }

        $totalBeforeProcessing = $subtotal + $platformFee + $serviceFee;
        $processingFee = ($totalBeforeProcessing * $processingFeePercentage) / 100;
        $total = $totalBeforeProcessing + $processingFee;

        if ($feeBearer === 'organizer') {
            $attendeeTotal = $subtotal;
            $organizerFees = $platformFee + $serviceFee + $processingFee;
        } else {
            $attendeeTotal = $total;
            $organizerFees = 0;
        }

        return [
            'subtotal' => round($subtotal, 2),
            'platform_fee' => round($platformFee, 2),
            'service_fee' => round($serviceFee, 2),
            'processing_fee' => round($processingFee, 2),
            'payment_gateway_fee' => round($processingFee, 2),
            'vat' => 0,
            'buyer_fees_total' => round($platformFee + $serviceFee + $processingFee, 2),
            'total' => round($total, 2),
            'attendee_pays' => round($attendeeTotal, 2),
            'organizer_fees' => round($organizerFees, 2),
            'fee_breakdown' => [
                'model' => 'legacy',
                'platform_fee_percentage' => $platformFeePercentage,
                'service_fee_percentage' => $serviceFeePercentage,
                'service_fee_fixed' => $serviceFeeFixed,
                'processing_fee_percentage' => $processingFeePercentage,
            ],
        ];
    }

    /**
     * Calculate organizer's net payout after fees
     *
     * NEW MODEL: Organizer pays 4% commission on ticket revenue
     * Buyer already paid gateway fees separately
     *
     * @param float $grossRevenue Total ticket revenue
     * @param string $feeBearer For backward compatibility
     * @return array
     */
    public function calculatePayout(float $grossRevenue, string $feeBearer = 'attendee'): array
    {
        $useLegacyModel = PlatformSetting::get('use_legacy_fee_model', false);

        if ($useLegacyModel) {
            $platformFeePercentage = PlatformSetting::get('platform_fee_percentage', 2.8);
            if ($feeBearer === 'attendee') {
                $platformFee = ($grossRevenue * $platformFeePercentage) / 100;
                $netPayout = $grossRevenue - $platformFee;
            } else {
                $platformFee = 0;
                $netPayout = $grossRevenue;
            }
        } else {
            // New competitive model: 4% commission
            $platformCommissionRate = PlatformSetting::get('platform_commission_rate', 4.0);
            $platformFee = ($grossRevenue * $platformCommissionRate) / 100;
            $netPayout = $grossRevenue - $platformFee;
        }

        return [
            'gross_revenue' => round($grossRevenue, 2),
            'platform_commission' => round($platformFee, 2),
            'platform_fee' => round($platformFee, 2), // Backward compatibility
            'net_payout' => round($netPayout, 2),
            'commission_rate' => $useLegacyModel
                ? PlatformSetting::get('platform_fee_percentage', 2.8) . '%'
                : PlatformSetting::get('platform_commission_rate', 4.0) . '%',
        ];
    }

    /**
     * Get fee summary for display
     *
     * @return array
     */
    public function getFeeSummary(): array
    {
        $useLegacyModel = PlatformSetting::get('use_legacy_fee_model', false);

        if ($useLegacyModel) {
            return [
                'model' => 'Legacy',
                'platform_fee' => PlatformSetting::get('platform_fee_percentage', 2.8) . '%',
                'service_fee' => PlatformSetting::get('service_fee_percentage', 3.7) . '% + GH₵' . PlatformSetting::get('service_fee_fixed', 7.16) . ' per ticket',
                'processing_fee' => PlatformSetting::get('payment_processing_fee', 2.9) . '% per order',
                'service_fee_enabled' => PlatformSetting::get('service_fee_enabled', true),
            ];
        }

        // New competitive model
        return [
            'model' => 'Competitive (Lowest in Ghana)',
            'organizer_commission' => PlatformSetting::get('platform_commission_rate', 4.0) . '% - Lowest in Ghana!',
            'buyer_gateway_fee' => PlatformSetting::get('paystack_fee_percentage', 1.95) . '% + GH₵' . PlatformSetting::get('paystack_fee_fixed', 0.10),
            'vat_on_gateway' => PlatformSetting::get('vat_rate', 12.5) . '% (on gateway fees only)',
            'transparency' => 'Buyers pay gateway fees directly - transparent pricing',
            'competitive_advantage' => 'Organizers pay only 4% vs 5-7.5% from competitors',
        ];
    }

    /**
     * Get detailed fee comparison for marketing/display
     *
     * @return array
     */
    public function getCompetitiveComparison(): array
    {
        $ourRate = PlatformSetting::get('platform_commission_rate', 4.0);

        return [
            'our_platform' => [
                'name' => '9yt !Trybe',
                'organizer_rate' => $ourRate . '%',
                'buyer_pays_gateway' => true,
                'total_organizer_cost' => $ourRate . '%',
                'highlight' => 'Lowest in Ghana',
            ],
            'competitors' => [
                [
                    'name' => 'Ayatickets',
                    'rate' => '5%',
                    'includes_gateway' => true,
                ],
                [
                    'name' => 'Egotickets',
                    'rate' => '5% - 7.5%',
                    'includes_gateway' => true,
                ],
                [
                    'name' => 'Tix Africa',
                    'rate' => '7.5% + GH₵5',
                    'includes_gateway' => true,
                ],
            ],
            'savings' => 'Organizers save ' . (5 - $ourRate) . '% compared to nearest competitor',
        ];
    }
}
