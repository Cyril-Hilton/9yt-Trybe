@extends('layouts.app')

@section('title', 'Shop - 9yt !Trybe Merch')

@if(isset($isEmpty) && $isEmpty)
@section('meta_robots', 'noindex, follow')
@endif


@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-blue-50 dark:from-gray-900 dark:via-slate-900/20 dark:to-slate-800/20 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Header -->
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-7xl font-extrabold gradient-text mb-6 pb-2">
                9yt !Trybe Shop
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 mb-6">
                Get your exclusive merch & represent the vibe
            </p>
            <div class="w-32 h-1 bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 mx-auto rounded-full"></div>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-8">
            @forelse($products as $product)
            <a href="{{ route('shop.show', $product) }}" class="group">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover-lift card-glow border-2 border-transparent hover:border-cyan-500 transition-all duration-300">
                    <!-- Product Image -->
                    <div class="relative overflow-hidden h-64">
                        @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        @else
                        <div class="w-full h-full animated-gradient-bg flex items-center justify-center">
                            <svg class="w-20 h-20 text-white opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        @endif
                        <!-- Stock Badge -->
                        @if($product->stock < 5 && $product->stock > 0)
                        <div class="absolute top-3 right-3 px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full pulse-button">
                            Only {{ $product->stock }} left!
                        </div>
                        @elseif($product->stock == 0)
                        <div class="absolute top-3 right-3 px-3 py-1 bg-gray-800 text-white text-xs font-bold rounded-full">
                            Sold Out
                        </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition line-clamp-2">
                            {{ $product->name }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                            {{ $product->description }}
                        </p>
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-2xl font-bold gradient-text">
                                GH₵{{ number_format($product->price, 2) }}
                            </span>
                            <span class="px-5 py-2.5 bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 text-white rounded-lg font-bold group-hover:shadow-lg group-hover:scale-105 transition-all">
                                BUY
                            </span>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-20">
                <div class="relative inline-block">
                    <svg class="w-32 h-32 text-cyan-400 mx-auto mb-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="text-3xl font-bold gradient-text mb-3">No Products Available Yet</h3>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Stay tuned! Fresh merch dropping soon 🔥</p>
            </div>
            @endforelse
        </div>

        @if($products->hasPages())
        <div class="mt-8">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
