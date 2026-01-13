@extends('layouts.admin')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="p-8 max-w-6xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Order {{ $order->order_number }}</h1>
                <p class="text-gray-400">{{ $order->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <a href="{{ route('admin.shop-orders.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                ← Back to Orders
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-600 bg-opacity-20 border border-green-600 rounded-lg">
        <p class="text-green-400 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">Order Items</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-4 p-4 bg-gray-900 rounded-lg">
                        @if($item->product && $item->product->image_url)
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="w-20 h-20 object-cover rounded-lg">
                        @else
                        <div class="w-20 h-20 bg-gray-700 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-white font-semibold">{{ $item->product_name }}</h3>
                            <p class="text-sm text-gray-400">
                                Quantity: {{ $item->quantity }} × GH₵{{ number_format($item->price, 2) }}
                            </p>
                            @if($item->size || $item->color)
                            <p class="text-xs text-gray-500">
                                @if($item->size)Size: {{ $item->size }}@endif
                                @if($item->size && $item->color) • @endif
                                @if($item->color)Color: {{ $item->color }}@endif
                            </p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-cyan-400">GH₵{{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="mt-6 pt-6 border-t border-gray-700 space-y-2">
                    <div class="flex justify-between text-gray-400">
                        <span>Subtotal:</span>
                        <span class="text-white font-semibold">GH₵{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Shipping:</span>
                        <span class="text-white font-semibold">
                            @if($order->shipping_fee > 0)
                                GH₵{{ number_format($order->shipping_fee, 2) }}
                            @else
                                <span class="text-green-400 font-bold">FREE</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-700">
                        <span class="text-white">Total:</span>
                        <span class="text-cyan-400">GH₵{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">Customer Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Name</p>
                        <p class="text-white font-semibold">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Email</p>
                        <a href="mailto:{{ $order->customer_email }}" class="text-indigo-400 hover:text-indigo-300 font-semibold">
                            {{ $order->customer_email }}
                        </a>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Phone</p>
                        <a href="tel:{{ $order->customer_phone }}" class="text-indigo-400 hover:text-indigo-300 font-semibold">
                            {{ $order->customer_phone }}
                        </a>
                    </div>
                    @if($order->user)
                    <div>
                        <p class="text-gray-400 text-sm mb-1">User Account</p>
                        <p class="text-white font-semibold">Registered User (ID: {{ $order->user_id }})</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">Shipping Address</h2>
                <div class="text-white">
                    <p class="font-semibold">{{ $order->customer_name }}</p>
                    <p class="text-gray-300 mt-2">{{ $order->shipping_address }}</p>
                    <p class="text-gray-300">{{ $order->city }}@if($order->region), {{ $order->region }}@endif</p>
                </div>
            </div>

            @if($order->notes)
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">Customer Notes</h2>
                <p class="text-gray-300">{{ $order->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Right Column - Order Management -->
        <div class="space-y-6">
            <!-- Order Status -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">Order Status</h2>
                <form action="{{ route('admin.shop-orders.update-status', $order) }}" method="POST">
                    @csrf
                    <select name="status" class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 mb-3">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Payment Status -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">Payment Status</h2>
                <div class="mb-4">
                    <p class="text-gray-400 text-sm mb-1">Payment Method</p>
                    <p class="text-white font-semibold">{{ $order->payment_method === 'cash_on_delivery' ? 'Cash on Delivery' : 'Card Payment (Paystack)' }}</p>
                </div>
                <form action="{{ route('admin.shop-orders.update-payment', $order) }}" method="POST">
                    @csrf
                    <select name="payment_status" class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 mb-3">
                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Update Payment
                    </button>
                </form>
            </div>

            <!-- Admin Notes -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">Admin Notes</h2>
                <form action="{{ route('admin.shop-orders.add-notes', $order) }}" method="POST">
                    @csrf
                    <textarea name="admin_notes" rows="4" class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 mb-3" placeholder="Add internal notes about this order...">{{ $order->admin_notes }}</textarea>
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Save Notes
                    </button>
                </form>
            </div>

            <!-- Order Actions -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">Actions</h2>
                <div class="space-y-2">
                    <a href="mailto:{{ $order->customer_email }}?subject=Order {{ $order->order_number }}" class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition">
                        Email Customer
                    </a>
                    <form action="{{ route('admin.shop-orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Delete this order? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Delete Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
