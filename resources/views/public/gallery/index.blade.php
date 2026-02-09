@extends('layouts.app')

@section('title', 'Gallery')

@if(isset($isEmpty) && $isEmpty)
@section('meta_robots', 'noindex, follow')
@endif


@section('content')
<div x-data="{ activeTab: 'new' }" class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-6xl font-extrabold gradient-text neon-glow mb-6">📸 Gallery</h1>
            <div class="w-24 h-1 bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 mx-auto rounded-full"></div>
        </div>

        <!-- Tabs -->
        <div class="flex justify-center mb-12">
            <div class="inline-flex bg-white dark:bg-gray-800 rounded-xl p-1 shadow-xl border-2 border-cyan-300 dark:border-cyan-700 card-glow">
                <button @click="activeTab = 'new'" :class="activeTab === 'new' ? 'bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:text-cyan-600 dark:hover:text-cyan-400'" class="px-6 py-3 rounded-lg font-semibold transition-all hover-lift flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    New Events
                </button>
                <button @click="activeTab = 'old'" :class="activeTab === 'old' ? 'bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:text-cyan-600 dark:hover:text-cyan-400'" class="px-6 py-3 rounded-lg font-semibold transition-all hover-lift flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    Old Events
                </button>
                <button @click="activeTab = 'magazine'" :class="activeTab === 'magazine' ? 'bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:text-cyan-600 dark:hover:text-cyan-400'" class="px-6 py-3 rounded-lg font-semibold transition-all hover-lift flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Photo Magazine
                </button>
            </div>
        </div>

        <!-- New Events Tab -->
        <div x-show="activeTab === 'new'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($newEvents as $image)
            <div class="group relative overflow-hidden rounded-xl shadow-lg card-glow hover-lift border-2 border-transparent hover:border-cyan-500 transition-all duration-300">
                <img src="{{ $image->image_url }}" alt="{{ $image->title }}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end">
                    <p class="text-white font-bold p-4 text-shadow">{{ $image->title }}</p>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 text-lg">No images available yet.</p>
            </div>
            @endforelse
        </div>

        <!-- Old Events Tab -->
        <div x-show="activeTab === 'old'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($oldEvents as $image)
            <div class="group relative overflow-hidden rounded-xl shadow-lg card-glow hover-lift border-2 border-transparent hover:border-cyan-500 transition-all duration-300">
                <img src="{{ $image->image_url }}" alt="{{ $image->title }}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end">
                    <p class="text-white font-bold p-4 text-shadow">{{ $image->title }}</p>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 text-lg">No images available yet.</p>
            </div>
            @endforelse
        </div>

        <!-- Magazine Tab -->
        <div x-show="activeTab === 'magazine'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($magazineImages as $image)
            <div class="group relative overflow-hidden rounded-xl shadow-lg card-glow hover-lift border-2 border-transparent hover:border-cyan-500 transition-all duration-300">
                <img src="{{ $image->image_url }}" alt="{{ $image->title }}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end">
                    <p class="text-white font-bold p-4 text-shadow">{{ $image->title }}</p>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 text-lg">No images available yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
