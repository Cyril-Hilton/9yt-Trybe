@extends('layouts.admin')

@section('title', 'SMS Dashboard')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">SMS Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage and send SMS messages to users and organizers</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Available Credits -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm opacity-90 mb-1">Available Credits</p>
            <h3 class="text-3xl font-bold">{{ number_format($creditBalance->balance) }}</h3>
        </div>

        <!-- Total Campaigns -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <p class="text-sm opacity-90 mb-1">Total Campaigns</p>
            <h3 class="text-3xl font-bold">{{ number_format($stats['total_campaigns']) }}</h3>
        </div>

        <!-- Total Sent -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm opacity-90 mb-1">Messages Sent</p>
            <h3 class="text-3xl font-bold">{{ number_format($stats['total_sent']) }}</h3>
        </div>

        <!-- Total Users -->
        <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-sm opacity-90 mb-1">Total Users</p>
            <h3 class="text-3xl font-bold">{{ number_format($stats['total_users']) }}</h3>
        </div>

        <!-- Total Organizers -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p class="text-sm opacity-90 mb-1">Organizers</p>
            <h3 class="text-3xl font-bold">{{ number_format($stats['total_organizers']) }}</h3>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Send Single SMS -->
        <a href="{{ route('admin.sms.send-single') }}" class="group bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border-2 border-transparent hover:border-indigo-500">
            <div class="flex items-start">
                <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl text-white group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Send Single SMS</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Send instant message to a single recipient</p>
                </div>
            </div>
        </a>

        <!-- Send Bulk SMS -->
        <a href="{{ route('admin.sms.send-bulk') }}" class="group bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border-2 border-transparent hover:border-green-500">
            <div class="flex items-start">
                <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl text-white group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Send Bulk SMS</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Send to multiple recipients, import Excel, or select specific contacts</p>
                </div>
            </div>
        </a>

        <!-- View Campaigns -->
        <a href="{{ route('admin.sms.campaigns.index') }}" class="group bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border-2 border-transparent hover:border-blue-500">
            <div class="flex items-start">
                <div class="p-3 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl text-white group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">View Campaigns</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Track and manage all SMS campaigns</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Campaigns -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Recent Campaigns</h2>

        @if($recentCampaigns->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Campaign</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Type</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Recipients</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Date</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentCampaigns as $campaign)
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-3 px-4">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $campaign->name }}</p>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $campaign->type === 'single' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($campaign->type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                                {{ $campaign->total_sent }} / {{ $campaign->total_recipients }}
                            </td>
                            <td class="py-3 px-4">
                                @if($campaign->status === 'completed')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                @elseif($campaign->status === 'processing')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Processing</span>
                                @elseif($campaign->status === 'failed')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Failed</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($campaign->status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                                {{ $campaign->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.sms.campaigns.show', $campaign->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">No campaigns yet. Start sending SMS messages!</p>
            </div>
        @endif
    </div>
</div>
@endsection
