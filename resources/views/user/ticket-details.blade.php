@extends('layouts.app')

@section('title', 'Order Details - 9yt !Trybe')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50/30 to-purple-50/30 dark:from-gray-900 dark:via-indigo-950/20 dark:to-purple-950/20 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6 no-print">
            <a href="{{ route('user.tickets') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition group">
                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to My Tickets
            </a>
        </div>

        <!-- Order Header Card -->
        <div class="glass-premium-card rounded-2xl p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">{{ $order->event->title }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 font-mono text-sm">Order #{{ $order->order_number }}</p>
                </div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold mt-4 md:mt-0 shadow-sm
                    @if($order->payment_status === 'completed') bg-gradient-to-r from-emerald-400 to-green-500 text-white
                    @elseif($order->payment_status === 'pending') bg-gradient-to-r from-amber-400 to-yellow-500 text-white
                    @else bg-gradient-to-r from-red-400 to-rose-500 text-white
                    @endif">
                    <span class="w-2 h-2 rounded-full bg-white/80 mr-2 animate-pulse"></span>
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>

            <!-- Event Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-start p-3 rounded-xl bg-white/50 dark:bg-gray-800/50">
                    <div class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 mr-3">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Event Date</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">{{ $order->event->formatted_date }}</p>
                    </div>
                </div>

                <div class="flex items-start p-3 rounded-xl bg-white/50 dark:bg-gray-800/50">
                    <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/30 mr-3">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Location</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
                            @if($order->event->location_type === 'venue')
                                {{ $order->event->venue_name }}
                            @elseif($order->event->location_type === 'online')
                                Online Event
                            @else
                                To Be Announced
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-start p-3 rounded-xl bg-white/50 dark:bg-gray-800/50">
                    <div class="p-2 rounded-lg bg-rose-100 dark:bg-rose-900/30 mr-3">
                        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Tickets</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">{{ $order->attendees_count }} {{ Str::plural('ticket', $order->attendees_count) }}</p>
                    </div>
                </div>

                <div class="flex items-start p-3 rounded-xl bg-white/50 dark:bg-gray-800/50">
                    <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 mr-3">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Order Total</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white mt-1">GH₵ {{ number_format($order->total, 2) }}</p>
                    </div>
                </div>
            </div>

            @if($order->event->location_type === 'venue' && $order->event->venue_address)
            <div class="mt-4 pt-4 border-t border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-start p-4 rounded-xl bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <div>
                        <p class="text-xs font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wide mb-1">Venue Address</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order->event->venue_address }}</p>
                        <a href="@if($order->event->venue_latitude && $order->event->venue_longitude)https://www.google.com/maps/dir/?api=1&destination={{ $order->event->venue_latitude }},{{ $order->event->venue_longitude }}@else https://www.google.com/maps/search/?api=1&query={{ urlencode($order->event->venue_address) }}@endif"
                           target="_blank"
                           class="inline-flex items-center mt-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            Get Directions
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Tickets Section -->
        <div class="glass-premium-card rounded-2xl p-6 mb-6">
            <div class="flex items-center mb-6">
                <div class="p-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-500 mr-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Your Tickets</h2>
            </div>

            <div class="space-y-6">
                @foreach($order->attendees as $index => $attendee)
                <div class="premium-ticket-card relative overflow-hidden rounded-2xl">
                    <!-- Ticket Gradient Border -->
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl"></div>

                    <!-- Ticket Inner Content -->
                    <div class="relative m-[3px] bg-white dark:bg-gray-900 rounded-2xl overflow-hidden">
                        <!-- Ticket Top Pattern -->
                        <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 opacity-10"></div>

                        <!-- Print-Only Event Header -->
                        <div class="hidden print-only-header px-6 pt-4 pb-2 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-900 text-center">{{ $order->event->title }}</h2>
                            <div class="flex justify-center items-center gap-4 mt-2 text-sm text-gray-600">
                                <span>{{ $order->event->formatted_date }}</span>
                                <span>•</span>
                                <span>
                                    @if($order->event->location_type === 'venue')
                                        {{ $order->event->venue_name }}
                                    @elseif($order->event->location_type === 'online')
                                        Online Event
                                    @else
                                        TBA
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Ticket Header -->
                        <div class="relative px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg shadow-indigo-500/30">
                                        {{ $attendee->ticket->name }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">#{{ $index + 1 }}</span>
                                </div>
                                @if($attendee->checked_in)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r from-emerald-400 to-green-500 text-white shadow-lg shadow-emerald-500/30">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Checked In
                                </span>
                                @endif
                            </div>

                            <!-- Attendee Info -->
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 flex-shrink-0 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white break-words min-w-0">{{ $attendee->attendee_name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 flex-shrink-0 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-gray-600 dark:text-gray-400 break-words min-w-0">{{ $attendee->attendee_email }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Event Flier Section -->
                        @if($order->event->banner_image)
                        <div class="px-6 py-4 print-hide-banner">
                            <div class="relative group">
                                <!-- Decorative Frame -->
                                <div class="absolute -inset-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-xl opacity-75 blur-sm group-hover:opacity-100 transition"></div>
                                <div class="relative bg-white dark:bg-gray-900 p-1 rounded-lg">
                                    <img src="{{ $order->event->banner_url }}"
                                         alt="{{ $order->event->title }}"
                                         class="w-full h-auto rounded-lg shadow-xl">
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Dashed Separator -->
                        <div class="relative my-4">
                            <div class="absolute left-0 w-6 h-6 bg-slate-50 dark:bg-gray-800 rounded-full -translate-x-1/2 -translate-y-1/2 top-1/2"></div>
                            <div class="absolute right-0 w-6 h-6 bg-slate-50 dark:bg-gray-800 rounded-full translate-x-1/2 -translate-y-1/2 top-1/2"></div>
                            <div class="border-t-2 border-dashed border-gray-300 dark:border-gray-700 mx-8"></div>
                        </div>

                        <!-- QR Code Section -->
                        <div class="px-6 pb-6 qr-section">
                            <div class="flex flex-col items-center">
                                <!-- QR Code Container -->
                                <div class="relative qr-container" style="position: relative; z-index: 1;">
                                    <!-- QR Code Frame -->
                                    <div class="relative bg-white p-4 rounded-xl shadow-2xl qr-frame" style="position: relative; z-index: 2;">
                                        <div class="relative bg-white p-3 rounded-lg qr-code-wrapper" style="position: relative; z-index: 3;">
                                            {!! QrCode::size(180)->margin(1)->generate($attendee->ticket_code) !!}
                                        </div>
                                    </div>
                                </div>

                                <!-- Scan Instructions -->
                                <div class="mt-4 text-center">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Scan at Entry</p>
                                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700">
                                        <span class="font-mono text-sm font-bold text-gray-800 dark:text-gray-200 tracking-wider">{{ $attendee->ticket_code }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Footer -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 px-6 py-4">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $order->event->formatted_date }}</span>
                                <span class="font-semibold text-indigo-600 dark:text-indigo-400">9yt !Trybe</span>
                                <span>{{ $order->event->location_type === 'venue' ? Str::limit($order->event->venue_name, 20) : 'Online Event' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Payment Information -->
        <div class="glass-premium-card rounded-2xl p-6 print-hide">
            <div class="flex items-center mb-6">
                <div class="p-2 rounded-lg bg-gradient-to-r from-emerald-500 to-green-500 mr-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Payment Summary</h2>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between py-3 px-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                    <span class="font-medium text-gray-900 dark:text-white">GH₵ {{ number_format($order->subtotal, 2) }}</span>
                </div>

                @if($order->service_fee > 0)
                <div class="flex justify-between py-3 px-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                    <span class="text-gray-600 dark:text-gray-400">Service Fee</span>
                    <span class="font-medium text-gray-900 dark:text-white">GH₵ {{ number_format($order->service_fee, 2) }}</span>
                </div>
                @endif

                @if($order->processing_fee > 0)
                <div class="flex justify-between py-3 px-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                    <span class="text-gray-600 dark:text-gray-400">Processing Fee</span>
                    <span class="font-medium text-gray-900 dark:text-white">GH₵ {{ number_format($order->processing_fee, 2) }}</span>
                </div>
                @endif

                <div class="flex justify-between py-4 px-4 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-500 mt-4">
                    <span class="font-semibold text-white">Total Paid</span>
                    <span class="font-bold text-white text-lg">GH₵ {{ number_format($order->total, 2) }}</span>
                </div>

                <div class="pt-4 space-y-2 text-sm">
                    <div class="flex justify-between py-2 px-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <span class="text-gray-600 dark:text-gray-400">Payment Method</span>
                        <span class="font-medium text-gray-900 dark:text-white capitalize">{{ $order->payment_method ?? 'N/A' }}</span>
                    </div>

                    @if($order->payment_reference)
                    <div class="flex justify-between py-2 px-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <span class="text-gray-600 dark:text-gray-400">Reference</span>
                        <span class="font-mono text-xs text-gray-900 dark:text-white">{{ $order->payment_reference }}</span>
                    </div>
                    @endif

                    @if($order->paid_at)
                    <div class="flex justify-between py-2 px-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <span class="text-gray-600 dark:text-gray-400">Paid On</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $order->paid_at->format('M d, Y g:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex flex-col sm:flex-row gap-3 no-print">
            <a href="{{ route('events.show', $order->event->slug) }}"
               class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-700 dark:text-gray-300 rounded-xl transition font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                View Event Details
            </a>

            <button onclick="downloadTicketPDF()"
                    class="glass-btn-primary glass-btn-lg inline-flex items-center justify-center font-medium shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Tickets (PDF)
            </button>
        </div>
    </div>
</div>

@section('styles')
<style>
    /* Premium Glass Card */
    .glass-premium-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow:
            0 4px 6px -1px rgba(0, 0, 0, 0.05),
            0 10px 15px -3px rgba(0, 0, 0, 0.05),
            0 20px 25px -5px rgba(0, 0, 0, 0.05),
            inset 0 1px 0 rgba(255, 255, 255, 0.6);
    }

    .dark .glass-premium-card {
        background: rgba(17, 24, 39, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow:
            0 4px 6px -1px rgba(0, 0, 0, 0.2),
            0 10px 15px -3px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }

    /* Premium Ticket Card */
    .premium-ticket-card {
        transition: all 0.3s ease;
    }

    .premium-ticket-card:hover {
        transform: translateY(-2px);
    }

    /* Mobile Optimization */
    @media screen and (max-width: 768px) {
        .premium-ticket-card {
            margin-left: -1rem;
            margin-right: -1rem;
            border-radius: 0;
        }

        .premium-ticket-card > div:first-child {
            border-radius: 0;
        }

        .premium-ticket-card > div > div {
            border-radius: 0 !important;
        }
    }

    /* BEAUTIFUL PRINT LAYOUT - OPTIMIZED FOR PDF */
    @media print {
        @page {
            size: A5 portrait;
            margin: 8mm;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        html, body {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* HIDE NON-TICKET ELEMENTS */
        nav, footer, header,
        .no-print, .print-hide,
        aside, .sidebar,
        .glass-premium-card:not(.glass-premium-card:has(.premium-ticket-card)),
        [class*="back"], [class*="Back"] {
            display: none !important;
        }

        .min-h-screen {
            min-height: auto !important;
            padding: 0 !important;
            background: white !important;
        }

        /* BEAUTIFUL TICKET CARD */
        .premium-ticket-card {
            page-break-after: always !important;
            page-break-inside: avoid !important;
            margin: 0 auto !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            border: 4px solid #6366f1 !important;
            border-radius: 16px !important;
            background: white !important;
            overflow: visible !important;
            box-shadow: none !important;
        }

        .premium-ticket-card:last-of-type {
            page-break-after: auto !important;
        }

        /* Remove decorative gradient border */
        .premium-ticket-card > div:first-child {
            display: none !important;
        }

        /* Inner ticket content */
        .premium-ticket-card > div:nth-child(2) {
            margin: 0 !important;
            background: white !important;
            border-radius: 12px !important;
        }

        /* Event Header with Logo & Title */
        .print-only-header {
            display: block !important;
            text-align: center !important;
            padding: 15px 10px !important;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
            margin: 0 !important;
            border-radius: 12px 12px 0 0 !important;
        }

        .print-only-header h2 {
            font-size: 18px !important;
            font-weight: 900 !important;
            color: white !important;
            margin: 0 0 8px 0 !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            line-height: 1.3 !important;
        }

        .print-only-header .flex {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            font-size: 11px !important;
            color: rgba(255, 255, 255, 0.95) !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
        }

        .print-only-header .flex span {
            color: rgba(255, 255, 255, 0.95) !important;
        }

        /* Attendee Information */
        .premium-ticket-card .px-6.pt-6.pb-4 {
            padding: 15px !important;
            background: white !important;
            border-bottom: 2px dashed #e5e7eb !important;
        }

        .premium-ticket-card .inline-flex.items-center.px-4 {
            background: #6366f1 !important;
            color: white !important;
            padding: 6px 14px !important;
            border-radius: 20px !important;
            font-size: 12px !important;
            font-weight: 700 !important;
        }

        .premium-ticket-card .text-lg {
            font-size: 15px !important;
            font-weight: 800 !important;
            color: #111827 !important;
        }

        .premium-ticket-card .text-gray-600 {
            color: #4b5563 !important;
            font-size: 11px !important;
        }

        .premium-ticket-card svg {
            color: #6366f1 !important;
        }

        /* Hide banner/flier */
        .print-hide-banner {
            display: none !important;
        }

        /* Separator */
        .premium-ticket-card .relative.my-4 {
            height: 20px !important;
            margin: 0 !important;
            display: block !important;
        }

        .premium-ticket-card .relative.my-4 .border-dashed {
            border-color: #d1d5db !important;
            border-width: 2px !important;
        }

        .premium-ticket-card .relative.my-4 .absolute {
            background: white !important;
            border: 3px solid #6366f1 !important;
        }

        /* QR CODE SECTION - CENTERED & BEAUTIFUL */
        .qr-section {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            padding: 20px 15px !important;
            background: #f9fafb !important;
        }

        .qr-container {
            display: block !important;
            margin: 0 auto 15px auto !important;
            padding: 10px !important;
            background: white !important;
            border: 3px solid #6366f1 !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25) !important;
        }

        .qr-frame {
            display: block !important;
            padding: 0 !important;
            background: white !important;
            border-radius: 8px !important;
        }

        .qr-code-wrapper {
            display: block !important;
            background: white !important;
            padding: 8px !important;
        }

        .qr-code-wrapper svg {
            display: block !important;
            width: 140px !important;
            height: 140px !important;
            margin: 0 auto !important;
        }

        /* Scan Instructions */
        .qr-section .mt-4 {
            margin-top: 10px !important;
            text-align: center !important;
        }

        .qr-section .text-xs {
            font-size: 10px !important;
            font-weight: 700 !important;
            color: #6b7280 !important;
            text-transform: uppercase !important;
            letter-spacing: 1.5px !important;
            margin-bottom: 8px !important;
        }

        .qr-section .inline-flex {
            display: inline-flex !important;
            background: white !important;
            border: 3px solid #4f46e5 !important;
            padding: 12px 24px !important;
            border-radius: 8px !important;
        }

        .qr-section .font-mono {
            font-size: 18px !important;
            font-weight: 900 !important;
            letter-spacing: 3px !important;
            color: #4f46e5 !important;
        }

        /* Ticket Footer - Brand & Info */
        .premium-ticket-card .bg-gradient-to-r.from-gray-50 {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
            padding: 12px 15px !important;
            border-radius: 0 0 12px 12px !important;
            margin-top: 0 !important;
        }

        .premium-ticket-card .bg-gradient-to-r.from-gray-50 .flex {
            display: flex !important;
            justify-content: space-between !important;
            color: rgba(255, 255, 255, 0.95) !important;
            font-size: 10px !important;
            font-weight: 600 !important;
        }

        .premium-ticket-card .bg-gradient-to-r.from-gray-50 span {
            color: rgba(255, 255, 255, 0.95) !important;
        }

        .premium-ticket-card .text-indigo-600 {
            color: white !important;
            font-weight: 800 !important;
        }

        /* Clean up badges */
        .premium-ticket-card .rounded-full {
            border-radius: 20px !important;
        }

        .premium-ticket-card .from-emerald-400 {
            background: #10b981 !important;
        }
    }
</style>

<script>
function downloadTicketPDF() {
    // Show loading state
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<svg class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Preparing PDF...';

    // Add document title for PDF
    const originalTitle = document.title;
    const orderCode = '{{ $order->order_code ?? 'ticket' }}';
    document.title = orderCode + '-Tickets';

    // Detect mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    // Configure print styles dynamically
    const printStyles = document.createElement('style');
    printStyles.id = 'dynamic-print-styles';
    printStyles.textContent = `
        @media print {
            @page {
                ${isMobile ? 'size: A4 portrait;' : 'size: 13cm 15cm;'}
                margin: ${isMobile ? '10mm' : '5mm'};
            }

            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Ensure tickets are visible */
            .premium-ticket-card {
                page-break-inside: avoid !important;
                page-break-after: always !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                ${isMobile ? 'transform: scale(0.95);' : ''}
            }

            /* Remove last ticket's page break */
            .premium-ticket-card:last-of-type {
                page-break-after: auto !important;
            }
        }
    `;
    document.head.appendChild(printStyles);

    // Show helpful message on mobile
    if (isMobile) {
        const message = document.createElement('div');
        message.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);background:rgba(6,182,212,0.95);color:white;padding:15px 25px;border-radius:12px;z-index:10000;font-size:14px;font-weight:600;box-shadow:0 10px 40px rgba(0,0,0,0.3);';
        message.textContent = '💡 Tip: Select "Save as PDF" in the next dialog';
        document.body.appendChild(message);

        setTimeout(() => {
            if (message.parentNode) {
                message.remove();
            }
        }, 3000);
    }

    // Trigger print dialog
    setTimeout(() => {
        window.print();

        // Restore button and title after print dialog
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalHTML;
            document.title = originalTitle;

            // Remove dynamic styles
            const dynamicStyles = document.getElementById('dynamic-print-styles');
            if (dynamicStyles) {
                dynamicStyles.remove();
            }
        }, 1000);
    }, isMobile ? 500 : 100);
}

// Listen for print events to track success
window.addEventListener('beforeprint', function() {
    console.log('Print dialog opened - Ready to save as PDF');
});

window.addEventListener('afterprint', function() {
    console.log('Print dialog closed');
});
</script>
@endsection
@endsection
