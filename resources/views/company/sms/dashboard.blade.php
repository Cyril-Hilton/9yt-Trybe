@extends('layouts.company')

@section('title', 'SMS Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        SMS Dashboard
                    </h1>
                    <p class="mt-2 text-gray-600">Manage your bulk SMS campaigns and credits</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('organization.sms.wallet.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold rounded-xl hover:from-green-700 hover:to-emerald-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Buy Credits
                    </a>
                    <a href="{{ route('organization.sms.campaigns.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Send SMS
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Credits Balance -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold opacity-90 mb-1">Available Credits</h3>
                <p class="text-3xl font-black">{{ number_format($stats['credits_balance']) }}</p>
                <p class="text-xs opacity-75 mt-2">SMS credits available</p>
            </div>

            <!-- Total Campaigns -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold opacity-90 mb-1">Total Campaigns</h3>
                <p class="text-3xl font-black">{{ number_format($stats['total_campaigns']) }}</p>
                <p class="text-xs opacity-75 mt-2">All time campaigns</p>
            </div>

            <!-- Total Sent -->
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold opacity-90 mb-1">Messages Sent</h3>
                <p class="text-3xl font-black">{{ number_format($stats['total_sent']) }}</p>
                <p class="text-xs opacity-75 mt-2">Successfully sent</p>
            </div>

            <!-- Delivery Rate -->
            <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold opacity-90 mb-1">Delivery Rate</h3>
                <p class="text-3xl font-black">{{ $stats['total_sent'] > 0 ? round(($stats['total_delivered'] / $stats['total_sent']) * 100, 1) : 0 }}%</p>
                <p class="text-xs opacity-75 mt-2">{{ number_format($stats['total_delivered']) }} delivered</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Pie Chart: Message Status Distribution -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                    Message Status Distribution
                </h3>
                <div class="flex items-center justify-center" style="height: 300px;">
                    <canvas id="statusPieChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="w-4 h-4 bg-green-500 rounded-full mx-auto mb-1"></div>
                        <p class="text-xs text-gray-600">Delivered</p>
                        <p class="text-sm font-bold text-gray-900">{{ number_format($stats['total_delivered']) }}</p>
                    </div>
                    <div>
                        <div class="w-4 h-4 bg-yellow-500 rounded-full mx-auto mb-1"></div>
                        <p class="text-xs text-gray-600">Pending</p>
                        <p class="text-sm font-bold text-gray-900">{{ number_format($stats['total_sent'] - $stats['total_delivered'] - $stats['total_failed']) }}</p>
                    </div>
                    <div>
                        <div class="w-4 h-4 bg-red-500 rounded-full mx-auto mb-1"></div>
                        <p class="text-xs text-gray-600">Failed</p>
                        <p class="text-sm font-bold text-gray-900">{{ number_format($stats['total_failed']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Line Graph: Performance Over Time -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                    Performance Over Time
                </h3>
                <div style="height: 300px;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Campaigns -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h2 class="text-xl font-bold text-white">Recent Campaigns</h2>
                            </div>
                            <a href="{{ route('organization.sms.campaigns.index') }}" class="text-white hover:text-indigo-100 text-sm font-semibold">
                                View All →
                            </a>
                        </div>
                    </div>

                    <div class="p-6">
                        @if($recentCampaigns->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentCampaigns as $campaign)
                                    <a href="{{ route('organization.sms.campaigns.show', $campaign->id) }}" class="block p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200 group">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h3 class="font-bold text-gray-900 group-hover:text-indigo-600 mb-1">{{ $campaign->name }}</h3>
                                                <p class="text-sm text-gray-600 mb-2 line-clamp-1">{{ Str::limit($campaign->message, 100) }}</p>
                                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                        </svg>
                                                        {{ number_format($campaign->total_recipients) }} recipients
                                                    </span>
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $campaign->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                @if($campaign->status === 'completed')
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                        ✓ Completed
                                                    </span>
                                                @elseif($campaign->status === 'processing')
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                                        ⟳ Processing
                                                    </span>
                                                @elseif($campaign->status === 'scheduled')
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                                        ⏰ Scheduled
                                                    </span>
                                                @elseif($campaign->status === 'failed')
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                        ✗ Failed
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                                        {{ ucfirst($campaign->status) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-gray-600 font-semibold mb-2">No campaigns yet</p>
                                <p class="text-sm text-gray-500 mb-4">Start sending SMS to your audience</p>
                                <a href="{{ route('organization.sms.campaigns.send-bulk') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200">
                                    Create Campaign
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Stats -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Quick Actions
                    </h2>
                    <div class="space-y-3">
                        <a href="{{ route('organization.sms.campaigns.send-bulk') }}" class="flex items-center p-3 border-2 border-gray-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200 group">
                            <div class="p-2 bg-indigo-100 rounded-lg group-hover:bg-indigo-200">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </div>
                            <span class="ml-3 font-semibold text-gray-700 group-hover:text-indigo-600">Send Bulk SMS</span>
                        </a>

                        <a href="{{ route('organization.sms.contacts.import') }}" class="flex items-center p-3 border-2 border-gray-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200 group">
                            <div class="p-2 bg-green-100 rounded-lg group-hover:bg-green-200">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <span class="ml-3 font-semibold text-gray-700 group-hover:text-indigo-600">Import Contacts</span>
                        </a>

                        <a href="{{ route('organization.sms.sender-ids.index') }}" class="flex items-center p-3 border-2 border-gray-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200 group">
                            <div class="p-2 bg-purple-100 rounded-lg group-hover:bg-purple-200">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <span class="ml-3 font-semibold text-gray-700 group-hover:text-indigo-600">Manage Sender IDs</span>
                        </a>
                    </div>
                </div>

                <!-- Credit Usage -->
                <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                    <h2 class="text-lg font-bold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Credit Usage
                    </h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm opacity-90">Total Purchased</span>
                            <span class="text-xl font-black">{{ number_format($stats['total_purchased']) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm opacity-90">Total Used</span>
                            <span class="text-xl font-black">{{ number_format($stats['total_used']) }}</span>
                        </div>
                        <div class="h-px bg-white/20 my-3"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold">Remaining</span>
                            <span class="text-2xl font-black">{{ number_format($stats['credits_balance']) }}</span>
                        </div>
                    </div>
                    <a href="{{ route('organization.sms.wallet.index') }}" class="mt-4 block w-full text-center py-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl font-bold transition-all duration-200">
                        Buy More Credits
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textColor = '#374151';
    const gridColor = '#e5e7eb';

    // Pie Chart: Message Status Distribution
    const pieCtx = document.getElementById('statusPieChart');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'Pending', 'Failed'],
                datasets: [{
                    data: [
                        {{ $stats['total_delivered'] }},
                        {{ $stats['total_sent'] - $stats['total_delivered'] - $stats['total_failed'] }},
                        {{ $stats['total_failed'] }}
                    ],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',   // Green
                        'rgba(234, 179, 8, 0.8)',   // Yellow
                        'rgba(239, 68, 68, 0.8)'    // Red
                    ],
                    borderColor: [
                        'rgba(34, 197, 94, 1)',
                        'rgba(234, 179, 8, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: textColor,
                            padding: 15,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Line Graph: Performance Over Time
    const lineCtx = document.getElementById('performanceChart');
    if (lineCtx) {
        @php
            // Get last 12 months of campaign data
            // Get last 12 months of campaign data
            $campaigns = \App\Models\SmsCampaign::where('owner_id', auth('company')->id())
                ->where('owner_type', get_class(auth('company')->user()))
                ->where('created_at', '>=', now()->subMonths(12))
                ->get();

            $monthlyData = $campaigns->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->created_at)->format('Y-m');
            })->map(function ($row, $key) {
                return [
                    'month' => $key,
                    'total' => $row->count(),
                    'sent' => $row->whereIn('status', ['sent', 'completed'])->count(),
                    'failed' => $row->where('status', 'failed')->count(),
                ];
            })->sortBy('month');

            $months = $monthlyData->pluck('month')->map(function($m) {
                return \Carbon\Carbon::parse($m)->format('M Y');
            });
            $totalCampaigns = $monthlyData->pluck('total');
            $sentCampaigns = $monthlyData->pluck('sent');
            $failedCampaigns = $monthlyData->pluck('failed');
        @endphp

        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [
                    {
                        label: 'Total Campaigns',
                        data: {!! json_encode($totalCampaigns) !!},
                        borderColor: 'rgba(99, 102, 241, 1)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Sent/Completed',
                        data: {!! json_encode($sentCampaigns) !!},
                        borderColor: 'rgba(34, 197, 94, 1)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 2,
                        tension: 0.4
                    },
                    {
                        label: 'Failed',
                        data: {!! json_encode($failedCampaigns) !!},
                        borderColor: 'rgba(239, 68, 68, 1)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: textColor,
                            padding: 15,
                            font: {
                                size: 11,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: textColor,
                        bodyColor: textColor,
                        borderColor: gridColor,
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: textColor,
                            precision: 0
                        },
                        grid: {
                            color: gridColor
                        }
                    },
                    x: {
                        ticks: {
                            color: textColor,
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            color: gridColor
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
