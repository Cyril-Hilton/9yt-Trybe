@extends('layouts.app')

@section('title', 'Polls & Voting - 9yt !Trybe')
@section('meta_description', 'Browse active polls, voting campaigns, and pageants on 9yt !Trybe.')

@section('content')
<div class="bg-gradient-to-br from-purple-700 via-indigo-700 to-slate-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Polls & Voting</h1>
        <p class="text-lg text-purple-100">Discover live polls and community votes.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($polls->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($polls as $poll)
                <a href="{{ route('polls.show', $poll->slug) }}" class="group">
                    <div class="glass-card rounded-2xl overflow-hidden hover-lift transition-all duration-300 h-full">
                        <img src="{{ $poll->banner_url }}" alt="{{ $poll->title }}" class="w-full h-40 object-cover">
                        <div class="p-5">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 group-hover:text-purple-600 transition-colors line-clamp-2">
                                {{ $poll->title }}
                            </h3>
                            @if($poll->company)
                                <p class="text-sm text-gray-600 dark:text-gray-400">By {{ $poll->company->name }}</p>
                            @endif
                            <div class="mt-3 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $poll->contestants_count }} contestants</span>
                                <span>{{ number_format($poll->total_votes ?? 0) }} votes</span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $polls->links() }}
        </div>
    @else
        <div class="glass-card rounded-2xl p-8 text-center">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No active polls yet</h3>
            <p class="text-gray-600 dark:text-gray-400">Check back soon for new voting campaigns.</p>
        </div>
    @endif
</div>
@endsection
