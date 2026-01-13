@extends('layouts.admin')

@section('title', 'Shop Orders Management')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Shop Orders</h1>
        <p class="text-gray-400">Manage customer orders and fulfillment</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-600 bg-opacity-20 border border-green-600 rounded-lg">
        <p class="text-green-400 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @php
            $total = \App\Models\ShopOrder::count();
            $pending = \App\Models\ShopOrder::where('status', 'pending')->count();
            $processing = \App\Models\ShopOrder::where('status', 'processing')->count();
            $delivered = \App\Models\ShopOrder::where('status', 'delivered')->count();
        @endphp
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700">
            <p class="text-gray-400 text-sm mb-1">Total Orders</p>
            <p class="text-3xl font-bold text-white">{{ $total }}</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-xl p-6">
            <p class="text-yellow-100 text-sm mb-1">Pending</p>
            <p class="text-3xl font-bold text-white">{{ $pending }}</p>
        </div>
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6">
            <p class="text-blue-100 text-sm mb-1">Processing</p>
            <p class="text-3xl font-bold text-white">{{ $processing }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6">
            <p class="text-green-100 text-sm mb-1">Delivered</p>
            <p class="text-3xl font-bold text-white">{{ $delivered }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-xl p-4 mb-6 border border-gray-700">
        <form action="{{ route('admin.shop-orders.index') }}" method="GET" class="flex flex-wrap gap-4">
            <select name="status" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ $status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ $status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <select name="payment_status" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="all" {{ $paymentStatus === 'all' ? 'selected' : '' }}>All Payment Status</option>
                <option value="pending" {{ $paymentStatus === 'pending' ? 'selected' : '' }}>Payment Pending</option>
                <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ $paymentStatus === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ $paymentStatus === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Filter
            </button>

            @if(request()->hasAny(['status', 'payment_status']))
            <a href="{{ route('admin.shop-orders.index') }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-gray-800 rounded-xl overflow-x-auto border border-gray-700">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-750 transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-white">{{ $order->order_number }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-white font-medium">{{ $order->customer_name }}</div>
                        <div class="text-xs text-gray-400">{{ $order->customer_email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-white">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-cyan-400">GH₵{{ number_format($order->total, 2) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($order->payment_status === 'paid') bg-green-600 text-white
                            @elseif($order->payment_status === 'pending') bg-yellow-600 text-white
                            @elseif($order->payment_status === 'failed') bg-red-600 text-white
                            @else bg-gray-600 text-white
                            @endif">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($order->status === 'delivered') bg-green-600 text-white
                            @elseif($order->status === 'processing') bg-blue-600 text-white
                            @elseif($order->status === 'shipped') bg-indigo-600 text-white
                            @elseif($order->status === 'cancelled') bg-red-600 text-white
                            @else bg-yellow-600 text-white
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-400">{{ $order->created_at->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.shop-orders.show', $order) }}" class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 transition">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-20 text-center">
                        <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-400 mb-2">No orders found</h3>
                        <p class="text-gray-500">Orders will appear here when customers make purchases</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="mt-8">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
