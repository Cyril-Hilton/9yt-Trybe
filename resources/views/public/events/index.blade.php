@extends('layouts.app')

@section('title', isset($category) ? $category->name . ' Events - 9yt !Trybe' : 'Browse Events - 9yt !Trybe')
@section('meta_description', isset($category) ? 'Browse ' . $category->name . ' events and book tickets on 9yt !Trybe.' : 'Browse events and book tickets on 9yt !Trybe.')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-br from-blue-600 via-slate-600 to-gray-700 dark:from-slate-900 dark:via-blue-900 dark:to-gray-900 text-white py-20 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <h1 class="text-5xl md:text-6xl font-extrabold mb-6 pb-2 text-white drop-shadow-lg">Discover Amazing Events</h1>
        @if(isset($category))
            <p class="text-sm uppercase tracking-widest text-cyan-200 font-semibold mb-2">Category</p>
            <p class="text-2xl md:text-3xl font-bold text-white mb-4">{{ $category->name }}</p>
        @endif
        <p class="text-xl md:text-2xl text-blue-100 dark:text-slate-300 mb-8">Find and book tickets for conferences, workshops, and more</p>

        <!-- Search Bar -->
        <form action="{{ route('events.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1" x-data="{ query: '{{ request('search') }}', suggestions: [], open: false, loading: false, error: '' }" @click.away="open = false">
                <div class="relative">
                    <input type="text"
                           name="search"
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
                           placeholder="Search for events..."
                           class="w-full px-6 py-4 rounded-2xl text-gray-900 dark:text-white glass-effect focus:ring-2 focus:ring-cyan-500 focus:outline-none transition-all">
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
                </div>
            </div>
            <div class="w-full md:w-64">
                <input type="text"
                       name="location"
                       value="{{ request('location') }}"
                       placeholder="Location"
                       class="w-full px-6 py-4 rounded-2xl text-gray-900 dark:text-white glass-effect focus:ring-2 focus:ring-cyan-500 focus:outline-none transition-all">
            </div>
            <button type="submit" class="px-8 py-4 glass-effect text-cyan-700 dark:text-cyan-300 rounded-2xl font-bold hover-lift transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Search
            </button>
        </form>

        <!-- View Switcher -->
        <div class="mt-6 flex justify-center gap-4">
            <a href="{{ route('events.index') }}"
               class="px-6 py-3 rounded-2xl font-semibold transition-all flex items-center gap-2 glass-effect text-cyan-700 dark:text-cyan-300 hover-lift">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                List View
            </a>
            <a href="{{ route('events.calendar') }}"
               class="px-6 py-3 rounded-2xl font-semibold transition-all flex items-center gap-2 glass-card text-white hover-lift">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Calendar View
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1">
            <div class="glass-card rounded-2xl p-6 sticky top-24 transition-all hover-lift">
                <h3 class="text-xl font-bold gradient-text mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filters
                </h3>

                <form action="{{ route('events.index') }}" method="GET">
                    <!-- Date Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Date</label>
                        <input type="date"
                               name="date"
                               value="{{ request('date') }}"
                               class="w-full px-4 py-3 border-2 border-cyan-300 dark:border-cyan-700 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 focus:outline-none transition-all">
                    </div>

                    <!-- Location Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Format</label>
                        <select name="location_type" class="w-full px-4 py-3 border-2 border-cyan-300 dark:border-cyan-700 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 focus:outline-none transition-all">
                            <option value="">All Formats</option>
                            <option value="venue" {{ request('location_type') === 'venue' ? 'selected' : '' }}>In-Person</option>
                            <option value="online" {{ request('location_type') === 'online' ? 'selected' : '' }}>Online</option>
                            <option value="tba" {{ request('location_type') === 'tba' ? 'selected' : '' }}>TBA</option>
                        </select>
                    </div>

                    <!-- Price Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Price</label>
                        <select name="price_filter" class="w-full px-4 py-3 border-2 border-cyan-300 dark:border-cyan-700 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 focus:outline-none transition-all">
                            <option value="">All Prices</option>
                            <option value="free" {{ request('price_filter') === 'free' ? 'selected' : '' }}>Free</option>
                            <option value="paid" {{ request('price_filter') === 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Sort By</label>
                        <select name="sort" class="w-full px-4 py-3 border-2 border-cyan-300 dark:border-cyan-700 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 focus:outline-none transition-all">
                            <option value="date" {{ request('sort') === 'date' ? 'selected' : '' }}>Date</option>
                            <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 hover:from-cyan-700 hover:via-cyan-600 hover:to-indigo-700 text-white rounded-xl font-bold transition-all shadow-lg hover-lift pulse-button">
                        Apply Filters
                    </button>

                    @if(request()->hasAny(['search', 'location', 'date', 'location_type', 'price_filter', 'sort']))
                    <a href="{{ route('events.index') }}" class="block w-full text-center px-4 py-2 mt-3 text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 font-medium transition">
                        Clear All
                    </a>
                    @endif
                </form>

                <!-- Mini Calendar -->
                <div class="mt-8 pt-6 border-t-2 border-cyan-200 dark:border-cyan-800" x-data="miniCalendar()">
                    <h3 class="text-lg font-bold gradient-text mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Quick Date Filter
                    </h3>
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-gray-700 dark:to-gray-600 rounded-lg p-4 border-2 border-blue-200 dark:border-blue-700">
                        <!-- Month Navigation -->
                        <div class="flex items-center justify-between mb-3">
                            <button @click="previousMonth()" class="p-1 hover:bg-white dark:hover:bg-gray-600 rounded transition">
                                <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="monthYear"></span>
                            <button @click="nextMonth()" class="p-1 hover:bg-white dark:hover:bg-gray-600 rounded transition">
                                <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                        <!-- Days of Week -->
                        <div class="grid grid-cols-7 gap-1 mb-2">
                            <template x-for="day in ['S','M','T','W','T','F','S']">
                                <div class="text-xs font-semibold text-center text-gray-600 dark:text-gray-300" x-text="day"></div>
                            </template>
                        </div>
                        <!-- Calendar Days -->
                        <div class="grid grid-cols-7 gap-1">
                            <template x-for="(day, index) in calendarDays" :key="index">
                                <a :href="day.date ? `{{ route('events.index') }}?date=${day.date}` : 'javascript:void(0)'"
                                   :class="{
                                       'text-gray-400 dark:text-gray-500 cursor-default': !day.date || day.isOtherMonth,
                                       'bg-blue-600 text-white font-bold': day.isToday && day.date,
                                       'bg-white dark:bg-gray-500 hover:bg-blue-100 dark:hover:bg-blue-800 text-gray-900 dark:text-white': day.date && !day.isToday && !day.isOtherMonth,
                                       'bg-cyan-100 dark:bg-cyan-900 font-semibold text-cyan-900 dark:text-cyan-100': day.hasEvents && !day.isToday,
                                       'cursor-pointer hover:shadow-md': day.date && !day.isOtherMonth
                                   }"
                                   class="text-sm text-center py-2 px-1 rounded transition-all duration-200 font-medium min-h-[32px] flex items-center justify-center"
                                   x-text="day.day"
                                   :onclick="!day.date || day.isOtherMonth ? 'return false;' : ''">
                                </a>
                            </template>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-3 text-center">Click a date to filter events</p>
                    </div>
                </div>

                <!-- Trending Events -->
                @if($trendingEvents->count() > 0)
                <div class="mt-8 pt-6 border-t-2 border-cyan-200 dark:border-cyan-800">
                    <h3 class="text-lg font-bold gradient-text mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Trending Now
                    </h3>
                    @foreach($trendingEvents as $trending)
                    <a href="{{ route('events.show', $trending->slug) }}" class="block mb-4 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-cyan-900/20 transition-all group">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-cyan-600 group-hover:to-cyan-500 line-clamp-2 transition">
                            {{ $trending->title }}
                        </div>
                        <div class="text-xs text-cyan-600 dark:text-cyan-400 mt-1 font-medium">
                            {{ $trending->start_date->format('M j') }}
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Event Grid -->
        <div class="lg:col-span-3">
            @if($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($events as $event)
                <a href="{{ route('events.show', $event->slug) }}" class="group">
                    <div class="glass-card rounded-2xl overflow-hidden hover-lift transition-all duration-300">
                        <!-- Event Image -->
                        <div class="relative bg-gradient-to-br from-cyan-500 to-cyan-500 overflow-hidden">
                            @if($event->banner_image)
                            <img src="{{ $event->banner_url }}"
                                 alt="{{ $event->title }}"
                                 class="w-full h-auto">
                            @else
                            <div class="absolute inset-0 flex items-center justify-center animated-gradient-bg">
                                <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/50 via-transparent to-transparent"></div>

                            <!-- External Event Badge (Top Left) -->
                            @if($event->is_external)
                            <span class="absolute top-3 left-3 px-3 py-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                External
                            </span>
                            @endif

                            <!-- Price Badge -->
                            @if(!$event->is_external)
                                @if($event->hasFreeTickets())
                                <span class="absolute top-3 right-3 px-3 py-1 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-bold rounded-full shadow-lg pulse-button">
                                    FREE
                                </span>
                                @elseif($event->cheapest_ticket_price > 0)
                                <span class="absolute top-3 right-3 px-3 py-1 bg-white text-cyan-600 text-xs font-bold rounded-full shadow-lg neon-border">
                                    From GH₵{{ number_format($event->cheapest_ticket_price, 2) }}
                                </span>
                                @endif
                            @endif
                        </div>

                        <!-- Event Details -->
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-cyan-600 group-hover:to-cyan-500 transition line-clamp-2">
                                {{ $event->title }}
                            </h3>

                            <!-- Date & Time -->
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <svg class="w-4 h-4 mr-2 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $event->start_date->format('M j, Y • g:i A') }}
                            </div>

                            <!-- Location -->
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-3">
                                @if($event->location_type === 'venue')
                                <svg class="w-4 h-4 mr-2 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <span class="line-clamp-1">{{ $event->venue_name ?? 'Venue TBA' }}</span>
                                @elseif($event->location_type === 'online')
                                <svg class="w-4 h-4 mr-2 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                                Online Event
                                @else
                                <svg class="w-4 h-4 mr-2 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                To Be Announced
                                @endif
                            </div>

                            <!-- Organizer -->
                            @if($event->company)
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 border-t-2 border-cyan-100 dark:border-cyan-900 pt-3">
                                @if($event->company->logo)
                                <img src="{{ $event->company->logo_url }}"
                                     alt="{{ $event->company->name }}"
                                     class="w-6 h-6 rounded-full mr-2 ring-2 ring-cyan-300 dark:ring-cyan-700">
                                @endif
                                <span class="line-clamp-1">{{ $event->company->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $events->links() }}
            </div>

            @else
            <div class="text-center py-16">
                <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-4 text-xl font-medium text-gray-900 dark:text-white">No events found</h3>
                @if($category)
                    <p class="mt-2 text-gray-500 dark:text-gray-400">There are no events in "{{ $category->name }}" yet. Try another category.</p>
                @else
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Try adjusting your search or filters</p>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function miniCalendar() {
    return {
        currentDate: new Date(),
        eventDates: @json($upcomingEventDates ?? []),

        get monthYear() {
            return this.currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        },

        get calendarDays() {
            const year = this.currentDate.getFullYear();
            const month = this.currentDate.getMonth();

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const prevLastDay = new Date(year, month, 0);

            const firstDayOfWeek = firstDay.getDay();
            const lastDate = lastDay.getDate();
            const prevLastDate = prevLastDay.getDate();

            const days = [];

            // Previous month days
            for (let i = firstDayOfWeek - 1; i >= 0; i--) {
                days.push({
                    day: prevLastDate - i,
                    date: null,
                    isOtherMonth: true,
                    isToday: false,
                    hasEvents: false
                });
            }

            // Current month days
            const today = new Date();
            for (let i = 1; i <= lastDate; i++) {
                const date = new Date(year, month, i);
                const dateStr = date.toISOString().split('T')[0];
                const isToday = date.toDateString() === today.toDateString();
                const hasEvents = this.eventDates.hasOwnProperty(dateStr);

                days.push({
                    day: i,
                    date: dateStr,
                    isOtherMonth: false,
                    isToday: isToday,
                    hasEvents: hasEvents
                });
            }

            // Next month days to fill the grid
            const remainingDays = 42 - days.length; // 6 rows x 7 days
            for (let i = 1; i <= remainingDays; i++) {
                days.push({
                    day: i,
                    date: null,
                    isOtherMonth: true,
                    isToday: false,
                    hasEvents: false
                });
            }

            return days;
        },

        previousMonth() {
            this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
        },

        nextMonth() {
            this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
        }
    };
}
</script>
@endsection
