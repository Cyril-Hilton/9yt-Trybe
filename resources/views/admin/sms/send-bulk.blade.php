@extends('layouts.admin')

@section('title', 'Send Bulk SMS')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Send Bulk SMS
                    </h1>
                    <p class="mt-2 text-gray-600">Send SMS to multiple recipients at once - Super Admin Privilege</p>
                </div>
                <a href="{{ route('admin.sms.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to SMS Dashboard
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.sms.send-bulk.store') }}" enctype="multipart/form-data" x-data="bulkSmsForm()" class="space-y-6">
            @csrf

            <!-- Campaign Details -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Campaign Details
                    </h2>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Campaign Name -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Campaign Name *
                        </label>
                        <input type="text"
                               name="campaign_name"
                               value="{{ old('campaign_name') }}"
                               placeholder="e.g., December Promotion"
                               class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('campaign_name') border-red-500 @enderror"
                               required>
                        @error('campaign_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sender ID - Admin can type ANY sender ID -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Sender ID *
                        </label>
                        <input type="text"
                               name="sender_id"
                               value="{{ old('sender_id', '9yt Trybe') }}"
                               placeholder="Type any sender ID (e.g., 9yt Trybe, YourBrand)"
                               class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('sender_id') border-red-500 @enderror"
                               required
                               maxlength="15">
                        @error('sender_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-indigo-600 dark:text-indigo-400 font-semibold">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <strong>Super Admin Privilege:</strong> You can type ANY sender ID without approval (max 15 characters)
                        </p>
                    </div>

                    <!-- Message -->
                    <div x-data="{ message: '{{ old('message') }}', maxLength: 1000 }">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Message *
                        </label>
                        <textarea name="message"
                                  rows="6"
                                  placeholder="Type your message here..."
                                  class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('message') border-red-500 @enderror"
                                  required
                                  x-model="message"
                                  maxlength="1000">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-1 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>Maximum 1000 characters</span>
                            <span x-text="message.length + ' / 1000'">0 / 1000</span>
                        </div>
                        <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-xl">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <strong>Credits per SMS:</strong>
                                <span x-text="Math.ceil(message.length / 160) || 1">1</span> credit(s)
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients Selection -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Select Recipients
                    </h2>
                </div>

                <div class="p-6">
                    <!-- Recipient Type Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Choose how to add recipients *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Manual Entry -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="manual" x-model="recipientType" class="peer sr-only" required>
                                <div class="h-full p-5 border-2 border-gray-300 rounded-2xl hover:border-blue-400 hover:shadow-lg peer-checked:border-blue-600 peer-checked:bg-gradient-to-br peer-checked:from-blue-50 peer-checked:to-blue-100 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">Manual Entry</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">Type or paste numbers</p>
                                        </div>
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-blue-600 items-center justify-center hidden peer-checked:flex">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Excel Import -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="excel" x-model="recipientType" class="peer sr-only">
                                <div class="h-full p-5 border-2 border-gray-300 rounded-2xl hover:border-green-400 hover:shadow-lg peer-checked:border-green-600 peer-checked:bg-gradient-to-br peer-checked:from-green-50 peer-checked:to-green-100 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">Excel Import</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">Upload Excel file</p>
                                        </div>
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-green-600 items-center justify-center hidden peer-checked:flex">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- All Contacts -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="all" x-model="recipientType" class="peer sr-only">
                                <div class="h-full p-5 border-2 border-gray-300 rounded-2xl hover:border-purple-400 hover:shadow-lg peer-checked:border-purple-600 peer-checked:bg-gradient-to-br peer-checked:from-purple-50 peer-checked:to-purple-100 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div class="w-14 h-14 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">All Contacts</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">Users + Organizers</p>
                                        </div>
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-purple-600 items-center justify-center hidden peer-checked:flex">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Users Only -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="users" x-model="recipientType" class="peer sr-only">
                                <div class="h-full p-5 border-2 border-gray-300 rounded-2xl hover:border-indigo-400 hover:shadow-lg peer-checked:border-indigo-600 peer-checked:bg-gradient-to-br peer-checked:from-indigo-50 peer-checked:to-indigo-100 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div class="w-14 h-14 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">Users Only</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">Attendees only</p>
                                        </div>
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-indigo-600 items-center justify-center hidden peer-checked:flex">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Organizers Only -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="organizers" x-model="recipientType" class="peer sr-only">
                                <div class="h-full p-5 border-2 border-gray-300 rounded-2xl hover:border-orange-400 hover:shadow-lg peer-checked:border-orange-600 peer-checked:bg-gradient-to-br peer-checked:from-orange-50 peer-checked:to-orange-100 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div class="w-14 h-14 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">Organizers Only</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">Event organizers</p>
                                        </div>
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-orange-600 items-center justify-center hidden peer-checked:flex">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Custom Selection -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="custom" x-model="recipientType" class="peer sr-only">
                                <div class="h-full p-5 border-2 border-gray-300 rounded-2xl hover:border-pink-400 hover:shadow-lg peer-checked:border-pink-600 peer-checked:bg-gradient-to-br peer-checked:from-pink-50 peer-checked:to-pink-100 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div class="w-14 h-14 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-7 h-7 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">Custom Selection</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">Pick specific people</p>
                                        </div>
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-pink-600 items-center justify-center hidden peer-checked:flex">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Manual Entry -->
                    <div x-show="recipientType === 'manual'" x-cloak>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Phone Numbers (one per line or comma-separated)
                        </label>
                        <textarea name="recipients"
                                  rows="8"
                                  placeholder="0241234567&#10;0551234567&#10;233201234567&#10;or&#10;0241234567, 0551234567, 233201234567"
                                  class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white font-mono text-sm"
                                  x-model="manualNumbers"
                                  @input="countManualNumbers()">{{ old('recipients') }}</textarea>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Recipients: <span class="font-bold text-indigo-600" x-text="recipientCount">0</span>
                        </p>
                    </div>

                    <!-- Excel Import -->
                    <div x-show="recipientType === 'excel'" x-cloak>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Upload Excel File
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center bg-gray-50 dark:bg-gray-800">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <div class="mt-4">
                                <label for="excel_file" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 px-3 py-2">
                                    <span>Upload a file</span>
                                    <input id="excel_file" name="excel_file" type="file" accept=".xlsx,.xls,.csv" class="sr-only">
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    Excel (.xlsx, .xls) or CSV files
                                </p>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                File should have phone numbers in the first column
                            </p>
                        </div>
                    </div>

                    <!-- All Contacts Info -->
                    <div x-show="recipientType === 'all'" x-cloak>
                        <div class="p-4 bg-purple-50 dark:bg-purple-900/30 border-2 border-purple-200 dark:border-purple-700 rounded-xl">
                            <p class="text-sm text-purple-800 dark:text-purple-200">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                This will send SMS to <strong>{{ $totalUsers }} users</strong> and <strong>{{ $totalOrganizers }} organizers</strong> (Total: <strong>{{ $totalUsers + $totalOrganizers }} contacts</strong>)
                            </p>
                        </div>
                    </div>

                    <!-- Users Only Info -->
                    <div x-show="recipientType === 'users'" x-cloak>
                        <div class="p-4 bg-indigo-50 dark:bg-indigo-900/30 border-2 border-indigo-200 dark:border-indigo-700 rounded-xl">
                            <p class="text-sm text-indigo-800 dark:text-indigo-200">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                This will send SMS to <strong>{{ $totalUsers }} attendees</strong>
                            </p>
                        </div>
                    </div>

                    <!-- Organizers Only Info -->
                    <div x-show="recipientType === 'organizers'" x-cloak>
                        <div class="p-4 bg-orange-50 dark:bg-orange-900/30 border-2 border-orange-200 dark:border-orange-700 rounded-xl">
                            <p class="text-sm text-orange-800 dark:text-orange-200">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                This will send SMS to <strong>{{ $totalOrganizers }} event organizers</strong>
                            </p>
                        </div>
                    </div>

                    <!-- Custom Selection -->
                    <div x-show="recipientType === 'custom'" x-cloak class="space-y-4">
                        <!-- Users Section -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                    Select Users (Attendees)
                                </label>
                                <button type="button" @click="toggleAllUsers()" class="text-xs px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded-lg hover:bg-indigo-200 dark:hover:bg-indigo-800 font-semibold">
                                    Select All / Deselect All
                                </button>
                            </div>
                            <div class="border-2 border-gray-300 dark:border-gray-600 rounded-xl p-4 max-h-64 overflow-y-auto bg-gray-50 dark:bg-gray-800">
                                @forelse($users as $user)
                                    <label class="flex items-center p-2 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg cursor-pointer">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $user->name }} ({{ $user->phone ?: $user->email }})
                                        </span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No users found</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Organizers Section -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                    Select Organizers
                                </label>
                                <button type="button" @click="toggleAllOrganizers()" class="text-xs px-3 py-1 bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300 rounded-lg hover:bg-orange-200 dark:hover:bg-orange-800 font-semibold">
                                    Select All / Deselect All
                                </button>
                            </div>
                            <div class="border-2 border-gray-300 dark:border-gray-600 rounded-xl p-4 max-h-64 overflow-y-auto bg-gray-50 dark:bg-gray-800">
                                @forelse($organizers as $organizer)
                                    <label class="flex items-center p-2 hover:bg-orange-50 dark:hover:bg-orange-900/30 rounded-lg cursor-pointer">
                                        <input type="checkbox" name="organizer_ids[]" value="{{ $organizer->id }}" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $organizer->name }} ({{ $organizer->phone ?: $organizer->email }})
                                        </span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No organizers found</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Estimated Cost -->
                    <div class="mt-6 p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/30 dark:to-pink-900/30 border-2 border-purple-200 dark:border-purple-700 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-purple-900 dark:text-purple-100">Estimated Cost</p>
                                <p class="text-xs text-purple-700 dark:text-purple-300">Based on current recipients and message length</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-black text-purple-600 dark:text-purple-400" x-text="totalCredits">0</p>
                                <p class="text-xs text-purple-700 dark:text-purple-300">Credits</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Options -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Delivery Schedule
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="schedule_type" value="now" x-model="scheduleType" class="peer sr-only" required checked>
                            <div class="p-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 transition-all duration-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-gray-900 dark:text-white">Send Now</span>
                                    <svg class="w-5 h-5 text-indigo-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Send immediately</p>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="schedule_type" value="later" x-model="scheduleType" class="peer sr-only">
                            <div class="p-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 transition-all duration-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-gray-900 dark:text-white">Schedule for Later</span>
                                    <svg class="w-5 h-5 text-indigo-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Choose date and time</p>
                            </div>
                        </label>
                    </div>

                    <!-- Scheduled DateTime -->
                    <div x-show="scheduleType === 'later'" x-cloak class="mt-4">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Schedule Date & Time
                        </label>
                        <input type="datetime-local"
                               name="scheduled_at"
                               class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.sms.dashboard') }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <span x-text="scheduleType === 'later' ? 'Schedule Bulk SMS' : 'Send Bulk SMS'">Send Bulk SMS</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function bulkSmsForm() {
    return {
        recipientType: '{{ old('recipient_type', 'manual') }}',
        scheduleType: '{{ old('schedule_type', 'now') }}',
        manualNumbers: '{{ old('recipients') }}',
        recipientCount: 0,

        get totalCredits() {
            const messageLength = document.querySelector('textarea[name="message"]')?.value.length || 0;
            const creditsPerSms = Math.ceil(messageLength / 160) || 1;

            let count = 0;
            if (this.recipientType === 'manual') {
                count = this.recipientCount;
            } else if (this.recipientType === 'all') {
                count = {{ $totalUsers + $totalOrganizers }};
            } else if (this.recipientType === 'users') {
                count = {{ $totalUsers }};
            } else if (this.recipientType === 'organizers') {
                count = {{ $totalOrganizers }};
            } else if (this.recipientType === 'custom') {
                count = document.querySelectorAll('input[name="user_ids[]"]:checked').length +
                       document.querySelectorAll('input[name="organizer_ids[]"]:checked').length;
            }

            return count * creditsPerSms;
        },

        countManualNumbers() {
            const numbers = this.manualNumbers
                .split(/[\n,]+/)
                .map(n => n.trim())
                .filter(n => n.length > 0);
            this.recipientCount = numbers.length;
        },

        toggleAllUsers() {
            const checkboxes = document.querySelectorAll('input[name="user_ids[]"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        },

        toggleAllOrganizers() {
            const checkboxes = document.querySelectorAll('input[name="organizer_ids[]"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        },

        init() {
            this.countManualNumbers();
        }
    }
}
</script>
@endsection
