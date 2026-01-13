@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h1>
        <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-gray-400">Welcome back, {{ auth()->guard('admin')->user()->name }}! Here's what's happening with your platform.</p>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Companies -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 text-white transform transition-all duration-200 hover:scale-105 border-2 border-blue-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-xs sm:text-sm font-medium">Total Companies</p>
                    <p class="text-2xl sm:text-3xl md:text-4xl font-bold mt-1 sm:mt-2">{{ $stats['total_companies'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg sm:rounded-xl p-2 sm:p-4">
                    <svg class="h-6 w-6 sm:h-8 sm:w-8 md:h-10 md:w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Companies -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-200 hover:scale-105 border-2 border-green-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Active Companies</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['active_companies'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl p-4">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Suspended Companies -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-200 hover:scale-105 border-2 border-red-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Suspended Companies</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['suspended_companies'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl p-4">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Conferences -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-200 hover:scale-105 border-2 border-purple-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Conferences</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_conferences'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl p-4">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Registrations -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-200 hover:scale-105 border-2 border-orange-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Total Registrations</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_registrations'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl p-4">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Surveys -->
        <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-200 hover:scale-105 border-2 border-pink-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-pink-100 text-sm font-medium">Total Surveys</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_surveys'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl p-4">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Admins -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white transform transition-all duration-200 hover:scale-105 border-2 border-indigo-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm font-medium">Admin Users</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_admins'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl p-4">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quick Action Card -->
        <div class="bg-gradient-to-br from-gray-700 to-gray-800 rounded-2xl shadow-xl p-6 text-white flex items-center justify-center border-2 border-gray-600">
            <div class="text-center">
                <p class="text-gray-300 text-sm font-medium mb-4">Quick Actions</p>
                <div class="space-y-2">
                    <a href="{{ route('admin.companies.create') }}" class="block px-4 py-2 bg-white bg-opacity-20 rounded-lg hover:bg-opacity-30 transition-all duration-200 text-sm font-semibold">
                        + Add Company
                    </a>
                    <a href="{{ route('admin.admins.create') }}" class="block px-4 py-2 bg-white bg-opacity-20 rounded-lg hover:bg-opacity-30 transition-all duration-200 text-sm font-semibold">
                        + Add Admin
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Companies -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border-2 border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <svg class="h-6 w-6 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Recent Companies
                </h3>
                <a href="{{ route('admin.companies.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                    View All →
                </a>
            </div>

            @if($recentCompanies->count() > 0)
                <div class="space-y-3">
                    @foreach($recentCompanies as $company)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold">
                                    {{ substr($company->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $company->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $company->email }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.companies.show', $company) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-gray-400 py-8">No companies registered yet</p>
            @endif
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border-2 border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <svg class="h-6 w-6 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Recent Registrations
                </h3>
            </div>

            @if($recentRegistrations->count() > 0)
                <div class="space-y-3">
                    @foreach($recentRegistrations as $registration)
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $registration->name ?? $registration->email }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $registration->conference->title ?? 'N/A' }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        By {{ $registration->conference->company->name ?? 'Unknown' }}
                                    </p>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $registration->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-gray-400 py-8">No registrations yet</p>
            @endif
        </div>
    </div>
</div>
@endsection
