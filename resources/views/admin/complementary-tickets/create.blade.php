@extends('layouts.admin')

@section('title', 'Issue Complementary Ticket')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.complementary-tickets.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold mb-4 inline-block">
                ← Back to Complementary Tickets
            </a>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Issue Complementary Ticket</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Issue a complementary ticket to a single recipient</p>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg">
                <p class="text-sm text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('admin.complementary-tickets.store') }}" id="ticketForm">
                @csrf

                <!-- Event Search -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Search Event <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="eventSearch" autocomplete="off" placeholder="Click to see all events or start typing to search..."
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('event_id') border-red-500 @enderror">

                        <!-- Search Results Dropdown - Scrollable -->
                        <div id="searchResults" class="hidden absolute z-10 w-full mt-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-96 overflow-y-auto"></div>
                    </div>

                    <!-- Selected Event Display -->
                    <div id="selectedEvent" class="hidden mt-4 p-4 bg-indigo-50 dark:bg-indigo-900/20 border-2 border-indigo-200 dark:border-indigo-700 rounded-lg">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-indigo-900 dark:text-indigo-300" id="selectedEventName"></p>
                                <p class="text-xs text-indigo-700 dark:text-indigo-400 mt-1" id="selectedEventDetails"></p>
                                <div class="mt-2 text-xs text-indigo-600 dark:text-indigo-400">
                                    <span class="font-semibold">General:</span> <span id="selectedEventGeneral">GHS 0</span> |
                                    <span class="font-semibold">VIP:</span> <span id="selectedEventVIP">GHS 0</span>
                                </div>
                            </div>
                            <button type="button" onclick="clearEventSelection()" class="ml-4 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="event_id" id="eventId" value="{{ old('event_id') }}">
                    @error('event_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Recipient Information -->
                <div class="mb-6 p-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recipient Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Recipient Name -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('recipient_name') border-red-500 @enderror">
                            @error('recipient_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="recipient_email" value="{{ old('recipient_email') }}" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('recipient_email') border-red-500 @enderror">
                            @error('recipient_email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Number
                            </label>
                            <input type="text" name="recipient_phone" value="{{ old('recipient_phone') }}" placeholder="0241234567"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('recipient_phone') border-red-500 @enderror">
                            @error('recipient_phone')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Ticket Details -->
                <div class="mb-6 p-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ticket Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Ticket Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Ticket Type <span class="text-red-500">*</span>
                            </label>
                            <select name="ticket_type" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('ticket_type') border-red-500 @enderror">
                                <option value="general" {{ old('ticket_type') == 'general' ? 'selected' : '' }}>General</option>
                                <option value="vip" {{ old('ticket_type') == 'vip' ? 'selected' : '' }}>VIP</option>
                            </select>
                            @error('ticket_type')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="10" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('quantity') border-red-500 @enderror">
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purpose -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Purpose
                            </label>
                            <select name="purpose"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('purpose') border-red-500 @enderror">
                                <option value="">Select purpose...</option>
                                <option value="media" {{ old('purpose') == 'media' ? 'selected' : '' }}>Media</option>
                                <option value="promoter" {{ old('purpose') == 'promoter' ? 'selected' : '' }}>Promoter</option>
                                <option value="volunteer" {{ old('purpose') == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                                <option value="influencer" {{ old('purpose') == 'influencer' ? 'selected' : '' }}>Influencer</option>
                                <option value="student" {{ old('purpose') == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="sponsor" {{ old('purpose') == 'sponsor' ? 'selected' : '' }}>Sponsor</option>
                                <option value="staff" {{ old('purpose') == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="other" {{ old('purpose') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('purpose')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes and Visibility -->
                <div class="mb-6 p-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Additional Information</h3>

                    <!-- Notes -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes (Internal)
                        </label>
                        <textarea name="notes" rows="3" placeholder="Optional notes for internal reference..."
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Visibility Toggle -->
                    <div>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="visible_to_organizer" value="1" {{ old('visible_to_organizer') ? 'checked' : '' }}
                                class="w-5 h-5 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                Make this ticket visible to the event organizer
                            </span>
                        </label>
                        <p class="mt-1 ml-8 text-xs text-gray-500 dark:text-gray-400">
                            By default, complementary tickets are hidden from organizers. Check this box to make it visible in their dashboard.
                        </p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.complementary-tickets.index') }}" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-lg transition">
                        Issue Complementary Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Event Search JavaScript -->
<script>
let searchTimeout;
let allEvents = []; // Store all events for client-side filtering
const searchInput = document.getElementById('eventSearch');
const searchResults = document.getElementById('searchResults');
const selectedEventDiv = document.getElementById('selectedEvent');
const eventIdInput = document.getElementById('eventId');

// Load all events initially
function loadAllEvents() {
    fetch(`{{ route('admin.complementary-tickets.search-events') }}`)
        .then(response => response.json())
        .then(data => {
            allEvents = data;
            displayEvents(data);
        })
        .catch(error => {
            console.error('Error fetching events:', error);
            searchResults.innerHTML = '<div class="p-4 text-sm text-red-500">Error loading events</div>';
            searchResults.classList.remove('hidden');
        });
}

// Display events in dropdown
function displayEvents(events) {
    if (events.length === 0) {
        searchResults.innerHTML = '<div class="p-4 text-sm text-gray-500 dark:text-gray-400">No events found</div>';
    } else {
        searchResults.innerHTML = events.map(event => `
            <div class="p-4 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0"
                 onclick="selectEvent(${event.id}, '${event.name.replace(/'/g, "\\'")}', '${event.date}', '${event.organizer.replace(/'/g, "\\'")}', ${event.general_price}, ${event.vip_price})">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">${event.name}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">${event.date} • ${event.organizer}</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                    General: GHS ${event.general_price} | VIP: GHS ${event.vip_price}
                </p>
            </div>
        `).join('');
    }
    searchResults.classList.remove('hidden');
}

// Show all events when clicking on input
searchInput.addEventListener('focus', function() {
    if (allEvents.length === 0) {
        loadAllEvents();
    } else {
        displayEvents(allEvents);
    }
});

// Filter events as user types
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim().toLowerCase();

    // If no query, show all events
    if (query.length === 0) {
        displayEvents(allEvents);
        return;
    }

    // Client-side filtering for instant results
    searchTimeout = setTimeout(() => {
        const filtered = allEvents.filter(event =>
            event.name.toLowerCase().includes(query)
        );

        // Sort: prioritize matches at the start
        filtered.sort((a, b) => {
            const aStarts = a.name.toLowerCase().startsWith(query);
            const bStarts = b.name.toLowerCase().startsWith(query);
            if (aStarts && !bStarts) return -1;
            if (!aStarts && bStarts) return 1;
            return 0;
        });

        displayEvents(filtered);
    }, 100); // Very fast filtering - 100ms
});

function selectEvent(id, name, date, organizer, generalPrice, vipPrice) {
    eventIdInput.value = id;
    searchInput.value = name;
    searchResults.classList.add('hidden');

    document.getElementById('selectedEventName').textContent = name;
    document.getElementById('selectedEventDetails').textContent = `${date} • ${organizer}`;
    document.getElementById('selectedEventGeneral').textContent = `GHS ${generalPrice}`;
    document.getElementById('selectedEventVIP').textContent = `GHS ${vipPrice}`;

    selectedEventDiv.classList.remove('hidden');
}

function clearEventSelection() {
    eventIdInput.value = '';
    searchInput.value = '';
    selectedEventDiv.classList.add('hidden');
    searchResults.classList.add('hidden');
}

// Hide dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
        searchResults.classList.add('hidden');
    }
});
</script>
@endsection
