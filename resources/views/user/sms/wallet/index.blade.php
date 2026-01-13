@extends('layouts.app')

@section('title', 'SMS Wallet')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        SMS Wallet & Credits
                    </h1>
                    <p class="mt-2 text-gray-600">Purchase SMS credits and manage your balance</p>
                </div>
                <a href="{{ route('user.sms.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Current Balance Card -->
        <div class="mb-8">
            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl shadow-2xl p-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold opacity-90 mb-2">Current Balance</p>
                        <h2 class="text-5xl font-black mb-4">{{ number_format($creditBalance->balance) }}</h2>
                        <p class="text-sm opacity-75">SMS Credits Available</p>
                    </div>
                    <div class="hidden md:block">
                        <svg class="w-32 h-32 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-white/20">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs opacity-75 mb-1">Total Purchased</p>
                            <p class="text-2xl font-bold">{{ number_format($creditBalance->total_purchased) }}</p>
                        </div>
                        <div>
                            <p class="text-xs opacity-75 mb-1">Total Used</p>
                            <p class="text-2xl font-bold">{{ number_format($creditBalance->total_used) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SMS Plans -->
        <div class="mb-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Choose Your Plan</h2>
                <p class="text-gray-600">Select a plan and pay securely with Paystack</p>
            </div>

            @if($plans->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($plans as $plan)
                        <div class="relative bg-white rounded-2xl shadow-xl border-2 border-gray-200 hover:border-indigo-300 hover:shadow-2xl transition-all duration-200 overflow-hidden group">
                            @if($plan->badge)
                                <div class="absolute top-4 right-4 z-10">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-yellow-400 to-orange-400 text-white">
                                        {{ $plan->badge }}
                                    </span>
                                </div>
                            @endif

                            <div class="p-6">
                                <div class="mb-4">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $plan->name }}</h3>
                                    @if($plan->description)
                                        <p class="text-sm text-gray-600">{{ $plan->description }}</p>
                                    @endif
                                </div>

                                <div class="mb-6">
                                    <div class="flex items-baseline">
                                        <span class="text-4xl font-black text-indigo-600">GH₵ {{ number_format($plan->price, 2) }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">{{ number_format($plan->sms_credits) }} SMS Credits</p>
                                </div>

                                <div class="space-y-3 mb-6">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>{{ number_format($plan->sms_credits) }} SMS messages</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-700">
                                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>GH₵ {{ number_format($plan->price / $plan->sms_credits, 4) }} per SMS</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-700">
                                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>No expiry date</span>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('user.sms.wallet.purchase') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg group-hover:shadow-xl">
                                        Choose Plan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-xl border-2 border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <p class="text-gray-600 font-semibold mb-2">No SMS plans available</p>
                    <p class="text-sm text-gray-500">Please contact the administrator to set up SMS plans.</p>
                </div>
            @endif
        </div>

        <!-- Transaction History -->
        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="text-xl font-bold text-white">Transaction History</h2>
                </div>
            </div>

            <div class="p-6">
                @if($transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-gray-200">
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Plan</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Credits</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->created_at->format('M d, Y') }}
                                            <br>
                                            <span class="text-xs text-gray-500">{{ $transaction->created_at->format('h:i A') }}</span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @if($transaction->type === 'purchase')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                                    Purchase
                                                </span>
                                            @elseif($transaction->type === 'manual_credit')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                    Manual Credit
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900">
                                            {{ $transaction->plan ? $transaction->plan->name : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            +{{ number_format($transaction->sms_credits) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($transaction->amount > 0)
                                                GHS {{ number_format($transaction->amount, 2) }}
                                            @else
                                                <span class="text-gray-500">Free</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @if($transaction->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                    ✓ Completed
                                                </span>
                                            @elseif($transaction->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                                    ⏳ Pending
                                                </span>
                                            @elseif($transaction->status === 'failed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                    ✗ Failed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('user.sms.transactions.show', $transaction->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-600 font-semibold mb-2">No transactions yet</p>
                        <p class="text-sm text-gray-500">Purchase your first SMS plan to get started</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
