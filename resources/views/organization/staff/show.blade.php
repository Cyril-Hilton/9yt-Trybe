@extends('layouts.company')

@section('title', 'View Attendant')

@section('content')
<div class="p-4 max-w-5xl mx-auto space-y-6">
    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ route('organization.staff.index') }}" class="text-cyan-600 dark:text-cyan-400 hover:text-cyan-800 dark:hover:text-cyan-300 flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Attendants
        </a>
    </div>

    {{-- Attendant Info Card --}}
    <div class="glass-card">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-cyan-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold">
                    {{ strtoupper(substr($staff->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $staff->name }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        @if($staff->status === 'active')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                            Active
                        </span>
                        @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                            Suspended
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('organization.staff.edit', $staff) }}" class="glass-btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>

                @if($staff->status === 'active')
                <form action="{{ route('organization.staff.suspend', $staff) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="glass-btn-warning">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Suspend
                    </button>
                </form>
                @else
                <form action="{{ route('organization.staff.activate', $staff) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="glass-btn-success">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Activate
                    </button>
                </form>
                @endif

                <form action="{{ route('organization.staff.destroy', $staff) }}" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this attendant?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="glass-btn-danger">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>

        {{-- Contact Information --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Phone Number</p>
                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $staff->phone }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Email</p>
                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $staff->email ?? 'Not provided' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Last Login</p>
                <p class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ $staff->last_login_at ? $staff->last_login_at->format('M j, Y g:i A') : 'Never' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Assigned Events --}}
    <div class="glass-card">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Assigned Events</h2>

        @if($events && count($events) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($events as $event)
            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $event->title }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $event->start_date ? $event->start_date->format('M j, Y') : 'No date set' }}
                </p>
                @if($event->location)
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $event->location }}
                </p>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400">Access to all company events</p>
        </div>
        @endif
    </div>

    {{-- Recent Check-ins --}}
    <div class="glass-card">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Recent Check-ins</h2>

        @if($recentCheckIns && count($recentCheckIns) > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/80 dark:bg-gray-800/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Ticket Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Date/Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recentCheckIns as $checkIn)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">
                            {{ substr($checkIn->ticket_code, -8) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                            {{ $checkIn->order->customer_name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($checkIn->check_in_method === 'qr')
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                                QR Code
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs rounded-full bg-cyan-100 dark:bg-cyan-900/30 text-cyan-800 dark:text-cyan-300">
                                Manual
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            {{ $checkIn->checked_in_at->format('M j, Y g:i A') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400">No check-ins yet</p>
        </div>
        @endif
    </div>

    {{-- Account Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="glass-card text-center">
            <div class="text-3xl font-bold text-cyan-600 dark:text-cyan-400">
                {{ $recentCheckIns->count() }}
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Total Check-ins</p>
        </div>

        <div class="glass-card text-center">
            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                {{ $staff->created_at->format('M j, Y') }}
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Member Since</p>
        </div>

        <div class="glass-card text-center">
            <div class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $events && count($events) > 0 ? count($events) : 'All' }}
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Events Access</p>
        </div>
    </div>
</div>
@endsection
