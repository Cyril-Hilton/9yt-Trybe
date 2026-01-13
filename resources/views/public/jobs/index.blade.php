@extends('layouts.app')

@section('title', 'Jobs & Portfolios')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-6xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent mb-6">Jobs & Portfolios</h1>
            <div class="w-24 h-1 bg-gradient-to-r from-cyan-600 to-blue-600 mx-auto mb-8"></div>
            <a href="{{ route('jobs.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-lg font-semibold hover:from-cyan-700 hover:to-blue-700 transition shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Your Portfolio
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($portfolios as $portfolio)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    @if($portfolio->profile_picture_url)
                    <img src="{{ $portfolio->profile_picture_url }}" alt="{{ $portfolio->full_name }}" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    @else
                    <div class="w-24 h-24 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-3xl font-bold text-white">{{ substr($portfolio->first_name, 0, 1) }}{{ substr($portfolio->last_name, 0, 1) }}</span>
                    </div>
                    @endif
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white text-center mb-2">{{ $portfolio->full_name }}</h3>
                    <p class="text-cyan-600 dark:text-cyan-400 text-center font-semibold mb-4">{{ $portfolio->title }}</p>
                    <p class="text-gray-600 dark:text-gray-400 text-sm text-center mb-4">{{ $portfolio->job_type }}</p>
                    <a href="{{ $portfolio->portfolio_link }}" target="_blank" class="block w-full text-center px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-lg font-semibold hover:from-cyan-700 hover:to-blue-700 transition">
                        View Portfolio
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-20">
                <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No portfolios available</h3>
                <p class="text-gray-600 dark:text-gray-400">Be the first to add your work!</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
