@extends('layouts.admin')

@section('title', 'Complementary Tickets')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">Complementary Tickets</h1>
                    <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400">Manage complementary tickets for events on the platform</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('admin.complementary-tickets.create') }}" class="inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-lg transition text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="hidden sm:inline">Issue Single Ticket</span>
                        <span class="sm:hidden">Issue Ticket</span>
                    </a>
                    <a href="{{ route('admin.complementary-tickets.bulk-create') }}" class="inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg transition text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span class="hidden sm:inline">Bulk Upload (Excel)</span>
                        <span class="sm:hidden">Bulk Upload</span>
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-lg">
                <p class="text-sm text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg">
                <p class="text-sm text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif

        @if(session('errors') && is_array(session('errors')))
            <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 rounded-lg">
                <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-300 mb-2">Some rows had errors:</p>
                <ul class="text-xs text-yellow-700 dark:text-yellow-400 list-disc list-inside space-y-1">
                    @foreach(array_slice(session('errors'), 0, 10) as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    @if(count(session('errors')) > 10)
                        <li class="font-semibold">... and {{ count(session('errors')) - 10 }} more errors</li>
                    @endif
                </ul>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-t-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Tickets</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                    </div>
                    <svg class="w-12 h-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-t-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Active</p>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['active']) }}</p>
                    </div>
                    <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-t-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Used</p>
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['used']) }}</p>
                    </div>
                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-t-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Cancelled</p>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['cancelled']) }}</p>
                    </div>
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('admin.complementary-tickets.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Event Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event</label>
                    <select name="event_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Used</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Purpose Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purpose</label>
                    <select name="purpose" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Purposes</option>
                        <option value="media" {{ request('purpose') == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="promoter" {{ request('purpose') == 'promoter' ? 'selected' : '' }}>Promoter</option>
                        <option value="volunteer" {{ request('purpose') == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                        <option value="influencer" {{ request('purpose') == 'influencer' ? 'selected' : '' }}>Influencer</option>
                        <option value="student" {{ request('purpose') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="sponsor" {{ request('purpose') == 'sponsor' ? 'selected' : '' }}>Sponsor</option>
                        <option value="staff" {{ request('purpose') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="other" {{ request('purpose') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, phone, reference..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Filter Buttons -->
                <div class="md:col-span-2 lg:col-span-4 flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <button type="submit" class="px-4 sm:px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition text-sm sm:text-base">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.complementary-tickets.index') }}" class="px-4 sm:px-6 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-semibold rounded-lg transition text-center text-sm sm:text-base">
                        Clear Filters
                    </a>
                    <a href="{{ route('admin.complementary-tickets.template-download') }}" class="sm:ml-auto px-4 sm:px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition text-center text-sm sm:text-base">
                        <span class="hidden sm:inline">Download CSV Template</span>
                        <span class="sm:hidden">Download Template</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Tickets Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recipient</th>
                            <th class="hidden md:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ticket</th>
                            <th class="hidden lg:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Purpose</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="hidden xl:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Issued By</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <div class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->recipient_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[150px]">{{ $ticket->recipient_email }}</div>
                                @if($ticket->recipient_phone)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->recipient_phone }}</div>
                                @endif
                            </td>
                            <td class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->event->title }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->event->start_date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <div class="text-xs sm:text-sm text-gray-900 dark:text-white">
                                    <span class="font-semibold">{{ ucfirst($ticket->ticket_type) }}</span>
                                    × {{ $ticket->quantity }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    GHS {{ number_format($ticket->original_price * $ticket->quantity, 2) }}
                                </div>
                                <div class="text-xs font-mono text-gray-500 dark:text-gray-400 hidden sm:block">{{ $ticket->ticket_reference }}</div>
                            </td>
                            <td class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                @if($ticket->purpose)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($ticket->purpose === 'media') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300
                                        @elseif($ticket->purpose === 'promoter') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                        @elseif($ticket->purpose === 'volunteer') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                        @elseif($ticket->purpose === 'influencer') bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300
                                        @elseif($ticket->purpose === 'student') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                        @elseif($ticket->purpose === 'sponsor') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300
                                        @elseif($ticket->purpose === 'staff') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                        @endif">
                                        {{ ucfirst($ticket->purpose) }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                @if($ticket->status === 'active')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 text-xs font-semibold rounded-full">Active</span>
                                @elseif($ticket->status === 'used')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 text-xs font-semibold rounded-full">Used</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 text-xs font-semibold rounded-full">Cancelled</span>
                                @endif
                            </td>
                            <td class="hidden xl:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $ticket->issuedBy->name ?? 'Admin' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-2">
                                    <!-- Toggle Visibility -->
                                    <form method="POST" action="{{ route('admin.complementary-tickets.toggle-visibility', $ticket->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-600 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400" title="{{ $ticket->visible_to_organizer ? 'Hide from organizer' : 'Show to organizer' }}">
                                            @if($ticket->visible_to_organizer)
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Cancel Ticket -->
                                    @if($ticket->status === 'active')
                                        <form method="POST" action="{{ route('admin.complementary-tickets.cancel', $ticket->id) }}" onsubmit="return confirm('Cancel this ticket?')" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Cancel ticket">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                <p class="mt-4 text-sm">No complementary tickets found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($tickets->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
