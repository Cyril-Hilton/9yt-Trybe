@extends('layouts.app')

@section('title', $poll->title . ' - Poll')
@section('meta_description', $poll->description ? Str::limit($poll->description, 150) : 'Poll details and contestants on 9yt !Trybe.')

@section('content')
<div class="bg-gradient-to-br from-slate-900 via-purple-900 to-indigo-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
            <img src="{{ $poll->banner_url }}" alt="{{ $poll->title }}" class="w-full md:w-64 h-40 md:h-44 object-cover rounded-2xl border border-white/20">
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-3xl md:text-4xl font-extrabold mb-2">{{ $poll->title }}</h1>
                @if($poll->description)
                    <p class="text-purple-100 max-w-2xl">{{ $poll->description }}</p>
                @endif
                <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-3 text-xs">
                    <span class="px-3 py-1 glass-card text-gray-900 dark:text-white">
                        {{ ucfirst($poll->poll_type) }}
                    </span>
                    <span class="px-3 py-1 glass-card text-gray-900 dark:text-white">
                        {{ ucfirst($poll->voting_type) }} voting
                    </span>
                    <span class="px-3 py-1 glass-card text-gray-900 dark:text-white">
                        {{ number_format($poll->total_votes ?? 0) }} votes
                    </span>
                </div>
                @if($poll->company && $poll->company->slug)
                    <div class="mt-4">
                        <a href="{{ route('organizers.show', $poll->company->slug) }}" class="text-cyan-300 hover:text-cyan-200 underline">
                            View organizer
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
    <section class="glass-card rounded-2xl p-6">
        @if($isActive)
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Voting is open</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Choose your favorite contestant below. If paid voting is enabled, follow the organizer's instructions for payment.
            </p>
        @elseif($isClosed)
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Voting closed</h2>
            <p class="text-gray-600 dark:text-gray-400">This poll is no longer accepting votes.</p>
        @else
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Voting starts soon</h2>
            <p class="text-gray-600 dark:text-gray-400">Check back when the poll becomes active.</p>
        @endif
    </section>

    <section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Contestants</h2>
            @if($leader)
                <span class="text-sm text-purple-600 dark:text-purple-300">Leader: {{ $leader->name }}</span>
            @endif
        </div>

        @if($poll->contestants->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($poll->contestants as $contestant)
                    <div class="glass-card rounded-2xl p-5 hover-lift transition-all duration-300">
                        <div class="flex items-center gap-4">
                            <img src="{{ $contestant->photo_url }}" alt="{{ $contestant->name }}" class="w-16 h-16 rounded-xl object-cover">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $contestant->name }}</h3>
                                @if($contestant->contestant_number)
                                    <p class="text-xs text-gray-500">#{{ $contestant->contestant_number }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                <span>{{ number_format($contestant->total_votes) }} votes</span>
                                <span>{{ number_format($contestant->getVotePercentage(), 1) }}%</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-purple-500 to-indigo-500" style="width: {{ min(100, $contestant->getVotePercentage()) }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="glass-card rounded-2xl p-6 text-center">
                <p class="text-gray-600 dark:text-gray-400">No contestants have been added yet.</p>
            </div>
        @endif
    </section>
</div>
@endsection
