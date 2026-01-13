@extends('layouts.company')

@section('title', 'Analytics - ' . $event->title)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <nav class="text-sm mb-4">
                <a href="{{ route('organization.events.index') }}" class="text-indigo-600 hover:text-indigo-800">Events</a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="{{ route('organization.events.show', $event) }}" class="text-indigo-600 hover:text-indigo-800">{{ Str::limit($event->title, 30) }}</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-600">Analytics</span>
            </nav>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Event Analytics</h1>
                    <p class="mt-2 text-gray-600">{{ $event->title }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Event Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $event->formatted_date }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Views -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Views</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_views']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format($stats['unique_viewers']) }} unique</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Likes -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Likes</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_likes']) }}</p>
                    </div>
                    <div class="p-3 bg-pink-100 rounded-lg">
                        <svg class="w-8 h-8 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tickets Sold -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Tickets Sold</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_tickets_sold']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format($stats['total_orders']) }} orders</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Revenue</p>
                        <p class="text-3xl font-bold text-green-600">GH₵{{ number_format($stats['total_revenue'], 2) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Orders Breakdown -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Orders Status</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Completed</span>
                        <span class="font-semibold text-green-600">{{ number_format($stats['total_orders']) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Pending</span>
                        <span class="font-semibold text-yellow-600">{{ number_format($stats['pending_orders']) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Failed</span>
                        <span class="font-semibold text-red-600">{{ number_format($stats['failed_orders']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Check-in Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Check-in Status</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Checked In</span>
                        <span class="font-semibold text-green-600">{{ number_format($stats['checked_in']) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Not Checked In</span>
                        <span class="font-semibold text-gray-600">{{ number_format($stats['not_checked_in']) }}</span>
                    </div>
                    @if($stats['total_tickets_sold'] > 0)
                    <div class="pt-2 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Check-in Rate</span>
                            <span class="font-semibold text-indigo-600">{{ number_format(($stats['checked_in'] / $stats['total_tickets_sold']) * 100, 1) }}%</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Engagement -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Engagement</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Views to Likes</span>
                        <span class="font-semibold text-indigo-600">{{ $stats['total_views'] > 0 ? number_format(($stats['total_likes'] / $stats['total_views']) * 100, 1) : 0 }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Conversion Rate</span>
                        <span class="font-semibold text-green-600">{{ $stats['total_views'] > 0 ? number_format(($stats['total_tickets_sold'] / $stats['total_views']) * 100, 1) : 0 }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Avg Tickets/Order</span>
                        <span class="font-semibold text-gray-900">{{ $stats['total_orders'] > 0 ? number_format($stats['total_tickets_sold'] / $stats['total_orders'], 1) : 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket Sales Breakdown -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Ticket Sales Breakdown</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($ticketSales as $ticket)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $ticket['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($ticket['type'] === 'paid') bg-green-100 text-green-800
                                    @elseif($ticket['type'] === 'free') bg-blue-100 text-blue-800
                                    @else bg-purple-100 text-purple-800
                                    @endif">
                                    {{ ucfirst($ticket['type']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($ticket['type'] === 'free')
                                    <span class="text-green-600 font-semibold">FREE</span>
                                @else
                                    GH₵{{ number_format($ticket['price'], 2) }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ number_format($ticket['sold']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ticket['total_quantity'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">GH₵{{ number_format($ticket['revenue'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No tickets configured</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Sales Trend (Last 30 Days)</h2>
            @if($dailySales->count() > 0)
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
            @else
            <div class="text-center py-12 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p>No sales data available yet</p>
            </div>
            @endif
        </div>

        <!-- Traffic Sources -->
        @if($viewSources->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Top Traffic Sources</h2>
            <div class="space-y-3">
                @foreach($viewSources as $source)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-indigo-600 rounded-full"></div>
                        <span class="text-sm text-gray-900 font-medium">{{ $source->source }}</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-600">{{ number_format($source->views) }} views</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@if($dailySales->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailySales->pluck('date')->map(function($date) { return date('M j', strtotime($date)); })) !!},
        datasets: [{
            label: 'Revenue (GH₵)',
            data: {!! json_encode($dailySales->pluck('revenue')) !!},
            borderColor: 'rgb(99, 102, 241)',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            tension: 0.3,
            fill: true
        }, {
            label: 'Orders',
            data: {!! json_encode($dailySales->pluck('orders')) !!},
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.3,
            fill: true,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Revenue (GH₵)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Orders'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            }
        }
    }
});
</script>
@endif
@endsection
