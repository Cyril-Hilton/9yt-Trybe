@extends('layouts.app')

@php
    $metaTitle = $metaOverrides['meta_title'] ?? ($product->meta_title ?: ($product->name . ' - Shop'));
    $metaDescription = $metaOverrides['meta_description'] ?? ($product->meta_description ?: Str::limit(strip_tags($product->description ?? ''), 155));
    $metaKeywords = !empty($product->ai_tags) ? implode(', ', $product->ai_tags) : $product->name . ', shop, 9yt !Trybe';
    $shareImage = $product->image_url ?: asset('ui/logo/9yt-trybe-logo-light.png');
@endphp

@section('title', $metaTitle . ' | 9yt !Trybe')
@section('meta_title', $metaTitle)
@section('meta_description', $metaDescription)
@section('meta_keywords', $metaKeywords)
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)
@section('og_image', $shareImage)
@section('twitter_title', $metaTitle)
@section('twitter_description', $metaDescription)
@section('twitter_image', $shareImage)

@push('head')
@php
    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => strip_tags((string) $product->description),
        'image' => [$shareImage],
        'offers' => [
            '@type' => 'Offer',
            'price' => $product->price,
            'priceCurrency' => 'GHS',
            'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => url('/shop/' . $product->slug),
        ],
    ];

    $faqItems = [];
    if (!empty($product->ai_faqs)) {
        foreach ($product->ai_faqs as $faq) {
            if (!empty($faq['question']) && !empty($faq['answer'])) {
                $faqItems[] = [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['answer'],
                    ],
                ];
            }
        }
    }
@endphp
<script type="application/ld+json">
{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
@if(!empty($faqItems))
    <script type="application/ld+json">
    {!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faqItems], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-pink-50 dark:from-gray-900 dark:via-slate-900/20 dark:to-slate-800/20 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('shop.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 transition group">
                <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Shop
            </a>
        </div>

        <!-- Product Detail Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden card-glow border border-cyan-200 dark:border-cyan-900">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6 sm:p-8 lg:p-12">
                <!-- Product Image -->
                <div class="relative">
                    @if($product->image_url)
                    <div class="relative rounded-xl overflow-hidden neon-border group">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                             class="w-full h-auto object-cover transform group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                    @else
                    <div class="w-full h-96 bg-gradient-to-br from-cyan-500 to-cyan-400 rounded-xl flex items-center justify-center neon-border">
                        <svg class="w-32 h-32 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="flex flex-col justify-between">
                    <div>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold gradient-text mb-4">
                            {{ $product->name }}
                        </h1>

                        <div class="flex items-center space-x-4 mb-6">
                            <div class="text-4xl sm:text-5xl font-bold text-cyan-600 dark:text-cyan-400 neon-glow">
                                GH₵{{ number_format($product->price, 2) }}
                            </div>
                            @if($product->stock > 0)
                            <span class="px-4 py-2 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm font-semibold pulse-button">
                                {{ $product->stock }} in stock
                            </span>
                            @else
                            <span class="px-4 py-2 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-full text-sm font-semibold">
                                Out of Stock
                            </span>
                            @endif
                        </div>

                        <div class="prose dark:prose-invert max-w-none mb-8">
                            <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                                {{ $product->description }}
                            </p>
                        </div>
                    </div>

                    <!-- Add to Cart Section -->
                    @if($product->stock > 0)
                    <form action="{{ route('shop.add-to-cart', $product) }}" method="POST" x-data="{ quantity: 1 }">
                        @csrf
                        <div class="space-y-6">
                            <!-- Quantity Selector -->
                            <div class="flex items-center space-x-4">
                                <label class="text-gray-700 dark:text-gray-300 font-semibold">Quantity:</label>
                                <div class="flex items-center space-x-2">
                                    <button type="button" @click="quantity = Math.max(1, quantity - 1)"
                                            class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-cyan-200 dark:hover:bg-cyan-900 text-gray-700 dark:text-gray-300 font-bold transition">
                                        -
                                    </button>
                                    <input type="number" name="quantity" x-model="quantity" min="1" :max="{{ $product->stock }}"
                                           class="w-20 px-3 py-2 text-center border-2 border-cyan-300 dark:border-cyan-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-bold focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                                    <button type="button" @click="quantity = Math.min({{ $product->stock }}, quantity + 1)"
                                            class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-cyan-200 dark:hover:bg-cyan-900 text-gray-700 dark:text-gray-300 font-bold transition">
                                        +
                                    </button>
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            <button type="submit"
                                    class="w-full py-4 px-8 bg-gradient-to-r from-cyan-600 via-cyan-500 to-red-600 hover:from-cyan-700 hover:via-cyan-600 hover:to-red-700 text-white text-lg font-bold rounded-xl shadow-lg hover-lift pulse-button transition-all duration-300 flex items-center justify-center space-x-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Add to Cart</span>
                            </button>

                            <!-- Continue Shopping Link -->
                            <a href="{{ route('shop.index') }}"
                               class="block text-center py-3 px-6 border-2 border-cyan-300 dark:border-cyan-700 hover:border-cyan-500 dark:hover:border-cyan-500 text-cyan-600 dark:text-cyan-400 rounded-xl font-semibold transition-all hover-lift">
                                Continue Shopping
                            </a>
                        </div>
                    </form>
                    @else
                    <div class="space-y-4">
                        <div class="p-6 bg-red-50 dark:bg-red-900/20 border-2 border-red-300 dark:border-red-800 rounded-xl">
                            <p class="text-red-800 dark:text-red-200 font-semibold text-center">This product is currently out of stock</p>
                        </div>
                        <a href="{{ route('shop.index') }}"
                           class="block text-center py-3 px-6 bg-gradient-to-r from-cyan-600 to-cyan-500 hover:from-cyan-700 hover:to-pink-700 text-white rounded-xl font-semibold transition-all hover-lift">
                            Back to Shop
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if(!empty($product->ai_faqs))
            <div class="mt-10 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Product FAQs</h2>
                <div class="space-y-4">
                    @foreach($product->ai_faqs as $faq)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $faq['question'] ?? '' }}</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $faq['answer'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Related Products Section -->
        @if($relatedProducts && $relatedProducts->count() > 0)
        <div class="mt-16">
            <h2 class="text-3xl font-bold gradient-text mb-8">You May Also Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                <a href="{{ route('shop.show', $related) }}" class="group">
                    <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover-lift card-glow border border-cyan-100 dark:border-cyan-900">
                        @if($related->image_url)
                        <img src="{{ $related->image_url }}" alt="{{ $related->name }}"
                             class="w-full h-48 object-cover transform group-hover:scale-110 transition-transform duration-500">
                        @else
                        <div class="w-full h-48 bg-gradient-to-br from-cyan-500 to-cyan-400 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition mb-2">
                                {{ Str::limit($related->name, 40) }}
                            </h3>
                            <p class="text-xl font-bold text-cyan-600 dark:text-cyan-400">
                                GH₵{{ number_format($related->price, 2) }}
                            </p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
