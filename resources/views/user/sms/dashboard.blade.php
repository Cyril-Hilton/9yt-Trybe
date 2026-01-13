@extends('layouts.app')

@section('title', 'Bulk SMS')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold gradient-text mb-2">📱 Bulk SMS</h1>
            <p class="text-gray-600 dark:text-gray-400">Send SMS to your contacts using Excel or Instant messaging</p>
        </div>

        <!-- SMS Credit Balance Card -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm mb-2">SMS Credit Balance</p>
                    <h2 class="text-5xl font-bold">{{ number_format($creditBalance->balance ?? 0) }}</h2>
                    <p class="text-green-100 text-sm mt-2">Credits Available</p>
                </div>
                <div class="hidden md:block">
                    <svg class="w-24 h-24 text-white opacity-20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 11H7V9h2v2zm4 0h-2V9h2v2zm4 0h-2V9h2v2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-6 flex flex-wrap gap-4">
                <a href="{{ route('user.sms.wallet.index') }}" class="px-6 py-3 bg-white text-green-600 font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition">
                    💳 Buy Credits
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Excel Messaging -->
            <a href="{{ route('user.sms.campaigns.create') }}?type=excel" class="block bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all p-8 border-2 border-transparent hover:border-blue-500">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg mr-4">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Excel Messaging</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Upload CSV/Excel file with contacts</p>
                    </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300 text-sm">Upload a CSV or Excel file containing phone numbers to send bulk SMS messages.</p>
            </a>

            <!-- Instant Messaging -->
            <a href="{{ route('user.sms.campaigns.create') }}?type=instant" class="block bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all p-8 border-2 border-transparent hover:border-purple-500">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg mr-4">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Instant Messaging</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Enter numbers separated by commas</p>
                    </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300 text-sm">Quickly send SMS by entering phone numbers directly, separated by commas.</p>
            </a>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Pie Chart: Message Status -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📊 Message Status Distribution</h3>
                <div class="flex items-center justify-center" style="height: 300px;">
                    <canvas id="statusPieChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="w-4 h-4 bg-green-500 rounded-full mx-auto mb-1"></div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Delivered</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $deliveredMessages }}</p>
                    </div>
                    <div>
                        <div class="w-4 h-4 bg-yellow-500 rounded-full mx-auto mb-1"></div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Pending</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $totalMessagesSent - $deliveredMessages - $failedMessages }}</p>
                    </div>
                    <div>
                        <div class="w-4 h-4 bg-red-500 rounded-full mx-auto mb-1"></div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Failed</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $failedMessages }}</p>
                    </div>
                </div>
            </div>

            <!-- Line Graph: Performance Over Time -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📈 Performance Over Time</h3>
                <div style="height: 300px;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Total Campaigns</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalCampaigns }}</h3>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                        <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Messages Sent</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalMessagesSent }}</h3>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Delivered</p>
                        <h3 class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $deliveredMessages }}</h3>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Failed</p>
                        <h3 class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $failedMessages }}</h3>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Campaigns -->
        @if($recentCampaigns->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Recent Campaigns</h2>
                    <a href="{{ route('user.sms.campaigns.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold text-sm">View All →</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Campaign</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recipients</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($recentCampaigns as $campaign)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $campaign->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($campaign->message, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $campaign->recipients_count }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($campaign->status === 'sent') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($campaign->status === 'scheduled') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                    @elseif($campaign->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @endif">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $campaign->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('user.sms.campaigns.show', $campaign->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-semibold">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Campaigns Yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Get started by creating your first SMS campaign</p>
            <a href="{{ route('user.sms.campaigns.create') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition">
                Create Campaign
            </a>
        </div>
        @endif

        <!-- Sender ID Notice -->
        <div class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-6 rounded-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-yellow-800 dark:text-yellow-300">Sender ID Required</h4>
                    <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-1">You need an approved Sender ID before sending SMS. This is the name recipients will see when they receive your messages.</p>
                </div>
                <a href="{{ route('user.sms.sender-ids.index') }}" class="ml-4 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition whitespace-nowrap">
                    Manage Sender IDs
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDarkMode = document.documentElement.classList.contains('dark');
    const textColor = isDarkMode ? '#e5e7eb' : '#374151';
    const gridColor = isDarkMode ? '#374151' : '#e5e7eb';

    // Pie Chart: Message Status Distribution
    const pieCtx = document.getElementById('statusPieChart');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'Pending', 'Failed'],
                datasets: [{
                    data: [
                        {{ $deliveredMessages }},
                        {{ $totalMessagesSent - $deliveredMessages - $failedMessages }},
                        {{ $failedMessages }}
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
            $monthlyData = \App\Models\SmsCampaign::where('owner_id', auth()->id())
                ->where('owner_type', get_class(auth()->user()))
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total,
                    SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

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
                        label: 'Sent',
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
                        backgroundColor: isDarkMode ? 'rgba(31, 41, 55, 0.95)' : 'rgba(255, 255, 255, 0.95)',
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

<style>
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endsection
