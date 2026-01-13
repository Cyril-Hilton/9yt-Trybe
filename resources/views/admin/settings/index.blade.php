@extends('layouts.admin')

@section('title', 'Platform Settings')

@section('content')
<div class="p-8" x-data="{
    useLegacy: {{ $settings['use_legacy_fee_model'] ? 'true' : 'false' }},
    platformCommission: {{ $settings['platform_commission_rate'] ?? 4.0 }},
    paystackPercent: {{ $settings['paystack_fee_percentage'] ?? 1.95 }},
    paystackFixed: {{ $settings['paystack_fee_fixed'] ?? 0.10 }},
    vatRate: {{ $settings['vat_rate'] ?? 12.5 }},
    platformFee: {{ $settings['platform_fee_percentage'] ?? 2.8 }},
    serviceFeePercent: {{ $settings['service_fee_percentage'] ?? 3.7 }},
    serviceFeeFixed: {{ $settings['service_fee_fixed'] ?? 7.16 }},
    processingFee: {{ $settings['payment_processing_fee'] ?? 2.9 }},
    serviceFeeEnabled: {{ $settings['service_fee_enabled'] ? 'true' : 'false' }},

    get competitiveBreakdown() {
        const ticket = 100;
        const commission = (ticket * this.platformCommission) / 100;
        const gateway = (ticket * this.paystackPercent) / 100 + this.paystackFixed;
        const vat = (gateway * this.vatRate) / 100;
        const buyerTotal = ticket + gateway + vat;
        const organizerNet = ticket - commission;

        return {
            ticket: ticket.toFixed(2),
            commission: commission.toFixed(2),
            gateway: gateway.toFixed(2),
            vat: vat.toFixed(2),
            buyerTotal: buyerTotal.toFixed(2),
            organizerNet: organizerNet.toFixed(2)
        };
    },

    get legacyBreakdown() {
        const ticket = 100;
        const platform = (ticket * this.platformFee) / 100;
        const service = this.serviceFeeEnabled ? ((ticket * this.serviceFeePercent) / 100 + this.serviceFeeFixed) : 0;
        const subtotal = ticket + platform + service;
        const processing = (subtotal * this.processingFee) / 100;
        const total = subtotal + processing;

        return {
            ticket: ticket.toFixed(2),
            platform: platform.toFixed(2),
            service: service.toFixed(2),
            processing: processing.toFixed(2),
            total: total.toFixed(2)
        };
    }
}">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white mb-2">Platform Settings</h1>
        <p class="text-gray-400">Configure platform-wide fee structures and pricing model</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-900 border border-green-700 text-green-200 px-6 py-4 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-900 border border-red-700 text-red-200 px-6 py-4 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Pricing Model Badge -->
    <div class="mb-6">
        <div class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-900 to-purple-900 border border-indigo-700 px-4 py-2 rounded-lg">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-white font-semibold">Active Model:</span>
            <span class="text-indigo-300" x-text="useLegacy ? 'Legacy Fee Model' : 'Competitive 4% Model (Lowest in Ghana!)'"></span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Fee Configuration Form -->
        <div class="lg:col-span-2">
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white">Fee Configuration</h2>

                    <!-- Model Toggle -->
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-400">Pricing Model:</span>
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox"
                                       x-model="useLegacy"
                                       class="sr-only peer">
                                <div class="w-14 h-7 bg-indigo-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gray-600"></div>
                            </div>
                            <span class="ml-3 text-sm font-medium" :class="useLegacy ? 'text-gray-400' : 'text-indigo-400'" x-text="useLegacy ? 'Legacy' : 'Competitive'"></span>
                        </label>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Hidden field for model toggle -->
                    <input type="hidden" name="use_legacy_fee_model" :value="useLegacy ? '1' : '0'" x-model="useLegacy">

                    <!-- NEW COMPETITIVE MODEL (4% Commission) -->
                    <div x-show="!useLegacy" x-transition>
                        <div class="mb-6 bg-gradient-to-r from-indigo-900/30 to-purple-900/30 border border-indigo-700/50 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-indigo-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-bold text-white mb-1">Competitive 4% Model</h3>
                                    <p class="text-sm text-indigo-200">
                                        <span class="font-semibold">Lowest commission rate in Ghana!</span> Organizers pay only 4% commission. Ticket buyers pay transparent gateway fees directly. No hidden costs.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Platform Commission Rate -->
                        <div class="mb-6 pb-6 border-b border-gray-700">
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                Platform Commission Rate
                                <span class="ml-2 text-xs bg-green-600 text-white px-2 py-1 rounded">Lowest in Ghana</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="number"
                                       name="platform_commission_rate"
                                       x-model="platformCommission"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       value="{{ old('platform_commission_rate', $settings['platform_commission_rate'] ?? 4.0) }}"
                                       class="flex-1 px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                       :required="!useLegacy">
                                <span class="text-white font-semibold">%</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-400">
                                Commission charged to organizers on ticket revenue. Competitors charge 5-7.5%.
                            </p>
                        </div>

                        <!-- Paystack Gateway Fees -->
                        <div class="mb-6 pb-6 border-b border-gray-700">
                            <label class="block text-sm font-semibold text-gray-300 mb-4">
                                Paystack Payment Gateway Fees
                                <span class="ml-2 text-xs bg-blue-600 text-white px-2 py-1 rounded">Paid by Buyer</span>
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-2">
                                        Percentage Fee
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input type="number"
                                               name="paystack_fee_percentage"
                                               x-model="paystackPercent"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               value="{{ old('paystack_fee_percentage', $settings['paystack_fee_percentage'] ?? 1.95) }}"
                                               class="flex-1 px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                               :required="!useLegacy">
                                        <span class="text-white">%</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-2">
                                        Fixed Fee (GH₵)
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <span class="text-white">GH₵</span>
                                        <input type="number"
                                               name="paystack_fee_fixed"
                                               x-model="paystackFixed"
                                               step="0.01"
                                               min="0"
                                               value="{{ old('paystack_fee_fixed', $settings['paystack_fee_fixed'] ?? 0.10) }}"
                                               class="flex-1 px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                               :required="!useLegacy">
                                    </div>
                                </div>
                            </div>

                            <p class="mt-3 text-sm text-gray-400">
                                Paystack's actual gateway charges (1.95% + GH₵0.10), passed transparently to buyers.
                            </p>
                        </div>

                        <!-- VAT Rate -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                VAT Rate
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="number"
                                       name="vat_rate"
                                       x-model="vatRate"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       value="{{ old('vat_rate', $settings['vat_rate'] ?? 12.5) }}"
                                       class="flex-1 px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                       :required="!useLegacy">
                                <span class="text-white font-semibold">%</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-400">
                                VAT applied only to gateway fees (Ghana standard: 12.5%). Paid by buyer.
                            </p>
                        </div>
                    </div>

                    <!-- LEGACY MODEL -->
                    <div x-show="useLegacy" x-transition>
                        <div class="mb-6 bg-yellow-900/30 border border-yellow-700/50 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-bold text-white mb-1">Legacy Fee Model</h3>
                                    <p class="text-sm text-yellow-200">
                                        This is the old pricing structure. Consider switching to the competitive model for better rates.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Platform Fee -->
                        <div class="mb-6 pb-6 border-b border-gray-700">
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                Platform Fee Percentage
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="number"
                                       name="platform_fee_percentage"
                                       x-model="platformFee"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       value="{{ old('platform_fee_percentage', $settings['platform_fee_percentage'] ?? 2.8) }}"
                                       class="flex-1 px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                       :required="useLegacy">
                                <span class="text-white font-semibold">%</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-400">
                                The percentage fee charged by the platform on each ticket sale.
                            </p>
                        </div>

                        <!-- Service Fee -->
                        <div class="mb-6 pb-6 border-b border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-semibold text-gray-300">
                                    Service Fee
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           name="service_fee_enabled"
                                           value="1"
                                           x-model="serviceFeeEnabled"
                                           {{ old('service_fee_enabled', $settings['service_fee_enabled'] ?? true) ? 'checked' : '' }}
                                           class="w-5 h-5 text-indigo-600 bg-gray-700 border-gray-600 rounded focus:ring-2 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-300">Enabled</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-2">
                                        Percentage
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input type="number"
                                               name="service_fee_percentage"
                                               x-model="serviceFeePercent"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               value="{{ old('service_fee_percentage', $settings['service_fee_percentage'] ?? 3.7) }}"
                                               class="flex-1 px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                               :required="useLegacy">
                                        <span class="text-white">%</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-2">
                                        Fixed Amount (GH₵)
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <span class="text-white">GH₵</span>
                                        <input type="number"
                                               name="service_fee_fixed"
                                               x-model="serviceFeeFixed"
                                               step="0.01"
                                               min="0"
                                               value="{{ old('service_fee_fixed', $settings['service_fee_fixed'] ?? 7.16) }}"
                                               class="flex-1 px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                               :required="useLegacy">
                                    </div>
                                </div>
                            </div>

                            <p class="mt-3 text-sm text-gray-400">
                                Service fee: (Percentage × Subtotal) + (Fixed Amount × Number of Tickets)
                            </p>
                        </div>

                        <!-- Payment Processing Fee -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                Payment Processing Fee
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="number"
                                       name="payment_processing_fee"
                                       x-model="processingFee"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       value="{{ old('payment_processing_fee', $settings['payment_processing_fee'] ?? 2.9) }}"
                                       class="flex-1 px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                       :required="useLegacy">
                                <span class="text-white font-semibold">%</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-400">
                                The payment gateway processing fee (applied to total amount including fees).
                            </p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-700">
                        <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Sidebar -->
        <div class="lg:col-span-1">
            <!-- Fee Preview -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 sticky top-6">
                <h3 class="text-lg font-bold text-white mb-4">Fee Structure Preview</h3>

                <!-- Competitive Model Preview -->
                <div x-show="!useLegacy" x-transition>
                    <div class="bg-gray-900 rounded-lg p-4 mb-4">
                        <p class="text-xs text-gray-400 mb-3">Example: GH₵100 Ticket</p>

                        <div class="space-y-2 text-sm mb-4">
                            <div class="font-semibold text-indigo-400 mb-2">What Buyer Pays:</div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Ticket Price</span>
                                <span class="text-white font-semibold">GH₵<span x-text="competitiveBreakdown.ticket"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400 text-xs">+ Gateway Fee</span>
                                <span class="text-white text-xs">GH₵<span x-text="competitiveBreakdown.gateway"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400 text-xs">+ VAT</span>
                                <span class="text-white text-xs">GH₵<span x-text="competitiveBreakdown.vat"></span></span>
                            </div>
                            <div class="pt-2 border-t border-gray-700">
                                <div class="flex justify-between">
                                    <span class="text-indigo-300 font-semibold">Buyer Total</span>
                                    <span class="text-indigo-400 font-bold text-lg">GH₵<span x-text="competitiveBreakdown.buyerTotal"></span></span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 text-sm pt-4 border-t border-gray-700">
                            <div class="font-semibold text-green-400 mb-2">What Organizer Gets:</div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Ticket Revenue</span>
                                <span class="text-white">GH₵<span x-text="competitiveBreakdown.ticket"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400 text-xs">- Platform Commission (<span x-text="platformCommission"></span>%)</span>
                                <span class="text-red-400 text-xs">-GH₵<span x-text="competitiveBreakdown.commission"></span></span>
                            </div>
                            <div class="pt-2 border-t border-gray-700">
                                <div class="flex justify-between">
                                    <span class="text-green-300 font-semibold">Net Payout</span>
                                    <span class="text-green-400 font-bold text-lg">GH₵<span x-text="competitiveBreakdown.organizerNet"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-900 bg-opacity-30 border border-green-700 rounded-lg p-4">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            <div class="text-xs text-green-200">
                                <p class="font-semibold mb-1">Competitive Advantage</p>
                                <p>Only <span x-text="platformCommission"></span>% commission vs 5-7.5% from competitors. Transparent gateway fees paid by buyers.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Legacy Model Preview -->
                <div x-show="useLegacy" x-transition>
                    <div class="bg-gray-900 rounded-lg p-4 mb-4">
                        <p class="text-xs text-gray-400 mb-2">Example: GH₵100 Ticket</p>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Ticket Price</span>
                                <span class="text-white font-semibold">GH₵<span x-text="legacyBreakdown.ticket"></span></span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-400">Platform Fee (<span x-text="platformFee"></span>%)</span>
                                <span class="text-white">GH₵<span x-text="legacyBreakdown.platform"></span></span>
                            </div>

                            <div x-show="serviceFeeEnabled" class="flex justify-between">
                                <span class="text-gray-400">Service Fee</span>
                                <span class="text-white">GH₵<span x-text="legacyBreakdown.service"></span></span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-400">Processing Fee (<span x-text="processingFee"></span>%)</span>
                                <span class="text-white">GH₵<span x-text="legacyBreakdown.processing"></span></span>
                            </div>

                            <div class="pt-2 border-t border-gray-700">
                                <div class="flex justify-between">
                                    <span class="text-gray-300 font-semibold">Total</span>
                                    <span class="text-indigo-400 font-bold text-lg">GH₵<span x-text="legacyBreakdown.total"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-900 bg-opacity-30 border border-yellow-700 rounded-lg p-4">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-yellow-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-xs text-yellow-200">
                                <p class="font-semibold mb-1">Legacy Model</p>
                                <p>Event organizers can choose who pays the fees. Consider switching to competitive model for better rates.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Platform Stats -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 mt-6">
                <h3 class="text-lg font-bold text-white mb-4">Platform Overview</h3>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Pricing Model</span>
                        <span class="text-white font-semibold text-xs">{{ $platformStats['pricing_model'] ?? 'Competitive (4%)' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Total Events</span>
                        <span class="text-white font-semibold">{{ $platformStats['total_events'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Active Events</span>
                        <span class="text-white font-semibold">{{ $platformStats['active_events'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm">Total Organizations</span>
                        <span class="text-white font-semibold">{{ $platformStats['total_companies'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-700">
                        <span class="text-gray-400 text-sm">Platform Revenue</span>
                        <span class="text-indigo-400 font-bold">GH₵{{ number_format($platformStats['platform_revenue'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
