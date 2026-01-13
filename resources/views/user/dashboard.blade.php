@extends('layouts.app')

@section('title', 'My Dashboard - 9yt !Trybe')

@section('styles')
<style>
    /* iOS 26 Liquid Glass Effect */
    .glass-stat-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15),
                    inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .dark .glass-stat-card {
        background: rgba(30, 41, 59, 0.85);
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(148, 163, 184, 0.15);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }
    .stat-card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(6, 182, 212, 0.25);
    }
    .gradient-text {
        background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 50%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-cyan-50/30 to-blue-50/30 dark:from-gray-900 dark:via-slate-900 dark:to-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Glass Effect -->
        <div class="mb-8 glass-stat-card rounded-2xl p-6 border-l-4 border-cyan-500">
            <h1 class="text-3xl font-bold gradient-text">Welcome back, {{ $user->name }}!</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Manage your tickets and event history</p>
        </div>

        <!-- Statistics Cards with Glass Effects -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="glass-stat-card rounded-2xl p-6 stat-card-hover transition-all duration-300 border-t-4 border-indigo-500">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-indigo-400/20 to-indigo-600/30 dark:from-indigo-500/30 dark:to-indigo-700/40 rounded-xl" style="backdrop-filter: blur(10px);">
                        <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Orders</p>
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['total_orders'] }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-stat-card rounded-2xl p-6 stat-card-hover transition-all duration-300 border-t-4 border-emerald-500">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-emerald-400/20 to-emerald-600/30 dark:from-emerald-500/30 dark:to-emerald-700/40 rounded-xl" style="backdrop-filter: blur(10px);">
                        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Upcoming Events</p>
                        <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['upcoming_events'] }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-stat-card rounded-2xl p-6 stat-card-hover transition-all duration-300 border-t-4 border-slate-400">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-slate-400/20 to-slate-600/30 dark:from-slate-500/30 dark:to-slate-700/40 rounded-xl" style="backdrop-filter: blur(10px);">
                        <svg class="w-8 h-8 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Past Events</p>
                        <p class="text-3xl font-bold text-slate-600 dark:text-slate-400">{{ $stats['past_events'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders with Glass Effect -->
        <div class="glass-stat-card rounded-2xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold gradient-text">Recent Orders</h2>
                <a href="{{ route('user.tickets') }}" class="text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 text-sm font-semibold flex items-center gap-1 transition">
                    View all tickets
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @if($recentOrders->count() > 0)
            <div class="space-y-4">
                @foreach($recentOrders as $order)
                <div class="border border-cyan-200/50 dark:border-cyan-800/30 rounded-xl p-4 hover:bg-cyan-50/50 dark:hover:bg-cyan-900/20 transition-all duration-300 hover:border-cyan-400/50 dark:hover:border-cyan-700/50 hover:shadow-lg hover:shadow-cyan-500/10">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400 transition">{{ $order->event->title }}</h3>
                            <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $order->event->formatted_date }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                    {{ $order->attendees_count }} {{ Str::plural('ticket', $order->attendees_count) }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right sm:ml-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                @if($order->payment_status === 'completed') bg-gradient-to-r from-emerald-400/20 to-emerald-600/30 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-700
                                @elseif($order->payment_status === 'pending') bg-gradient-to-r from-amber-400/20 to-amber-600/30 text-amber-700 dark:text-amber-400 border border-amber-300 dark:border-amber-700
                                @else bg-gradient-to-r from-red-400/20 to-red-600/30 text-red-700 dark:text-red-400 border border-red-300 dark:border-red-700
                                @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                            <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">GH₵ {{ number_format($order->total, 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-cyan-100 dark:border-cyan-900/30">
                        <a href="{{ route('user.tickets.show', $order->order_number) }}"
                           class="inline-flex items-center text-sm font-semibold text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 transition">
                            View details
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-600/30 flex items-center justify-center">
                    <svg class="w-10 h-10 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">No tickets yet</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Start exploring events and get your first tickets!</p>
                <a href="{{ route('events.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Browse Events
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
