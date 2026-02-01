@extends('layouts.app')

@section('title', 'Search Results - ' . ($query ?? ''))
@section('meta_robots', 'noindex, follow')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Search Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Search Results
            </h1>
            @if($query)
                <p class="text-gray-600 dark:text-gray-400">
                    Found <span class="font-semibold text-cyan-600 dark:text-cyan-400">{{ $totalResults }}</span> results for "<span class="font-semibold">{{ $query }}</span>"
                </p>
                @if(!empty($aiSearch) && !empty($aiSearch['corrected_query']) && $aiSearch['corrected_query'] !== $query)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        Showing results for <span class="font-semibold text-cyan-600 dark:text-cyan-400">{{ $aiSearch['corrected_query'] }}</span>
                        <span class="mx-2">•</span>
                        <a href="{{ route('search', ['q' => $query]) }}" class="text-cyan-600 dark:text-cyan-400 hover:underline">Search instead for "{{ $query }}"</a>
                    </p>
                @endif
                @if(!empty($aiSearch) && !empty($aiSearch['intents']))
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        AI intent: {{ implode(', ', $aiSearch['intents']) }}
                    </p>
                @endif
            @else
                <p class="text-gray-600 dark:text-gray-400">
                    Search across events, organizers, blogs, polls, contestants, categories, products and more
                </p>
            @endif
        </div>

        <!-- Search Bar -->
        <div class="mb-8">
            <form action="{{ route('search') }}" method="GET" class="relative"
                  x-data="{ query: '{{ $query }}', suggestions: [], open: false, loading: false, error: '' }"
                  @click.away="open = false">
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        x-model="query"
                        @focus="open = true"
                        @click="open = true"
                        @keydown.escape="open = false"
                        @input.debounce.300ms="
                            error = '';
                            if (!query || query.length < 2) { suggestions = []; return; }
                            loading = true;
                            const controller = new AbortController();
                            const timeoutId = setTimeout(() => controller.abort(), 5000);
                            fetch('/search/quick?q=' + encodeURIComponent(query), { signal: controller.signal })
                                .then(r => r.json())
                                .then(data => { suggestions = data.suggestions || []; })
                                .catch((err) => {
                                    suggestions = [];
                                    error = err && err.name === 'AbortError'
                                        ? 'Search timed out. Please try again.'
                                        : 'Search is temporarily unavailable.';
                                })
                                .finally(() => { clearTimeout(timeoutId); loading = false; });
                        "
                        placeholder="Search anything... events, organizers, polls, products, categories..."
                        class="w-full px-6 py-4 pl-14 rounded-2xl glass-effect text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all"
                        autocomplete="off"
                    >
                    <svg class="absolute left-5 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl transition font-medium">
                        Search
                    </button>
                </div>
                <div x-show="open && suggestions.length" x-cloak
                     class="absolute z-20 mt-2 w-full rounded-2xl glass-card shadow-xl max-h-80 overflow-auto">
                    <template x-for="suggestion in suggestions" :key="suggestion.type + '-' + suggestion.id">
                        <a :href="suggestion.url"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-cyan-50 dark:hover:bg-cyan-900/30 transition">
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="suggestion.title"></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="suggestion.subtitle"></div>
                            </div>
                        </a>
                    </template>
                    <div x-show="loading" class="px-4 py-3 text-sm text-gray-500">Loading...</div>
                </div>
                <div x-show="open && error" x-cloak class="absolute z-20 mt-2 w-full rounded-2xl glass-card shadow-xl">
                    <div class="px-4 py-3 text-sm text-red-600" x-text="error"></div>
                </div>

                <!-- Filter Tabs -->
                <div class="flex items-center gap-2 mt-4 overflow-x-auto pb-2 scrollbar-hide">
                    <a href="{{ route('search', ['q' => $query, 'type' => 'all']) }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium {{ $type === 'all' ? 'bg-cyan-600 text-white' : 'glass-card text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-800/50' }} transition-all whitespace-nowrap">
                        All
                    </a>
                    <a href="{{ route('search', ['q' => $query, 'type' => 'events']) }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium {{ $type === 'events' ? 'bg-cyan-600 text-white' : 'glass-card text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-800/50' }} transition-all whitespace-nowrap flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Events ({{ $events->count() }})
                    </a>
                    <a href="{{ route('search', ['q' => $query, 'type' => 'organizers']) }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium {{ $type === 'organizers' ? 'bg-cyan-600 text-white' : 'glass-card text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-800/50' }} transition-all whitespace-nowrap flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Organizers ({{ $companies->count() }})
                    </a>
                    @if(isset($polls))
                    <a href="{{ route('search', ['q' => $query, 'type' => 'polls']) }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium {{ $type === 'polls' ? 'bg-cyan-600 text-white' : 'glass-card text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-800/50' }} transition-all whitespace-nowrap flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Polls ({{ $polls->count() }})
                    </a>
                    @endif
                    @if(isset($contestants))
                    <a href="{{ route('search', ['q' => $query, 'type' => 'contestants']) }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium {{ $type === 'contestants' ? 'bg-cyan-600 text-white' : 'glass-card text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-800/50' }} transition-all whitespace-nowrap flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Contestants ({{ $contestants->count() }})
                    </a>
                    @endif
                    <a href="{{ route('search', ['q' => $query, 'type' => 'categories']) }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium {{ $type === 'categories' ? 'bg-cyan-600 text-white' : 'glass-card text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-800/50' }} transition-all whitespace-nowrap flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Categories ({{ $categories->count() }})
                    </a>
                    <a href="{{ route('search', ['q' => $query, 'type' => 'products']) }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium {{ $type === 'products' ? 'bg-cyan-600 text-white' : 'glass-card text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-800/50' }} transition-all whitespace-nowrap flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Products ({{ $products->count() }})
                    </a>
                    @if(isset($blogs))
                    <a href="{{ route('search', ['q' => $query, 'type' => 'blogs']) }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium {{ $type === 'blogs' ? 'bg-cyan-600 text-white' : 'glass-card text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-800/50' }} transition-all whitespace-nowrap flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4H5m14 8H5m14 4H5"/></svg>
                        Blogs ({{ $blogs->count() }})
                    </a>
                    @endif
                    @if(isset($surveys))
                    <a href="{{ route('search', ['q' => $query, 'type' => 'surveys']) }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium {{ $type === 'surveys' ? 'bg-cyan-600 text-white' : 'glass-card text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-800/50' }} transition-all whitespace-nowrap flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Surveys ({{ $surveys->count() }})
                    </a>
                    @endif
                    @if(isset($conferences))
                    <a href="{{ route('search', ['q' => $query, 'type' => 'conferences']) }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium {{ $type === 'conferences' ? 'bg-cyan-600 text-white' : 'glass-card text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-800/50' }} transition-all whitespace-nowrap flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Conferences ({{ $conferences->count() }})
                    </a>
                    @endif
                </div>
            </form>
        </div>

        @if($query && $totalResults > 0)
            <!-- Events Results -->
            @if($events->count() > 0 && ($type === 'all' || $type === 'events'))
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Events
                        <span class="text-sm font-normal text-gray-500">({{ $events->count() }} found)</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($events as $event)
                            <a href="{{ route('events.show', $event->slug) }}" class="group">
                                <div class="glass-card rounded-2xl overflow-hidden hover-lift transition-all duration-300">
                                    @if($event->banner_image)
                                        <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="p-5">
                                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">
                                            {{ $event->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                                            {{ $event->summary }}
                                        </p>
                                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $event->start_date->format('M d, Y') }}
                                            </span>
                                            @if($event->company)
                                                <span class="text-cyan-600 dark:text-cyan-400">{{ $event->company->name }}</span>
                                            @endif
                                        </div>
                                        @if($event->categories->count() > 0)
                                            <div class="mt-3 flex flex-wrap gap-1">
                                                @foreach($event->categories->take(2) as $category)
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Polls Results -->
            @if(isset($polls) && $polls->count() > 0 && ($type === 'all' || $type === 'polls'))
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Polls & Voting
                        <span class="text-sm font-normal text-gray-500">({{ $polls->count() }} found)</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($polls as $poll)
                            <a href="{{ url('/polls/' . $poll->slug) }}" class="group">
                                <div class="glass-card rounded-2xl overflow-hidden hover-lift transition-all duration-300">
                                    @if($poll->banner_image)
                                        <img src="{{ $poll->banner_url }}" alt="{{ $poll->title }}" class="w-full h-40 object-cover">
                                    @else
                                        <div class="w-full h-40 bg-gradient-to-br from-purple-400 to-pink-600 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="p-5">
                                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                            {{ $poll->title }}
                                        </h3>
                                        <div class="flex items-center justify-between text-sm text-gray-500">
                                            <span>{{ $poll->contestants_count }} contestants</span>
                                            <span class="text-purple-600 font-semibold">{{ number_format($poll->total_votes ?? 0) }} votes</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Contestants Results -->
            @if(isset($contestants) && $contestants->count() > 0 && ($type === 'all' || $type === 'contestants'))
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Contestants
                        <span class="text-sm font-normal text-gray-500">({{ $contestants->count() }} found)</span>
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($contestants as $contestant)
                            <a href="{{ $contestant->poll ? url('/polls/' . $contestant->poll->slug) : '#' }}" class="group">
                                <div class="glass-card rounded-2xl p-4 text-center hover-lift transition-all duration-300">
                                    @if($contestant->photo)
                                        <img src="{{ asset('storage/' . $contestant->photo) }}" alt="{{ $contestant->name }}" class="w-20 h-20 mx-auto rounded-full object-cover mb-3 ring-2 ring-pink-500/30 group-hover:ring-pink-500 transition-all">
                                    @else
                                        <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-pink-400 to-rose-600 flex items-center justify-center mb-3 ring-2 ring-pink-500/30">
                                            <svg class="w-10 h-10 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm group-hover:text-pink-600 transition-colors">
                                        {{ $contestant->name }}
                                    </h3>
                                    @if($contestant->contestant_number)
                                        <p class="text-xs text-gray-500 mb-1">#{{ $contestant->contestant_number }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500">{{ $contestant->poll->title ?? 'Poll' }}</p>
                                    <p class="mt-2 text-sm font-bold text-pink-600">{{ number_format($contestant->total_votes) }} votes</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Companies/Organizers Results -->
            @if($companies->count() > 0 && ($type === 'all' || $type === 'organizers'))
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Organizers
                        <span class="text-sm font-normal text-gray-500">({{ $companies->count() }} found)</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($companies as $company)
                            <a href="{{ route('organizers.show', $company->slug) }}" class="group">
                                <div class="glass-card rounded-2xl p-6 hover-lift transition-all duration-300 flex items-center gap-4">
                                    @if($company->logo)
                                        <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="w-16 h-16 rounded-xl object-cover">
                                    @else
                                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-400 to-cyan-600 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ $company->name }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $company->events_count }} events
                                        </p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Categories Results -->
            @if($categories->count() > 0 && ($type === 'all' || $type === 'categories'))
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Categories
                        <span class="text-sm font-normal text-gray-500">({{ $categories->count() }} found)</span>
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($categories as $category)
                            <a href="{{ route('categories.show', $category->slug) }}" class="group">
                                <div class="glass-card rounded-2xl p-5 hover-lift transition-all duration-300 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-gradient-to-br from-{{ $category->color ?? 'gray' }}-400/20 to-{{ $category->color ?? 'gray' }}-600/30 flex items-center justify-center">
                                        <div class="text-{{ $category->color ?? 'gray' }}-600 dark:text-{{ $category->color ?? 'gray' }}-400 w-6 h-6">
                                            @if(method_exists($category, 'getIconHtml'))
                                                {!! $category->getIconHtml() !!}
                                            @else
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                            @endif
                                        </div>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-{{ $category->color ?? 'cyan' }}-600 transition-colors">
                                        {{ $category->name }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $category->events_count }} events
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Products Results -->
            @if($products->count() > 0 && ($type === 'all' || $type === 'products'))
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Products
                        <span class="text-sm font-normal text-gray-500">({{ $products->count() }} found)</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            <a href="{{ route('shop.show', $product->slug) }}" class="group">
                                <div class="glass-card rounded-2xl overflow-hidden hover-lift transition-all duration-300">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                                            {{ $product->name }}
                                        </h3>
                                        <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                            GH₵ {{ number_format($product->price, 2) }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Speakers Results -->
            @if(isset($speakers) && $speakers->count() > 0 && ($type === 'all' || $type === 'speakers'))
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        </svg>
                        Speakers & Team
                        <span class="text-sm font-normal text-gray-500">({{ $speakers->count() }} found)</span>
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($speakers as $speaker)
                            <div class="glass-card rounded-2xl p-4 text-center">
                                <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-orange-400 to-amber-600 flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">
                                    {{ $speaker->full_name }}
                                </h3>
                                @if($speaker->title)
                                    <p class="text-xs text-gray-500">{{ $speaker->title }}</p>
                                @endif
                                @if($speaker->role)
                                    <p class="text-xs text-orange-600 mt-1">{{ $speaker->role }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Blog Results -->
            @if(isset($blogs) && $blogs->count() > 0 && ($type === 'all' || $type === 'blogs'))
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4H5m14 8H5m14 4H5"/>
                        </svg>
                        Blog Posts
                        <span class="text-sm font-normal text-gray-500">({{ $blogs->count() }} found)</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($blogs as $article)
                            <a href="{{ route('blog.show', $article->slug) }}" class="group">
                                <div class="glass-card rounded-2xl overflow-hidden hover-lift transition-all duration-300 h-full">
                                    @if($article->image_url)
                                        <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-44 object-cover">
                                    @else
                                        <div class="w-full h-44 bg-gradient-to-br from-amber-400 to-orange-600 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4H5m14 8H5m14 4H5"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="p-5">
                                        <p class="text-xs uppercase tracking-[0.2em] text-amber-600 dark:text-amber-400 font-semibold">
                                            {{ $article->category ?: 'Blog' }}
                                        </p>
                                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                            {{ $article->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                            {{ $article->description }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-3">
                                            {{ $article->published_at ? $article->published_at->format('M d, Y') : '' }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Surveys Results -->
            @if(isset($surveys) && $surveys->count() > 0 && ($type === 'all' || $type === 'surveys'))
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Surveys
                        <span class="text-sm font-normal text-gray-500">({{ $surveys->count() }} found)</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($surveys as $survey)
                            <a href="{{ url('/survey/' . $survey->slug) }}" class="group">
                                <div class="glass-card rounded-2xl p-6 hover-lift transition-all duration-300">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-orange-600 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-1 line-clamp-2 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                                {{ $survey->title }}
                                            </h3>
                                            @if($survey->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-2">{{ Str::limit($survey->description, 100) }}</p>
                                            @endif
                                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ number_format($survey->responses_count ?? 0) }} responses
                                                </span>
                                                @if($survey->company)
                                                    <span class="text-amber-600">{{ $survey->company->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Conferences Results -->
            @if(isset($conferences) && $conferences->count() > 0 && ($type === 'all' || $type === 'conferences'))
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Conferences
                        <span class="text-sm font-normal text-gray-500">({{ $conferences->count() }} found)</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($conferences as $conference)
                            <a href="{{ url('/register/' . $conference->slug) }}" class="group">
                                <div class="glass-card rounded-2xl overflow-hidden hover-lift transition-all duration-300">
                                    @if($conference->header_image)
                                        <img src="{{ asset('storage/' . $conference->header_image) }}" alt="{{ $conference->title }}" class="w-full h-40 object-cover">
                                    @else
                                        <div class="w-full h-40 bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center">
                                            @if($conference->logo)
                                                <img src="{{ asset('storage/' . $conference->logo) }}" alt="{{ $conference->title }}" class="h-20 w-20 object-contain rounded-lg">
                                            @else
                                                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="p-5">
                                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            {{ $conference->title }}
                                        </h3>
                                        @if($conference->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                                                {{ Str::limit($conference->description, 100) }}
                                            </p>
                                        @endif
                                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                @if($conference->start_date)
                                                    {{ $conference->start_date->format('M d, Y') }}
                                                @endif
                                            </span>
                                            <span class="flex items-center gap-1 text-indigo-600 dark:text-indigo-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ number_format($conference->registrations_count ?? 0) }} registered
                                            </span>
                                        </div>
                                        @if($conference->venue)
                                            <p class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $conference->venue }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @elseif($query && $totalResults === 0)
            <!-- No Results -->
            <div class="glass-card rounded-2xl p-12 text-center">
                <svg class="w-20 h-20 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No results found for "{{ $query }}"</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    We couldn't find anything matching your search. Try different keywords or browse our suggestions below.
                </p>

                @if(isset($suggestions) && count($suggestions) > 0)
                    <div class="mt-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Suggestions</h4>
                        <div class="flex flex-wrap justify-center gap-3">
                            @foreach($suggestions as $suggestion)
                                <a href="{{ $suggestion['url'] }}"
                                   class="px-4 py-2 rounded-xl glass-card text-gray-700 dark:text-gray-300 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 hover:text-cyan-600 dark:hover:text-cyan-400 transition-all flex items-center gap-2">
                                    @if($suggestion['type'] === 'event')
                                        <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    @elseif($suggestion['type'] === 'category')
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    @else
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                    @endif
                                    {{ $suggestion['text'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-500 mb-4">Or browse popular sections:</p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="{{ route('events.index') }}" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl transition font-medium text-sm">
                            Browse Events
                        </a>
                        <a href="{{ url('/polls') }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition font-medium text-sm">
                            View Polls
                        </a>
                        <a href="{{ route('organizers.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition font-medium text-sm">
                            Explore Organizers
                        </a>
                        <a href="{{ route('shop.index') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl transition font-medium text-sm">
                            Shop Products
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State - No Query -->
            <div class="glass-card rounded-2xl p-12 text-center">
                <svg class="w-20 h-20 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Search Everything</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Find events, organizers, polls, contestants, categories, products, and more - all in one place.
                </p>
                <div class="max-w-md mx-auto">
                    <p class="text-sm text-gray-500 mb-4">Try searching for:</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        <a href="{{ route('search', ['q' => 'music']) }}" class="px-3 py-1 text-sm rounded-full glass-card text-gray-600 dark:text-gray-400 hover:text-cyan-600">music</a>
                        <a href="{{ route('search', ['q' => 'tech']) }}" class="px-3 py-1 text-sm rounded-full glass-card text-gray-600 dark:text-gray-400 hover:text-cyan-600">tech</a>
                        <a href="{{ route('search', ['q' => 'concert']) }}" class="px-3 py-1 text-sm rounded-full glass-card text-gray-600 dark:text-gray-400 hover:text-cyan-600">concert</a>
                        <a href="{{ route('search', ['q' => 'accra']) }}" class="px-3 py-1 text-sm rounded-full glass-card text-gray-600 dark:text-gray-400 hover:text-cyan-600">accra</a>
                        <a href="{{ route('search', ['q' => 'vote']) }}" class="px-3 py-1 text-sm rounded-full glass-card text-gray-600 dark:text-gray-400 hover:text-cyan-600">vote</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection
