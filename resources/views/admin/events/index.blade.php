@extends('layouts.admin')

@section('title', 'Event Management')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Event Management</h1>
                <p class="text-gray-400">Review and approve events from organizations</p>
            </div>
            <a href="{{ route('admin.events.create') }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create External Event
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700">
            <p class="text-gray-400 text-sm mb-1">Total Events</p>
            <p class="text-3xl font-bold text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-xl p-6">
            <p class="text-yellow-100 text-sm mb-1">Pending Approval</p>
            <p class="text-3xl font-bold text-white">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6">
            <p class="text-green-100 text-sm mb-1">Approved</p>
            <p class="text-3xl font-bold text-white">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-xl p-6">
            <p class="text-red-100 text-sm mb-1">Rejected</p>
            <p class="text-3xl font-bold text-white">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-xl p-4 mb-6 border border-gray-700">
        <form action="{{ route('admin.events.index') }}" method="GET" class="flex flex-wrap gap-4">
            <select name="status" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events..."
                   class="flex-1 px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Filter
            </button>

            @if(request()->hasAny(['status', 'search']))
            <a href="{{ route('admin.events.index') }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Events Table -->
    <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700">
        <table class="min-w-full divide-y divide-gray-700 responsive-table">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Organization</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Stats</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($events as $event)
                <tr class="hover:bg-gray-750 transition">
                    <td class="px-6 py-4" data-label="Event">
                        <div class="text-sm font-medium text-white">{{ Str::limit($event->title, 50) }}</div>
                        <div class="text-xs text-gray-400">{{ $event->location_type === 'venue' ? $event->venue_name : ucfirst($event->location_type) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap" data-label="Organization">
                        @if($event->company)
                            <div class="text-sm text-white">{{ $event->company->name }}</div>
                            <div class="text-xs text-gray-400">{{ $event->company->email }}</div>
                        @else
                            <div class="text-sm text-white">External Event</div>
                            <div class="text-xs text-gray-400">No organizer assigned</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300" data-label="Date">
                        {{ $event->start_date->format('M j, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap" data-label="Status">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($event->status === 'approved') bg-green-900 text-green-200
                            @elseif($event->status === 'pending') bg-yellow-900 text-yellow-200
                            @elseif($event->status === 'rejected') bg-red-900 text-red-200
                            @else bg-gray-700 text-gray-300
                            @endif">
                            {{ ucfirst($event->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400" data-label="Stats">
                        {{ $event->views_count }} views • {{ $event->tickets_sold }} sold
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" data-label="Actions">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.events.show', $event) }}"
                               class="text-indigo-400 hover:text-indigo-300 transition"
                               title="View Event">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            <a href="{{ route('admin.events.edit', $event) }}"
                               class="text-blue-400 hover:text-blue-300 transition"
                               title="Edit Event">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            @if($event->isPending())
                            <form action="{{ route('admin.events.approve', $event) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-400 hover:text-green-300 transition" title="Approve Event">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            </form>
                            @endif

                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 transition" title="Delete Event">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-gray-400">No events found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($events->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
