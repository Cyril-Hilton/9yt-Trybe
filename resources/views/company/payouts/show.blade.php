@extends('layouts.company')

@section('title', 'Payout Details')

@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Payout Details</h1>
                <p class="mt-2 text-gray-600">{{ $payout->event->title }}</p>
            </div>
            <a href="{{ route('organization.payouts.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                ← Back to Payouts
            </a>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        @if(session('info'))
        <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-lg">
            {{ session('info') }}
        </div>
        @endif

        <!-- Status Banner -->
        <div class="mb-6 rounded-lg p-6 border-2
            @if($payout->status === 'pending') bg-yellow-50 border-yellow-200
            @elseif($payout->status === 'processing') bg-blue-50 border-blue-200
            @elseif($payout->status === 'completed') bg-green-50 border-green-200
            @elseif($payout->status === 'failed') bg-red-50 border-red-200
            @endif">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold
                        @if($payout->status === 'pending') text-yellow-800
                        @elseif($payout->status === 'processing') text-blue-800
                        @elseif($payout->status === 'completed') text-green-800
                        @elseif($payout->status === 'failed') text-red-800
                        @endif">
                        @if($payout->status === 'pending')
                            ⏳ Payment Setup Required
                        @elseif($payout->status === 'processing')
                            🔄 Payout Being Processed
                        @elseif($payout->status === 'completed')
                            ✅ Payout Completed
                        @elseif($payout->status === 'failed')
                            ❌ Payout Failed
                        @endif
                    </h3>
                    <p class="text-sm mt-1
                        @if($payout->status === 'pending') text-yellow-700
                        @elseif($payout->status === 'processing') text-blue-700
                        @elseif($payout->status === 'completed') text-green-700
                        @elseif($payout->status === 'failed') text-red-700
                        @endif">
                        @if($payout->status === 'pending' && !$payout->payment_account_id)
                            Please set up your payment account to receive this payout
                        @elseif($payout->status === 'pending')
                            Payment account set up. Our team will process your payout within 2-3 business days.
                        @elseif($payout->status === 'processing')
                            Your payout is being processed. You'll receive a confirmation email once complete.
                        @elseif($payout->status === 'completed')
                            Payment has been sent to your account. Check your confirmation email for details.
                        @elseif($payout->status === 'failed')
                            {{ $payout->failure_reason ?? 'Payout failed. Please contact support.' }}
                        @endif
                    </p>
                </div>
                @if($payout->status === 'pending' && !$payout->payment_account_id)
                <a href="{{ route('organization.payouts.setup', $payout) }}" class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition">
                    Set Up Payment
                </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Payout Amount Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">💰 Payout Amount</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Gross Revenue:</span>
                        <span class="font-semibold text-gray-900">GH₵{{ number_format($payout->gross_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Platform Fee (4%):</span>
                        <span class="font-semibold text-red-600">- GH₵{{ number_format($payout->platform_fees, 2) }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span class="text-lg font-bold text-gray-900">Net Payout:</span>
                            <span class="text-2xl font-bold text-green-600">GH₵{{ number_format($payout->net_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Performance Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">📊 Event Performance</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tickets Sold:</span>
                        <span class="font-semibold text-gray-900">{{ number_format($payout->total_tickets_sold) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Attendees Checked In:</span>
                        <span class="font-semibold text-gray-900">{{ number_format($payout->total_attendees) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Check-in Rate:</span>
                        <span class="font-semibold text-gray-900">
                            @if($payout->total_tickets_sold > 0)
                                {{ number_format(($payout->total_attendees / $payout->total_tickets_sold) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Average Ticket Price:</span>
                            <span class="font-semibold text-gray-900">
                                @if($payout->total_tickets_sold > 0)
                                    GH₵{{ number_format($payout->gross_amount / $payout->total_tickets_sold, 2) }}
                                @else
                                    GH₵0.00
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Account Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">💳 Payment Account</h3>
            @if($payout->paymentAccount)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Payment Method:</span>
                        <p class="font-semibold text-gray-900">
                            @if($payout->payout_method === 'mobile_money')
                                📱 Mobile Money
                            @else
                                🏦 Bank Transfer
                            @endif
                        </p>
                    </div>

                    @if($payout->payout_method === 'mobile_money')
                        <div>
                            <span class="text-sm text-gray-600">Network:</span>
                            <p class="font-semibold text-gray-900">{{ $payout->paymentAccount->mobile_money_network }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Phone Number:</span>
                            <p class="font-semibold text-gray-900">{{ $payout->paymentAccount->mobile_money_number }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Account Name:</span>
                            <p class="font-semibold text-gray-900">{{ $payout->paymentAccount->mobile_money_name }}</p>
                        </div>
                    @else
                        <div>
                            <span class="text-sm text-gray-600">Bank:</span>
                            <p class="font-semibold text-gray-900">{{ $payout->paymentAccount->bank_name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Account Number:</span>
                            <p class="font-semibold text-gray-900">{{ $payout->paymentAccount->account_number }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Account Name:</span>
                            <p class="font-semibold text-gray-900">{{ $payout->paymentAccount->account_name }}</p>
                        </div>
                        @if($payout->paymentAccount->branch)
                        <div>
                            <span class="text-sm text-gray-600">Branch:</span>
                            <p class="font-semibold text-gray-900">{{ $payout->paymentAccount->branch }}</p>
                        </div>
                        @endif
                    @endif
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="mt-2 text-gray-600">No payment account set up yet</p>
                    <a href="{{ route('organization.payouts.setup', $payout) }}" class="mt-4 inline-block px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Set Up Payment Account
                    </a>
                </div>
            @endif
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">📅 Timeline</h3>
            <div class="space-y-4">
                <!-- Created -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 bg-blue-600 rounded-full"></div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900">Payout Created</p>
                        <p class="text-sm text-gray-600">{{ $payout->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>

                @if($payout->congratulatory_email_sent_at)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 bg-blue-600 rounded-full"></div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900">Congratulatory Email Sent</p>
                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($payout->congratulatory_email_sent_at)->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
                @endif

                @if($payout->processed_at)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 bg-blue-600 rounded-full"></div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900">Processing Started</p>
                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($payout->processed_at)->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
                @endif

                @if($payout->completed_at)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 bg-green-600 rounded-full"></div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900">Payment Completed</p>
                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($payout->completed_at)->format('M d, Y \a\t h:i A') }}</p>
                        @if($payout->payout_reference)
                            <p class="text-xs text-gray-500 mt-1">Reference: {{ $payout->payout_reference }}</p>
                        @endif
                    </div>
                </div>
                @endif

                @if($payout->payment_confirmation_email_sent_at)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 bg-green-600 rounded-full"></div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900">Confirmation Email Sent</p>
                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($payout->payment_confirmation_email_sent_at)->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
