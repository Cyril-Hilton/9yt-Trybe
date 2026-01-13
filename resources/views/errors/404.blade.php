@extends('layouts.app')

@section('title', 'Page Not Found - 404')
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full text-center">
        <!-- 404 Illustration -->
        <div class="mb-8">
            <div class="text-9xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">
                404
            </div>
        </div>

        <!-- Error Message -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
            Page Not Found
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-400 mb-8">
            Sorry, we couldn't find the page you're looking for. It might have been moved or deleted.
        </p>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-lg font-semibold hover:from-cyan-700 hover:to-blue-700 transition shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Go Home
            </a>
            <button onclick="window.history.back()" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Go Back
            </button>
        </div>

        <!-- Quick Links -->
        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">You might be looking for:</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('events.index') }}" class="text-cyan-600 dark:text-cyan-400 hover:underline font-medium">
                    Events
                </a>
                <a href="{{ route('shop.index') }}" class="text-cyan-600 dark:text-cyan-400 hover:underline font-medium">
                    Shop
                </a>
                <a href="{{ route('gallery.index') }}" class="text-cyan-600 dark:text-cyan-400 hover:underline font-medium">
                    Gallery
                </a>
                <a href="{{ route('jobs.index') }}" class="text-cyan-600 dark:text-cyan-400 hover:underline font-medium">
                    Jobs
                </a>
                <a href="{{ route('team.index') }}" class="text-cyan-600 dark:text-cyan-400 hover:underline font-medium">
                    Team
                </a>
                <a href="{{ route('contact') }}" class="text-cyan-600 dark:text-cyan-400 hover:underline font-medium">
                    Contact
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
