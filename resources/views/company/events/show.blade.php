@extends('layouts.company')

@section('title', $event->title)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $event->title }}</h1>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                        @if($event->status === 'approved') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                        @elseif($event->status === 'pending') bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                        @elseif($event->status === 'rejected') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                        @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                        @endif">
                        {{ ucfirst($event->status) }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">{{ $event->formatted_date }} • {{ $event->formatted_time }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('organization.events.edit', $event) }}"
                   class="px-4 py-2 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                    Edit Event
                </a>
                @if($event->isApproved())
                <a href="{{ route('events.show', $event->slug) }}" target="_blank"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition">
                    View Public Page
                </a>
                @endif
            </div>
        </div>

        @if($event->isRejected())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-red-900 dark:text-red-200 mb-2">Rejection Reason</h3>
            <p class="text-red-800 dark:text-red-300">{{ $event->rejection_reason }}</p>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Views</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_views']) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tickets Sold</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_tickets_sold']) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Revenue</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">GH₵{{ number_format($stats['total_revenue'], 0) }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                        <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Likes</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_likes']) }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <a href="{{ route('organization.events.attendees.index', $event) }}"
               class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-xl transition group">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">Manage Attendees</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $stats['total_tickets_sold'] }} attendees</p>
                    </div>
                    <svg class="w-8 h-8 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </a>

            <a href="{{ route('organization.events.analytics', $event) }}"
               class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-xl transition group">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">View Analytics</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Detailed insights</p>
                    </div>
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </a>

            <a href="{{ route('organization.finance.invoices') }}"
               class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-xl transition group">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">View Orders</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $stats['completed_orders'] }} completed</p>
                    </div>
                    <svg class="w-8 h-8 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </a>
        </div>

        <!-- Tickets Overview -->
        @if($event->tickets->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Tickets</h2>
            <div class="space-y-4">
                @foreach($event->tickets as $ticket)
                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $ticket->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $ticket->formatted_price }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Sold</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $ticket->sold }}@if($ticket->quantity)/<span class="text-gray-500 dark:text-gray-400">{{ $ticket->quantity }}</span>@endif</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-6">
            <p class="text-yellow-800 dark:text-yellow-200">No tickets created yet. <a href="{{ route('organization.events.edit', $event) }}" class="font-semibold underline hover:text-yellow-900 dark:hover:text-yellow-100">Add tickets</a> to start selling.</p>
        </div>
        @endif

        <!-- Event Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Event Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Location</h3>
                    @if($event->location_type === 'venue')
                    <p class="text-gray-700 dark:text-gray-300">{{ $event->venue_name }}</p>
                    @if($event->venue_address)
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $event->venue_address }}</p>
                    @endif
                    @elseif($event->location_type === 'online')
                    <p class="text-gray-700 dark:text-gray-300">Online Event</p>
                    @if($event->online_platform)
                    <p class="text-sm text-gray-600 dark:text-gray-400">Platform: {{ ucfirst($event->online_platform) }}</p>
                    @endif
                    @else
                    <p class="text-gray-700 dark:text-gray-300">To Be Announced</p>
                    @endif
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Fee Structure</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ ucfirst($event->fee_bearer) }} pays fees</p>
                </div>

                @if($event->age_restriction)
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Age Restriction</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $event->age_restriction }}</p>
                </div>
                @endif

                @if($event->door_time)
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Door Time</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($event->door_time)->format('g:i A') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
