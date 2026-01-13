<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventOrder;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        // Check which pricing model is in use
        $useLegacyModel = PlatformSetting::get('use_legacy_fee_model', false);

        $settings = [
            // Pricing model toggle
            'use_legacy_fee_model' => $useLegacyModel,

            // New competitive model settings (4% commission)
            'platform_commission_rate' => PlatformSetting::get('platform_commission_rate', 4.0),
            'paystack_fee_percentage' => PlatformSetting::get('paystack_fee_percentage', 1.95),
            'paystack_fee_fixed' => PlatformSetting::get('paystack_fee_fixed', 0.10),
            'vat_rate' => PlatformSetting::get('vat_rate', 12.5),

            // Legacy model settings (for backward compatibility)
            'platform_fee_percentage' => PlatformSetting::get('platform_fee_percentage', 2.8),
            'service_fee_percentage' => PlatformSetting::get('service_fee_percentage', 3.7),
            'service_fee_fixed' => PlatformSetting::get('service_fee_fixed', 7.16),
            'payment_processing_fee' => PlatformSetting::get('payment_processing_fee', 2.9),
            'service_fee_enabled' => PlatformSetting::get('service_fee_enabled', true),
        ];

        // Calculate platform revenue based on active model
        $totalRevenue = Event::approved()->sum('total_revenue');
        if ($useLegacyModel) {
            $platformRevenue = ($totalRevenue * $settings['platform_fee_percentage']) / 100;
        } else {
            $platformRevenue = ($totalRevenue * $settings['platform_commission_rate']) / 100;
        }

        $platformStats = [
            'total_events' => Event::count(),
            'active_events' => Event::approved()->where('end_date', '>=', now())->count(),
            'total_companies' => Company::count(),
            'platform_revenue' => $platformRevenue,
            'pricing_model' => $useLegacyModel ? 'Legacy' : 'Competitive (4%)',
        ];

        return view('admin.settings.index', compact('settings', 'platformStats'));
    }

    public function update(Request $request)
    {
        // Determine which model to validate based on toggle
        $useLegacyModel = $request->has('use_legacy_fee_model');

        if ($useLegacyModel) {
            // Legacy model validation
            $validated = $request->validate([
                'use_legacy_fee_model' => 'nullable|boolean',
                'platform_fee_percentage' => 'required|numeric|min:0|max:100',
                'service_fee_percentage' => 'required|numeric|min:0|max:100',
                'service_fee_fixed' => 'required|numeric|min:0',
                'payment_processing_fee' => 'required|numeric|min:0|max:100',
                'service_fee_enabled' => 'nullable|boolean',
            ]);
            $validated['service_fee_enabled'] = $request->has('service_fee_enabled');
        } else {
            // New competitive model validation
            $validated = $request->validate([
                'use_legacy_fee_model' => 'nullable|boolean',
                'platform_commission_rate' => 'required|numeric|min:0|max:100',
                'paystack_fee_percentage' => 'required|numeric|min:0|max:100',
                'paystack_fee_fixed' => 'required|numeric|min:0',
                'vat_rate' => 'required|numeric|min:0|max:100',
            ]);
        }

        // Handle model toggle
        $validated['use_legacy_fee_model'] = $useLegacyModel;

        // Save all settings
        foreach ($validated as $key => $value) {
            $type = in_array($key, ['service_fee_enabled', 'use_legacy_fee_model']) ? 'boolean' : 'number';

            PlatformSetting::set(
                $key,
                $value,
                $type,
                'fees',
                $this->getSettingDescription($key)
            );
        }

        $message = $useLegacyModel
            ? 'Platform settings updated! Using legacy fee model.'
            : 'Platform settings updated! Using competitive 4% commission model - Lowest in Ghana!';

        return back()->with('success', $message);
    }

    protected function getSettingDescription(string $key): string
    {
        return match ($key) {
            // New competitive model
            'use_legacy_fee_model' => 'Toggle between legacy fee model and new competitive 4% model',
            'platform_commission_rate' => 'Platform commission rate charged to organizers (4% - Lowest in Ghana!)',
            'paystack_fee_percentage' => 'Paystack payment gateway fee percentage (passed to buyer)',
            'paystack_fee_fixed' => 'Paystack fixed fee per transaction in GHS (passed to buyer)',
            'vat_rate' => 'VAT rate applied to gateway fees only (Ghana standard: 12.5%)',

            // Legacy model
            'platform_fee_percentage' => 'Platform fee percentage charged on ticket sales',
            'service_fee_percentage' => 'Service fee percentage to keep the platform running',
            'service_fee_fixed' => 'Fixed service fee per ticket in GHS',
            'payment_processing_fee' => 'Payment processing fee percentage per order',
            'service_fee_enabled' => 'Enable or disable service fee charges',

            default => '',
        };
    }
}
