@extends('layouts.admin')

@section('title', $poll->title . ' - Poll Details')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.polls.index') }}" class="text-gray-400 hover:text-white mb-2 inline-flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Polls
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $poll->title }}</h1>
                <p class="text-gray-400 mt-1">Created by {{ $poll->company->name ?? 'Unknown' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 rounded-full text-sm font-medium
                    @if($poll->status === 'active') bg-green-600/20 text-green-400 border border-green-600/30
                    @elseif($poll->status === 'draft') bg-amber-600/20 text-amber-400 border border-amber-600/30
                    @elseif($poll->status === 'closed') bg-gray-600/20 text-gray-400 border border-gray-600/30
                    @else bg-red-600/20 text-red-400 border border-red-600/30
                    @endif">
                    {{ ucfirst($poll->status) }}
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-600 bg-opacity-20 border border-green-600 rounded-lg">
        <p class="text-green-400 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl p-6">
            <p class="text-indigo-100 text-sm mb-1">Total Votes</p>
            <p class="text-3xl font-bold text-white">{{ number_format($analytics['total_votes']) }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-600 to-emerald-600 rounded-xl p-6">
            <p class="text-green-100 text-sm mb-1">Revenue</p>
            <p class="text-2xl font-bold text-white">GH₵{{ number_format($analytics['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl p-6">
            <p class="text-blue-100 text-sm mb-1">Unique Voters</p>
            <p class="text-3xl font-bold text-white">{{ number_format($analytics['unique_voters']) }}</p>
        </div>
        <div class="bg-gradient-to-br from-pink-600 to-rose-600 rounded-xl p-6">
            <p class="text-pink-100 text-sm mb-1">Contestants</p>
            <p class="text-3xl font-bold text-white">{{ $analytics['contestants_count'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl p-6 border border-gray-700">
            <p class="text-gray-400 text-sm mb-1">Page Views</p>
            <p class="text-3xl font-bold text-white">{{ number_format($analytics['views_count']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Poll Details -->
        <div class="lg:col-span-1">
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 mb-6">
                <h3 class="text-lg font-bold text-white mb-4">Poll Details</h3>

                @if($poll->banner_image)
                <img src="{{ Storage::url($poll->banner_image) }}" alt="{{ $poll->title }}" class="w-full h-48 object-cover rounded-lg mb-4">
                @endif

                <div class="space-y-4">
                    <div>
                        <p class="text-gray-400 text-sm">Type</p>
                        <p class="text-white font-medium">{{ ucfirst($poll->poll_type) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Voting Type</p>
                        <p class="text-white font-medium">{{ ucfirst($poll->voting_type) }}
                            @if($poll->voting_type === 'paid')
                            <span class="text-green-400">(GH₵{{ number_format($poll->vote_price, 2) }}/vote)</span>
                            @endif
                        </p>
                    </div>
                    @if($poll->event)
                    <div>
                        <p class="text-gray-400 text-sm">Linked Event</p>
                        <p class="text-white font-medium">{{ $poll->event->title }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-gray-400 text-sm">Duration</p>
                        <p class="text-white font-medium">
                            @if($poll->start_date && $poll->end_date)
                            {{ $poll->start_date->format('M d, Y') }} - {{ $poll->end_date->format('M d, Y') }}
                            @else
                            No date limit
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Created</p>
                        <p class="text-white font-medium">{{ $poll->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-bold text-white mb-4">Actions</h3>
                <div class="space-y-3">
                    @if($poll->status === 'draft')
                    <form action="{{ route('admin.polls.approve', $poll) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Approve & Activate
                        </button>
                    </form>
                    @elseif($poll->status === 'active')
                    <form action="{{ route('admin.polls.suspend', $poll) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition">
                            Suspend Poll
                        </button>
                    </form>
                    @elseif($poll->status === 'suspended')
                    <form action="{{ route('admin.polls.reactivate', $poll) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Reactivate Poll
                        </button>
                    </form>
                    @endif

                    <form action="{{ route('admin.polls.destroy', $poll) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this poll? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Delete Poll
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contestants -->
        <div class="lg:col-span-2">
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Contestants Leaderboard</h3>
                    @if($analytics['leader'])
                    <span class="px-3 py-1 bg-gradient-to-r from-amber-500 to-yellow-500 text-white text-sm font-medium rounded-full">
                        Leader: {{ $analytics['leader']->name }}
                    </span>
                    @endif
                </div>

                @if($poll->contestants->count() > 0)
                <div class="space-y-4">
                    @foreach($poll->contestants as $index => $contestant)
                    <div class="flex items-center p-4 bg-gray-900 rounded-xl {{ $index === 0 ? 'ring-2 ring-amber-500' : '' }}">
                        <div class="flex-shrink-0 mr-4">
                            <div class="relative">
                                @if($contestant->photo)
                                <img src="{{ Storage::url($contestant->photo) }}" alt="{{ $contestant->name }}" class="w-16 h-16 object-cover rounded-full">
                                @else
                                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                    {{ strtoupper(substr($contestant->name, 0, 1)) }}
                                </div>
                                @endif
                                @if($index === 0)
                                <div class="absolute -top-1 -right-1 w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400 text-sm font-mono">#{{ $contestant->contestant_number }}</span>
                                <h4 class="text-white font-semibold truncate">{{ $contestant->name }}</h4>
                            </div>
                            <div class="mt-2">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-gray-700 rounded-full overflow-hidden">
                                        @php
                                            $maxVotes = $poll->contestants->max('votes_count') ?: 1;
                                            $percentage = ($contestant->votes_count / $maxVotes) * 100;
                                        @endphp
                                        <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-gray-400 text-sm w-16 text-right">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </div>
                        </div>

                        <div class="ml-4 text-right">
                            <p class="text-2xl font-bold text-white">{{ number_format($contestant->votes_count) }}</p>
                            <p class="text-gray-400 text-xs">votes</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h4 class="text-gray-400 font-medium">No contestants yet</h4>
                    <p class="text-gray-500 text-sm">The organizer hasn't added any contestants</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
