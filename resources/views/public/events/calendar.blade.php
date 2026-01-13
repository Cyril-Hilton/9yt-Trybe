@extends('layouts.app')

@section('title', 'Event Calendar - ' . $date->format('F Y') . ' - 9yt !Trybe')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-br from-blue-600 via-slate-600 to-gray-700 dark:from-slate-900 dark:via-blue-900 dark:to-gray-900 text-white py-12 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4 pb-2 text-white drop-shadow-lg">Event Calendar</h1>
        <p class="text-lg md:text-xl text-blue-100 dark:text-slate-300 mb-6">Browse events by date</p>

        <!-- View Switcher -->
        <div class="flex justify-center gap-4">
            <a href="{{ route('events.index', ['view' => 'list']) }}"
               class="px-6 py-3 rounded-xl font-semibold transition-all flex items-center gap-2 bg-white/20 text-white hover:bg-white/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                List View
            </a>
            <a href="{{ route('events.calendar', ['year' => $date->year, 'month' => $date->month]) }}"
               class="px-6 py-3 rounded-xl font-semibold transition-all flex items-center gap-2 bg-white text-blue-700 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Calendar View
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Calendar Area -->
        <div class="flex-1">
            <!-- Month Navigation -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 transition-colors">
                <div class="flex items-center justify-between">
                    <a href="{{ route('events.calendar', ['year' => $date->copy()->subMonth()->year, 'month' => $date->copy()->subMonth()->month]) }}"
                       class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg transition">
                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Previous
                    </a>

                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $date->format('F Y') }}</h2>

                    <a href="{{ route('events.calendar', ['year' => $date->copy()->addMonth()->year, 'month' => $date->copy()->addMonth()->month]) }}"
                       class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg transition">
                        Next
                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Quick Month Jumps -->
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach(range(1, 12) as $m)
                    <a href="{{ route('events.calendar', ['year' => now()->year, 'month' => $m]) }}"
                       class="px-3 py-1 text-sm rounded {{ $date->month == $m && $date->year == now()->year ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }} transition">
                        {{ \Carbon\Carbon::create()->month($m)->format('M') }}
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 transition-colors">
                <div class="grid grid-cols-7 gap-2">
                    <!-- Day Headers -->
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="text-center font-semibold text-gray-700 dark:text-gray-300 py-2">{{ $day }}</div>
                    @endforeach

                    <!-- Calendar Days -->
                    @php
                        $firstDayOfMonth = $date->copy()->startOfMonth();
                        $lastDayOfMonth = $date->copy()->endOfMonth();
                        $startPadding = $firstDayOfMonth->dayOfWeek;
                        $daysInMonth = $firstDayOfMonth->daysInMonth;
                    @endphp

                    <!-- Padding for days before month starts -->
                    @for($i = 0; $i < $startPadding; $i++)
                    <div class="bg-gray-50 dark:bg-gray-900 rounded p-2 h-24"></div>
                    @endfor

                    <!-- Actual days of the month -->
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $currentDate = $date->copy()->day($day);
                            $dateKey = $currentDate->format('Y-m-d');
                            $dayEvents = $events->get($dateKey, collect());
                            $isToday = $currentDate->isToday();
                        @endphp
                        <div class="border rounded p-2 h-24 overflow-y-auto {{ $isToday ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' }} hover:shadow-md transition">
                            <div class="font-semibold {{ $isToday ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-900 dark:text-white' }} mb-1">
                                {{ $day }}
                                @if($isToday)
                                <span class="ml-1 px-1 text-xs bg-indigo-600 text-white rounded">Today</span>
                                @endif
                            </div>
                            @if($dayEvents->count() > 0)
                                @foreach($dayEvents->take(2) as $event)
                                <a href="{{ route('events.show', $event->slug) }}"
                                   class="block text-xs bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200 rounded px-1 py-0.5 mb-1 truncate hover:bg-indigo-200 dark:hover:bg-indigo-700 transition"
                                   title="{{ $event->title }}">
                                    • {{ Str::limit($event->title, 15) }}
                                </a>
                                @endforeach
                                @if($dayEvents->count() > 2)
                                <div class="text-xs text-gray-600 dark:text-gray-400">+{{ $dayEvents->count() - 2 }} more</div>
                                @endif
                            @endif
                        </div>
                    @endfor

                    <!-- Padding for days after month ends -->
                    @php
                        $endPadding = 7 - (($startPadding + $daysInMonth) % 7);
                        if($endPadding == 7) $endPadding = 0;
                    @endphp
                    @for($i = 0; $i < $endPadding; $i++)
                    <div class="bg-gray-50 dark:bg-gray-900 rounded p-2 h-24"></div>
                    @endfor
                </div>
            </div>

            <!-- Event List View -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-colors">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">All Events in {{ $date->format('F Y') }}</h3>

                @if($events->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400">No events scheduled for this month</p>
                    <a href="{{ route('events.index') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                        Browse all events →
                    </a>
                </div>
                @else
                <div class="space-y-4">
                    @foreach($events as $dateKey => $dayEvents)
                        <div class="border-l-4 border-indigo-500 pl-4">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-2">
                                {{ \Carbon\Carbon::parse($dateKey)->format('l, F j, Y') }}
                            </h4>
                            <div class="space-y-3">
                                @foreach($dayEvents as $event)
                                <a href="{{ route('events.show', $event->slug) }}"
                                   class="block bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <div class="flex items-start gap-4">
                                        @if($event->banner_image)
                                        <div class="w-24 bg-gradient-to-br from-indigo-500 to-blue-600 rounded overflow-hidden flex-shrink-0">
                                            <img src="{{ $event->banner_url }}" alt="{{ $event->title }}"
                                                 class="w-full h-auto">
                                        </div>
                                        @else
                                        <div class="w-24 h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded flex items-center justify-center text-white text-xs flex-shrink-0">
                                            No Image
                                        </div>
                                        @endif
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-900 dark:text-white">{{ $event->title }}</h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $event->start_date->format('g:i A') }}
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                @if($event->location_type === 'venue')
                                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    {{ $event->venue_name }}
                                                @elseif($event->location_type === 'online')
                                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                                    </svg>
                                                    Online Event
                                                @else
                                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Location TBA
                                                @endif
                                            </p>
                                            <div class="mt-2 flex items-center gap-2">
                                                @if($event->hasFreeTickets())
                                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-bold rounded">FREE</span>
                                                @else
                                                    <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-xs font-bold rounded">From GH₵{{ number_format($event->cheapest_ticket_price, 2) }}</span>
                                                @endif
                                                @if($event->company)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">by {{ $event->company->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:w-80">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 transition-colors">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('events.index') }}"
                       class="block w-full px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-center font-semibold">
                        Browse All Events
                    </a>
                    <a href="{{ route('events.calendar', ['year' => now()->year, 'month' => now()->month]) }}"
                       class="block w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-center font-semibold">
                        Current Month
                    </a>
                </div>
            </div>

            <!-- Trending Events -->
            @if($trendingEvents->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-colors">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Trending Events</h3>
                <div class="space-y-4">
                    @foreach($trendingEvents as $trendingEvent)
                    <a href="{{ route('events.show', $trendingEvent->slug) }}"
                       class="block hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg p-2 transition">
                        <div class="relative bg-gradient-to-br from-indigo-500 to-blue-600 rounded mb-2 overflow-hidden">
                            @if($trendingEvent->banner_image)
                            <img src="{{ $trendingEvent->banner_url }}" alt="{{ $trendingEvent->title }}"
                                 class="w-full h-auto rounded">
                            @endif
                            @if($trendingEvent->hasFreeTickets())
                            <span class="absolute top-2 right-2 px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full">FREE</span>
                            @endif
                        </div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ Str::limit($trendingEvent->title, 40) }}</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $trendingEvent->formatted_date }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">{{ $trendingEvent->formatted_location }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
