@extends('layouts.admin')

@section('title', 'SMS Campaigns')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        SMS Campaigns
                    </h1>
                    <p class="mt-2 text-gray-600">Manage and track admin SMS campaigns</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.sms.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('admin.sms.send-single') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold rounded-xl hover:from-blue-700 hover:to-cyan-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Send Single SMS
                    </a>
                    <a href="{{ route('admin.sms.send-bulk') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                        </svg>
                        Send Bulk SMS
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 p-6 mb-6">
            <form method="GET" action="{{ route('admin.sms.campaigns.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Campaign name..." class="w-full px-4 py-2 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full px-4 py-2 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="single" {{ request('type') === 'single' ? 'selected' : '' }}>Single SMS</option>
                        <option value="bulk" {{ request('type') === 'bulk' ? 'selected' : '' }}>Bulk SMS</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.sms.campaigns.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Campaigns ({{ $campaigns->total() }})
                </h2>
            </div>

            <div class="p-6">
                @if($campaigns->count() > 0)
                    <div class="space-y-4">
                        @foreach($campaigns as $campaign)
                            <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-indigo-300 hover:shadow-lg transition-all duration-200">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-xl font-bold text-gray-900">{{ $campaign->name }}</h3>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $campaign->type === 'single' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ ucfirst($campaign->type) }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                                {{ ucfirst($campaign->status) }}
                                            </span>
                                        </div>

                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $campaign->message }}</p>

                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 mb-1">Recipients</p>
                                                <p class="text-lg font-bold text-gray-900">{{ number_format($campaign->total_recipients) }}</p>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 mb-1">Sent</p>
                                                <p class="text-lg font-bold text-blue-600">{{ number_format($campaign->total_sent) }}</p>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 mb-1">Delivered</p>
                                                <p class="text-lg font-bold text-green-600">{{ number_format($campaign->total_delivered) }}</p>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <p class="text-xs text-gray-500 mb-1">Credits Used</p>
                                                <p class="text-lg font-bold text-purple-600">{{ number_format($campaign->credits_used) }}</p>
                                            </div>
                                        </div>

                                        <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500">
                                            @if($campaign->sender_id)
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                    </svg>
                                                    Sender: {{ $campaign->sender_id }}
                                                </span>
                                            @endif
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $campaign->created_at->diffForHumans() }}
                                            </span>
                                            @if($campaign->scheduled_at)
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    Scheduled: {{ $campaign->scheduled_at->format('M d, Y h:i A') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="ml-4">
                                        <a href="{{ route('admin.sms.campaigns.show', $campaign->id) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-colors duration-200 text-center">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $campaigns->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-gray-600 font-semibold mb-2">No campaigns found</p>
                        <p class="text-sm text-gray-500 mb-4">Start by creating your first SMS campaign</p>
                        <a href="{{ route('admin.sms.send-bulk') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create Campaign
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
