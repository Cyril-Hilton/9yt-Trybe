@extends('layouts.admin')

@section('title', 'Payout Management')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Payout Management</h1>
        <p class="text-gray-400">Process and manage event payouts to organizations</p>
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

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700">
            <p class="text-gray-400 text-sm mb-1">Total Payouts</p>
            <p class="text-3xl font-bold text-white">{{ $statusCounts['all'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-xl p-6">
            <p class="text-yellow-100 text-sm mb-1">Pending</p>
            <p class="text-3xl font-bold text-white">{{ $statusCounts['pending'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6">
            <p class="text-blue-100 text-sm mb-1">Processing</p>
            <p class="text-3xl font-bold text-white">{{ $statusCounts['processing'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6">
            <p class="text-green-100 text-sm mb-1">Completed</p>
            <p class="text-3xl font-bold text-white">{{ $statusCounts['completed'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-xl p-6">
            <p class="text-red-100 text-sm mb-1">Failed</p>
            <p class="text-3xl font-bold text-white">{{ $statusCounts['failed'] }}</p>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="bg-gray-800 rounded-xl border border-gray-700 mb-6">
        <div class="flex overflow-x-auto">
            <a href="{{ route('admin.payouts.index') }}"
               class="px-6 py-4 text-sm font-medium border-b-2 transition whitespace-nowrap
                      {{ $status === 'all' ? 'text-indigo-400 border-indigo-500' : 'text-gray-400 border-transparent hover:text-white' }}">
                All Payouts ({{ $statusCounts['all'] }})
            </a>
            <a href="{{ route('admin.payouts.index', ['status' => 'pending']) }}"
               class="px-6 py-4 text-sm font-medium border-b-2 transition whitespace-nowrap
                      {{ $status === 'pending' ? 'text-yellow-400 border-yellow-500' : 'text-gray-400 border-transparent hover:text-white' }}">
                Pending ({{ $statusCounts['pending'] }})
            </a>
            <a href="{{ route('admin.payouts.index', ['status' => 'processing']) }}"
               class="px-6 py-4 text-sm font-medium border-b-2 transition whitespace-nowrap
                      {{ $status === 'processing' ? 'text-blue-400 border-blue-500' : 'text-gray-400 border-transparent hover:text-white' }}">
                Processing ({{ $statusCounts['processing'] }})
            </a>
            <a href="{{ route('admin.payouts.index', ['status' => 'completed']) }}"
               class="px-6 py-4 text-sm font-medium border-b-2 transition whitespace-nowrap
                      {{ $status === 'completed' ? 'text-green-400 border-green-500' : 'text-gray-400 border-transparent hover:text-white' }}">
                Completed ({{ $statusCounts['completed'] }})
            </a>
            <a href="{{ route('admin.payouts.index', ['status' => 'failed']) }}"
               class="px-6 py-4 text-sm font-medium border-b-2 transition whitespace-nowrap
                      {{ $status === 'failed' ? 'text-red-400 border-red-500' : 'text-gray-400 border-transparent hover:text-white' }}">
                Failed ({{ $statusCounts['failed'] }})
            </a>
        </div>
    </div>

    <!-- Payouts Table -->
    <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700">
        @if($payouts->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Payout #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Organization</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Payment Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($payouts as $payout)
                    <tr class="hover:bg-gray-750 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-white">#{{ $payout->id }}</div>
                            <div class="text-xs text-gray-400">{{ $payout->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-white">{{ $payout->event ? Str::limit($payout->event->title, 40) : 'Event not found' }}</div>
                            <div class="text-xs text-gray-400">{{ $payout->total_tickets_sold }} tickets sold</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($payout->company)
                                <div class="text-sm text-white">{{ $payout->company->name }}</div>
                                <div class="text-xs text-gray-400">{{ $payout->company->email }}</div>
                            @else
                                <div class="text-sm text-gray-500">No organizer</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-green-400">GH₵{{ number_format($payout->net_amount, 2) }}</div>
                            <div class="text-xs text-gray-400">Gross: GH₵{{ number_format($payout->gross_amount, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payout->paymentAccount)
                                <div class="text-sm text-white">
                                    @if($payout->payout_method === 'mobile_money')
                                        📱 {{ $payout->paymentAccount->mobile_money_network }}
                                    @else
                                        🏦 {{ Str::limit($payout->paymentAccount->bank_name, 20) }}
                                    @endif
                                </div>
                            @else
                                <span class="text-sm text-gray-500">Not set</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payout->status === 'pending')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-900 text-yellow-200">
                                    Pending
                                </span>
                            @elseif($payout->status === 'processing')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-900 text-blue-200">
                                    Processing
                                </span>
                            @elseif($payout->status === 'completed')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-900 text-green-200">
                                    Completed
                                </span>
                            @elseif($payout->status === 'failed')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-900 text-red-200">
                                    Failed
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('admin.payouts.show', $payout) }}"
                               class="text-indigo-400 hover:text-indigo-300 font-medium">
                                View Details →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payouts->hasPages())
        <div class="px-6 py-4 border-t border-gray-700 bg-gray-900">
            {{ $payouts->links() }}
        </div>
        @endif
        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-400">No payouts found</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if($status === 'all')
                    No payouts have been created yet.
                @else
                    No {{ $status }} payouts found.
                @endif
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
