@extends('layouts.company')

@section('title', 'Polls & Voting Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Polls & Voting</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Manage your pageants, voting polls, and surveys</p>
        </div>
        <a href="{{ route('organization.polls.create') }}" class="px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white rounded-xl font-semibold shadow-lg transition-all hover:scale-105">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New Poll
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-xl">
        {{ session('error') }}
    </div>
    @endif

    @if($polls->isEmpty())
    <!-- Empty State -->
    <div class="text-center py-16 glass-card rounded-2xl">
        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-r from-cyan-600 to-blue-600 flex items-center justify-center">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">No Polls Yet</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">Create your first poll to start collecting votes for pageants, contests, or surveys.</p>
        <a href="{{ route('organization.polls.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white rounded-xl font-semibold shadow-lg transition-all hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Your First Poll
        </a>
    </div>
    @else
    <!-- Polls Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($polls as $poll)
        <div class="glass-card rounded-2xl overflow-hidden hover:scale-105 transition-all duration-300 shadow-lg">
            <!-- Poll Banner -->
            <div class="h-48 bg-gradient-to-br from-cyan-500 to-blue-600 relative">
                @if($poll->banner_image)
                <img src="{{ asset('storage/' . $poll->banner_image) }}" alt="{{ $poll->title }}" class="w-full h-full object-cover">
                @endif

                <!-- Status Badge -->
                <div class="absolute top-4 right-4">
                    @if($poll->status === 'active')
                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-lg">
                        🟢 Active
                    </span>
                    @elseif($poll->status === 'draft')
                    <span class="px-3 py-1 bg-gray-500 text-white text-xs font-bold rounded-full shadow-lg">
                        📝 Draft
                    </span>
                    @else
                    <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full shadow-lg">
                        🔒 Closed
                    </span>
                    @endif
                </div>

                <!-- Type Badge -->
                <div class="absolute top-4 left-4">
                    <span class="px-3 py-1 bg-white/90 dark:bg-gray-800/90 text-gray-900 dark:text-white text-xs font-bold rounded-full shadow-lg">
                        {{ ucfirst($poll->poll_type) }}
                    </span>
                </div>
            </div>

            <!-- Poll Info -->
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 truncate">
                    {{ $poll->title }}
                </h3>

                @if($poll->event)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    📅 {{ $poll->event->title }}
                </p>
                @endif

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 mb-6 py-4 border-t border-b border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-cyan-600 dark:text-cyan-400">{{ $poll->contestants_count }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Contestants</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($poll->total_votes) }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Votes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $poll->views_count }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Views</div>
                    </div>
                </div>

                @if($poll->voting_type === 'paid')
                <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</div>
                    <div class="text-lg font-bold text-green-600 dark:text-green-400">
                        GH₵ {{ number_format($poll->total_revenue, 2) }}
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex gap-2">
                    <a href="{{ route('organization.polls.show', $poll) }}" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg text-center hover:bg-gray-200 dark:hover:bg-gray-700 transition text-sm font-semibold">
                        View Details
                    </a>
                    <a href="{{ route('organization.polls.edit', $poll) }}" class="px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $polls->links() }}
    </div>
    @endif
</div>
@endsection
