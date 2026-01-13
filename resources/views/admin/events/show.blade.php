@extends('layouts.admin')

@section('title', 'Review Event - ' . $event->title)

@section('content')
<div class="p-8">
    <div class="mb-6">
        <nav class="text-sm mb-4">
            <a href="{{ route('admin.events.index') }}" class="text-indigo-400 hover:text-indigo-300">Events</a>
            <span class="mx-2 text-gray-500">/</span>
            <span class="text-gray-300">{{ Str::limit($event->title, 30) }}</span>
        </nav>

        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold text-white">{{ $event->title }}</h1>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                        @if($event->status === 'approved') bg-green-900 text-green-200
                        @elseif($event->status === 'pending') bg-yellow-900 text-yellow-200
                        @elseif($event->status === 'rejected') bg-red-900 text-red-200
                        @else bg-gray-700 text-gray-300
                        @endif">
                        {{ ucfirst($event->status) }}
                    </span>
                </div>
                <p class="text-gray-400">
                    By {{ $event->company ? $event->company->name : 'External Event (No Organizer)' }}
                </p>
            </div>

            @if($event->isPending())
            <div class="flex space-x-3">
                <form action="{{ route('admin.events.approve', $event) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                        Approve Event
                    </button>
                </form>
                <button onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                        class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">
                    Reject Event
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Event Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Banner -->
            @if($event->banner_image)
            <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700 bg-gradient-to-br from-indigo-600 to-blue-600">
                <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="w-full h-auto">
            </div>
            @endif

            <!-- Basic Info -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">Event Information</h2>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-400">Summary</p>
                        <p class="text-white">{{ $event->summary ?? 'No summary provided' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-400">Date & Time</p>
                        <p class="text-white">{{ $event->formatted_date }}</p>
                        <p class="text-gray-300">{{ $event->formatted_time }}</p>
                    </div>

                    @if($event->region)
                    <div>
                        <p class="text-sm text-gray-400">Region</p>
                        <p class="text-white">{{ $event->region }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-400">Location</p>
                        @if($event->location_type === 'venue')
                        <p class="text-white">{{ $event->venue_name }}</p>
                        @if($event->venue_address)
                        <p class="text-gray-300">{{ $event->venue_address }}</p>
                        @endif
                        @elseif($event->location_type === 'online')
                        <p class="text-white">Online Event</p>
                        @if($event->online_platform)
                        <p class="text-gray-300">Platform: {{ ucfirst($event->online_platform) }}</p>
                        @endif
                        @else
                        <p class="text-white">To Be Announced</p>
                        @endif
                    </div>

                    @if($event->overview)
                    <div>
                        <p class="text-sm text-gray-400 mb-2">Overview</p>
                        <div class="text-gray-300 whitespace-pre-line">{{ $event->overview }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tickets -->
            @if($event->tickets->count() > 0)
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">Tickets</h2>
                <div class="space-y-3">
                    @foreach($event->tickets as $ticket)
                    <div class="flex items-center justify-between p-4 bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-semibold text-white">{{ $ticket->name }}</p>
                            @if($ticket->description)
                            <p class="text-sm text-gray-400">{{ $ticket->description }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-indigo-400">{{ $ticket->formatted_price }}</p>
                            @if($ticket->quantity)
                            <p class="text-xs text-gray-400">Qty: {{ $ticket->quantity }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- FAQs -->
            @if($event->faqs->count() > 0)
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">FAQs</h2>
                <div class="space-y-4">
                    @foreach($event->faqs as $faq)
                    <div>
                        <p class="font-semibold text-white mb-2">{{ $faq->question }}</p>
                        <p class="text-gray-400">{{ $faq->answer }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Organization Info -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-bold text-white mb-4">Organization</h3>
                @if($event->company)
                    <div class="flex items-center mb-4">
                        @if($event->company->logo)
                        <img src="{{ $event->company->logo_url }}" alt="{{ $event->company->name }}" class="w-12 h-12 rounded-full mr-3">
                        @endif
                        <div>
                            <p class="font-semibold text-white">{{ $event->company->name }}</p>
                            <p class="text-sm text-gray-400">{{ $event->company->email }}</p>
                        </div>
                    </div>
                    @if($event->company->website)
                    <a href="{{ $event->company->website }}" target="_blank" class="text-sm text-indigo-400 hover:text-indigo-300">
                        {{ $event->company->website }}
                    </a>
                    @endif
                @else
                    <p class="text-gray-400 text-sm">External event - no organizer assigned</p>
                @endif
            </div>

            <!-- Stats -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-bold text-white mb-4">Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Views</span>
                        <span class="font-semibold text-white">{{ $event->views_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Likes</span>
                        <span class="font-semibold text-white">{{ $event->likes_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tickets Sold</span>
                        <span class="font-semibold text-white">{{ $event->tickets_sold }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Revenue</span>
                        <span class="font-semibold text-white">GH₵{{ number_format($event->total_revenue, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Fee Settings -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-bold text-white mb-4">Fee Settings</h3>
                <p class="text-gray-300"><strong>{{ ucfirst($event->fee_bearer) }}</strong> pays the fees</p>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-xl max-w-lg w-full p-6 border border-gray-700">
        <h3 class="text-2xl font-bold text-white mb-4">Reject Event</h3>
        <p class="text-gray-400 mb-4">Please provide a reason for rejecting this event:</p>

        <form action="{{ route('admin.events.reject', $event) }}" method="POST">
            @csrf
            <textarea name="rejection_reason" rows="4" required
                      class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none mb-4"
                      placeholder="Explain why this event cannot be approved..."></textarea>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Reject Event
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
