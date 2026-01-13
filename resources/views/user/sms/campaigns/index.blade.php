@extends('layouts.app')

@section('title', 'SMS Campaigns')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('user.sms.dashboard') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold mb-4 inline-block">
                    ← Back to Dashboard
                </a>
                <h1 class="text-4xl font-bold gradient-text mb-2">📨 SMS Campaigns</h1>
                <p class="text-gray-600 dark:text-gray-400">View and manage your SMS campaigns</p>
            </div>
            <a href="{{ route('user.sms.campaigns.create') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition">
                + Send New SMS
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-lg">
                <p class="text-sm text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg">
                <p class="text-sm text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Filters & Search -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <form method="GET" action="{{ route('user.sms.campaigns.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Campaign</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Search by name..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition"
                    >
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select
                        name="status"
                        id="status"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition"
                    >
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                        Apply Filters
                    </button>
                    <a href="{{ route('user.sms.campaigns.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        @if($campaigns->count() > 0)
        <!-- Campaigns List -->
        <div class="space-y-4">
            @foreach($campaigns as $campaign)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <!-- Campaign Info -->
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mr-3">{{ $campaign->name }}</h3>

                                <!-- Status Badge -->
                                @if($campaign->status === 'completed')
                                    <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 text-xs font-semibold rounded-full">
                                        ✓ Completed
                                    </span>
                                @elseif($campaign->status === 'sent')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 text-xs font-semibold rounded-full">
                                        📤 Sent
                                    </span>
                                @elseif($campaign->status === 'scheduled')
                                    <span class="px-3 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300 text-xs font-semibold rounded-full">
                                        ⏰ Scheduled
                                    </span>
                                @elseif($campaign->status === 'processing')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 text-xs font-semibold rounded-full">
                                        ⏳ Processing
                                    </span>
                                @elseif($campaign->status === 'failed')
                                    <span class="px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 text-xs font-semibold rounded-full">
                                        ✗ Failed
                                    </span>
                                @elseif($campaign->status === 'cancelled')
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 text-xs font-semibold rounded-full">
                                        ⊘ Cancelled
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 text-xs font-semibold rounded-full">
                                        {{ ucfirst($campaign->status) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Message Preview -->
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">{{ $campaign->message }}</p>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Recipients</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($campaign->total_recipients) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Sent</p>
                                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ number_format($campaign->total_sent) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Delivered</p>
                                    <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ number_format($campaign->total_delivered) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Failed</p>
                                    <p class="text-lg font-bold text-red-600 dark:text-red-400">{{ number_format($campaign->total_failed) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Credits</p>
                                    <p class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ number_format($campaign->credits_used) }}</p>
                                </div>
                            </div>

                            <!-- Meta Info -->
                            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600 dark:text-gray-400">
                                @if($campaign->sender_id)
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        Sender: <span class="font-mono font-bold ml-1">{{ $campaign->sender_id }}</span>
                                    </span>
                                @endif
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Created: {{ $campaign->created_at->format('M d, Y g:i A') }}
                                </span>
                                @if($campaign->scheduled_at)
                                    <span class="flex items-center text-purple-600 dark:text-purple-400 font-semibold">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Scheduled: {{ $campaign->scheduled_at->format('M d, Y g:i A') }}
                                    </span>
                                @endif
                                @if($campaign->delivery_rate > 0)
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        Delivery Rate: <span class="font-bold ml-1">{{ $campaign->delivery_rate }}%</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-2 ml-4">
                            <a href="{{ route('user.sms.campaigns.show', $campaign->id) }}" class="p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition" title="View Details">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            @if($campaign->status === 'scheduled')
                                <form method="POST" action="{{ route('user.sms.campaigns.cancel', $campaign->id) }}" onsubmit="return confirm('Cancel this scheduled campaign?')">
                                    @csrf
                                    <button type="submit" class="p-2 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition" title="Cancel Campaign">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif

                            @if(in_array($campaign->status, ['draft', 'failed', 'cancelled']))
                                <form method="POST" action="{{ route('user.sms.campaigns.destroy', $campaign->id) }}" onsubmit="return confirm('Delete this campaign? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Delete Campaign">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($campaigns->hasPages())
            <div class="mt-8">
                {{ $campaigns->links() }}
            </div>
        @endif

        @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                @if(request()->has('search') || request()->has('status'))
                    No Campaigns Found
                @else
                    No SMS Campaigns Yet
                @endif
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                @if(request()->has('search') || request()->has('status'))
                    Try adjusting your filters to find what you're looking for.
                @else
                    Start sending SMS messages to your audience
                @endif
            </p>
            <a href="{{ route('user.sms.campaigns.create') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition">
                Send Your First SMS
            </a>
        </div>
        @endif
    </div>
</div>

<style>
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endsection
