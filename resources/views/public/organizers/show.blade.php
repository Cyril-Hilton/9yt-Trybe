@extends('layouts.app')

@php
    $metaTitle = $metaOverrides['meta_title'] ?? ($organizer->meta_title ?: ($organizer->name . ' - Organizer'));
    $metaDescription = $metaOverrides['meta_description'] ?? ($organizer->meta_description ?: ($organizer->description ? Str::limit($organizer->description, 150) : 'Organizer profile and events on 9yt !Trybe.'));
    $metaKeywords = !empty($organizer->ai_tags) ? implode(', ', $organizer->ai_tags) : $organizer->name . ', event organizer, events, tickets';
    $shareImage = $organizer->logo_url ?: asset('ui/logo/9yt-trybe-logo-light.png');
@endphp

@section('title', $metaTitle)
@section('meta_title', $metaTitle)
@section('meta_description', $metaDescription)
@section('meta_keywords', $metaKeywords)
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)
@section('og_image', $shareImage)
@section('twitter_title', $metaTitle)
@section('twitter_description', $metaDescription)
@section('twitter_image', $shareImage)

@push('head')
@php
    $faqItems = [];
    if (!empty($organizer->ai_faqs)) {
        foreach ($organizer->ai_faqs as $faq) {
            if (!empty($faq['question']) && !empty($faq['answer'])) {
                $faqItems[] = [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['answer'],
                    ],
                ];
            }
        }
    }
@endphp
@if(!empty($faqItems))
    <script type="application/ld+json">
    {!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faqItems], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif
@endpush

@section('content')
<div class="bg-gradient-to-br from-slate-900 via-blue-900 to-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6 items-center md:items-start">
        @if($organizer->logo_url)
            <img src="{{ $organizer->logo_url }}" alt="{{ $organizer->name }}" class="w-28 h-28 rounded-2xl object-cover border border-white/20">
        @else
            <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-blue-400 to-cyan-600 flex items-center justify-center text-white font-bold text-3xl">
                {{ strtoupper(substr($organizer->name, 0, 1)) }}
            </div>
        @endif
        <div class="flex-1 text-center md:text-left">
            <h1 class="text-3xl md:text-4xl font-extrabold mb-2">{{ $organizer->name }}</h1>
            @if($organizer->description)
                <p class="text-blue-100 dark:text-slate-300 max-w-2xl">{{ $organizer->description }}</p>
            @endif
            <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-4 text-sm text-blue-100">
                <span class="px-3 py-1 glass-card text-gray-900 dark:text-white">
                    {{ $approvedEventsCount }} events
                </span>
                <span class="px-3 py-1 glass-card text-gray-900 dark:text-white">
                    {{ $followersCount }} followers
                </span>
                @if($organizer->website)
                    <a href="{{ $organizer->website }}" class="px-3 py-1 glass-card text-gray-900 dark:text-white hover:text-cyan-600 transition" rel="noopener" target="_blank">
                        Visit website
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    <section>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Upcoming Events</h2>
        @if($upcomingEvents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($upcomingEvents as $event)
                    <a href="{{ route('events.show', $event->slug) }}" class="group">
                        <div class="glass-card rounded-2xl overflow-hidden hover-lift transition-all duration-300 h-full">
                            <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="w-full h-40 object-cover">
                            <div class="p-5">
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 group-hover:text-cyan-600 transition-colors line-clamp-2">
                                    {{ $event->title }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $event->start_date ? $event->start_date->format('M d, Y') : 'TBA' }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="glass-card rounded-2xl p-6 text-center">
                <p class="text-gray-600 dark:text-gray-400">No upcoming events at the moment.</p>
            </div>
        @endif
    </section>

    <section>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Past Events</h2>
        @if($pastEvents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pastEvents as $event)
                    <a href="{{ route('events.show', $event->slug) }}" class="group">
                        <div class="glass-card rounded-2xl overflow-hidden hover-lift transition-all duration-300 h-full">
                            <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="w-full h-40 object-cover">
                            <div class="p-5">
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 group-hover:text-cyan-600 transition-colors line-clamp-2">
                                    {{ $event->title }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $event->start_date ? $event->start_date->format('M d, Y') : 'TBA' }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="glass-card rounded-2xl p-6 text-center">
                <p class="text-gray-600 dark:text-gray-400">No past events yet.</p>
            </div>
        @endif
    </section>

    @if(!empty($organizer->ai_faqs))
        <section>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Organizer FAQs</h2>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                @foreach($organizer->ai_faqs as $faq)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $faq['question'] ?? '' }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $faq['answer'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
