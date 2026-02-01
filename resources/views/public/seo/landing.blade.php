@extends('layouts.app')

@php
    $metaTitle = $landing['meta_title'] ?? 'Events on 9yt !Trybe';
    $metaDescription = $landing['meta_description'] ?? 'Discover events and book tickets on 9yt !Trybe.';
@endphp

@section('title', $metaTitle)
@section('meta_title', $metaTitle)
@section('meta_description', $metaDescription)
@section('meta_keywords', ($context ?? 'events') . ', events, tickets, 9yt !Trybe')
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)
@section('twitter_title', $metaTitle)
@section('twitter_description', $metaDescription)

@push('head')
@php
    $itemList = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'itemListElement' => $events->values()->map(function ($event, $index) {
            return [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => route('events.show', $event->slug),
                'name' => $event->title,
            ];
        })->toArray(),
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($itemList, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<section class="bg-gradient-to-br from-slate-900 via-blue-900 to-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-xs uppercase tracking-[0.3em] text-cyan-300 font-semibold mb-3">
            {{ ucfirst($type) }} events
        </p>
        <h1 class="text-4xl sm:text-5xl font-extrabold mb-4">
            {{ $landing['headline'] ?? 'Discover Events' }}
        </h1>
        <p class="text-lg sm:text-xl text-cyan-100 max-w-3xl">
            {{ $landing['intro'] ?? 'Find the best experiences curated for you.' }}
        </p>
    </div>
</section>

<section class="bg-white dark:bg-black py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    <a href="{{ route('events.show', $event->slug) }}" class="group">
                        <div class="glass-card rounded-2xl overflow-hidden hover-lift transition-all duration-300 h-full">
                            @if($event->banner_image)
                                <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="w-full h-44 object-cover">
                            @else
                                <div class="w-full h-44 bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="p-5">
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">
                                    {{ $event->title }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">
                                    {{ $event->summary }}
                                </p>
                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-500">
                                    <span>{{ $event->start_date ? $event->start_date->format('M d, Y') : 'TBA' }}</span>
                                    <span>{{ $event->venue_name ?? $event->region ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">No events found</h2>
                <p class="text-gray-600 dark:text-gray-400">Check back soon for new listings.</p>
            </div>
        @endif
    </div>
</section>
@endsection
