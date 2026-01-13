@extends('layouts.admin')

@section('title', 'Payout Details')

@section('content')
<div class="p-8" x-data="{ showCompleteModal: false, showFailModal: false }">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Payout #{{ $payout->id }}</h1>
            <p class="text-gray-400">{{ $payout->event ? $payout->event->title : 'Event not found' }}</p>
        </div>
        <a href="{{ route('admin.payouts.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
            ← Back to Payouts
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-900/50 border border-green-700 text-green-200 px-6 py-4 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-900/50 border border-red-700 text-red-200 px-6 py-4 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-900/50 border border-red-700 text-red-200 px-6 py-4 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Status Banner & Actions -->
    <div class="mb-6 rounded-xl p-6 border-2
        @if($payout->status === 'pending') bg-yellow-900/30 border-yellow-600
        @elseif($payout->status === 'processing') bg-blue-900/30 border-blue-600
        @elseif($payout->status === 'completed') bg-green-900/30 border-green-600
        @elseif($payout->status === 'failed') bg-red-900/30 border-red-600
        @endif">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold
                    @if($payout->status === 'pending') text-yellow-300
                    @elseif($payout->status === 'processing') text-blue-300
                    @elseif($payout->status === 'completed') text-green-300
                    @elseif($payout->status === 'failed') text-red-300
                    @endif">
                    @if($payout->status === 'pending')
                        ⏳ Pending Setup
                    @elseif($payout->status === 'processing')
                        🔄 Processing
                    @elseif($payout->status === 'completed')
                        ✅ Completed
                    @elseif($payout->status === 'failed')
                        ❌ Failed
                    @endif
                </h3>
                <p class="text-sm mt-1
                    @if($payout->status === 'pending') text-yellow-200
                    @elseif($payout->status === 'processing') text-blue-200
                    @elseif($payout->status === 'completed') text-green-200
                    @elseif($payout->status === 'failed') text-red-200
                    @endif">
                    @if($payout->status === 'pending')
                        @if($payout->payment_account_id)
                            Payment account set up. Ready to process.
                        @else
                            Waiting for organizer to set up payment account.
                        @endif
                    @elseif($payout->status === 'processing')
                        Payout is being processed. Complete or mark as failed.
                    @elseif($payout->status === 'completed')
                        Payout completed on {{ $payout->completed_at ? \Carbon\Carbon::parse($payout->completed_at)->format('M d, Y') : 'N/A' }}
                    @elseif($payout->status === 'failed')
                        {{ $payout->failure_reason ?? 'Payout failed.' }}
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                @if($payout->status === 'pending' && $payout->payment_account_id)
                    <form action="{{ route('admin.payouts.process', $payout) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                            Start Processing
                        </button>
                    </form>
                @endif

                @if($payout->status === 'processing')
                    <button @click="showCompleteModal = true" class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                        Mark as Completed
                    </button>
                    <button @click="showFailModal = true" class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">
                        Mark as Failed
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Payout Amount Card -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-bold text-white mb-4">💰 Payout Amount</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-400">Gross Revenue:</span>
                    <span class="font-semibold text-white">GH₵{{ number_format($payout->gross_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Platform Fee:</span>
                    <span class="font-semibold text-red-400">- GH₵{{ number_format($payout->platform_fees, 2) }}</span>
                </div>
                <div class="border-t border-gray-700 pt-3">
                    <div class="flex justify-between">
                        <span class="text-lg font-bold text-white">Net Payout:</span>
                        <span class="text-2xl font-bold text-green-400">GH₵{{ number_format($payout->net_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Performance -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-bold text-white mb-4">📊 Event Performance</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-400">Tickets Sold:</span>
                    <span class="font-semibold text-white">{{ number_format($payout->total_tickets_sold) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Checked In:</span>
                    <span class="font-semibold text-white">{{ number_format($payout->total_attendees) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Check-in Rate:</span>
                    <span class="font-semibold text-white">
                        @if($payout->total_tickets_sold > 0)
                            {{ number_format(($payout->total_attendees / $payout->total_tickets_sold) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Organization Info -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-bold text-white mb-4">🏢 Organization</h3>
            @if($payout->company)
                <div class="space-y-2">
                    <div>
                        <p class="text-gray-400 text-sm">Name</p>
                        <p class="font-semibold text-white">{{ $payout->company->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Email</p>
                        <p class="font-semibold text-white">{{ $payout->company->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Phone</p>
                        <p class="font-semibold text-white">{{ $payout->company->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            @else
                <p class="text-gray-400 text-sm">No organizer assigned</p>
            @endif
        </div>
    </div>

    <!-- Payment Account Details -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 mb-6">
        <h3 class="text-lg font-bold text-white mb-4">💳 Payment Account Details</h3>
        @if($payout->paymentAccount)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <span class="text-sm text-gray-400">Payment Method:</span>
                    <p class="font-semibold text-white">
                        @if($payout->payout_method === 'mobile_money')
                            📱 Mobile Money
                        @else
                            🏦 Bank Transfer
                        @endif
                    </p>
                </div>

                @if($payout->payout_method === 'mobile_money')
                    <div>
                        <span class="text-sm text-gray-400">Network:</span>
                        <p class="font-semibold text-white">{{ $payout->paymentAccount->mobile_money_network }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-400">Phone Number:</span>
                        <p class="font-semibold text-white">{{ $payout->paymentAccount->mobile_money_number }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-400">Account Name:</span>
                        <p class="font-semibold text-white">{{ $payout->paymentAccount->mobile_money_name }}</p>
                    </div>
                @else
                    <div>
                        <span class="text-sm text-gray-400">Bank:</span>
                        <p class="font-semibold text-white">{{ $payout->paymentAccount->bank_name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-400">Account Number:</span>
                        <p class="font-semibold text-white">{{ $payout->paymentAccount->account_number }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-400">Account Name:</span>
                        <p class="font-semibold text-white">{{ $payout->paymentAccount->account_name }}</p>
                    </div>
                    @if($payout->paymentAccount->branch)
                    <div>
                        <span class="text-sm text-gray-400">Branch:</span>
                        <p class="font-semibold text-white">{{ $payout->paymentAccount->branch }}</p>
                    </div>
                    @endif
                @endif
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-2 text-gray-400">Payment account not set up yet</p>
            </div>
        @endif
    </div>

    <!-- Timeline & Admin Notes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Timeline -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-bold text-white mb-4">📅 Timeline</h3>
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 bg-blue-500 rounded-full"></div>
                    <div class="ml-4">
                        <p class="font-semibold text-white">Payout Created</p>
                        <p class="text-sm text-gray-400">{{ $payout->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>

                @if($payout->congratulatory_email_sent_at)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 bg-blue-500 rounded-full"></div>
                    <div class="ml-4">
                        <p class="font-semibold text-white">Congratulatory Email Sent</p>
                        <p class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($payout->congratulatory_email_sent_at)->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
                @endif

                @if($payout->processed_at)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 bg-blue-500 rounded-full"></div>
                    <div class="ml-4">
                        <p class="font-semibold text-white">Processing Started</p>
                        <p class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($payout->processed_at)->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
                @endif

                @if($payout->completed_at)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 bg-green-500 rounded-full"></div>
                    <div class="ml-4">
                        <p class="font-semibold text-white">Payment Completed</p>
                        <p class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($payout->completed_at)->format('M d, Y \a\t h:i A') }}</p>
                        @if($payout->payout_reference)
                            <p class="text-xs text-gray-500 mt-1">Ref: {{ $payout->payout_reference }}</p>
                        @endif
                    </div>
                </div>
                @endif

                @if($payout->payment_confirmation_email_sent_at)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 bg-green-500 rounded-full"></div>
                    <div class="ml-4">
                        <p class="font-semibold text-white">Confirmation Email Sent</p>
                        <p class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($payout->payment_confirmation_email_sent_at)->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Admin Notes -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-bold text-white mb-4">📝 Admin Notes</h3>
            @if($payout->admin_notes)
                <div class="bg-gray-900 p-4 rounded-lg border border-gray-700">
                    <p class="text-gray-300 whitespace-pre-wrap">{{ $payout->admin_notes }}</p>
                </div>
            @else
                <p class="text-gray-500">No admin notes yet.</p>
            @endif

            @if($payout->failure_reason)
                <div class="mt-4">
                    <h4 class="text-sm font-semibold text-red-400 mb-2">Failure Reason:</h4>
                    <div class="bg-red-900/20 p-4 rounded-lg border border-red-800">
                        <p class="text-red-300">{{ $payout->failure_reason }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Complete Payout Modal -->
    <div x-show="showCompleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showCompleteModal = false" class="fixed inset-0 bg-black opacity-75"></div>
            <div class="relative bg-gray-800 rounded-xl max-w-lg w-full p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white mb-4">Complete Payout</h3>
                <form action="{{ route('admin.payouts.complete', $payout) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Payment Reference *</label>
                        <input type="text" name="payout_reference" required
                               class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none"
                               placeholder="e.g., TXN123456789">
                        <p class="text-xs text-gray-400 mt-1">Bank transfer reference or mobile money transaction ID</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" rows="3"
                                  class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none"
                                  placeholder="Add any internal notes..."></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                            Complete & Send Email
                        </button>
                        <button type="button" @click="showCompleteModal = false" class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Fail Payout Modal -->
    <div x-show="showFailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showFailModal = false" class="fixed inset-0 bg-black opacity-75"></div>
            <div class="relative bg-gray-800 rounded-xl max-w-lg w-full p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white mb-4">Mark Payout as Failed</h3>
                <form action="{{ route('admin.payouts.fail', $payout) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Failure Reason *</label>
                        <textarea name="failure_reason" rows="4" required
                                  class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none"
                                  placeholder="Explain why the payout failed..."></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">
                            Mark as Failed
                        </button>
                        <button type="button" @click="showFailModal = false" class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
[x-cloak] { display: none !important; }
</style>
@endsection
