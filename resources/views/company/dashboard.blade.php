@extends('layouts.company')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 py-12 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-black text-white mb-4">
                    Welcome back, {{ Auth::guard('company')->user()->name }}! 👋
                </h1>
                <p class="text-xl text-indigo-100">Here's what's happening with your events today</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <!-- Stats Overview Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Conferences Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-200 hover:scale-105 hover:shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-xl p-3 backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <a href="{{ route('organization.conferences.index') }}" class="text-white/80 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <p class="text-blue-100 text-sm font-semibold mb-1">Conferences</p>
                <p class="text-4xl font-black mb-2">{{ number_format($stats['total_conferences']) }}</p>
                <div class="flex items-center text-sm">
                    <span class="bg-white/20 px-2 py-1 rounded-full">{{ $stats['active_conferences'] }} active</span>
                </div>
            </div>

            <!-- Registrations Card -->
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-200 hover:scale-105 hover:shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-xl p-3 backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
                <p class="text-green-100 text-sm font-semibold mb-1">Total Registrations</p>
                <p class="text-4xl font-black mb-2">{{ number_format($stats['total_registrations']) }}</p>
                <p class="text-sm text-green-100">{{ number_format($stats['total_views']) }} total views</p>
            </div>

            <!-- Surveys Card -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-200 hover:scale-105 hover:shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-xl p-3 backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <a href="{{ route('organization.surveys.index') }}" class="text-white/80 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <p class="text-purple-100 text-sm font-semibold mb-1">Surveys</p>
                <p class="text-4xl font-black mb-2">{{ number_format($stats['total_surveys']) }}</p>
                <div class="flex items-center text-sm">
                    <span class="bg-white/20 px-2 py-1 rounded-full">{{ number_format($stats['total_responses']) }} responses</span>
                </div>
            </div>

            <!-- SMS Card -->
            <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-200 hover:scale-105 hover:shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-xl p-3 backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </div>
                    <a href="{{ route('organization.sms.dashboard') }}" class="text-white/80 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <p class="text-orange-100 text-sm font-semibold mb-1">SMS Credits</p>
                <p class="text-4xl font-black mb-2">{{ number_format($stats['sms_credits']) }}</p>
                <p class="text-sm text-orange-100">{{ number_format($stats['total_sent']) }} messages sent</p>
            </div>

            <!-- Polls & Voting Card -->
            <div class="bg-gradient-to-br from-cyan-500 to-teal-600 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-200 hover:scale-105 hover:shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-xl p-3 backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <a href="{{ route('organization.polls.index') }}" class="text-white/80 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <p class="text-cyan-100 text-sm font-semibold mb-1">Polls & Voting</p>
                <p class="text-4xl font-black mb-2">{{ number_format($stats['total_polls']) }}</p>
                <div class="flex items-center justify-between text-sm">
                    <span class="bg-white/20 px-2 py-1 rounded-full">{{ number_format($stats['total_votes']) }} votes</span>
                    <span class="font-bold">GH₵{{ number_format($stats['poll_revenue'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                <a href="{{ route('organization.conferences.create') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl p-6 border-2 border-transparent hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-200 transform hover:scale-105">
                    <div class="flex items-center">
                        <div class="bg-blue-100 dark:bg-blue-900/30 rounded-lg p-3 group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Create Conference</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Start a new event</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('organization.surveys.create') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl p-6 border-2 border-transparent hover:border-purple-500 dark:hover:border-purple-400 transition-all duration-200 transform hover:scale-105">
                    <div class="flex items-center">
                        <div class="bg-purple-100 dark:bg-purple-900/30 rounded-lg p-3 group-hover:bg-purple-200 dark:group-hover:bg-purple-800/50 transition-colors">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">Create Survey</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Collect feedback</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('organization.sms.campaigns.send-bulk') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl p-6 border-2 border-transparent hover:border-orange-500 dark:hover:border-orange-400 transition-all duration-200 transform hover:scale-105">
                    <div class="flex items-center">
                        <div class="bg-orange-100 dark:bg-orange-900/30 rounded-lg p-3 group-hover:bg-orange-200 dark:group-hover:bg-orange-800/50 transition-colors">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="font-bold text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400">Send Bulk SMS</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Message your audience</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('organization.sms.wallet.index') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl p-6 border-2 border-transparent hover:border-green-500 dark:hover:border-green-400 transition-all duration-200 transform hover:scale-105">
                    <div class="flex items-center">
                        <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-3 group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="font-bold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">Buy SMS Credits</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Top up your balance</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('organization.polls.create') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl p-6 border-2 border-transparent hover:border-cyan-500 dark:hover:border-cyan-400 transition-all duration-200 transform hover:scale-105">
                    <div class="flex items-center">
                        <div class="bg-cyan-100 dark:bg-cyan-900/30 rounded-lg p-3 group-hover:bg-cyan-200 dark:group-hover:bg-cyan-800/50 transition-colors">
                            <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="font-bold text-gray-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400">Create Poll</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Start voting/pageant</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border-2 border-indigo-100 dark:border-gray-700 overflow-hidden" x-data="{ activeTab: 'conferences' }">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">Recent Activity</h2>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <nav class="flex space-x-4 px-6" aria-label="Tabs">
                    <button @click="activeTab = 'conferences'"
                            :class="activeTab === 'conferences' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Conferences
                    </button>
                    <button @click="activeTab = 'surveys'"
                            :class="activeTab === 'surveys' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Surveys
                    </button>
                    <button @click="activeTab = 'sms'"
                            :class="activeTab === 'sms' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        SMS Campaigns
                    </button>
                    <button @click="activeTab = 'polls'"
                            :class="activeTab === 'polls' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        Polls
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Conferences Tab -->
                <div x-show="activeTab === 'conferences'" x-cloak>
                    @forelse($recentConferences as $conference)
                        <div class="flex items-center justify-between p-4 mb-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-200">
                            <div class="flex-1">
                                <a href="{{ route('organization.conferences.show', $conference) }}" class="font-bold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                                    {{ $conference->title }}
                                </a>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $conference->start_date->format('M d, Y') }} • {{ $conference->registrations_count }} registrations
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full
                                @if($conference->status === 'active') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400
                                @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                                {{ ucfirst($conference->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-400 font-semibold mb-2">No conferences yet</p>
                            <a href="{{ route('organization.conferences.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold">
                                Create your first conference →
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Surveys Tab -->
                <div x-show="activeTab === 'surveys'" x-cloak>
                    @forelse($recentSurveys as $survey)
                        <div class="flex items-center justify-between p-4 mb-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-purple-300 dark:hover:border-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200">
                            <div class="flex-1">
                                <a href="{{ route('organization.surveys.show', $survey) }}" class="font-bold text-gray-900 dark:text-white hover:text-purple-600 dark:hover:text-purple-400">
                                    {{ $survey->title }}
                                </a>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $survey->responses_count }} responses • Created {{ $survey->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full
                                @if($survey->status === 'active') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400
                                @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                                {{ ucfirst($survey->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-400 font-semibold mb-2">No surveys yet</p>
                            <a href="{{ route('organization.surveys.create') }}" class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-semibold">
                                Create your first survey →
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- SMS Tab -->
                <div x-show="activeTab === 'sms'" x-cloak>
                    @forelse($recentSmsCampaigns as $campaign)
                        <div class="flex items-center justify-between p-4 mb-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-orange-300 dark:hover:border-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all duration-200">
                            <div class="flex-1">
                                <a href="{{ route('organization.sms.campaigns.show', $campaign) }}" class="font-bold text-gray-900 dark:text-white hover:text-orange-600 dark:hover:text-orange-400">
                                    {{ $campaign->name }}
                                </a>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ number_format($campaign->total_recipients) }} recipients • {{ $campaign->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full
                                @if($campaign->status === 'completed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400
                                @elseif($campaign->status === 'processing') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400
                                @elseif($campaign->status === 'scheduled') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400
                                @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                                {{ ucfirst($campaign->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-400 font-semibold mb-2">No SMS campaigns yet</p>
                            <a href="{{ route('organization.sms.campaigns.send-bulk') }}" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-semibold">
                                Send your first campaign →
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Polls Tab -->
                <div x-show="activeTab === 'polls'" x-cloak>
                    @forelse($recentPolls as $poll)
                        <div class="flex items-center justify-between p-4 mb-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-cyan-300 dark:hover:border-cyan-600 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 transition-all duration-200">
                            <div class="flex-1">
                                <a href="{{ route('organization.polls.show', $poll) }}" class="font-bold text-gray-900 dark:text-white hover:text-cyan-600 dark:hover:text-cyan-400">
                                    {{ $poll->title }}
                                </a>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $poll->contestants_count }} contestants • {{ number_format($poll->total_votes) }} votes • Created {{ $poll->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full
                                @if($poll->status === 'active') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400
                                @elseif($poll->status === 'draft') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400
                                @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                                {{ ucfirst($poll->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-400 font-semibold mb-2">No polls yet</p>
                            <a href="{{ route('organization.polls.create') }}" class="text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 font-semibold">
                                Create your first poll →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Upcoming Conferences -->
        @if($upcomingConferences->count() > 0)
        <div class="mt-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-2xl p-8 text-white">
            <h2 class="text-2xl font-bold mb-6 flex items-center">
                <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Upcoming Conferences
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($upcomingConferences as $conference)
                    <a href="{{ route('organization.conferences.show', $conference) }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl p-6 transition-all duration-200 transform hover:scale-105">
                        <h3 class="font-bold text-lg mb-2">{{ $conference->title }}</h3>
                        <p class="text-indigo-100 text-sm mb-3">
                            {{ $conference->start_date->format('F j, Y \a\t g:i A') }}
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-black">{{ $conference->start_date->diffForHumans() }}</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
