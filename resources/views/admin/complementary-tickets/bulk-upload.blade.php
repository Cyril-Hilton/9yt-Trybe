@extends('layouts.admin')

@section('title', 'Bulk Upload Complementary Tickets')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.complementary-tickets.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold mb-4 inline-block">
                ← Back to Complementary Tickets
            </a>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Bulk Upload Complementary Tickets</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Upload an Excel file to issue multiple tickets at once</p>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg">
                <p class="text-sm text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Instructions Card -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-xl p-6 mb-8">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-blue-900 dark:text-blue-300 mb-2">How to Bulk Upload</h3>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800 dark:text-blue-400">
                        <li>Download the CSV template using the button below</li>
                        <li>Fill in the recipient details in the template
                            <ul class="list-disc list-inside ml-6 mt-1 text-xs space-y-1">
                                <li><strong>Name:</strong> Required - Full name of recipient</li>
                                <li><strong>Email:</strong> Required - Recipient's email address</li>
                                <li><strong>Phone:</strong> Optional - Phone number (e.g., 0241234567)</li>
                                <li><strong>Ticket Type:</strong> Required - Either "general" or "vip"</li>
                                <li><strong>Quantity:</strong> Required - Number between 1 and 10</li>
                                <li><strong>Notes:</strong> Optional - Internal notes</li>
                            </ul>
                        </li>
                        <li>Select the event from the dropdown below</li>
                        <li>Upload your completed Excel file (CSV, XLS, or XLSX)</li>
                        <li>Choose purpose and visibility settings</li>
                        <li>Click "Upload and Issue Tickets"</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Download Template Button -->
        <div class="mb-8 text-center">
            <a href="{{ route('admin.complementary-tickets.template-download') }}" class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-lg transition">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download CSV Template
            </a>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">This template includes sample data to guide you</p>
        </div>

        <!-- Upload Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <form method="POST" action="{{ route('admin.complementary-tickets.bulk-store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Event Search (Same as single form) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Event <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="eventSearch" autocomplete="off" placeholder="Start typing event name..."
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 @error('event_id') border-red-500 @enderror">

                        <!-- Search Results Dropdown -->
                        <div id="searchResults" class="hidden absolute z-10 w-full mt-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-64 overflow-y-auto"></div>
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

                <!-- File Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Upload Excel File <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label for="excel_file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-800 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-12 h-12 mb-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">CSV, XLS, XLSX</p>
                                <p id="fileName" class="mt-2 text-sm font-semibold text-indigo-600 dark:text-indigo-400"></p>
                            </div>
                            <input id="excel_file" name="excel_file" type="file" accept=".csv,.xls,.xlsx" class="hidden" onchange="displayFileName()" required />
                        </label>
                    </div>
                    @error('excel_file')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purpose (applies to all tickets in upload) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Purpose (applies to all tickets)
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

                <!-- Visibility Toggle -->
                <div class="mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="visible_to_organizer" value="1" {{ old('visible_to_organizer') ? 'checked' : '' }}
                            class="w-5 h-5 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                            Make all uploaded tickets visible to the event organizer
                        </span>
                    </label>
                    <p class="mt-1 ml-8 text-xs text-gray-500 dark:text-gray-400">
                        By default, complementary tickets are hidden from organizers. Check this box to make all uploaded tickets visible.
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.complementary-tickets.index') }}" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-lg transition">
                        Upload and Issue Tickets
                    </button>
                </div>
            </form>
        </div>

        <!-- Tips Card -->
        <div class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-200 dark:border-yellow-700 rounded-xl p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-yellow-900 dark:text-yellow-300 mb-2">Important Tips</h3>
                    <ul class="list-disc list-inside space-y-1 text-sm text-yellow-800 dark:text-yellow-400">
                        <li>The first row of your Excel file should be the header (will be skipped)</li>
                        <li>Email notifications will be sent automatically to all recipients</li>
                        <li>If any row has errors, it will be skipped and reported to you</li>
                        <li>No file size limit</li>
                        <li>You can upload up to 1000 tickets at once</li>
                        <li>Each recipient will receive a unique QR code and ticket reference</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Search JavaScript -->
<script>
let searchTimeout;
const searchInput = document.getElementById('eventSearch');
const searchResults = document.getElementById('searchResults');
const selectedEventDiv = document.getElementById('selectedEvent');
const eventIdInput = document.getElementById('eventId');

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();

    if (query.length < 2) {
        searchResults.classList.add('hidden');
        return;
    }

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('admin.complementary-tickets.search-events') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    searchResults.innerHTML = '<div class="p-4 text-sm text-gray-500 dark:text-gray-400">No events found</div>';
                } else {
                    searchResults.innerHTML = data.map(event => `
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
            })
            .catch(error => {
                console.error('Error fetching events:', error);
                searchResults.innerHTML = '<div class="p-4 text-sm text-red-500">Error loading events</div>';
                searchResults.classList.remove('hidden');
            });
    }, 300);
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

function displayFileName() {
    const input = document.getElementById('excel_file');
    const fileNameDisplay = document.getElementById('fileName');
    if (input.files.length > 0) {
        fileNameDisplay.textContent = `Selected: ${input.files[0].name}`;
    }
}

// Hide dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
        searchResults.classList.add('hidden');
    }
});
</script>
@endsection
