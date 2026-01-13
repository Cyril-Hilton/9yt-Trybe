@extends('layouts.app')

@section('title', 'Checkout - ' . $event->title)
@section('meta_robots', 'noindex, nofollow')

@section('styles')
<style>
    /* iOS 26 Liquid Glass Effect for Checkout */
    .glass-checkout-card {
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.12),
                    inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .dark .glass-checkout-card {
        background: rgba(30, 41, 59, 0.88);
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(148, 163, 184, 0.15);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }
    .gradient-text {
        background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 50%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .input-glass {
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(6, 182, 212, 0.3);
        backdrop-filter: blur(10px);
    }
    .dark .input-glass {
        background: rgba(30, 41, 59, 0.6);
        border: 1px solid rgba(6, 182, 212, 0.3);
    }
    .input-glass:focus {
        border-color: rgba(6, 182, 212, 0.8);
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen py-12 bg-gradient-to-br from-gray-50 via-cyan-50/30 to-blue-50/30 dark:from-gray-900 dark:via-slate-900 dark:to-gray-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with Glass Effect -->
            <div class="text-center mb-8 glass-checkout-card rounded-2xl p-6">
                <h1 class="text-3xl font-bold gradient-text">Complete Your Registration</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $event->title }}</p>
            </div>

            @if(session('error'))
            <div class="mb-6 glass-checkout-card border-l-4 border-red-500 text-red-800 dark:text-red-400 px-4 py-3 rounded-xl">
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('events.checkout.process', $event->slug) }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Contact Information -->
                    <div class="lg:col-span-2">
                        <div class="glass-checkout-card rounded-2xl p-6 mb-6 border-t-4 border-cyan-500">
                            <h2 class="text-xl font-bold gradient-text mb-2">Purchaser Contact Information</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">This information will be used for order confirmation and communication</p>

                            <div class="mb-4">
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="customer_name"
                                       id="customer_name"
                                       required
                                       value="{{ old('customer_name', $user->name ?? '') }}"
                                       class="w-full px-4 py-3 input-glass dark:text-white rounded-xl focus:outline-none transition-all">
                                @error('customer_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       name="customer_email"
                                       id="customer_email"
                                       required
                                       value="{{ old('customer_email', $user->email ?? '') }}"
                                       class="w-full px-4 py-3 input-glass dark:text-white rounded-xl focus:outline-none transition-all">
                                @error('customer_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input type="tel"
                                       name="customer_phone"
                                       id="customer_phone"
                                       required
                                       value="{{ old('customer_phone', $user->phone ?? '') }}"
                                       placeholder="+233 XX XXX XXXX"
                                       class="w-full px-4 py-3 input-glass dark:text-white rounded-xl focus:outline-none transition-all">
                                @error('customer_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Attendee Information -->
                        @php
                            $attendeeIndex = 0;
                        @endphp
                        @foreach($event->tickets as $ticket)
                            @if(isset(request('tickets')[$ticket->id]) && request('tickets')[$ticket->id] > 0)
                                @php
                                    $quantity = request('tickets')[$ticket->id];
                                @endphp

                                <div class="glass-checkout-card rounded-2xl p-6 mb-6 border-t-4 border-indigo-500">
                                    <h2 class="text-xl font-bold gradient-text mb-2">
                                        Attendee Information - {{ $ticket->name }}
                                    </h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        Please provide details for each attendee
                                    </p>

                                    @for($i = 0; $i < $quantity; $i++)
                                        <div class="mb-6 pb-6 @if($i < $quantity - 1) border-b border-cyan-200/50 dark:border-cyan-800/30 @endif">
                                            <h3 class="text-sm font-semibold text-cyan-700 dark:text-cyan-400 mb-4 flex items-center gap-2">
                                                <span class="w-6 h-6 rounded-full bg-cyan-500/20 flex items-center justify-center text-xs">{{ $i + 1 }}</span>
                                                Attendee {{ $i + 1 }} of {{ $quantity }}
                                            </h3>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label for="attendee_name_{{ $attendeeIndex }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                        Full Name <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text"
                                                           name="attendees[{{ $attendeeIndex }}][name]"
                                                           id="attendee_name_{{ $attendeeIndex }}"
                                                           required
                                                           value="{{ old('attendees.' . $attendeeIndex . '.name', $i === 0 ? ($user->name ?? '') : '') }}"
                                                           class="w-full px-4 py-3 input-glass dark:text-white rounded-xl focus:outline-none transition-all">
                                                    @error('attendees.' . $attendeeIndex . '.name')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label for="attendee_email_{{ $attendeeIndex }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                        Email Address <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="email"
                                                           name="attendees[{{ $attendeeIndex }}][email]"
                                                           id="attendee_email_{{ $attendeeIndex }}"
                                                           required
                                                           value="{{ old('attendees.' . $attendeeIndex . '.email', $i === 0 ? ($user->email ?? '') : '') }}"
                                                           class="w-full px-4 py-3 input-glass dark:text-white rounded-xl focus:outline-none transition-all">
                                                    @error('attendees.' . $attendeeIndex . '.email')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>

                                            <input type="hidden" name="attendees[{{ $attendeeIndex }}][ticket_id]" value="{{ $ticket->id }}">
                                        </div>

                                        @php $attendeeIndex++; @endphp
                                    @endfor
                                </div>

                                <!-- Hidden ticket inputs for backward compatibility -->
                                <input type="hidden" name="tickets[{{ $ticket->id }}]" value="{{ $quantity }}">
                            @endif
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="glass-checkout-card rounded-2xl p-6 sticky top-6 border-t-4 border-emerald-500">
                            <h2 class="text-xl font-bold gradient-text mb-4">Order Summary</h2>

                            <div class="mb-4 pb-4 border-b border-cyan-200/50 dark:border-cyan-800/30">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $event->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $event->formatted_date }}
                                </p>
                            </div>

                            <!-- Selected Tickets -->
                            @php
                                $subtotal = 0;
                                // Use new competitive model (4% commission)
                                $paystackPercentage = 1.95; // Paystack fee
                                $paystackFixed = 0.10; // Fixed per transaction
                                $vatRate = 12.5; // VAT on gateway fees
                            @endphp

                            <div class="space-y-3 mb-4 pb-4 border-b border-cyan-200/50 dark:border-cyan-800/30">
                                @foreach($event->tickets as $ticket)
                                    @if(isset(request('tickets')[$ticket->id]) && request('tickets')[$ticket->id] > 0)
                                        @php
                                            $quantity = request('tickets')[$ticket->id];
                                            $ticketTotal = $ticket->price * $quantity;
                                            $subtotal += $ticketTotal;
                                        @endphp
                                        <div class="flex justify-between text-sm items-start">
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $ticket->name }}</p>
                                                <p class="text-xs text-cyan-600 dark:text-cyan-400">{{ $ticket->formatted_price }} × {{ $quantity }}</p>
                                            </div>
                                            <p class="font-bold text-gray-900 dark:text-white">GH₵{{ number_format($ticketTotal, 2) }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Price Breakdown -->
                            <div class="space-y-2 text-sm mb-4">
                                <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                    <span>Subtotal</span>
                                    <span>GH₵{{ number_format($subtotal, 2) }}</span>
                                </div>

                                @if($event->fee_bearer === 'attendee' && $subtotal > 0)
                                    @php
                                        // Calculate gateway fees (Paystack)
                                        $gatewayFee = (($subtotal * $paystackPercentage) / 100) + $paystackFixed;
                                        $vat = ($gatewayFee * $vatRate) / 100;
                                        $buyerFees = $gatewayFee + $vat;
                                        $total = $subtotal + $buyerFees;
                                    @endphp
                                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                        <span>Payment Gateway Fee</span>
                                        <span>GH₵{{ number_format($gatewayFee, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                        <span>VAT on Gateway Fee ({{ $vatRate }}%)</span>
                                        <span>GH₵{{ number_format($vat, 2) }}</span>
                                    </div>
                                @else
                                    @php
                                        $total = $subtotal;
                                    @endphp
                                    <div class="flex items-center gap-1 text-green-600 dark:text-green-400 text-xs">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Fees covered by organizer</span>
                                    </div>
                                @endif

                                <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white pt-3 border-t border-cyan-200/50 dark:border-cyan-800/30">
                                    <span>Total</span>
                                    <span class="text-emerald-600 dark:text-emerald-400">GH₵{{ number_format($total, 2) }}</span>
                                </div>
                            </div>

                            <button type="submit"
                                    class="w-full py-4 bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 hover:from-cyan-700 hover:via-cyan-600 hover:to-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-1 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Continue to Payment
                            </button>

                            <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-4 flex items-center justify-center gap-1">
                                <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Secured by <strong class="text-cyan-600 dark:text-cyan-400">Paystack</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
