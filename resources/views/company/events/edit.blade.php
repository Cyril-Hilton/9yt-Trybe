@extends('layouts.company')

@section('title', 'Edit Event - ' . $event->title)

@section('content')
<div class="py-12" x-data="eventForm()">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <nav class="text-sm mb-4">
                <a href="{{ route('organization.events.index') }}" class="text-indigo-600 hover:text-indigo-800">Events</a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="{{ route('organization.events.show', $event) }}" class="text-indigo-600 hover:text-indigo-800">{{ Str::limit($event->title, 30) }}</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-600">Edit</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Edit Event</h1>
            <p class="mt-2 text-gray-600">Update your event details</p>

            @if($event->tickets_sold > 0)
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="text-sm text-yellow-800">
                        <p class="font-semibold">Tickets have been sold for this event</p>
                        <p>Ticket types and pricing cannot be modified. You can still update other event details.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <form action="{{ route('organization.events.update', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Basic Information</h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Event Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required value="{{ old('title', $event->title) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                           placeholder="e.g., Annual Tech Conference 2024">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Summary <span class="text-gray-500 text-xs">(Max 500 characters)</span>
                    </label>
                    <textarea name="summary" rows="3" maxlength="500"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                              placeholder="Brief description of your event">{{ old('summary', $event->summary) }}</textarea>
                    @error('summary')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Event Categories <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-3">Select all categories that apply to your event (multiple selection allowed)</p>

                    @php
                        $selectedCategoryIds = old('categories', $event->categories->pluck('id')->toArray());
                    @endphp

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach(\App\Models\Category::active()->get() as $category)
                        <label class="relative flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-{{ $category->color }}-400 hover:bg-{{ $category->color }}-50/30 group">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                   {{ (is_array($selectedCategoryIds) && in_array($category->id, $selectedCategoryIds)) ? 'checked' : '' }}
                                   class="w-4 h-4 text-{{ $category->color }}-600 border-gray-300 rounded focus:ring-2 focus:ring-{{ $category->color }}-500">

                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <div class="flex-shrink-0 w-6 h-6 text-{{ $category->color }}-600 group-hover:text-{{ $category->color }}-700">
                                    {!! $category->getIconHtml() !!}
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900 truncate">
                                    {{ $category->name }}
                                </span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('categories')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('categories.*')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <div class="flex items-start gap-3 p-4 bg-blue-50 border-2 border-blue-200 rounded-xl">
                        <input type="checkbox" name="is_holiday" id="is_holiday" value="1"
                               {{ old('is_holiday', $event->is_holiday) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 mt-0.5">
                        <div class="flex-1">
                            <label for="is_holiday" class="block text-sm font-medium text-gray-900 cursor-pointer mb-1">
                                This event falls on a holiday
                            </label>
                            <p class="text-xs text-gray-600 mb-3">Check this if your event is related to or occurs on a public holiday</p>

                            <div class="space-y-3" id="holiday-fields" style="display: {{ old('is_holiday', $event->is_holiday) ? 'block' : 'none' }};">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Holiday Name</label>
                                    <input type="text" name="holiday_name" value="{{ old('holiday_name', $event->holiday_name) }}"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                           placeholder="e.g., Independence Day, Christmas, Eid">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Holiday Country/Region</label>
                                    <input type="text" name="holiday_country" value="{{ old('holiday_country', $event->holiday_country) }}"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                           placeholder="e.g., Ghana, West Africa, Global">
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('is_holiday')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('holiday_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('holiday_country')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image</label>
                    @if($event->banner_image)
                    <div class="mb-3">
                        <img src="{{ $event->banner_url }}" alt="Current banner" class="h-32 rounded-lg border border-gray-300">
                        <p class="mt-1 text-xs text-gray-500">Current banner (upload a new one to replace)</p>
                    </div>
                    @endif
                    <input type="file" name="banner_image" accept="image/*"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <p class="mt-1 text-xs text-gray-500">Recommended: 900x370px</p>
                    @error('banner_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Event Images -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Images (Optional)</label>
                    @if($event->images->isNotEmpty())
                    <div class="mb-3 grid grid-cols-4 gap-2">
                        @foreach($event->images as $image)
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Event image" class="h-24 w-full object-cover rounded border border-gray-300">
                        @endforeach
                    </div>
                    <p class="mb-2 text-xs text-gray-600">Current images ({{ $event->images->count() }} image{{ $event->images->count() > 1 ? 's' : '' }}). Upload new ones to replace all.</p>
                    @endif
                    <input type="file" name="images[]" accept="image/*" multiple
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <p class="mt-1 text-xs text-gray-500">You can upload multiple images.</p>
                    @error('images.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Date & Time -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Date & Time</h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Type <span class="text-red-500">*</span></label>
                    <select name="event_type" x-model="eventType" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="single" {{ old('event_type', $event->event_type) === 'single' ? 'selected' : '' }}>Single Event</option>
                        <option value="recurring" {{ old('event_type', $event->event_type) === 'recurring' ? 'selected' : '' }}>Recurring Event</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="start_date" required value="{{ old('start_date', $event->start_date ? $event->start_date->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date & Time <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="end_date" required value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('end_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Door Time (Optional)</label>
                    <input type="time" name="door_time" value="{{ old('door_time', $event->door_time) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <p class="mt-1 text-xs text-gray-500">What time should attendees arrive?</p>
                </div>
            </div>

            <!-- Location -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Location</h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Region <span class="text-red-500">*</span></label>
                    <select name="region" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Select a region</option>
                        <option value="Greater Accra" {{ old('region', $event->region) == 'Greater Accra' ? 'selected' : '' }}>Greater Accra</option>
                        <option value="Ashanti" {{ old('region', $event->region) == 'Ashanti' ? 'selected' : '' }}>Ashanti</option>
                        <option value="Western" {{ old('region', $event->region) == 'Western' ? 'selected' : '' }}>Western</option>
                        <option value="Eastern" {{ old('region', $event->region) == 'Eastern' ? 'selected' : '' }}>Eastern</option>
                        <option value="Central" {{ old('region', $event->region) == 'Central' ? 'selected' : '' }}>Central</option>
                        <option value="Northern" {{ old('region', $event->region) == 'Northern' ? 'selected' : '' }}>Northern</option>
                        <option value="Upper East" {{ old('region', $event->region) == 'Upper East' ? 'selected' : '' }}>Upper East</option>
                        <option value="Upper West" {{ old('region', $event->region) == 'Upper West' ? 'selected' : '' }}>Upper West</option>
                        <option value="Volta" {{ old('region', $event->region) == 'Volta' ? 'selected' : '' }}>Volta</option>
                        <option value="Brong Ahafo" {{ old('region', $event->region) == 'Brong Ahafo' ? 'selected' : '' }}>Brong Ahafo</option>
                        <option value="Western North" {{ old('region', $event->region) == 'Western North' ? 'selected' : '' }}>Western North</option>
                        <option value="Ahafo" {{ old('region', $event->region) == 'Ahafo' ? 'selected' : '' }}>Ahafo</option>
                        <option value="Bono East" {{ old('region', $event->region) == 'Bono East' ? 'selected' : '' }}>Bono East</option>
                        <option value="Oti" {{ old('region', $event->region) == 'Oti' ? 'selected' : '' }}>Oti</option>
                        <option value="Savannah" {{ old('region', $event->region) == 'Savannah' ? 'selected' : '' }}>Savannah</option>
                        <option value="North East" {{ old('region', $event->region) == 'North East' ? 'selected' : '' }}>North East</option>
                    </select>
                    @error('region')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location Type <span class="text-red-500">*</span></label>
                    <select name="location_type" x-model="locationType" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="venue" {{ old('location_type', $event->location_type) === 'venue' ? 'selected' : '' }}>Venue</option>
                        <option value="online" {{ old('location_type', $event->location_type) === 'online' ? 'selected' : '' }}>Online Event</option>
                        <option value="tba" {{ old('location_type', $event->location_type) === 'tba' ? 'selected' : '' }}>To Be Announced</option>
                    </select>
                </div>

                <!-- Venue Fields -->
                <div x-show="locationType === 'venue'" x-cloak>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Venue Name <span class="text-red-500">*</span></label>
                        <input type="text" name="venue_name" value="{{ old('venue_name', $event->venue_name) }}"
                               :required="locationType === 'venue'"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="e.g., Accra International Conference Centre">
                        @error('venue_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Venue Address <span class="text-red-500">*</span></label>
                        <input type="text" id="venue_address" name="venue_address" value="{{ old('venue_address', $event->venue_address) }}"
                               :required="locationType === 'venue'"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="Search for venue address..."
                               autocomplete="off">
                        <input type="hidden" id="address_validated" value="{{ $event->venue_address ? '1' : '0' }}">
                        <p class="mt-1 text-xs text-gray-500">Start typing or paste an address, then select from Google Maps suggestions</p>
                        @error('venue_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <!-- Google Map -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location Map</label>
                        <div id="map" class="w-full h-80 rounded-lg border border-gray-300"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Latitude (Auto-filled)</label>
                            <input type="text" id="venue_latitude" name="venue_latitude" value="{{ old('venue_latitude', $event->venue_latitude) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-gray-50"
                                   placeholder="5.6037" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Longitude (Auto-filled)</label>
                            <input type="text" id="venue_longitude" name="venue_longitude" value="{{ old('venue_longitude', $event->venue_longitude) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-gray-50"
                                   placeholder="-0.1870" readonly>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Parking Information</label>
                        <textarea name="parking_info" rows="2"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                  placeholder="Details about parking availability">{{ old('parking_info', $event->parking_info) }}</textarea>
                    </div>
                </div>

                <!-- Online Fields -->
                <div x-show="locationType === 'online'" x-cloak>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform <span class="text-red-500">*</span></label>
                        <select name="online_platform" :required="locationType === 'online'"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="">Select Platform</option>
                            <option value="zoom" {{ old('online_platform', $event->online_platform) === 'zoom' ? 'selected' : '' }}>Zoom</option>
                            <option value="google_meet" {{ old('online_platform', $event->online_platform) === 'google_meet' ? 'selected' : '' }}>Google Meet</option>
                            <option value="microsoft_teams" {{ old('online_platform', $event->online_platform) === 'microsoft_teams' ? 'selected' : '' }}>Microsoft Teams</option>
                            <option value="custom" {{ old('online_platform', $event->online_platform) === 'custom' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meeting Link</label>
                        <input type="url" name="online_link" value="{{ old('online_link', $event->online_link) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="https://zoom.us/j/...">
                        <p class="mt-1 text-xs text-gray-500">This will be shared with attendees after registration</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meeting Details</label>
                        <textarea name="online_meeting_details" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                  placeholder="Meeting ID, passcode, or other instructions">{{ old('online_meeting_details', $event->online_meeting_details) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Overview -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Overview</h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Description</label>
                    <textarea name="overview" rows="10"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none font-mono text-sm"
                              placeholder="Detailed description of your event...">{{ old('overview', $event->overview) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Use line breaks for formatting. HTML not supported.</p>
                    @error('overview')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Videos -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    Videos
                    <span class="ml-2 text-sm font-normal text-gray-500">(YouTube & Vimeo only)</span>
                </h2>
                <div x-data="{ videos: [''] }">
                    <template x-for="(video, index) in videos" :key="index">
                        <div class="flex items-center gap-2 mb-3">
                            <input type="url" :name="'videos[' + index + ']'" x-model="videos[index]"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                   placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                            <button type="button" @click="videos.splice(index, 1)" x-show="videos.length > 1"
                                    class="px-4 py-3 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">Remove</button>
                        </div>
                    </template>
                    <button type="button" @click="videos.push('')"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                        + Add Video
                    </button>
                </div>
                <div class="mt-2 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-sm text-blue-800 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        We only support YouTube and Vimeo video URLs
                    </p>
                </div>
            </div>

            <!-- FAQs -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
                <div x-data="{ faqs: [{ question: '', answer: '' }] }">
                    <template x-for="(faq, index) in faqs" :key="index">
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="mb-3">
                                <input type="text" :name="'faqs[' + index + '][question]'" x-model="faq.question"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                       placeholder="Question">
                            </div>
                            <div class="mb-3">
                                <textarea :name="'faqs[' + index + '][answer]'" x-model="faq.answer" rows="2"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                          placeholder="Answer"></textarea>
                            </div>
                            <button type="button" @click="faqs.splice(index, 1)" x-show="faqs.length > 1"
                                    class="text-sm text-red-600 hover:text-red-800">Remove FAQ</button>
                        </div>
                    </template>
                    <button type="button" @click="faqs.push({ question: '', answer: '' })"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                        + Add FAQ
                    </button>
                </div>
            </div>

            <!-- Good to Know -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Good to Know</h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Age Restriction</label>
                    <input type="text" name="age_restriction" value="{{ old('age_restriction', $event->age_restriction) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                           placeholder="e.g., 18+ only">
                </div>
            </div>

            <!-- Tickets -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6" x-data="ticketManager()">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Tickets</h2>

                @if($event->tickets_sold > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-yellow-800 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Tickets have been sold. You cannot add, remove, or modify ticket types and pricing.
                    </p>
                </div>

                <!-- Display existing tickets (read-only) -->
                @foreach($event->tickets as $existingTicket)
                <div class="border border-gray-300 rounded-lg p-4 mb-4 bg-gray-50">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $existingTicket->name }}</h3>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">Read-only</span>
                    </div>
                    @if($existingTicket->description)
                    <p class="text-sm text-gray-600 mb-2">{{ $existingTicket->description }}</p>
                    @endif
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium text-gray-900">{{ ucfirst($existingTicket->type) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Price:</span>
                            <span class="font-medium text-gray-900">{{ $existingTicket->formatted_price }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Sold:</span>
                            <span class="font-medium text-gray-900">{{ $existingTicket->sold }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Available:</span>
                            <span class="font-medium text-gray-900">{{ $existingTicket->quantity ? $existingTicket->quantity : 'Unlimited' }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <p class="text-sm text-gray-600 mb-4">Manage your event tickets. You must have at least one ticket to publish the event.</p>

                <template x-for="(ticket, index) in tickets" :key="index">
                    <div class="border border-gray-300 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold text-gray-900" x-text="'Ticket #' + (index + 1)"></h3>
                            <button type="button" @click="removeTicket(index)" x-show="tickets.length > 1"
                                    class="text-sm text-red-600 hover:text-red-800 font-medium">Remove</button>
                        </div>

                        <!-- Ticket Name -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ticket Name <span class="text-red-500">*</span></label>
                            <input type="text" :name="'tickets[' + index + '][name]'" x-model="ticket.name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                   placeholder="e.g., General Admission, VIP, Early Bird">
                        </div>

                        <!-- Ticket Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                            <textarea :name="'tickets[' + index + '][description]'" x-model="ticket.description" rows="2"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                      placeholder="Brief description of what's included"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Ticket Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ticket Type <span class="text-red-500">*</span></label>
                                <select :name="'tickets[' + index + '][type]'" x-model="ticket.type" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    <option value="free">Free</option>
                                    <option value="paid">Paid</option>
                                    <option value="donation">Donation (Pay What You Want)</option>
                                </select>
                            </div>

                            <!-- Price (for paid tickets) -->
                            <div x-show="ticket.type === 'paid'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price (GH₵) <span class="text-red-500">*</span></label>
                                <input type="number" :name="'tickets[' + index + '][price]'" x-model="ticket.price"
                                       :required="ticket.type === 'paid'" min="0" step="0.01"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                       placeholder="0.00">
                            </div>

                            <!-- Minimum Donation (for donation tickets) -->
                            <div x-show="ticket.type === 'donation'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Donation (GH₵)</label>
                                <input type="number" :name="'tickets[' + index + '][min_donation]'" x-model="ticket.min_donation"
                                       min="0" step="0.01"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                       placeholder="0.00">
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Available Quantity
                                <span class="text-gray-500 text-xs">(Leave blank for unlimited)</span>
                            </label>
                            <input type="number" :name="'tickets[' + index + '][quantity]'" x-model="ticket.quantity"
                                   min="1"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                   placeholder="Leave blank for unlimited">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Min Per Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Min Tickets Per Order</label>
                                <input type="number" :name="'tickets[' + index + '][min_per_order]'" x-model="ticket.min_per_order"
                                       min="1" value="1"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>

                            <!-- Max Per Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Max Tickets Per Order</label>
                                <input type="number" :name="'tickets[' + index + '][max_per_order]'" x-model="ticket.max_per_order"
                                       min="1" value="10"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Sales Start -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sales Start Date (Optional)</label>
                                <input type="datetime-local" :name="'tickets[' + index + '][sales_start]'" x-model="ticket.sales_start"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>

                            <!-- Sales End -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sales End Date (Optional)</label>
                                <input type="datetime-local" :name="'tickets[' + index + '][sales_end]'" x-model="ticket.sales_end"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addTicket"
                        class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 text-sm font-medium">
                    + Add Another Ticket Type
                </button>
                @endif
            </div>

            <!-- Fee Settings -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Ticket Fees</h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Who pays the fees? <span class="text-red-500">*</span></label>
                    <select name="fee_bearer" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="attendee" {{ old('fee_bearer', $event->fee_bearer) === 'attendee' ? 'selected' : '' }}>Pass fees to attendees (recommended)</option>
                        <option value="organizer" {{ old('fee_bearer', $event->fee_bearer) === 'organizer' ? 'selected' : '' }}>Absorb fees yourself</option>
                    </select>
                    <p class="mt-2 text-sm text-gray-600">Platform and service fees will be applied to ticket sales</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <a href="{{ route('organization.events.show', $event) }}"
                   class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                    Cancel
                </a>
                <div class="flex space-x-3">
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition shadow-lg">
                        Update Event
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function eventForm() {
    return {
        eventType: '{{ old('event_type', $event->event_type) }}',
        locationType: '{{ old('location_type', $event->location_type) }}'
    }
}

function ticketManager() {
    @php
        $initialTickets = [];
        if ($event->tickets_sold == 0 && $event->tickets->isNotEmpty()) {
            foreach ($event->tickets as $ticket) {
                $initialTickets[] = [
                    'name' => $ticket->name,
                    'description' => $ticket->description,
                    'type' => $ticket->type,
                    'price' => $ticket->price,
                    'min_donation' => $ticket->min_donation,
                    'quantity' => $ticket->quantity,
                    'min_per_order' => $ticket->min_per_order,
                    'max_per_order' => $ticket->max_per_order,
                    'sales_start' => $ticket->sales_start ? $ticket->sales_start->format('Y-m-d\TH:i') : '',
                    'sales_end' => $ticket->sales_end ? $ticket->sales_end->format('Y-m-d\TH:i') : '',
                ];
            }
        } else {
            $initialTickets = [[
                'name' => '',
                'description' => '',
                'type' => 'paid',
                'price' => '',
                'min_donation' => '',
                'quantity' => '',
                'min_per_order' => 1,
                'max_per_order' => 10,
                'sales_start' => '',
                'sales_end' => ''
            ]];
        }
    @endphp

    return {
        tickets: @json($initialTickets),
        addTicket() {
            this.tickets.push({
                name: '',
                description: '',
                type: 'paid',
                price: '',
                min_donation: '',
                quantity: '',
                min_per_order: 1,
                max_per_order: 10,
                sales_start: '',
                sales_end: ''
            });
        },
        removeTicket(index) {
            if (this.tickets.length > 1) {
                this.tickets.splice(index, 1);
            }
        }
    }
}

// Google Maps Integration
let map;
let marker;
let autocomplete;

function initMap() {
    // Default location (Accra, Ghana)
    const defaultLocation = { lat: 5.6037, lng: -0.1870 };

    // Initialize map
    map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation,
        zoom: 13,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true,
    });

    // Initialize marker
    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
    });

    // Initialize autocomplete
    const input = document.getElementById('venue_address');
    autocomplete = new google.maps.places.Autocomplete(input, {
        fields: ['formatted_address', 'geometry', 'name', 'address_components'],
        types: ['establishment', 'geocode']
    });

    // Track if address was selected from dropdown
    let addressSelected = false;

    // When user selects an address from dropdown
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();

        if (!place.geometry || !place.geometry.location) {
            alert('Please select a valid address from the dropdown suggestions.');
            document.getElementById('address_validated').value = '0';
            addressSelected = false;
            return;
        }

        // Mark as validated
        addressSelected = true;
        document.getElementById('address_validated').value = '1';

        // Update map
        updateMap(place.geometry.location);

        // Update address field with formatted address
        input.value = place.formatted_address || place.name;

        // Update coordinates
        document.getElementById('venue_latitude').value = place.geometry.location.lat().toFixed(8);
        document.getElementById('venue_longitude').value = place.geometry.location.lng().toFixed(8);
    });

    // Handle paste events - trigger autocomplete
    input.addEventListener('paste', function(e) {
        // Reset validation on paste
        addressSelected = false;
        document.getElementById('address_validated').value = '0';

        setTimeout(function() {
            // Trigger autocomplete suggestions after paste
            const event = new Event('input', { bubbles: true });
            input.dispatchEvent(event);
            google.maps.event.trigger(autocomplete, 'place_changed');
        }, 100);
    });

    // Handle manual typing - reset validation
    input.addEventListener('input', function() {
        if (input.value === '') {
            addressSelected = false;
            document.getElementById('address_validated').value = '0';
        }
    });

    // Form submission validation
    const form = input.closest('form');
    form.addEventListener('submit', function(e) {
        const locationType = document.querySelector('select[name="location_type"]').value;
        if (locationType === 'venue') {
            const addressValue = input.value.trim();
            const hasCoordinates = document.getElementById('venue_latitude').value &&
                                  document.getElementById('venue_longitude').value;

            if (addressValue && !hasCoordinates) {
                e.preventDefault();
                alert('Please select your venue address from the Google Maps dropdown suggestions.');
                input.focus();
                return false;
            }
        }
    });

    // When user drags the marker
    marker.addListener('dragend', function(event) {
        const position = event.latLng;
        document.getElementById('venue_latitude').value = position.lat().toFixed(8);
        document.getElementById('venue_longitude').value = position.lng().toFixed(8);

        // Reverse geocode to get address
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: position }, function(results, status) {
            if (status === 'OK' && results[0]) {
                document.getElementById('venue_address').value = results[0].formatted_address;
            }
        });
    });

    // If there are existing values, populate the map
    const oldLat = document.getElementById('venue_latitude').value;
    const oldLng = document.getElementById('venue_longitude').value;
    if (oldLat && oldLng) {
        updateMap(new google.maps.LatLng(parseFloat(oldLat), parseFloat(oldLng)));
    }
}

function updateMap(location) {
    map.setCenter(location);
    map.setZoom(15);
    marker.setPosition(location);
    marker.setVisible(true);
}

// Load Google Maps API
(function() {
    const apiKey = '{{ env('GOOGLE_MAPS_API_KEY') }}';
    if (!apiKey) {
        console.warn('Google Maps API key is not set. Please add GOOGLE_MAPS_API_KEY to your .env file.');
        document.getElementById('map').innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100 text-gray-600"><p>Google Maps API key not configured. Please add GOOGLE_MAPS_API_KEY to your .env file.</p></div>';
        return;
    }

    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places&callback=initMap`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
})();

// Holiday checkbox toggle
document.addEventListener('DOMContentLoaded', function() {
    const holidayCheckbox = document.getElementById('is_holiday');
    const holidayFields = document.getElementById('holiday-fields');

    if (holidayCheckbox && holidayFields) {
        holidayCheckbox.addEventListener('change', function() {
            holidayFields.style.display = this.checked ? 'block' : 'none';
            if (!this.checked) {
                // Clear holiday fields when unchecked
                holidayFields.querySelector('input[name="holiday_name"]').value = '';
                holidayFields.querySelector('input[name="holiday_country"]').value = '';
            }
        });
    }
});
</script>
@endsection
