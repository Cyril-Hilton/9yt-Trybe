@extends('layouts.admin')

@section('title', 'Edit Event')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Edit Event</h1>
                <p class="text-gray-400">Update event information and settings</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Events
            </a>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700" x-data="{ isExternal: {{ $event->is_external ? 'true' : 'false' }}, locationType: '{{ $event->location_type }}' }">
        <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- External Event Toggle -->
            <div class="mb-8 p-4 bg-blue-900/30 border-2 border-blue-600 rounded-xl">
                <label class="flex items-start cursor-pointer">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="is_external" value="1" x-model="isExternal"
                               {{ $event->is_external ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                    </div>
                    <div class="ml-3">
                        <span class="text-lg font-bold text-blue-300">External Event</span>
                        <p class="text-sm text-gray-300 mt-1">Check this if the event has its own ticketing platform. Users will be redirected to the external ticket URL instead of purchasing through this platform.</p>
                    </div>
                </label>
            </div>

            <!-- Basic Information -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Basic Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Event Title -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Event Title *</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $event->title) }}" required
                               class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               placeholder="e.g., Black Sherif Concert 2025">
                        @error('title')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Organization (Optional) -->
                    <div class="md:col-span-2">
                        <label for="company_id" class="block text-sm font-medium text-gray-300 mb-2">Assign to Organization (Optional)</label>
                        <select name="company_id" id="company_id"
                                class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">None - Platform Event</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $event->company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Leave empty for platform-managed events</p>
                        @error('company_id')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Summary -->
                    <div class="md:col-span-2">
                        <label for="summary" class="block text-sm font-medium text-gray-300 mb-2">Short Summary * (Max 500 chars)</label>
                        <textarea name="summary" id="summary" rows="3" required maxlength="500"
                                  class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                  placeholder="Brief description of the event">{{ old('summary', $event->summary) }}</textarea>
                        @error('summary')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Overview -->
                    <div class="md:col-span-2">
                        <label for="overview" class="block text-sm font-medium text-gray-300 mb-2">Detailed Overview</label>
                        <textarea name="overview" id="overview" rows="6"
                                  class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                  placeholder="Detailed description about the event">{{ old('overview', $event->overview) }}</textarea>
                        @error('overview')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Event Flier Image -->
                    <div class="md:col-span-2">
                        <label for="banner_image" class="block text-sm font-medium text-gray-300 mb-2">Event Flier Image</label>
                        @if($event->banner_image)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $event->banner_image) }}" alt="{{ $event->title }}" class="h-32 rounded-lg object-cover">
                                <p class="text-xs text-gray-400 mt-1">Current image (upload new image to replace)</p>
                            </div>
                        @endif
                        <input type="file" name="banner_image" id="banner_image" accept="image/*"
                               class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        <p class="text-xs text-gray-400 mt-1">Recommended: 1920x1080px</p>
                        @error('banner_image')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="md:col-span-2">
                        <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Event Status *</label>
                        <select name="status" id="status"
                                class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="pending" {{ old('status', $event->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status', $event->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('status', $event->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Date & Time -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Date & Time
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-300 mb-2">Start Date & Time *</label>
                        <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date', $event->start_date ? $event->start_date->format('Y-m-d\TH:i') : '') }}" required
                               class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('start_date')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-300 mb-2">End Date & Time *</label>
                        <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d\TH:i') : '') }}" required
                               class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('end_date')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Timezone -->
                    <div class="md:col-span-2">
                        <label for="timezone" class="block text-sm font-medium text-gray-300 mb-2">Timezone</label>
                        <input type="text" name="timezone" id="timezone" value="{{ old('timezone', $event->timezone ?? 'Africa/Accra') }}"
                               class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('timezone')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Location
                </h3>

                <div class="grid grid-cols-1 gap-6">
                    <!-- Location Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Location Type *</label>
                        <div class="grid grid-cols-3 gap-4">
                            <label class="flex items-center p-4 bg-gray-700 border-2 border-gray-600 rounded-lg cursor-pointer hover:bg-gray-650 transition"
                                   :class="locationType === 'venue' ? 'border-blue-500 bg-gray-650' : ''">
                                <input type="radio" name="location_type" value="venue" x-model="locationType" {{ old('location_type', $event->location_type) === 'venue' ? 'checked' : '' }} required class="mr-3">
                                <span class="text-white">Physical Venue</span>
                            </label>
                            <label class="flex items-center p-4 bg-gray-700 border-2 border-gray-600 rounded-lg cursor-pointer hover:bg-gray-650 transition"
                                   :class="locationType === 'online' ? 'border-blue-500 bg-gray-650' : ''">
                                <input type="radio" name="location_type" value="online" x-model="locationType" {{ old('location_type', $event->location_type) === 'online' ? 'checked' : '' }} required class="mr-3">
                                <span class="text-white">Online</span>
                            </label>
                            <label class="flex items-center p-4 bg-gray-700 border-2 border-gray-600 rounded-lg cursor-pointer hover:bg-gray-650 transition"
                                   :class="locationType === 'hybrid' ? 'border-blue-500 bg-gray-650' : ''">
                                <input type="radio" name="location_type" value="hybrid" x-model="locationType" {{ old('location_type', $event->location_type) === 'hybrid' ? 'checked' : '' }} required class="mr-3">
                                <span class="text-white">Hybrid</span>
                            </label>
                        </div>
                        @error('location_type')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Physical Venue Fields -->
                    <div x-show="locationType === 'venue' || locationType === 'hybrid'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="region" class="block text-sm font-medium text-gray-300 mb-2">Region</label>
                            <select name="region" id="region"
                                    class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Select Region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region }}" {{ old('region', $event->region) == $region ? 'selected' : '' }}>{{ $region }}</option>
                                @endforeach
                            </select>
                            @error('region')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="venue_name" class="block text-sm font-medium text-gray-300 mb-2">Venue Name</label>
                            <input type="text" name="venue_name" id="venue_name" value="{{ old('venue_name', $event->venue_name) }}"
                                   class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                   placeholder="e.g., Accra Sports Stadium">
                            @error('venue_name')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="venue_address" class="block text-sm font-medium text-gray-300 mb-2">Venue Address</label>
                            <input type="text" name="venue_address" id="venue_address" value="{{ old('venue_address', $event->venue_address) }}"
                                   class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                   placeholder="Full address of the venue">
                            @error('venue_address')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Online Event Fields -->
                    <div x-show="locationType === 'online' || locationType === 'hybrid'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="online_platform" class="block text-sm font-medium text-gray-300 mb-2">Platform</label>
                            <input type="text" name="online_platform" id="online_platform" value="{{ old('online_platform', $event->online_platform) }}"
                                   class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                   placeholder="e.g., Zoom, YouTube Live, etc.">
                            @error('online_platform')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="online_link" class="block text-sm font-medium text-gray-300 mb-2">Online Link</label>
                            <input type="url" name="online_link" id="online_link" value="{{ old('online_link', $event->online_link) }}"
                                   class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                   placeholder="https://...">
                            @error('online_link')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- External Event Details (Shown only if external) -->
            <div x-show="isExternal" class="mb-8 p-6 bg-blue-900/20 border-2 border-blue-600 rounded-xl">
                <h3 class="text-xl font-bold text-blue-300 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    External Ticketing Information
                </h3>
                <p class="text-sm text-blue-200 mb-4">Provide at least one contact method: URL, USSD code, or phone number</p>

                <div class="grid grid-cols-1 gap-6">
                    <!-- External Ticket URL -->
                    <div>
                        <label for="external_ticket_url" class="block text-sm font-medium text-gray-300 mb-2">External Ticket URL (Optional)</label>
                        <input type="url" name="external_ticket_url" id="external_ticket_url" value="{{ old('external_ticket_url', $event->external_ticket_url) }}"
                               class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               placeholder="https://tickets.example.com/black-sherif-concert">
                        <p class="text-xs text-gray-400 mt-1">Users will be redirected to this URL when they click "Buy Tickets Externally"</p>
                        @error('external_ticket_url')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- USSD Code -->
                    <div>
                        <label for="external_ussd_code" class="block text-sm font-medium text-gray-300 mb-2">USSD Code (Optional)</label>
                        <input type="text" name="external_ussd_code" id="external_ussd_code" value="{{ old('external_ussd_code', $event->external_ussd_code) }}"
                               class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               placeholder="*920*100# or similar">
                        <p class="text-xs text-gray-400 mt-1">USSD code for mobile ticket purchase</p>
                        @error('external_ussd_code')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reservation Phone -->
                    <div>
                        <label for="external_reservation_phone" class="block text-sm font-medium text-gray-300 mb-2">Reservation Phone Number (Optional)</label>
                        <input type="text" name="external_reservation_phone" id="external_reservation_phone" value="{{ old('external_reservation_phone', $event->external_reservation_phone) }}"
                               class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               placeholder="+233 20 123 4567">
                        <p class="text-xs text-gray-400 mt-1">Phone number for reservations or ticket inquiries</p>
                        @error('external_reservation_phone')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- External Description -->
                    <div>
                        <label for="external_description" class="block text-sm font-medium text-gray-300 mb-2">Additional Notes</label>
                        <textarea name="external_description" id="external_description" rows="3"
                                  class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                  placeholder="Any additional information about the external ticketing">{{ old('external_description', $event->external_description) }}</textarea>
                        @error('external_description')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-700">
                <a href="{{ route('admin.events.index') }}" class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Event
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
