@extends('layouts.app')

@section('title', 'Campaign Details - ' . $campaign->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('user.sms.campaigns.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold mb-4 inline-block">
                ← Back to Campaigns
            </a>
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h1 class="text-4xl font-bold gradient-text mb-2">{{ $campaign->name }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">Campaign created on {{ $campaign->created_at->format('F d, Y g:i A') }}</p>
                </div>

                <!-- Campaign Status Badge & Actions -->
                <div class="ml-4 flex items-center gap-3">
                    @if($campaign->status === 'completed')
                        <span class="px-4 py-2 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 text-sm font-semibold rounded-full">
                            ✓ Completed
                        </span>
                        <a href="{{ route('user.sms.campaigns.resend', $campaign->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Resend
                        </a>
                    @elseif($campaign->status === 'sent')
                        <span class="px-4 py-2 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 text-sm font-semibold rounded-full">
                            📤 Sent
                        </span>
                    @elseif($campaign->status === 'scheduled')
                        <span class="px-4 py-2 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300 text-sm font-semibold rounded-full">
                            ⏰ Scheduled
                        </span>
                    @elseif($campaign->status === 'processing')
                        <span class="px-4 py-2 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 text-sm font-semibold rounded-full">
                            ⏳ Processing
                        </span>
                    @elseif($campaign->status === 'failed')
                        <span class="px-4 py-2 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 text-sm font-semibold rounded-full">
                            ✗ Failed
                        </span>
                        <a href="{{ route('user.sms.campaigns.resend', $campaign->id) }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Retry
                        </a>
                    @elseif($campaign->status === 'cancelled')
                        <span class="px-4 py-2 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 text-sm font-semibold rounded-full">
                            ⊘ Cancelled
                        </span>
                    @else
                        <span class="px-4 py-2 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 text-sm font-semibold rounded-full">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-lg">
                <p class="text-sm text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
        @endif

        @if($campaign->status === 'failed' && $campaign->messages->where('status', 'failed')->count() > 0)
            @php
                $failedMessage = $campaign->messages->where('status', 'failed')->first();
                $apiResponse = is_string($failedMessage->api_response)
                    ? json_decode($failedMessage->api_response, true)
                    : $failedMessage->api_response;
            @endphp
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg p-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-lg font-bold text-red-800 dark:text-red-300 mb-2">Campaign Failed - Mnotify API Error</h4>

                        @if(is_array($apiResponse))
                            @if(isset($apiResponse['message']))
                                <p class="text-sm text-red-700 dark:text-red-300 font-semibold mb-2">
                                    Error: {{ $apiResponse['message'] }}
                                </p>
                            @endif

                            @if(isset($apiResponse['code']))
                                <p class="text-xs text-red-600 dark:text-red-400 mb-2">
                                    Error Code: {{ $apiResponse['code'] }}
                                </p>
                            @endif

                            <details class="mt-3">
                                <summary class="text-xs text-red-700 dark:text-red-300 cursor-pointer hover:underline font-semibold">
                                    View Full API Response
                                </summary>
                                <pre class="mt-2 text-xs bg-red-100 dark:bg-red-900/40 p-3 rounded overflow-x-auto text-red-900 dark:text-red-200">{{ json_encode($apiResponse, JSON_PRETTY_PRINT) }}</pre>
                            </details>
                        @else
                            <p class="text-sm text-red-700 dark:text-red-300">
                                {{ $failedMessage->failed_reason ?? 'Unknown error occurred' }}
                            </p>
                        @endif

                        <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded">
                            <p class="text-xs font-semibold text-yellow-800 dark:text-yellow-300 mb-2">Common Causes:</p>
                            <ul class="text-xs text-yellow-700 dark:text-yellow-400 list-disc list-inside space-y-1">
                                <li>Insufficient SMS credits in your account</li>
                                <li>Sender ID "{{ $campaign->sender_id }}" not approved or not registered with Mnotify</li>
                                <li>Invalid Mnotify API key in configuration</li>
                                <li>Mnotify service temporarily unavailable</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Campaign Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Recipients -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-t-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Recipients</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($campaign->total_recipients) }}</p>
                    </div>
                    <svg class="w-12 h-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Delivered -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-t-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Delivered</p>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($campaign->total_delivered) }}</p>
                        @if($campaign->delivery_rate > 0)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $campaign->delivery_rate }}% rate</p>
                        @endif
                    </div>
                    <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Failed -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-t-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Failed</p>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($campaign->total_failed) }}</p>
                    </div>
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Credits Used -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-t-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Credits Used</p>
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($campaign->credits_used) }}</p>
                    </div>
                    <svg class="w-12 h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Campaign Details & Message -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Message Content -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    Message Content
                </h3>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $campaign->message }}</p>
                </div>
                <div class="mt-4 flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>{{ mb_strlen($campaign->message) }} characters</span>
                    <span>≈ {{ ceil(mb_strlen($campaign->message) / 160) }} SMS credit(s) per recipient</span>
                </div>
            </div>

            <!-- Campaign Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Campaign Info
                </h3>
                <div class="space-y-4">
                    @if($campaign->sender_id)
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Sender ID</p>
                            <p class="text-sm font-mono font-bold text-gray-900 dark:text-white">{{ $campaign->sender_id }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Campaign Type</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($campaign->type ?? 'Standard') }}</p>
                    </div>

                    @if($campaign->scheduled_at)
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Scheduled For</p>
                            <p class="text-sm font-semibold text-purple-600 dark:text-purple-400">{{ $campaign->scheduled_at->format('M d, Y g:i A') }}</p>
                        </div>
                    @endif

                    @if($campaign->sent_at)
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Sent At</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $campaign->sent_at->format('M d, Y g:i A') }}</p>
                        </div>
                    @endif

                    @if($campaign->completed_at)
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Completed At</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $campaign->completed_at->format('M d, Y g:i A') }}</p>
                        </div>
                    @endif

                    <!-- Actions -->
                    @if($campaign->status === 'scheduled')
                        <form method="POST" action="{{ route('user.sms.campaigns.cancel', $campaign->id) }}" onsubmit="return confirm('Cancel this scheduled campaign? Credits will be refunded.')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition">
                                Cancel Campaign
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Delivery Statistics -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Delivery Statistics
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full mb-2">
                        <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $deliveryStats['pending'] }}</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pending</p>
                </div>

                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-2">
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $deliveryStats['submitted'] }}</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Submitted</p>
                </div>

                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full mb-2">
                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $deliveryStats['delivered'] }}</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Delivered</p>
                </div>

                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full mb-2">
                        <span class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $deliveryStats['failed'] }}</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Failed</p>
                </div>

                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-full mb-2">
                        <span class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $deliveryStats['rejected'] }}</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Rejected</p>
                </div>

                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full mb-2">
                        <span class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $deliveryStats['expired'] }}</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Expired</p>
                </div>
            </div>
        </div>

        <!-- Message List -->
        @if($campaign->messages->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Individual Messages ({{ $campaign->messages->count() }})
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recipient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Message ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sent At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Delivered At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($campaign->messages as $message)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $message->recipient }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($message->status === 'delivered')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 text-xs font-semibold rounded-full">✓ Delivered</span>
                                @elseif($message->status === 'submitted')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 text-xs font-semibold rounded-full">📤 Submitted</span>
                                @elseif($message->status === 'failed')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 text-xs font-semibold rounded-full">✗ Failed</span>
                                @elseif($message->status === 'rejected')
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300 text-xs font-semibold rounded-full">⊘ Rejected</span>
                                @elseif($message->status === 'expired')
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 text-xs font-semibold rounded-full">⌛ Expired</span>
                                @else
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 text-xs font-semibold rounded-full">⏳ {{ ucfirst($message->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400 font-mono">
                                {{ $message->message_id ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $message->sent_at ? $message->sent_at->format('M d, g:i A') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $message->delivered_at ? $message->delivered_at->format('M d, g:i A') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endsection
