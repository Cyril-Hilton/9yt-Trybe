@extends('layouts.company')

@section('title', 'Create Event')

@section('content')
<div class="py-12" x-data="eventForm()">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Create New Event</h1>
            <p class="mt-2 text-gray-600">Fill in the details to create your event</p>
        </div>

        <form action="{{ route('organization.events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Basic Information</h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Event Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required value="{{ old('title') }}"
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
                              placeholder="Brief description of your event">{{ old('summary') }}</textarea>
                    @error('summary')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Event Categories <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-3">Select all categories that apply to your event (multiple selection allowed)</p>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach(\App\Models\Category::active()->get() as $category)
                        <label class="relative flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-{{ $category->color }}-400 hover:bg-{{ $category->color }}-50/30 group">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                   {{ (is_array(old('categories')) && in_array($category->id, old('categories'))) ? 'checked' : '' }}
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
                               {{ old('is_holiday') ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 mt-0.5">
                        <div class="flex-1">
                            <label for="is_holiday" class="block text-sm font-medium text-gray-900 cursor-pointer mb-1">
                                This event falls on a holiday
                            </label>
                            <p class="text-xs text-gray-600 mb-3">Check this if your event is related to or occurs on a public holiday</p>

                            <div class="space-y-3" id="holiday-fields" style="display: none;">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Holiday Name</label>
                                    <input type="text" name="holiday_name" value="{{ old('holiday_name') }}"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                           placeholder="e.g., Independence Day, Christmas, Eid">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Holiday Country/Region</label>
                                    <input type="text" name="holiday_country" value="{{ old('holiday_country') }}"
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Flier Image</label>
                    <input type="file" name="banner_image" accept="image/*"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <p class="mt-1 text-xs text-gray-500">Recommended: 900x370px</p>
                    @error('banner_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Event Images -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Images (Optional)</label>
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
                        <option value="single">Single Event</option>
                        <option value="recurring">Recurring Event</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="start_date" required value="{{ old('start_date') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date & Time <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="end_date" required value="{{ old('end_date') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('end_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Door Time (Optional)</label>
                    <input type="time" name="door_time" value="{{ old('door_time') }}"
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
                        <option value="Greater Accra" {{ old('region') == 'Greater Accra' ? 'selected' : '' }}>Greater Accra</option>
                        <option value="Ashanti" {{ old('region') == 'Ashanti' ? 'selected' : '' }}>Ashanti</option>
                        <option value="Western" {{ old('region') == 'Western' ? 'selected' : '' }}>Western</option>
                        <option value="Eastern" {{ old('region') == 'Eastern' ? 'selected' : '' }}>Eastern</option>
                        <option value="Central" {{ old('region') == 'Central' ? 'selected' : '' }}>Central</option>
                        <option value="Northern" {{ old('region') == 'Northern' ? 'selected' : '' }}>Northern</option>
                        <option value="Upper East" {{ old('region') == 'Upper East' ? 'selected' : '' }}>Upper East</option>
                        <option value="Upper West" {{ old('region') == 'Upper West' ? 'selected' : '' }}>Upper West</option>
                        <option value="Volta" {{ old('region') == 'Volta' ? 'selected' : '' }}>Volta</option>
                        <option value="Brong Ahafo" {{ old('region') == 'Brong Ahafo' ? 'selected' : '' }}>Brong Ahafo</option>
                        <option value="Western North" {{ old('region') == 'Western North' ? 'selected' : '' }}>Western North</option>
                        <option value="Ahafo" {{ old('region') == 'Ahafo' ? 'selected' : '' }}>Ahafo</option>
                        <option value="Bono East" {{ old('region') == 'Bono East' ? 'selected' : '' }}>Bono East</option>
                        <option value="Oti" {{ old('region') == 'Oti' ? 'selected' : '' }}>Oti</option>
                        <option value="Savannah" {{ old('region') == 'Savannah' ? 'selected' : '' }}>Savannah</option>
                        <option value="North East" {{ old('region') == 'North East' ? 'selected' : '' }}>North East</option>
                    </select>
                    @error('region')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location Type <span class="text-red-500">*</span></label>
                    <select name="location_type" x-model="locationType" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="venue">Venue</option>
                        <option value="online">Online Event</option>
                        <option value="tba">To Be Announced</option>
                    </select>
                </div>

                <!-- Venue Fields -->
                <div x-show="locationType === 'venue'" x-cloak>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Venue Name <span class="text-red-500">*</span></label>
                        <input type="text" name="venue_name" value="{{ old('venue_name') }}"
                               :required="locationType === 'venue'"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="e.g., Accra International Conference Centre">
                        @error('venue_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4" x-data="addressAutocomplete()" x-init="init()">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Venue Address <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" id="venue_address" name="venue_address" value="{{ old('venue_address') }}"
                                   :required="locationType === 'venue'"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                   placeholder="Search for venue address..."
                                   autocomplete="off"
                                   x-model="query"
                                   @input.debounce.300ms="searchAddresses()">
                            
                            <!-- OSM Suggestions -->
                            <template x-if="provider === 'osm' && suggestions.length > 0">
                                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                                    <template x-for="item in suggestions" :key="item.place_id">
                                        <button type="button" @click="selectAddress(item)" 
                                                class="w-full text-left px-4 py-2 hover:bg-gray-100 border-b border-gray-100 last:border-0">
                                            <div class="font-medium text-sm" x-text="item.display_name"></div>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <input type="hidden" id="address_validated" value="0">
                        <p class="mt-1 text-xs text-gray-500" x-text="provider === 'google' ? 'Start typing or paste an address, then select from Google Maps suggestions' : 'Start typing to see suggestions from OpenStreetMap'"></p>
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
                            <input type="text" id="venue_latitude" name="venue_latitude" value="{{ old('venue_latitude') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-gray-50"
                                   placeholder="5.6037" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Longitude (Auto-filled)</label>
                            <input type="text" id="venue_longitude" name="venue_longitude" value="{{ old('venue_longitude') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-gray-50"
                                   placeholder="-0.1870" readonly>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Parking Information</label>
                        <textarea name="parking_info" rows="2"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                  placeholder="Details about parking availability">{{ old('parking_info') }}</textarea>
                    </div>
                </div>

                <!-- Online Fields -->
                <div x-show="locationType === 'online'" x-cloak>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform <span class="text-red-500">*</span></label>
                        <select name="online_platform" :required="locationType === 'online'"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="">Select Platform</option>
                            <option value="zoom">Zoom</option>
                            <option value="google_meet">Google Meet</option>
                            <option value="microsoft_teams">Microsoft Teams</option>
                            <option value="custom">Other</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meeting Link</label>
                        <input type="url" name="online_link" value="{{ old('online_link') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               placeholder="https://zoom.us/j/...">
                        <p class="mt-1 text-xs text-gray-500">This will be shared with attendees after registration</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meeting Details</label>
                        <textarea name="online_meeting_details" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                  placeholder="Meeting ID, passcode, or other instructions">{{ old('online_meeting_details') }}</textarea>
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
                              placeholder="Detailed description of your event...">{{ old('overview') }}</textarea>
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
                    <input type="text" name="age_restriction" value="{{ old('age_restriction') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                           placeholder="e.g., 18+ only">
                </div>
            </div>

            <!-- Tickets -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6" x-data="ticketManager()">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Tickets</h2>
                <p class="text-sm text-gray-600 mb-4">Create different ticket types for your event. You must add at least one ticket to publish the event.</p>

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
            </div>

            <!-- Fee Settings -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Ticket Fees</h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Who pays the fees? <span class="text-red-500">*</span></label>
                    <select name="fee_bearer" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="attendee">Pass fees to attendees (recommended)</option>
                        <option value="organizer">Absorb fees yourself</option>
                    </select>
                    <p class="mt-2 text-sm text-gray-600">Platform and service fees will be applied to ticket sales</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <a href="{{ route('organization.events.index') }}"
                   class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                    Cancel
                </a>
                <div class="flex space-x-3">
                    <button type="submit" name="action" value="draft"
                            class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition">
                        Save as Draft
                    </button>
                    <button type="submit" name="action" value="publish"
                            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition shadow-lg">
                        Create & Publish
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function eventForm() {
    return {
        eventType: 'single',
        locationType: 'venue'
    }
}

function ticketManager() {
    return {
        tickets: [{
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
        }],
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

function addressAutocomplete() {
    return {
        provider: "{{ config('services.maps.provider', 'osm') }}",
        query: "{{ old('venue_address') }}",
        suggestions: [],
        init() {
            if (this.provider === 'google') {
                // Google initialization is handled in initMap
            }
        },
        async searchAddresses() {
            if (this.provider !== 'osm' || this.query.length < 3) {
                this.suggestions = [];
                return;
            }

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(this.query)}&format=json&limit=5&addressdetails=1&accept-language=en`);
                this.suggestions = await response.json();
            } catch (error) {
                console.error('OSM Search error:', error);
            }
        },
        selectAddress(item) {
            this.query = item.display_name;
            this.suggestions = [];
            document.getElementById('address_validated').value = '1';
            
            // Update coordinates
            const lat = parseFloat(item.lat);
            const lng = parseFloat(item.lon);
            document.getElementById('venue_latitude').value = lat.toFixed(8);
            document.getElementById('venue_longitude').value = lng.toFixed(8);

            // Update map
            if (this.provider === 'osm') {
                updateOSMMap(lat, lng);
            }
        }
    }
}

// Maps Integration
let map;
let marker;
let autocomplete;
const provider = "{{ config('services.maps.provider', 'osm') }}";

function initMap() {
    if (provider === 'google') {
        initGoogleMap();
    } else {
        initOSMMap();
    }
}

function initGoogleMap() {
    const defaultLocation = { lat: 5.6037, lng: -0.1870 };
    map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation, zoom: 13,
    });
    marker = new google.maps.Marker({ map: map, draggable: true });

    const input = document.getElementById('venue_address');
    autocomplete = new google.maps.places.Autocomplete(input, {
        fields: ['formatted_address', 'geometry', 'name'],
        types: ['establishment', 'geocode']
    });

    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        if (!place.geometry) return;
        updateGoogleMap(place.geometry.location);
        input.value = place.formatted_address || place.name;
        document.getElementById('venue_latitude').value = place.geometry.location.lat().toFixed(8);
        document.getElementById('venue_longitude').value = place.geometry.location.lng().toFixed(8);
        document.getElementById('address_validated').value = '1';
    });

    marker.addListener('dragend', function(event) {
        const pos = event.latLng;
        document.getElementById('venue_latitude').value = pos.lat().toFixed(8);
        document.getElementById('venue_longitude').value = pos.lng().toFixed(8);
        reverseGeocode(pos.lat(), pos.lng());
    });
}

function initOSMMap() {
    const lat = parseFloat(document.getElementById('venue_latitude').value) || 5.6037;
    const lng = parseFloat(document.getElementById('venue_longitude').value) || -0.1870;

    map = L.map('map').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    marker.on('dragend', function(event) {
        const pos = marker.getLatLng();
        document.getElementById('venue_latitude').value = pos.lat.toFixed(8);
        document.getElementById('venue_longitude').value = pos.lng.toFixed(8);
        reverseGeocode(pos.lat, pos.lng);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('venue_latitude').value = e.latlng.lat.toFixed(8);
        document.getElementById('venue_longitude').value = e.latlng.lng.toFixed(8);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });
}

function updateOSMMap(lat, lng) {
    if (!map) return;
    map.setView([lat, lng], 15);
    marker.setLatLng([lat, lng]);
}

function reverseGeocode(lat, lng) {
    if (provider === 'google') {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: { lat, lng } }, (results, status) => {
            if (status === 'OK' && results[0]) {
                document.getElementById('venue_address').value = results[0].formatted_address;
            }
        });
    } else {
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=en`)
            .then(r => r.json())
            .then(data => {
                if (data.display_name) {
                    document.getElementById('venue_address').value = data.display_name;
                    document.getElementById('address_validated').value = '1';
                }
            });
    }
}

function updateGoogleMap(location) {
    map.setCenter(location);
    map.setZoom(15);
    marker.setPosition(location);
    marker.setVisible(true);
}

// Load appropriate API
(function() {
    if (provider === 'google') {
        const apiKey = '{{ env('GOOGLE_MAPS_API_KEY') }}';
        if (apiKey) {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places&callback=initMap`;
            script.async = true; script.defer = true;
            document.head.appendChild(script);
        }
    } else {
        // OSM/Leaflet is already loaded in layout
        window.addEventListener('load', initMap);
    }
})();

// Holiday detection with geolocation and API integration
document.addEventListener('DOMContentLoaded', function() {
    const holidayCheckbox = document.getElementById('is_holiday');
    const holidayFields = document.getElementById('holiday-fields');
    const startDateInput = document.querySelector('input[name="start_date"]');
    const holidayNameInput = document.querySelector('input[name="holiday_name"]');
    const holidayCountryInput = document.querySelector('input[name="holiday_country"]');

    let userCountryCode = 'GH'; // Default to Ghana

    // Detect user's country on page load
    fetch('/api/holidays/detect-country')
        .then(response => response.json())
        .then(data => {
            if (data.countryCode) {
                userCountryCode = data.countryCode;
                console.log('Detected country:', userCountryCode);
            }
        })
        .catch(error => console.error('Country detection error:', error));

    if (holidayCheckbox && holidayFields) {
        // Show fields if checkbox is already checked (e.g., from old() values)
        if (holidayCheckbox.checked) {
            holidayFields.style.display = 'block';
        }

        holidayCheckbox.addEventListener('change', function() {
            holidayFields.style.display = this.checked ? 'block' : 'none';
            if (!this.checked) {
                // Clear holiday fields when unchecked
                if (holidayNameInput) holidayNameInput.value = '';
                if (holidayCountryInput) holidayCountryInput.value = '';
            }
        });
    }

    // Auto-detect holidays when date changes
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            if (!selectedDate || !userCountryCode) return;

            // Check if selected date is a holiday
            fetch('/api/holidays/check-date', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    date: selectedDate,
                    country: userCountryCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.isHoliday && data.holiday) {
                    // Show notification
                    const holidayName = data.holiday.localName || data.holiday.name;
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 z-50 glass-card rounded-2xl p-4 max-w-sm shadow-2xl animate-slide-in';
                    notification.innerHTML = `
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 dark:text-white mb-1">Holiday Detected!</h4>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                    This date is <strong>${holidayName}</strong>
                                </p>
                                <button onclick="this.closest('.animate-slide-in').remove(); document.getElementById('is_holiday').click(); document.querySelector('input[name=holiday_name]').value='${holidayName.replace(/'/g, "\\'")}'; document.querySelector('input[name=holiday_country]').value='${userCountryCode}';"
                                        class="text-xs px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    Mark as Holiday Event
                                </button>
                            </div>
                            <button onclick="this.closest('.animate-slide-in').remove()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    `;
                    document.body.appendChild(notification);

                    // Auto-remove after 8 seconds
                    setTimeout(() => {
                        notification.remove();
                    }, 8000);
                }
            })
            .catch(error => console.error('Holiday check error:', error));
        });
    }
});
</script>
@endsection
