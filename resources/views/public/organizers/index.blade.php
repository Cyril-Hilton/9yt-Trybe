@extends('layouts.app')

@section('title', 'Organizers - 9yt !Trybe')
@section('meta_description', 'Discover event organizers and browse their upcoming events on 9yt !Trybe.')

@section('content')
<div class="bg-gradient-to-br from-blue-600 via-slate-600 to-gray-700 dark:from-slate-900 dark:via-blue-900 dark:to-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Organizers</h1>
        <p class="text-lg text-blue-100 dark:text-slate-300">Meet the teams creating events on 9yt !Trybe.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($organizers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($organizers as $organizer)
                <a href="{{ route('organizers.show', $organizer->slug) }}" class="group">
                    <div class="glass-card rounded-2xl p-6 hover-lift transition-all duration-300 h-full flex items-center gap-4">
                        @if($organizer->logo_url)
                            <img src="{{ $organizer->logo_url }}" alt="{{ $organizer->name }}" class="w-16 h-16 rounded-xl object-cover">
                        @else
                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-400 to-cyan-600 flex items-center justify-center text-white font-bold text-xl">
                                {{ strtoupper(substr($organizer->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors truncate">
                                {{ $organizer->name }}
                            </h3>
                            @if($organizer->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ Str::limit($organizer->description, 120) }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                {{ $organizer->approved_events_count ?? 0 }} events
                            </p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $organizers->links() }}
        </div>
    @else
        <div class="glass-card rounded-2xl p-8 text-center">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No organizers yet</h3>
            <p class="text-gray-600 dark:text-gray-400">Check back soon for new organizers and events.</p>
        </div>
    @endif
</div>
@endsection
