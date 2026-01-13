@extends('layouts.app')

@section('title', 'Order Confirmation - ' . $order->order_number)
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-green-50 dark:from-gray-900 dark:via-slate-900/20 dark:to-slate-800/20 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Message -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full mb-4">
                <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold gradient-text mb-2">Order Confirmed!</h1>
            <p class="text-xl text-gray-600 dark:text-gray-400">Thank you for your purchase</p>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 rounded-lg text-center">
            <p class="text-green-800 dark:text-green-200 font-semibold">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Order Details -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8 border border-gray-200 dark:border-gray-700 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Order Number</h2>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Order Date</h2>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Payment Status</h2>
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                        @if($order->payment_status === 'paid') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                        @elseif($order->payment_status === 'pending') bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                        @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                        @endif">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Order Status</h2>
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                        @if($order->status === 'delivered') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                        @elseif($order->status === 'processing') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                        @else bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            <!-- Customer & Shipping Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Customer Information</h3>
                    <div class="text-gray-700 dark:text-gray-300 space-y-1">
                        <p>{{ $order->customer_name }}</p>
                        <p>{{ $order->customer_email }}</p>
                        <p>{{ $order->customer_phone }}</p>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Shipping Address</h3>
                    <div class="text-gray-700 dark:text-gray-300 space-y-1">
                        <p>{{ $order->shipping_address }}</p>
                        <p>{{ $order->city }}@if($order->region), {{ $order->region }}@endif</p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Order Items</h3>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center space-x-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                        @if($item->product && $item->product->image_url)
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="w-20 h-20 rounded-lg object-cover">
                        @else
                        <div class="w-20 h-20 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $item->product_name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Quantity: {{ $item->quantity }}
                                @if($item->size) • Size: {{ $item->size }}@endif
                                @if($item->color) • Color: {{ $item->color }}@endif
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">GH₵{{ number_format($item->price, 2) }} each</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900 dark:text-white">GH₵{{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Total -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="space-y-2">
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Subtotal</span>
                        <span class="font-semibold">GH₵{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Shipping</span>
                        <span class="font-semibold">
                            @if($order->shipping_fee > 0)
                                GH₵{{ number_format($order->shipping_fee, 2) }}
                            @else
                                <span class="text-green-600 dark:text-green-400">FREE</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span>Total</span>
                        <span class="text-cyan-600 dark:text-cyan-400">GH₵{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($order->notes)
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Order Notes</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $order->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('shop.index') }}" class="px-8 py-3 bg-white dark:bg-gray-800 border-2 border-cyan-300 dark:border-cyan-700 text-cyan-600 dark:text-cyan-400 rounded-lg font-semibold hover:bg-cyan-50 dark:hover:bg-gray-700 transition text-center">
                Continue Shopping
            </a>
            @auth
            <a href="{{ route('user.dashboard') }}" class="px-8 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-bold rounded-lg hover:from-cyan-700 hover:to-blue-700 transition shadow-lg text-center">
                View My Orders
            </a>
            @endauth
        </div>

        <!-- Info Box -->
        <div class="mt-8 p-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-start space-x-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-1">What's Next?</h4>
                    <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                        <li>✓ You will receive an order confirmation email shortly</li>
                        <li>✓ We'll notify you when your order ships</li>
                        <li>✓ Track your order status in your dashboard</li>
                        @if($order->payment_method === 'cash_on_delivery')
                        <li>✓ Please have exact cash ready upon delivery</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
