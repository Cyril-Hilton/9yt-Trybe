@extends('layouts.admin')

@section('title', 'Campaign Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        {{ $campaign->name }}
                    </h1>
                    <p class="mt-2 text-gray-600">Campaign details and delivery statistics</p>
                </div>
                <a href="{{ route('admin.sms.campaigns.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Campaigns
                </a>
            </div>
        </div>

        <div class="mb-8">
            @if($campaign->status === 'completed')
                <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-6">
                    <div class="flex items-center">
                        <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-green-900">Campaign Completed</h3>
                            <p class="text-sm text-green-700">All messages have been processed</p>
                        </div>
                    </div>
                </div>
            @elseif($campaign->status === 'processing')
                <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6">
                    <div class="flex items-center">
                        <svg class="h-12 w-12 text-blue-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-blue-900">Campaign Processing</h3>
                            <p class="text-sm text-blue-700">Messages are being sent</p>
                        </div>
                    </div>
                </div>
            @elseif($campaign->status === 'scheduled')
                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-6">
                    <div class="flex items-center">
                        <svg class="h-12 w-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-yellow-900">Campaign Scheduled</h3>
                            <p class="text-sm text-yellow-700">
                                Scheduled for {{ $campaign->scheduled_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($campaign->status === 'failed')
                <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-6">
                    <div class="flex items-center">
                        <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-red-900">Campaign Failed</h3>
                            <p class="text-sm text-red-700">There was an error sending this campaign</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($campaign->status === 'failed' && $campaign->messages->where('status', 'failed')->count() > 0)
            @php
                $failedMessage = $campaign->messages->where('status', 'failed')->first();
                $apiResponse = is_string($failedMessage->api_response)
                    ? json_decode($failedMessage->api_response, true)
                    : $failedMessage->api_response;
            @endphp
            <div class="mb-8 bg-red-50 border-2 border-red-200 rounded-2xl p-6">
                <div class="flex items-start">
                    <svg class="w-8 h-8 text-red-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-xl font-bold text-red-900 mb-3">Detailed Error Information</h4>

                        @if(is_array($apiResponse))
                            @if(isset($apiResponse['message']))
                                <div class="mb-3 p-3 bg-red-100 rounded-lg">
                                    <p class="text-sm font-bold text-red-800">Provider Error</p>
                                    <p class="text-sm text-red-700">{{ $apiResponse['message'] }}</p>
                                </div>
                            @endif

                            @if(isset($apiResponse['code']))
                                <p class="text-sm text-red-600 mb-3">
                                    <span class="font-semibold">Error Code:</span> {{ $apiResponse['code'] }}
                                </p>
                            @endif

                            <details class="mt-4">
                                <summary class="text-sm text-red-800 cursor-pointer hover:underline font-bold">
                                    View full API response
                                </summary>
                                <pre class="mt-3 text-xs bg-red-100 p-4 rounded-lg overflow-x-auto text-red-900 border border-red-300">{{ json_encode($apiResponse, JSON_PRETTY_PRINT) }}</pre>
                            </details>
                        @else
                            <p class="text-sm text-red-700">
                                {{ $failedMessage->failed_reason ?? 'Unknown error occurred. Please contact support.' }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Campaign Info</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-600">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </dd>
                        </div>
                        @if($campaign->sender_id)
                            <div>
                                <dt class="text-gray-600">Sender ID</dt>
                                <dd class="mt-1 font-semibold text-gray-900">{{ $campaign->sender_id }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-gray-600">Created</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ $campaign->created_at->format('M d, Y h:i A') }}</dd>
                        </div>
                        @if($campaign->scheduled_at)
                            <div>
                                <dt class="text-gray-600">Scheduled For</dt>
                                <dd class="mt-1 font-semibold text-gray-900">{{ $campaign->scheduled_at->format('M d, Y h:i A') }}</dd>
                            </div>
                        @endif
                        @if($campaign->sent_at)
                            <div>
                                <dt class="text-gray-600">Sent At</dt>
                                <dd class="mt-1 font-semibold text-gray-900">{{ $campaign->sent_at->format('M d, Y h:i A') }}</dd>
                            </div>
                        @endif
                        @if($campaign->completed_at)
                            <div>
                                <dt class="text-gray-600">Completed At</dt>
                                <dd class="mt-1 font-semibold text-gray-900">{{ $campaign->completed_at->format('M d, Y h:i A') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold opacity-90">Total Recipients</span>
                            <svg class="w-8 h-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <p class="text-4xl font-black">{{ number_format($campaign->total_recipients) }}</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold opacity-90">Credits Used</span>
                            <svg class="w-8 h-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <p class="text-4xl font-black">{{ number_format($campaign->credits_used) }}</p>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden h-full">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            Message Content
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 min-h-[200px]">
                            <p class="text-gray-900 whitespace-pre-wrap text-base leading-relaxed">{{ $campaign->message }}</p>
                        </div>
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-200">
                                <span class="text-sm text-indigo-600 font-semibold">Characters</span>
                                <p class="text-2xl font-black text-indigo-900 mt-1">{{ strlen($campaign->message) }}</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-xl border border-purple-200">
                                <span class="text-sm text-purple-600 font-semibold">Type</span>
                                <p class="text-2xl font-black text-purple-900 mt-1">{{ ucfirst($campaign->type) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Delivery Breakdown</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-gray-500">Pending</p>
                    <p class="text-xl font-black text-gray-800">{{ number_format($deliveryStats['pending']) }}</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-blue-600">Submitted</p>
                    <p class="text-xl font-black text-blue-800">{{ number_format($deliveryStats['submitted']) }}</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-green-600">Delivered</p>
                    <p class="text-xl font-black text-green-800">{{ number_format($deliveryStats['delivered']) }}</p>
                </div>
                <div class="bg-red-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-red-600">Failed</p>
                    <p class="text-xl font-black text-red-800">{{ number_format($deliveryStats['failed']) }}</p>
                </div>
                <div class="bg-yellow-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-yellow-600">Rejected</p>
                    <p class="text-xl font-black text-yellow-800">{{ number_format($deliveryStats['rejected']) }}</p>
                </div>
                <div class="bg-gray-100 rounded-xl p-4 text-center">
                    <p class="text-xs text-gray-600">Expired</p>
                    <p class="text-xl font-black text-gray-800">{{ number_format($deliveryStats['expired']) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
