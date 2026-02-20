@extends('layouts.app')

@php
    $metaTitle = $metaOverrides['meta_title'] ?? ($event->meta_title ?: ($event->title . ' - Book Tickets'));
    $metaDescription = $metaOverrides['meta_description'] ?? ($event->meta_description ?: Str::limit(strip_tags($event->summary ?? $event->overview ?? ('Book tickets for ' . $event->title)), 155));
    $metaKeywords = !empty($event->ai_tags)
        ? implode(', ', $event->ai_tags)
        : 'event, ' . $event->title . ', tickets, ' . ($event->region ?? 'Ghana') . ', ' . ($event->venue_name ?? 'online event');
    $shareImage = $event->flier_url;
@endphp

@section('title', $metaTitle . ' | 9yt !Trybe')

@section('meta_title', $metaTitle)
@section('meta_description', $metaDescription)
@section('meta_keywords', $metaKeywords)

@section('og_type', 'event')
@section('og_title', $metaTitle)
@section('og_description', Str::limit(strip_tags($metaDescription), 200))
@section('og_image', $shareImage)

@section('twitter_title', $metaTitle)
@section('twitter_description', Str::limit(strip_tags($metaDescription), 200))
@section('twitter_image', $shareImage)

@push('head')
<!-- Schema.org Structured Data for SEO -->
<script type="application/ld+json">
@php
$schemaData = [
    '@context' => 'https://schema.org',
    '@type' => 'Event',
    'name' => $event->title,
    'description' => strip_tags($event->summary ?? $event->overview ?? $event->title),
    'eventStatus' => 'https://schema.org/EventScheduled',
    'eventAttendanceMode' => 'https://schema.org/' . ($event->location_type === 'venue' ? 'OfflineEventAttendanceMode' : 'OnlineEventAttendanceMode'),
    'image' => $event->flier_url,
    'organizer' => [
        '@type' => 'Organization',
        'name' => $event->company->name ?? '9yt !Trybe',
        'url' => $event->company ? url('/organizers/' . $event->company->slug) : url('/'),
    ],
    'performer' => [
        '@type' => 'Organization',
        'name' => $event->company->name ?? '9yt !Trybe',
        'url' => $event->company ? url('/organizers/' . $event->company->slug) : url('/'),
    ],
];

if ($event->start_date) {
    $schemaData['startDate'] = $event->start_date->toIso8601String();
}

if ($event->end_date) {
    $schemaData['endDate'] = $event->end_date->toIso8601String();
} elseif ($event->start_date) {
    $schemaData['endDate'] = $event->start_date->toIso8601String();
}

if ($event->location_type === 'venue' && $event->venue_name) {
    $schemaData['location'] = [
        '@type' => 'Place',
        'name' => $event->venue_name,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $event->venue_address ?? '',
            'addressRegion' => $event->region ?? 'Ghana',
        ],
    ];
} elseif ($event->meeting_link) {
    $schemaData['location'] = [
        '@type' => 'VirtualLocation',
        'url' => $event->meeting_link,
    ];
}

if ($event->tickets->count() > 0) {
    $schemaData['offers'] = $event->tickets->map(function($ticket) use ($event) {
        return [
            '@type' => 'Offer',
            'name' => $ticket->name,
            'price' => $ticket->price,
            'priceCurrency' => 'GHS',
            'availability' => $ticket->quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut',
            'validFrom' => $event->created_at->toIso8601String(),
            'url' => url('/events/' . $event->slug),
        ];
    })->toArray();
}

