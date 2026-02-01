@extends('layouts.company')

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
                    <p class="mt-2 text-gray-600">Send SMS to multiple recipients at once</p>
                </div>
                <a href="{{ route('organization.sms.campaigns.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Campaigns
                </a>
            </div>
        </div>

        <!-- Balance Card -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Available Balance</p>
                        <h2 class="text-3xl font-black">{{ number_format($creditBalance->balance) }} Credits</h2>
                    </div>
                    <a href="{{ route('organization.sms.wallet.index') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl font-bold transition-all duration-200">
                        Buy Credits
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('organization.sms.campaigns.send-bulk.store') }}" x-data="bulkSmsForm()" class="space-y-6">
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
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Campaign Name *
                        </label>
                        <input type="text"
                               name="campaign_name"
                               value="{{ old('campaign_name') }}"
                               placeholder="e.g., December Promotion"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('campaign_name') border-red-500 @enderror"
                               required>
                        @error('campaign_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sender ID -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Sender ID (Optional)
                        </label>
                        <select name="sender_id" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Use Default Sender ID</option>
                            @foreach($senderIds as $senderId)
                                <option value="{{ $senderId->sender_id }}">
                                    {{ $senderId->sender_id }}
                                    @if($senderId->is_default)
                                        (Default)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @if($senderIds->count() === 0)
                            <p class="mt-1 text-xs text-yellow-600">
                                No approved sender IDs. <a href="{{ route('organization.sms.sender-ids.create') }}" class="underline font-semibold">Request one here</a>
                            </p>
                    @endif
                </div>

                <!-- AI SMS Assistant -->
                <div x-data="aiSmsAssistant()">
                    <div class="rounded-2xl border border-blue-200 bg-blue-50/70 p-4">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <h3 class="text-sm font-bold text-blue-900">AI SMS Assistant</h3>
                            <span class="text-xs text-blue-700">Generate a compliant SMS draft</span>
                        </div>
                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Purpose</label>
                                <input type="text" x-model="purpose"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       placeholder="e.g., promo blast, reminder">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tone</label>
                                <select x-model="tone"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="friendly and professional">Friendly & Professional</option>
                                    <option value="urgent and action-oriented">Urgent & Action-Oriented</option>
                                    <option value="warm and celebratory">Warm & Celebratory</option>
                                    <option value="minimal and direct">Minimal & Direct</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Key Details *</label>
                                <input type="text" x-model="details"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       placeholder="Event name, date, offer, location, link">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">CTA (Optional)</label>
                                <input type="text" x-model="cta"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       placeholder="Book now, Reply YES, Visit link">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Max Length</label>
                                <select x-model="maxLength"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="160">160 chars</option>
                                    <option value="320">320 chars</option>
                                    <option value="480">480 chars</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-3">
                            <button type="button" @click="generateSms"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition"
                                    :disabled="loading">
                                <span x-show="!loading">Generate SMS</span>
                                <span x-show="loading">Generating...</span>
                            </button>
                            <span x-text="statusMessage" class="text-xs text-gray-600"></span>
                        </div>
                    </div>
                </div>

                <!-- Message -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Message *
                    </label>
                        <textarea name="message"
                                  rows="6"
                                  placeholder="Type your message here..."
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('message') border-red-500 @enderror"
                                  required
                                  x-model="message"
                                  maxlength="1000">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                            <span>Maximum 1000 characters</span>
                            <span x-text="message.length + ' / 1000'">0 / 1000</span>
                        </div>
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                            <p class="text-sm text-blue-800">
                                <strong>Credits per SMS:</strong>
                                <span x-text="creditsPerSms"></span> credit(s)
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
                        <label class="block text-sm font-bold text-gray-700 mb-3">Choose how to add recipients *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                            <!-- Manual Entry -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="manual" x-model="recipientType" class="peer sr-only" required>
                                <div class="h-full p-5 border-2 border-gray-300 rounded-2xl hover:border-blue-400 hover:shadow-lg peer-checked:border-blue-600 peer-checked:bg-gradient-to-br peer-checked:from-blue-50 peer-checked:to-blue-100 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <!-- Icon -->
                                        <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 peer-checked:bg-blue-600">
                                            <svg class="w-7 h-7 text-blue-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </div>
                                        <!-- Title -->
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">Manual Entry</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">Type or paste numbers</p>
                                        </div>
                                        <!-- Checkmark -->
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-blue-600 items-center justify-center hidden peer-checked:flex">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- From Contacts -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="contacts" x-model="recipientType" class="peer sr-only">
                                <div class="h-full p-5 border-2 border-gray-300 rounded-2xl hover:border-green-400 hover:shadow-lg peer-checked:border-green-600 peer-checked:bg-gradient-to-br peer-checked:from-green-50 peer-checked:to-green-100 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <!-- Icon -->
                                        <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 peer-checked:bg-green-600">
                                            <svg class="w-7 h-7 text-green-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <!-- Title -->
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">From Contacts</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">Select saved contacts</p>
                                        </div>
                                        <!-- Checkmark -->
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-green-600 items-center justify-center hidden peer-checked:flex">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- By Group -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="group" x-model="recipientType" class="peer sr-only">
                                <div class="h-full p-5 border-2 border-gray-300 rounded-2xl hover:border-orange-400 hover:shadow-lg peer-checked:border-orange-600 peer-checked:bg-gradient-to-br peer-checked:from-orange-50 peer-checked:to-orange-100 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <!-- Icon -->
                                        <div class="w-14 h-14 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 peer-checked:bg-orange-600">
                                            <svg class="w-7 h-7 text-orange-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                        <!-- Title -->
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">By Group</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">Select contact group</p>
                                        </div>
                                        <!-- Checkmark -->
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-orange-600 items-center justify-center hidden peer-checked:flex">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Conference -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="conference" x-model="recipientType" class="peer sr-only">
                                <div class="h-full p-5 border-2 border-gray-300 rounded-2xl hover:border-indigo-400 hover:shadow-lg peer-checked:border-indigo-600 peer-checked:bg-gradient-to-br peer-checked:from-indigo-50 peer-checked:to-indigo-100 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <!-- Icon -->
                                        <div class="w-14 h-14 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 peer-checked:bg-indigo-600">
                                            <svg class="w-7 h-7 text-indigo-600 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <!-- Title -->
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">Conference</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">From registrations</p>
                                        </div>
                                        <!-- Checkmark -->
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-indigo-600 items-center justify-center hidden peer-checked:flex">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Use Our Contacts (Premium) -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="use_our_contacts" x-model="recipientType" class="peer sr-only">
                                <div class="h-full p-5 border-2 border-purple-300 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 hover:border-purple-500 hover:shadow-2xl hover:shadow-purple-200 peer-checked:border-purple-600 peer-checked:from-purple-100 peer-checked:to-pink-100 peer-checked:shadow-2xl peer-checked:shadow-purple-300 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                                    <!-- Premium Badge -->
                                    <div class="absolute -top-2 -right-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg transform rotate-12 group-hover:rotate-0 transition-transform duration-300">
                                        PREMIUM
                                    </div>
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <!-- Icon -->
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                        </div>
                                        <!-- Title -->
                                        <div>
                                            <span class="font-black text-purple-900 dark:text-purple-100 block mb-1 text-lg">Use Our Contacts</span>
                                            <div class="flex items-center justify-center space-x-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>GH₵1.00/contact</span>
                                            </div>
                                        </div>
                                        <!-- Checkmark -->
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-gradient-to-r from-purple-600 to-pink-600 items-center justify-center hidden peer-checked:flex shadow-lg">
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
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Phone Numbers (one per line or comma-separated)
                        </label>
                        <textarea name="recipients"
                                  rows="8"
                                  placeholder="0241234567&#10;0551234567&#10;233201234567&#10;or&#10;0241234567, 0551234567, 233201234567"
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-sm"
                                  x-model="manualNumbers"
                                  @input="countManualNumbers()">{{ old('recipients') }}</textarea>
                        <p class="mt-1 text-sm text-gray-600">
                            Recipients: <span class="font-bold text-indigo-600" x-text="recipientCount">0</span>
                        </p>
                    </div>

                    <!-- From Contacts -->
                    <div x-show="recipientType === 'contacts'" x-cloak>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Select Contacts
                        </label>
                        <div class="border-2 border-gray-300 rounded-xl p-4 max-h-96 overflow-y-auto">
                            <p class="text-sm text-gray-600 mb-3">
                                You don't have any saved contacts yet.
                                <a href="{{ route('organization.sms.contacts.import') }}" class="text-indigo-600 font-semibold underline">Import contacts</a>
                            </p>
                            <!-- Contacts will be loaded here if available -->
                        </div>
                    </div>

                    <!-- By Group -->
                    <div x-show="recipientType === 'group'" x-cloak>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Select Group
                        </label>
                        <select name="group" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Choose a group...</option>
                            @foreach($groups as $group)
                                <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                        @if(count($groups) === 0)
                            <p class="mt-1 text-xs text-yellow-600">
                                No contact groups found. <a href="{{ route('organization.sms.contacts.import') }}" class="underline font-semibold">Import contacts with groups</a>
                            </p>
                        @endif
                    </div>

                    <!-- From Conference Registrations -->
                    <div x-show="recipientType === 'conference'" x-cloak>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Select Conference *
                                </label>
                                <select name="conference_id"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        x-model="selectedConference"
                                        @change="updateConferenceCount()">
                                    <option value="">Choose a conference...</option>
                                    @foreach($conferences as $conference)
                                        <option value="{{ $conference->id }}"
                                                data-online="{{ $conference->online_count }}"
                                                data-inperson="{{ $conference->in_person_count }}"
                                                data-total="{{ $conference->registrations_count }}">
                                            {{ $conference->title }} ({{ $conference->registrations_count }} registrations)
                                        </option>
                                    @endforeach
                                </select>
                                @if($conferences->count() === 0)
                                    <p class="mt-1 text-xs text-yellow-600">
                                        No conferences found. <a href="{{ route('organization.conferences.create') }}" class="underline font-semibold">Create a conference first</a>
                                    </p>
                                @endif
                            </div>

                            <div x-show="selectedConference" x-cloak>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    Filter by Attendance Type *
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="attendance_filter" value="all" x-model="attendanceFilter" class="peer sr-only" @change="updateConferenceCount()">
                                        <div class="p-3 border-2 border-gray-300 rounded-lg hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all duration-200">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-sm text-gray-900">All Attendees</span>
                                                <svg class="w-4 h-4 text-indigo-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-1" x-text="'(' + conferenceTotal + ' people)'"></p>
                                        </div>
                                    </label>

                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="attendance_filter" value="online" x-model="attendanceFilter" class="peer sr-only" @change="updateConferenceCount()">
                                        <div class="p-3 border-2 border-gray-300 rounded-lg hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all duration-200">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-sm text-gray-900">Online Only</span>
                                                <svg class="w-4 h-4 text-indigo-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-1" x-text="'(' + conferenceOnline + ' people)'"></p>
                                        </div>
                                    </label>

                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="attendance_filter" value="in_person" x-model="attendanceFilter" class="peer sr-only" @change="updateConferenceCount()">
                                        <div class="p-3 border-2 border-gray-300 rounded-lg hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all duration-200">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-sm text-gray-900">In-Person Only</span>
                                                <svg class="w-4 h-4 text-indigo-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-1" x-text="'(' + conferenceInPerson + ' people)'"></p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Use Our Contacts (Purchase Contacts) -->
                    <div x-show="recipientType === 'use_our_contacts'" x-cloak>
                        <div class="space-y-4">
                            <!-- Info Banner -->
                            <div class="bg-gradient-to-r from-purple-100 to-pink-100 border-2 border-purple-300 rounded-xl p-4">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-purple-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <h4 class="font-bold text-purple-900 mb-1">How It Works</h4>
                                        <ul class="text-sm text-purple-800 space-y-1">
                                            <li>✓ Specify how many contacts you need</li>
                                            <li>✓ We provide verified phone numbers from our database</li>
                                            <li>✓ Cost: GH₵1.00 per contact + SMS unit charges</li>
                                            <li>✓ Admin approval required before sending</li>
                                            <li>✓ Pay via Paystack after approval</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Target Recipients Input -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    How many contacts do you need? *
                                </label>
                                <input type="number"
                                       name="target_recipient_count"
                                       min="1"
                                       max="100000"
                                       placeholder="e.g., 200"
                                       class="w-full px-4 py-3 border-2 border-purple-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-lg font-semibold"
                                       x-bind:required="recipientType === 'use_our_contacts'"
                                       x-bind:disabled="recipientType !== 'use_our_contacts'"
                                       x-model.number="targetContactCount"
                                       @input="recipientCount = targetContactCount">
                                <p class="mt-2 text-sm text-gray-600">
                                    Enter the number of people you want to reach (minimum: 1, maximum: 100,000)
                                </p>
                            </div>

                            <!-- Cost Breakdown -->
                            <div class="bg-white border-2 border-purple-200 rounded-xl p-4 space-y-3">
                                <h4 class="font-bold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Cost Breakdown
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                        <span class="text-gray-600">Contact Purchase Fee:</span>
                                        <span class="font-bold text-purple-600" x-text="'GH₵' + (targetContactCount * 1).toFixed(2)">GH₵0.00</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                        <span class="text-gray-600">SMS Units Required:</span>
                                        <span class="font-bold text-indigo-600" x-text="(targetContactCount * creditsPerSms) + ' credits'">0 credits</span>
                                    </div>
                                    <div class="flex justify-between py-3 bg-purple-50 rounded-lg px-3">
                                        <span class="font-bold text-gray-900">Total Amount to Pay:</span>
                                        <span class="font-black text-xl text-purple-600" x-text="'GH₵' + (targetContactCount * 1).toFixed(2)">GH₵0.00</span>
                                    </div>
                                    <p class="text-xs text-gray-500 italic pt-2">
                                        * SMS units will be deducted from your balance. Only the contact fee needs payment.
                                    </p>
                                </div>
                            </div>

                            <!-- Workflow Steps -->
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                <h4 class="font-bold text-gray-900 mb-3 text-sm">📋 What Happens Next:</h4>
                                <ol class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <span class="font-bold text-purple-600 mr-2">1.</span>
                                        <span>Submit your campaign for admin review</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="font-bold text-purple-600 mr-2">2.</span>
                                        <span>Admin approves/rejects within 24 hours</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="font-bold text-purple-600 mr-2">3.</span>
                                        <span>Receive billing email with Paystack payment link</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="font-bold text-purple-600 mr-2">4.</span>
                                        <span>Complete payment via Paystack</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="font-bold text-purple-600 mr-2">5.</span>
                                        <span>SMS sent automatically after payment confirmation</span>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Estimated Cost -->
                    <div class="mt-6 p-4 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-purple-900">Estimated Cost</p>
                                <p class="text-xs text-purple-700">Based on current recipients and message length</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-black text-purple-600" x-text="totalCredits"></p>
                                <p class="text-xs text-purple-700">Credits</p>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-purple-200 grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-purple-700">Recipients:</span>
                                <span class="font-bold text-purple-900" x-text="recipientCount"></span>
                            </div>
                            <div>
                                <span class="text-purple-700">Credits/SMS:</span>
                                <span class="font-bold text-purple-900" x-text="creditsPerSms"></span>
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
                            <div class="p-4 border-2 border-gray-300 rounded-xl hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all duration-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-gray-900">Send Now</span>
                                    <svg class="w-5 h-5 text-indigo-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600">Send immediately</p>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="schedule_type" value="later" x-model="scheduleType" class="peer sr-only">
                            <div class="p-4 border-2 border-gray-300 rounded-xl hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all duration-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-gray-900">Schedule for Later</span>
                                    <svg class="w-5 h-5 text-indigo-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600">Choose date and time</p>
                            </div>
                        </label>
                    </div>

                    <!-- Scheduled DateTime -->
                    <div x-show="scheduleType === 'later'" x-cloak class="mt-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Schedule Date & Time
                        </label>
                        <input type="datetime-local"
                               name="scheduled_at"
                               x-bind:required="scheduleType === 'later'"
                               x-bind:disabled="scheduleType !== 'later'"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('organization.sms.campaigns.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <span x-text="requiresApproval ? 'Submit for Approval' : (scheduleType === 'later' ? 'Schedule Campaign' : 'Send Bulk SMS')"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function aiSmsAssistant() {
    return {
        purpose: '',
        details: '',
        cta: '',
        tone: 'friendly and professional',
        maxLength: 160,
        loading: false,
        statusMessage: '',
        async generateSms() {
            if (!this.details.trim()) {
                this.statusMessage = 'Add key details first.';
                return;
            }

            this.loading = true;
            this.statusMessage = 'Thinking...';

            try {
                const response = await fetch('{{ route('organization.ai.sms-draft') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                    },
                    body: JSON.stringify({
                        purpose: this.purpose,
                        details: this.details,
                        cta: this.cta,
                        tone: this.tone,
                        max_length: this.maxLength
                    })
                });

                const data = await response.json();
                if (!response.ok || !data.success) {
                    this.statusMessage = data.message || 'AI is unavailable right now.';
                    return;
                }

                const messageField = document.querySelector('textarea[name=\"message\"]');
                if (messageField) {
                    messageField.value = data.data.message || '';
                    messageField.dispatchEvent(new Event('input', { bubbles: true }));
                }

                this.statusMessage = 'Draft ready. Review and send.';
            } catch (error) {
                console.error('AI SMS error:', error);
                this.statusMessage = 'AI request failed. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}

function bulkSmsForm() {
    return {
        message: '{{ old('message') }}',
        recipientType: '{{ old('recipient_type', 'manual') }}',
        scheduleType: '{{ old('schedule_type', 'now') }}',
        manualNumbers: '{{ old('recipients') }}',
        recipientCount: 0,
        selectedConference: '{{ old('conference_id', '') }}',
        attendanceFilter: '{{ old('attendance_filter', 'all') }}',
        conferenceTotal: 0,
        conferenceOnline: 0,
        conferenceInPerson: 0,
        targetContactCount: {{ old('target_recipient_count', 0) }},

        get creditsPerSms() {
            if (this.message.length === 0) return 1;
            // Simple estimation: 160 chars for GSM, 70 for Unicode
            const hasUnicode = /[^\x00-\x7F]/.test(this.message);
            const limit = hasUnicode ? 70 : 160;
            return Math.ceil(this.message.length / limit);
        },

        get totalCredits() {
            return this.recipientCount * this.creditsPerSms;
        },

        get requiresApproval() {
            return this.recipientType === 'use_our_contacts';
        },

        countManualNumbers() {
            // Split by newline or comma and filter empty values
            const numbers = this.manualNumbers
                .split(/[\n,]+/)
                .map(n => n.trim())
                .filter(n => n.length > 0);
            this.recipientCount = numbers.length;
        },

        updateConferenceCount() {
            if (!this.selectedConference) {
                this.conferenceTotal = 0;
                this.conferenceOnline = 0;
                this.conferenceInPerson = 0;
                this.recipientCount = 0;
                return;
            }

            // Get the selected option element
            const selectElement = document.querySelector('select[name="conference_id"]');
            const selectedOption = selectElement.options[selectElement.selectedIndex];

            if (selectedOption) {
                this.conferenceTotal = parseInt(selectedOption.dataset.total || 0);
                this.conferenceOnline = parseInt(selectedOption.dataset.online || 0);
                this.conferenceInPerson = parseInt(selectedOption.dataset.inperson || 0);

                // Update recipient count based on filter
                if (this.attendanceFilter === 'all') {
                    this.recipientCount = this.conferenceTotal;
                } else if (this.attendanceFilter === 'online') {
                    this.recipientCount = this.conferenceOnline;
                } else if (this.attendanceFilter === 'in_person') {
                    this.recipientCount = this.conferenceInPerson;
                }
            }
        },

        init() {
            this.countManualNumbers();
            // Update conference count if a conference is pre-selected (e.g., from old input)
            if (this.selectedConference) {
                this.$nextTick(() => {
                    this.updateConferenceCount();
                });
            }

            // Set initial count if using our contacts
            if (this.recipientType === 'use_our_contacts' && this.targetContactCount > 0) {
                this.recipientCount = this.targetContactCount;
            }

            // Watch recipientType changes to reset count when switching
            this.$watch('recipientType', (value) => {
                if (value !== 'manual' && value !== 'use_our_contacts') {
                    this.recipientCount = 0;
                }
                if (value === 'conference' && this.selectedConference) {
                    this.updateConferenceCount();
                }
                if (value === 'use_our_contacts' && this.targetContactCount > 0) {
                    this.recipientCount = this.targetContactCount;
                }
            });
        }
    }
}
</script>
@endsection
