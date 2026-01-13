@extends('layouts.app')

@section('title', 'Shopping Cart')
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent mb-6">Shopping Cart</h1>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 rounded-lg">
            <p class="text-green-800 dark:text-green-200 font-semibold">{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 rounded-lg">
            <p class="text-red-800 dark:text-red-200 font-semibold">{{ session('error') }}</p>
        </div>
        @endif

        @if($cartItems->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
            @foreach($cartItems as $item)
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center space-x-4 flex-1">
                        @if($item->product->image_url)
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-cover rounded-lg">
                        @endif
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">GH₵{{ number_format($item->product->price, 2) }} each</p>
                            @if($item->size)
                            <p class="text-sm text-gray-500 dark:text-gray-400">Size: {{ $item->size }}</p>
                            @endif
                            @if($item->color)
                            <p class="text-sm text-gray-500 dark:text-gray-400">Color: {{ $item->color }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Quantity Controls -->
                        <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg">
                            <form action="{{ route('shop.cart.update', $item) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
                                <button type="submit" class="px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                            </form>

                            <span class="px-4 py-2 text-gray-900 dark:text-white font-semibold min-w-[3rem] text-center">{{ $item->quantity }}</span>

                            <form action="{{ route('shop.cart.update', $item) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                <button type="submit" class="px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <!-- Subtotal -->
                        <div class="text-right min-w-[100px]">
                            <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">GH₵{{ number_format($item->subtotal, 2) }}</p>
                        </div>

                        <!-- Remove Button -->
                        <form action="{{ route('shop.cart.remove', $item) }}" method="POST" onsubmit="return confirm('Remove this item from cart?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="p-6 bg-gray-50 dark:bg-gray-700/50">
                <div class="flex items-center justify-between mb-6">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">Total:</span>
                    <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">GH₵{{ number_format($total, 2) }}</span>
                </div>
                <div class="space-y-3">
                    @auth
                    <a href="{{ route('shop.checkout') }}" class="block w-full px-6 py-4 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg font-bold hover:from-indigo-700 hover:to-blue-700 transition shadow-lg text-lg text-center">
                        Proceed to Checkout
                    </a>
                    @else
                    <a href="{{ route('user.login') }}?redirect={{ urlencode(route('shop.checkout')) }}" class="block w-full px-6 py-4 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg font-bold hover:from-indigo-700 hover:to-blue-700 transition shadow-lg text-lg text-center">
                        Login to Checkout
                    </a>
                    @endauth
                    <a href="{{ route('shop.index') }}" class="block w-full px-6 py-3 bg-white dark:bg-gray-800 border-2 border-indigo-300 dark:border-indigo-700 text-indigo-600 dark:text-indigo-400 rounded-lg font-semibold hover:bg-indigo-50 dark:hover:bg-gray-700 transition text-center">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-20">
            <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Your cart is empty</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Add some items from the shop!</p>
            <a href="{{ route('shop.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg font-semibold hover:from-indigo-700 hover:to-blue-700 transition">
                Continue Shopping
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