echo json_encode($schemaData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
@endphp
</script>
@php
    $faqItems = [];
    if ($event->faqs->count() > 0) {
        foreach ($event->faqs as $faq) {
            $faqItems[] = [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq->answer,
                ],
            ];
        }
    } elseif (!empty($event->ai_faqs)) {
        foreach ($event->ai_faqs as $faq) {
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

@section('styles')
<style>
    [x-cloak] { display: none !important; }
    .banner-container {
        height: 280px;
        max-width: 900px;
        margin: 0 auto;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    }
    @media (min-width: 640px) {
        .banner-container { height: 350px; }
    }
    @media (min-width: 768px) {
        .banner-container { height: 420px; }
    }
    @media (min-width: 1024px) {
        .banner-container { height: 500px; }
    }
    .banner-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
    }
</style>
@endsection

@section('content')
<div x-data="{
    showTicketModal: false,
    selectedTickets: {
        @foreach($event->tickets as $ticket)
        {{ $ticket->id }}: 0,
        @endforeach
    },
    totalAmount: 0,
    checkoutError: '',
    showBookingPicker: false,
    openBookingPicker() {
        this.showBookingPicker = true;
        document.body.classList.add('overflow-hidden');
    },
    closeBookingPicker() {
        this.showBookingPicker = false;
        document.body.classList.remove('overflow-hidden');
    },
    getRideLink(app) {
        const lat = {{ $event->venue_latitude ?? 0 }};
        const lng = {{ $event->venue_longitude ?? 0 }};
        const name = encodeURIComponent('{{ $event->title }} at {{ $event->venue_name }}');
        
        switch(app) {
            case 'uber':
                return `https://m.uber.com/ul/?action=setPickup&pickup=my_location&dropoff[latitude]=${lat}&dropoff[longitude]=${lng}&dropoff[nickname]=${name}`;
            case 'bolt':
                return `https://bolt.eu/ride?destination_lat=${lat}&destination_lng=${lng}`;
            case 'yango':
                return `https://yango.go.link/route?end-lat=${lat}&end-lon=${lng}`;
            default:
                return '#';
        }
    },
    getTotalTickets() {
        return Object.values(this.selectedTickets).reduce((sum, qty) => sum + parseInt(qty || 0), 0);
    },
    validateCheckout() {
        if (this.getTotalTickets() === 0) {
            this.checkoutError = 'Add at least 1 ticket';
            return false;
        }
        this.checkoutError = '';
        return true;
    }
}">
    <!-- Event Banner -->
    <div class="bg-gradient-to-br from-slate-900 via-pink-900 to-indigo-900 dark:bg-gray-950 relative overflow-hidden">
        <div class="banner-container relative">
            @if($event->banner_image)
            <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="banner-image">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/40 to-transparent"></div>
            @else
            <div class="banner-image animated-gradient-bg flex items-center justify-center">
                <svg class="w-32 h-32 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            @endif
        </div>
    </div>

    <!-- Event Header -->
    <div class="bg-gradient-to-br from-gray-50 via-slate-50/30 to-pink-50/30 dark:from-gray-800 dark:via-slate-900/10 dark:to-slate-800/10 border-b-2 border-cyan-200 dark:border-cyan-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <h1 class="text-4xl md:text-5xl font-extrabold gradient-text neon-glow mb-4">{{ $event->title }}</h1>
                    @if($event->company)
                    <div class="flex items-center text-gray-600 dark:text-gray-400 mb-4">
                        @if($event->company->logo)
                        <img src="{{ $event->company->logo_url }}" alt="{{ $event->company->name }}" class="w-12 h-12 rounded-full mr-3">
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $event->company->name }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Social Actions -->
                    <div class="flex flex-wrap items-center gap-3">
                        <form action="{{ route('events.like', $event->slug) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 px-4 py-2 {{ $userLiked ? 'bg-gradient-to-r from-red-500 to-cyan-500 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-2 border-cyan-300 dark:border-cyan-700' }} rounded-xl font-semibold hover-lift transition-all shadow-md">
                                <svg class="w-5 h-5 {{ $userLiked ? 'fill-current' : '' }}" fill="{{ $userLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                Like
                            </button>
                        </form>

                        <form action="{{ route('events.follow-organization', $event) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 {{ $userFollowing ? 'bg-gradient-to-r from-cyan-600 to-cyan-500 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-2 border-cyan-300 dark:border-cyan-700' }} rounded-xl font-semibold hover-lift transition-all shadow-md">
                                {{ $userFollowing ? '✓ Following' : '+ Follow' }}
                            </button>
                        </form>

                        <button onclick="shareEvent()" class="px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-2 border-cyan-300 dark:border-cyan-700 rounded-xl font-semibold hover-lift transition-all shadow-md">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            Share
                        </button>
                    </div>
                </div>

                <!-- Get Tickets Button -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-cyan-300 dark:border-cyan-700 p-6 sticky top-24 transition-colors duration-300 card-glow shadow-xl">
                        @if($event->is_external)
                            <!-- External Event Badge -->
                            <div class="mb-4 px-3 py-2 bg-blue-100 dark:bg-blue-900/30 border border-blue-300 dark:border-blue-700 rounded-lg text-center">
                                <span class="text-sm font-semibold text-blue-700 dark:text-blue-300 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    External Event
                                </span>
                            </div>

                            <div class="space-y-3">
                                <!-- External Ticket URL Button -->
                                @if($event->external_ticket_url)
                                <a href="{{ $event->external_ticket_url }}" target="_blank" rel="noopener noreferrer" class="block w-full py-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 text-white rounded-xl font-bold text-lg transition-all shadow-lg hover-lift pulse-button">
                                    <div class="flex items-center justify-center gap-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        Buy Tickets Online
                                    </div>
                                </a>
                                @endif

                                <!-- USSD Code -->
                                @if($event->external_ussd_code)
                                <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-300 dark:border-green-700 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">Dial USSD Code</p>
                                            <p class="text-xl font-bold text-green-700 dark:text-green-300">{{ $event->external_ussd_code }}</p>
                                        </div>
                                        <button onclick="navigator.clipboard.writeText('{{ $event->external_ussd_code }}')" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                                            Copy
                                        </button>
                                    </div>
                                </div>
                                @endif

                                <!-- Reservation Phone -->
                                @if($event->external_reservation_phone)
                                <div class="p-4 bg-gradient-to-r from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 border-2 border-cyan-300 dark:border-cyan-700 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">Call for Reservations</p>
                                            <p class="text-xl font-bold text-cyan-700 dark:text-cyan-300">{{ $event->external_reservation_phone }}</p>
                                        </div>
                                        <a href="tel:{{ $event->external_reservation_phone }}" class="px-3 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            Call
                                        </a>
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if($event->external_description)
                            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $event->external_description }}</p>
                            </div>
                            @endif
                        @else
                            @if($event->hasFreeTickets())
                            <div class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-green-500 to-emerald-600 mb-2 neon-glow">FREE</div>
                            @elseif($event->cheapest_ticket_price > 0)
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1 font-medium">Starting from</div>
                            <div class="text-4xl font-extrabold gradient-text mb-2 neon-glow">GH₵{{ number_format($event->cheapest_ticket_price, 2) }}</div>
                            @endif

                            @if(!$event->isSoldOut())
                            <button @click="showTicketModal = true" class="w-full py-4 bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 hover:from-cyan-700 hover:via-cyan-600 hover:to-indigo-700 text-white rounded-xl font-bold text-lg transition-all shadow-lg hover-lift pulse-button flex items-center justify-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                Get Tickets
                            </button>
                            @else
                            <button disabled class="w-full py-4 bg-gray-300 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-xl font-bold text-lg cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Sold Out
                            </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Details -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Date & Time -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-cyan-200 dark:border-cyan-900 p-6 mb-6 transition-colors duration-300 hover-lift">
                    <h2 class="text-2xl font-bold gradient-text mb-4">📅 Date and Time</h2>
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400 mr-4 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $event->formatted_date }}</p>
                            <p class="text-gray-600 dark:text-gray-400">{{ $event->formatted_time }}</p>
                            @if($event->door_time)
                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Doors open at {{ \Carbon\Carbon::parse($event->door_time)->format('g:i A') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-cyan-200 dark:border-cyan-900 p-6 mb-6 transition-colors duration-300 hover-lift">
                    <h2 class="text-2xl font-bold gradient-text mb-4">📍 Location</h2>
                    <div class="flex items-start">
                        @if($event->location_type === 'venue')
                        <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400 mr-4 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $event->venue_name }}</p>
                            @if($event->venue_address)
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $event->venue_address }}</p>

                            <!-- Book a Ride Action -->
                            <div class="mb-6">
                                <button @click="openBookingPicker()" 
                                        class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white rounded-xl font-bold shadow-lg hover-lift transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Book a Ride to Venue
                                </button>
                            </div>

                            @if($event->venue_latitude && $event->venue_longitude)
                            <div class="mt-4 h-64 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden border border-cyan-200 dark:border-cyan-800">
                                <div id="show_map" class="w-full h-full"></div>
                                @if(config('services.maps.provider', 'osm') === 'google' && config('services.google.maps_api_key'))
                                <script>
                                    function initShowMap() {
                                        const loc = { lat: {{ $event->venue_latitude }}, lng: {{ $event->venue_longitude }} };
                                        const map = new google.maps.Map(document.getElementById('show_map'), {
                                            zoom: 15,
                                            center: loc,
                                        });
                                        new google.maps.Marker({
                                            position: loc,
                                            map: map,
                                        });
                                    }

                                    function initShowMapFallback() {
                                        const lat = {{ $event->venue_latitude }};
                                        const lng = {{ $event->venue_longitude }};
                                        const map = L.map('show_map').setView([lat, lng], 15);
                                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                            attribution: '&copy; OpenStreetMap contributors'
                                        }).addTo(map);
                                        L.marker([lat, lng]).addTo(map);
                                    }
                                </script>
                                <script>
                                    (function() {
                                        const script = document.createElement('script');
                                        script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initShowMap';
                                        script.async = true;
                                        script.defer = true;
                                        script.onerror = initShowMapFallback;
                                        document.head.appendChild(script);
                                    })();
                                </script>
                                @else
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const lat = {{ $event->venue_latitude }};
                                        const lng = {{ $event->venue_longitude }};
                                        const map = L.map('show_map').setView([lat, lng], 15);
                                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                            attribution: '&copy; OpenStreetMap contributors'
                                        }).addTo(map);
                                        L.marker([lat, lng]).addTo(map);
                                    });
                                </script>
                                @endif
                            </div>
                            @endif
                            @endif
                        </div>
                        @elseif($event->location_type === 'online')
                        <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400 mr-4 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                        <div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">Online Event</p>
                            <p class="text-gray-600 dark:text-gray-400">Link will be provided after registration</p>
                            @if($event->online_platform)
                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Platform: {{ ucfirst($event->online_platform) }}</p>
                            @endif
                        </div>
                        @else
                        <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400 mr-4 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">To Be Announced</p>
                            <p class="text-gray-600 dark:text-gray-400">Location details will be shared soon</p>
                        </div>
                        @endif
                    </div>

                    @if($event->parking_info)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="font-semibold text-gray-900 dark:text-white mb-2">Parking Information</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ $event->parking_info }}</p>
                    </div>
                    @endif
                </div>

                <!-- About -->
                @if($event->overview || $event->summary)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-cyan-200 dark:border-cyan-900 p-6 mb-6 transition-colors duration-300 hover-lift">
                    <h2 class="text-2xl font-bold gradient-text mb-4">ℹ️ About This Event</h2>
                    @if($event->summary)
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">{{ $event->summary }}</p>
                    @endif
                    @if($event->overview)
                    <div class="prose max-w-none text-gray-700 dark:text-gray-300">
                        {!! nl2br(e($event->overview)) !!}
                    </div>
                    @endif
                </div>
                @endif

                <!-- Images -->
                @if($event->images->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-cyan-200 dark:border-cyan-900 p-6 mb-6 transition-colors duration-300 hover-lift">
                    <h2 class="text-2xl font-bold gradient-text mb-4">📸 Event Photos</h2>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($event->images as $image)
                        <img src="{{ $image->image_url }}" alt="{{ $image->caption }}" class="w-full h-48 object-cover rounded-lg">
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Videos -->
                @if($event->videos->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-cyan-200 dark:border-cyan-900 p-6 mb-6 transition-colors duration-300 hover-lift">
                    <h2 class="text-2xl font-bold gradient-text mb-4">🎥 Videos</h2>
                    <div class="space-y-4">
                        @foreach($event->videos as $video)
                        <div class="relative" style="padding-bottom: 56.25%;">
                            <iframe
                                src="{{ $video->embed_url }}"
                                class="absolute top-0 left-0 w-full h-full rounded-lg"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                        @if($video->title)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ $video->title }}</p>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- FAQs -->
                @if($event->faqs->count() > 0 || !empty($event->ai_faqs))
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-cyan-200 dark:border-cyan-900 p-6 mb-6 transition-colors duration-300 hover-lift">
                    <h2 class="text-2xl font-bold gradient-text mb-4">❓ Frequently Asked Questions</h2>
                    <div class="space-y-4">
                        @foreach($event->faqs as $faq)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $faq->question }}</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $faq->answer }}</p>
                        </div>
                        @endforeach
                        @if($event->faqs->count() === 0 && !empty($event->ai_faqs))
                            @foreach($event->ai_faqs as $faq)
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $faq['question'] ?? '' }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">{{ $faq['answer'] ?? '' }}</p>
                            </div>
                            @endforeach
                        @endif

                    </div>
                </div>
                @endif

                <!-- Good to Know -->
                @if($event->age_restriction)
                <div class="bg-yellow-50 dark:bg-yellow-900 dark:bg-opacity-20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-6 transition-colors duration-300">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Good to Know</h3>
                    <p class="text-gray-700 dark:text-gray-300"><strong>Age Restriction:</strong> {{ $event->age_restriction }}</p>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Organizer Info -->
                @if($event->company)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-cyan-200 dark:border-cyan-900 p-6 mb-6 transition-colors duration-300 hover-lift">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Organized by</h3>
                    <div class="flex items-center mb-4">
                        @if($event->company->logo)
                        <img src="{{ $event->company->logo_url }}" alt="{{ $event->company->name }}" class="w-16 h-16 rounded-full mr-4">
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $event->company->name }}</p>
                        </div>
                    </div>
                    @if($event->company->description)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($event->company->description, 150) }}</p>
                    @endif
                    <form action="{{ route('events.follow-organization', $event) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 {{ $userFollowing ? 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : 'bg-indigo-600 text-white' }} rounded-lg hover:bg-indigo-700 transition font-medium">
                            {{ $userFollowing ? 'Following' : 'Follow' }}
                        </button>
                    </form>
                </div>
                @endif

                <!-- Related Events -->
                @if($relatedEvents->count() > 0 && $event->company)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-colors duration-300">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">More from {{ $event->company->name }}</h3>
                    <div class="space-y-4">
                        @foreach($relatedEvents as $related)
                        <a href="{{ route('events.show', $related->slug) }}" class="block group">
                            <div class="flex">
                                <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($related->banner_image)
                                    <img src="{{ $related->banner_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="font-semibold text-gray-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 line-clamp-2">{{ $related->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $related->start_date->format('M j, Y') }}</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Ticket Modal -->
    <div x-show="showTicketModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="showTicketModal = false">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showTicketModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75"
                 @click="showTicketModal = false">
            </div>

            <div x-show="showTicketModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form action="{{ route('events.checkout', $event->slug) }}" method="GET">
                    <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4 transition-colors duration-300">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Select Tickets</h3>
                            <button type="button" @click="showTicketModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            @foreach($event->tickets as $ticket)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 transition-colors duration-300 {{ !$ticket->isAvailable() ? 'bg-gray-50 dark:bg-gray-900 opacity-60' : '' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <h4 class="font-semibold {{ $ticket->isAvailable() ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-500' }}">{{ $ticket->name }}</h4>
                                        @if($ticket->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $ticket->description }}</p>
                                        @endif
                                        <p class="text-lg font-bold {{ $ticket->isAvailable() ? 'text-cyan-600 dark:text-cyan-400' : 'text-gray-500' }} mt-2">{{ $ticket->formatted_price }}</p>
                                    </div>
                                    <div class="flex items-center ml-4">
                                        @if($ticket->isAvailable())
                                        <button type="button"
                                                class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-l-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                                @click="selectedTickets[{{ $ticket->id }}] = Math.max(0, (selectedTickets[{{ $ticket->id }}] || 0) - 1)">
                                            -
                                        </button>
                                        <input type="number"
                                               name="tickets[{{ $ticket->id }}]"
                                               x-model="selectedTickets[{{ $ticket->id }}]"
                                               min="0"
                                               max="{{ min($ticket->remaining, $ticket->max_per_order) }}"
                                               class="w-16 px-2 py-1 border-t border-b border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-center focus:outline-none">
                                        <button type="button"
                                                class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-r-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                                @click="selectedTickets[{{ $ticket->id }}] = Math.min({{ min($ticket->remaining, $ticket->max_per_order) }}, (selectedTickets[{{ $ticket->id }}] || 0) + 1)">
                                            +
                                        </button>
                                        @else
                                        <div class="text-center">
                                            <span class="text-sm font-semibold text-red-600 dark:text-red-400">Sold Out</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 transition-colors duration-300">
                        @auth
                        <button type="submit"
                                @click.prevent="if (validateCheckout()) { $el.closest('form').submit(); }"
                                class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg font-bold hover:from-indigo-700 hover:to-blue-700 transition">
                            Proceed to Checkout
                        </button>
                        <p x-show="checkoutError"
                           x-text="checkoutError"
                           class="text-red-600 dark:text-red-400 text-sm font-medium text-center mt-2"></p>
                        @else
                        <!-- Login Required Notice for Guests -->
                        <div class="text-center">
                            <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-lg p-4 mb-4">
                                <div class="flex items-center justify-center gap-2 text-amber-700 dark:text-amber-300 mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <span class="font-semibold">Account Required</span>
                                </div>
                                <p class="text-sm text-amber-600 dark:text-amber-400">Please login or create an account to purchase tickets</p>
                            </div>
                            <div class="flex gap-3">
                                <a href="{{ route('user.login', ['redirect' => url()->current()]) }}"
                                   class="flex-1 px-4 py-3 border-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400 rounded-lg font-bold hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition text-center">
                                    Log In
                                </a>
                                <a href="{{ route('user.register', ['redirect' => url()->current()]) }}"
                                   class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg font-bold hover:from-indigo-700 hover:to-blue-700 transition text-center">
                                    Sign Up
                                </a>
                            </div>
                        </div>
                        @endauth
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Booking Ride Picker Modal -->
    <div x-show="showBookingPicker" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-cloak>
        <div @click.away="closeBookingPicker()" 
             class="glass-modal w-full max-w-sm overflow-hidden animate-in fade-in zoom-in duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Book a Ride</h3>
                    <button @click="closeBookingPicker()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Destination:</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $event->title }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 line-clamp-1">{{ $event->venue_name }}</p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <!-- Uber -->
                    <button @click.prevent="let url = getRideLink('uber'); if(url !== '#') { window.open(url, '_blank'); } setTimeout(() => closeBookingPicker(), 300);"
                       class="flex items-center justify-between p-4 rounded-2xl bg-black text-white hover:scale-[1.02] transition-transform shadow-lg cursor-pointer w-full text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 flex items-center justify-center p-0 overflow-hidden rounded-lg">
                                <img src="{{ asset('assets/images/rides/uber.png') }}" alt="Uber" class="w-full h-full object-cover">
                            </span>
                            <span class="font-bold text-white">Uber</span>
                        </div>
                        <svg class="w-5 h-5 opacity-50 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Bolt -->
                    <button @click.prevent="let url = getRideLink('bolt'); if(url !== '#') { window.open(url, '_blank'); } setTimeout(() => closeBookingPicker(), 300);"
                       class="flex items-center justify-between p-4 rounded-2xl bg-[#34d186] text-white hover:scale-[1.02] transition-transform shadow-lg cursor-pointer w-full text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 flex items-center justify-center p-0 overflow-hidden rounded-lg">
                                <img src="{{ asset('assets/images/rides/bolt.png') }}" alt="Bolt" class="w-full h-full object-cover">
                            </span>
                            <span class="font-bold text-white">Bolt</span>
                        </div>
                        <svg class="w-5 h-5 opacity-50 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Yango -->
                    <button @click.prevent="let url = getRideLink('yango'); if(url !== '#') { window.open(url, '_blank'); } setTimeout(() => closeBookingPicker(), 300);"
                       class="flex items-center justify-between p-4 rounded-2xl bg-[#ff0000] text-white hover:scale-[1.02] transition-transform shadow-lg cursor-pointer w-full text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 flex items-center justify-center p-0 overflow-hidden rounded-lg">
                                <img src="{{ asset('assets/images/rides/yango.png') }}" alt="Yango" class="w-full h-full object-cover">
                            </span>
                            <span class="font-bold text-white">Yango</span>
                        </div>
                        <svg class="w-5 h-5 opacity-50 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function shareEvent() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $event->title }}',
            text: '{{ $event->summary }}',
            url: window.location.href
        });
    } else {
        const url = window.location.href;
        navigator.clipboard.writeText(url);
        alert('Link copied to clipboard!');
    }
}
</script>
@endsection
