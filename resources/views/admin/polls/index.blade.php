@extends('layouts.admin')

@section('title', 'Polls & Voting Management')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Polls & Voting</h1>
                <p class="text-gray-400 text-sm sm:text-base">Manage all polls, pageants, and voting campaigns</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.polls.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-semibold">
                    Create Poll
                </a>
                <a href="{{ route('admin.surveys.create') }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition text-sm font-semibold">
                    Create Survey/Form
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-600 bg-opacity-20 border border-green-600 rounded-lg">
        <p class="text-green-400 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700">
            <p class="text-gray-400 text-sm mb-1">Total Polls</p>
            <p class="text-3xl font-bold text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6">
            <p class="text-green-100 text-sm mb-1">Active</p>
            <p class="text-3xl font-bold text-white">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-amber-600 to-amber-700 rounded-xl p-6">
            <p class="text-amber-100 text-sm mb-1">Draft</p>
            <p class="text-3xl font-bold text-white">{{ $stats['draft'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-gray-600 to-gray-700 rounded-xl p-6">
            <p class="text-gray-100 text-sm mb-1">Closed</p>
            <p class="text-3xl font-bold text-white">{{ $stats['closed'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl p-6">
            <p class="text-indigo-100 text-sm mb-1">Total Revenue</p>
            <p class="text-2xl font-bold text-white">GH₵{{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-xl p-4 mb-6 border border-gray-700">
        <form action="{{ route('admin.polls.index') }}" method="GET" class="flex flex-wrap gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search polls..."
                   class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none flex-1 min-w-[200px]">

            <select name="status" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>

            <select name="type" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>All Types</option>
                <option value="pageant" {{ request('type') === 'pageant' ? 'selected' : '' }}>Pageant</option>
                <option value="voting" {{ request('type') === 'voting' ? 'selected' : '' }}>Voting</option>
                <option value="survey" {{ request('type') === 'survey' ? 'selected' : '' }}>Survey</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status', 'type']))
            <a href="{{ route('admin.polls.index') }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Polls Table -->
    <div class="bg-gray-800 rounded-xl overflow-x-auto border border-gray-700">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Poll</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Organizer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Contestants</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Votes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Revenue</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($polls as $poll)
                <tr class="hover:bg-gray-750 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($poll->banner_image)
                            <img src="{{ Storage::url($poll->banner_image) }}" alt="{{ $poll->title }}" class="w-12 h-12 object-cover rounded-lg mr-3">
                            @else
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg mr-3 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-white">{{ Str::limit($poll->title, 30) }}</div>
                                <div class="text-xs text-gray-400">{{ $poll->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-white">{{ $poll->company->name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
                            @if($poll->poll_type === 'pageant') bg-pink-600/20 text-pink-400 border border-pink-600/30
                            @elseif($poll->poll_type === 'voting') bg-blue-600/20 text-blue-400 border border-blue-600/30
                            @else bg-purple-600/20 text-purple-400 border border-purple-600/30
                            @endif">
                            {{ ucfirst($poll->poll_type) }}
                        </span>
                        <span class="ml-1 px-2 py-0.5 rounded text-xs {{ $poll->voting_type === 'paid' ? 'bg-green-600/20 text-green-400' : 'bg-gray-600/20 text-gray-400' }}">
                            {{ ucfirst($poll->voting_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
                            @if($poll->status === 'active') bg-green-600/20 text-green-400 border border-green-600/30
                            @elseif($poll->status === 'draft') bg-amber-600/20 text-amber-400 border border-amber-600/30
                            @elseif($poll->status === 'closed') bg-gray-600/20 text-gray-400 border border-gray-600/30
                            @else bg-red-600/20 text-red-400 border border-red-600/30
                            @endif">
                            {{ ucfirst($poll->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-white">
                        {{ $poll->contestants_count }}
                    </td>
                    <td class="px-6 py-4 text-sm text-white">
                        {{ number_format($poll->total_votes ?? 0) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-white font-medium">
                        GH₵{{ number_format($poll->total_revenue ?? 0, 2) }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.polls.show', $poll) }}" class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 transition">
                                View
                            </a>
                            @if($poll->status === 'draft')
                            <form action="{{ route('admin.polls.approve', $poll) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition">
                                    Approve
                                </button>
                            </form>
                            @elseif($poll->status === 'active')
                            <form action="{{ route('admin.polls.suspend', $poll) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-amber-600 text-white text-xs rounded hover:bg-amber-700 transition">
                                    Suspend
                                </button>
                            </form>
                            @elseif($poll->status === 'suspended')
                            <form action="{{ route('admin.polls.reactivate', $poll) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition">
                                    Reactivate
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.polls.destroy', $poll) }}" method="POST" onsubmit="return confirm('Delete this poll?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-20 text-center">
                        <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-400 mb-2">No polls found</h3>
                        <p class="text-gray-500">Polls created by organizers will appear here</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($polls->hasPages())
    <div class="mt-8">
        {{ $polls->links() }}
    </div>
    @endif
</div>
@endsection
