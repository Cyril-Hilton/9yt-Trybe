@extends('layouts.app')

@section('title', 'Fee Calculator - See Our Honest Pricing')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                💰 Transparent Fee Calculator
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-2">
                Unlike Eventbrite, we show you EXACTLY what you'll pay. No hidden fees. No surprises.
            </p>
            <p class="text-sm text-green-600 dark:text-green-400 font-semibold">
                ✅ We're 40-50% cheaper than Eventbrite | 85% cheaper than Ticketmaster
            </p>
        </div>

        <!-- Calculator Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Calculate Your Fees</h2>

            <form id="feeCalculatorForm" class="space-y-6">
                <!-- Ticket Price -->
                <div>
                    <label for="subtotal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Ticket Price (GH₵)
                    </label>
                    <input
                        type="number"
                        id="subtotal"
                        name="subtotal"
                        step="0.01"
                        min="0"
                        value="50"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        required
                    >
                </div>

                <!-- Ticket Count -->
                <div>
                    <label for="ticket_count" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Number of Tickets
                    </label>
                    <input
                        type="number"
                        id="ticket_count"
                        name="ticket_count"
                        min="1"
                        value="1"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        required
                    >
                </div>

                <!-- Fee Bearer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Who Pays the Service Fee?
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                            <input type="radio" name="fee_bearer" value="attendee" checked class="mr-3">
                            <span class="text-gray-900 dark:text-white">Attendee (Most Common)</span>
                        </label>
                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                            <input type="radio" name="fee_bearer" value="organizer" class="mr-3">
                            <span class="text-gray-900 dark:text-white">Organizer (You)</span>
                        </label>
                        <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                            <input type="radio" name="fee_bearer" value="split" class="mr-3">
                            <span class="text-gray-900 dark:text-white">Split 50/50</span>
                        </label>
                    </div>
                </div>

                <!-- Calculate Button -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-4 px-6 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg"
                >
                    Calculate Fees
                </button>
            </form>
        </div>

        <!-- Results Card (Hidden initially) -->
        <div id="resultsCard" class="hidden bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Fee Breakdown</h2>

            <!-- Our Platform -->
            <div class="mb-8 p-6 bg-green-50 dark:bg-green-900/20 rounded-xl border-2 border-green-500">
                <h3 class="text-lg font-bold text-green-700 dark:text-green-400 mb-4">9yt !Trybe (Us)</h3>
                <div class="space-y-2 text-gray-700 dark:text-gray-300">
                    <div class="flex justify-between">
                        <span>Ticket Price:</span>
                        <span class="font-semibold" id="our_subtotal">GH₵ 0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Service Fee (2.5%):</span>
                        <span class="font-semibold" id="our_service_fee">GH₵ 0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Payment Processing:</span>
                        <span class="font-semibold" id="our_payment_fee">GH₵ 0.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-green-700 dark:text-green-400 pt-2 border-t border-green-300 dark:border-green-700">
                        <span>Customer Pays:</span>
                        <span id="our_total">GH₵ 0.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Organizer Receives:</span>
                        <span class="font-semibold" id="our_organizer_receives">GH₵ 0.00</span>
                    </div>
                </div>
            </div>

            <!-- Eventbrite Comparison -->
            <div class="mb-6 p-6 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-300 dark:border-red-700">
                <h3 class="text-lg font-bold text-red-700 dark:text-red-400 mb-4">Eventbrite</h3>
                <div class="space-y-2 text-gray-700 dark:text-gray-300">
                    <div class="flex justify-between">
                        <span>Ticket Price:</span>
                        <span class="font-semibold" id="eb_subtotal">GH₵ 0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Fees (~5-6%):</span>
                        <span class="font-semibold text-red-600 dark:text-red-400" id="eb_fees">GH₵ 0.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-red-300 dark:border-red-700">
                        <span>Customer Pays:</span>
                        <span id="eb_total">GH₵ 0.00</span>
                    </div>
                </div>
            </div>

            <!-- Savings Highlight -->
            <div class="p-6 bg-gradient-to-r from-green-500 to-blue-500 rounded-xl text-white text-center">
                <p class="text-2xl font-bold mb-2">You Save <span id="savings">GH₵ 0.00</span>!</p>
                <p class="text-sm opacity-90">That's <span id="savings_percent">0%</span> cheaper than Eventbrite</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('feeCalculatorForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        subtotal: parseFloat(formData.get('subtotal')),
        ticket_count: parseInt(formData.get('ticket_count')),
        fee_bearer: formData.get('fee_bearer')
    };

    try {
        const response = await fetch('{{ route('fee-calculator.calculate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        // Update Our Platform
        document.getElementById('our_subtotal').textContent = 'GH₵ ' + data.subtotal.toFixed(2);
        document.getElementById('our_service_fee').textContent = 'GH₵ ' + result.service_fee.toFixed(2);
        document.getElementById('our_payment_fee').textContent = 'GH₵ ' + result.payment_processing_fee.toFixed(2);
        document.getElementById('our_total').textContent = 'GH₵ ' + result.attendee_pays.toFixed(2);
        document.getElementById('our_organizer_receives').textContent = 'GH₵ ' + result.organizer_receives.toFixed(2);

        // Update Eventbrite
        document.getElementById('eb_subtotal').textContent = 'GH₵ ' + data.subtotal.toFixed(2);
        document.getElementById('eb_fees').textContent = 'GH₵ ' + result.eventbrite_fees.total_fees.toFixed(2);
        document.getElementById('eb_total').textContent = 'GH₵ ' + result.eventbrite_fees.customer_pays.toFixed(2);

        // Update Savings
        const savings = result.our_savings || 0;
        const savingsPercent = ((savings / result.eventbrite_fees.total_fees) * 100).toFixed(0);
        document.getElementById('savings').textContent = 'GH₵ ' + savings.toFixed(2);
        document.getElementById('savings_percent').textContent = savingsPercent;

        // Show results
        document.getElementById('resultsCard').classList.remove('hidden');
        document.getElementById('resultsCard').scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    } catch (error) {
        console.error('Error calculating fees:', error);
        alert('Error calculating fees. Please try again.');
    }
});
</script>
@endpush
@endsection
