@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Transaction Details
                    </h1>
                    <p class="mt-2 text-gray-600">Reference: {{ $transaction->reference }}</p>
                </div>
                <a href="{{ route('user.sms.wallet.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Wallet
                </a>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="mb-8">
            @if($transaction->status === 'completed')
                <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-green-900">Transaction Successful!</h3>
                            <p class="text-sm text-green-700 mt-1">Your SMS credits have been added to your account.</p>
                        </div>
                    </div>
                </div>
            @elseif($transaction->status === 'pending')
                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-yellow-900">Transaction Pending</h3>
                            <p class="text-sm text-yellow-700 mt-1">
                                @if($transaction->type === 'purchase')
                                    Payment not completed. Click "Continue to Pay Now" below to retry.
                                @else
                                    Your payment is being processed. Please wait...
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($transaction->status === 'failed')
                <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-red-900">Transaction Failed</h3>
                            <p class="text-sm text-red-700 mt-1">There was an issue processing your payment.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Transaction Information -->
        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Transaction Information
                </h2>
            </div>

            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-semibold text-gray-500 mb-1">Transaction Reference</dt>
                        <dd class="text-lg font-bold text-gray-900 font-mono">{{ $transaction->reference }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-semibold text-gray-500 mb-1">Transaction Type</dt>
                        <dd class="text-lg font-bold text-gray-900">
                            @if($transaction->type === 'purchase')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-800">
                                    Purchase
                                </span>
                            @elseif($transaction->type === 'manual_credit')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800">
                                    Manual Credit
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-purple-100 text-purple-800">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-semibold text-gray-500 mb-1">SMS Credits</dt>
                        <dd class="text-2xl font-black text-indigo-600">+{{ number_format($transaction->credits) }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-semibold text-gray-500 mb-1">Amount Paid</dt>
                        <dd class="text-2xl font-black text-gray-900">
                            @if($transaction->amount > 0)
                                GHS {{ number_format($transaction->amount, 2) }}
                            @else
                                <span class="text-green-600">FREE</span>
                            @endif
                        </dd>
                    </div>

                    @if($transaction->plan)
                        <div>
                            <dt class="text-sm font-semibold text-gray-500 mb-1">Plan Name</dt>
                            <dd class="text-lg font-bold text-gray-900">{{ $transaction->plan->name ?? 'N/A' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-semibold text-gray-500 mb-1">Price per SMS</dt>
                            <dd class="text-lg font-bold text-gray-900">
                                @if($transaction->plan->sms_credits > 0)
                                    GHS {{ number_format($transaction->plan->price / $transaction->plan->sms_credits, 4) }}
                                @else
                                    N/A
                                @endif
                            </dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-semibold text-gray-500 mb-1">Payment Method</dt>
                        <dd class="text-lg font-bold text-gray-900">
                            {{ ucfirst($transaction->payment_method ?? 'N/A') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-semibold text-gray-500 mb-1">Status</dt>
                        <dd class="text-lg font-bold text-gray-900">
                            @if($transaction->status === 'completed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800">
                                    ✓ Completed
                                </span>
                            @elseif($transaction->status === 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-800">
                                    ⏳ Pending
                                </span>
                            @elseif($transaction->status === 'failed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800">
                                    ✗ Failed
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-gray-100 text-gray-800">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-semibold text-gray-500 mb-1">Created Date</dt>
                        <dd class="text-lg font-bold text-gray-900">
                            {{ $transaction->created_at->format('M d, Y - h:i A') }}
                        </dd>
                    </div>

                    @if($transaction->completed_at)
                        <div>
                            <dt class="text-sm font-semibold text-gray-500 mb-1">Completed Date</dt>
                            <dd class="text-lg font-bold text-gray-900">
                                {{ $transaction->completed_at->format('M d, Y - h:i A') }}
                            </dd>
                        </div>
                    @endif

                    @if($transaction->type === 'manual_credit' && $transaction->creditedByAdmin)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-semibold text-gray-500 mb-1">Credited By</dt>
                            <dd class="text-lg font-bold text-gray-900">
                                {{ $transaction->creditedByAdmin->name ?? 'Admin' }} ({{ $transaction->creditedByAdmin->email ?? 'N/A' }})
                            </dd>
                        </div>
                    @endif

                    @if($transaction->notes)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-semibold text-gray-500 mb-1">Notes</dt>
                            <dd class="text-base text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-200">
                                {{ $transaction->notes }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center space-x-4">
            <a href="{{ route('organization.sms.wallet.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                View All Transactions
            </a>

            @if($transaction->status === 'pending' && $transaction->type === 'purchase' && $transaction->plan)
                <form id="retryPaymentForm" method="POST" action="{{ route('organization.sms.wallet.purchase') }}" class="inline-block">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $transaction->sms_plan_id }}">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white font-bold rounded-xl hover:from-orange-700 hover:to-red-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Continue to Pay Now
                    </button>
                </form>
            @endif

            @if($transaction->status === 'completed')
                <a href="{{ route('organization.sms.campaigns.send-bulk') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Start Sending SMS
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
