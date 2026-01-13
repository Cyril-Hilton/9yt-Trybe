@extends('layouts.app')

@section('title', 'Send Bulk SMS')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent">
                        Send Bulk SMS
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Send SMS to multiple recipients at once</p>
                </div>
                <a href="{{ route('user.sms.campaigns.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-200">
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
                        <h2 class="text-3xl font-black">{{ number_format($creditBalance->balance ?? 0) }} Credits</h2>
                    </div>
                    <a href="{{ route('user.sms.wallet.index') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl font-bold transition-all duration-200">
                        Buy Credits
                    </a>
                </div>
            </div>
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

        <form method="POST" action="{{ route('user.sms.campaigns.send') }}" enctype="multipart/form-data" x-data="bulkSmsForm()" class="space-y-6">
            @csrf

            <!-- Campaign Details -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border-2 border-indigo-100 dark:border-indigo-900/50 overflow-hidden">
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
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sender ID -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Sender ID (Optional)
                        </label>
                        <select name="sender_id" class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
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
                            <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                                No approved sender IDs. <a href="{{ route('user.sms.sender-ids.create') }}" class="underline font-semibold">Request one here</a>
                            </p>
                        @endif
                    </div>

                    <!-- Message -->
                    <div>
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
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <div class="mt-1 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>Maximum 1000 characters</span>
                            <span x-text="message.length + ' / 1000'">0 / 1000</span>
                        </div>
                        <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                            <p class="text-sm text-blue-800 dark:text-blue-300">
                                <strong>Credits per SMS:</strong>
                                <span x-text="creditsPerSms"></span> credit(s)
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients Selection -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border-2 border-indigo-100 dark:border-indigo-900/50 overflow-hidden">
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
                    <div class="mb-6" x-data="{ showUpgradeModal: false }">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Choose how to add recipients *</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Manual Entry -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="manual" x-model="recipientType" class="peer sr-only" required checked>
                                <div class="h-full p-5 border-2 border-gray-300 dark:border-gray-600 rounded-2xl hover:border-blue-400 hover:shadow-lg peer-checked:border-blue-600 peer-checked:bg-gradient-to-br peer-checked:from-blue-50 peer-checked:to-blue-100 dark:peer-checked:from-blue-900/20 dark:peer-checked:to-blue-800/20 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                            <!-- From Contacts -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="contacts" x-model="recipientType" class="peer sr-only">
                                <div class="h-full p-5 border-2 border-gray-300 dark:border-gray-600 rounded-2xl hover:border-green-400 hover:shadow-lg peer-checked:border-green-600 peer-checked:bg-gradient-to-br peer-checked:from-green-50 peer-checked:to-green-100 dark:peer-checked:from-green-900/20 dark:peer-checked:to-green-800/20 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">From Contacts</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">Select saved contacts</p>
                                        </div>
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
                                <div class="h-full p-5 border-2 border-gray-300 dark:border-gray-600 rounded-2xl hover:border-orange-400 hover:shadow-lg peer-checked:border-orange-600 peer-checked:bg-gradient-to-br peer-checked:from-orange-50 peer-checked:to-orange-100 dark:peer-checked:from-orange-900/20 dark:peer-checked:to-orange-800/20 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div class="w-14 h-14 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-7 h-7 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">By Group</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">Select contact group</p>
                                        </div>
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-orange-600 items-center justify-center hidden peer-checked:flex">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Excel Upload -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="recipient_type" value="excel" x-model="recipientType" class="peer sr-only">
                                <div class="h-full p-5 border-2 border-gray-300 dark:border-gray-600 rounded-2xl hover:border-teal-400 hover:shadow-lg peer-checked:border-teal-600 peer-checked:bg-gradient-to-br peer-checked:from-teal-50 peer-checked:to-teal-100 dark:peer-checked:from-teal-900/20 dark:peer-checked:to-teal-800/20 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div class="w-14 h-14 rounded-full bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-7 h-7 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white block mb-1">Excel Upload</span>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">Upload Excel file</p>
                                        </div>
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-teal-600 items-center justify-center hidden peer-checked:flex">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Use Our Contacts (Premium - Upgrade Required) -->
                            <div class="relative cursor-pointer group" @click="showUpgradeModal = true">
                                <div class="h-full p-5 border-2 border-purple-300 dark:border-purple-700 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 hover:border-purple-500 dark:hover:border-purple-500 hover:shadow-2xl hover:shadow-purple-200 dark:hover:shadow-purple-900/50 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                                    <!-- Premium Badge -->
                                    <div class="absolute -top-2 -right-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg transform rotate-12 group-hover:rotate-0 transition-transform duration-300">
                                        ORGANIZERS
                                    </div>
                                    <div class="flex flex-col items-center text-center space-y-3">
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-black text-purple-900 dark:text-purple-100 block mb-1 text-sm">Use Our Contacts</span>
                                            <div class="flex items-center justify-center space-x-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-2 py-1 rounded-full text-xs font-bold shadow-md">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>GH₵1/contact</span>
                                            </div>
                                            <p class="text-xs text-purple-700 dark:text-purple-300 mt-1">Organizers Only</p>
                                        </div>
                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-purple-600/10 dark:bg-purple-400/10 rounded-2xl">
                                            <span class="text-sm font-bold text-purple-900 dark:text-purple-100">Click to Learn More</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upgrade Modal -->
                        <div x-show="showUpgradeModal"
                             x-cloak
                             @click.away="showUpgradeModal = false"
                             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                             style="display: none;">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all"
                                 @click.stop>
                                <!-- Header -->
                                <div class="text-center mb-6">
                                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Organizer Feature</h3>
                                    <p class="text-gray-600 dark:text-gray-400">This feature is available for event organizers only</p>
                                </div>

                                <!-- Feature Description -->
                                <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 mb-6 border border-purple-200 dark:border-purple-800">
                                    <h4 class="font-bold text-purple-900 dark:text-purple-100 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                        </svg>
                                        Use Our Premium Contact Database
                                    </h4>
                                    <ul class="text-sm text-purple-800 dark:text-purple-200 space-y-1">
                                        <li>✓ Access thousands of verified contacts</li>
                                        <li>✓ Pay only GH₵1.00 per contact used</li>
                                        <li>✓ Admin approval for quality assurance</li>
                                        <li>✓ Boost your campaign reach instantly</li>
                                    </ul>
                                </div>

                                <!-- CTA -->
                                <div class="space-y-3">
                                    <a href="{{ route('organization.register') }}"
                                       class="block w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-pink-700 transform hover:scale-105 transition-all shadow-lg text-center">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                        </svg>
                                        Sign Up as Organizer
                                    </a>
                                    <button @click="showUpgradeModal = false"
                                            class="block w-full px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                        Maybe Later
                                    </button>
                                </div>

                                <!-- Close Button -->
                                <button @click="showUpgradeModal = false"
                                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
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
                                  class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-sm dark:bg-gray-700 dark:text-white"
                                  x-model="manualNumbers"
                                  @input="countManualNumbers()">{{ old('recipients') }}</textarea>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Recipients: <span class="font-bold text-indigo-600 dark:text-indigo-400" x-text="recipientCount">0</span>
                        </p>
                    </div>

                    <!-- Excel Upload -->
                    <div x-show="recipientType === 'excel'" x-cloak>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Upload Excel File
                        </label>
                        <input type="file"
                               name="excel_file"
                               accept=".xlsx,.xls,.csv"
                               class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">

                        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                            <h4 class="text-sm font-bold text-blue-900 dark:text-blue-300 mb-2">📋 How to use column placeholders:</h4>
                            <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                                <li>• Use <code class="px-2 py-0.5 bg-blue-100 dark:bg-blue-800 rounded">[1]</code> to insert values from column 1</li>
                                <li>• Use <code class="px-2 py-0.5 bg-blue-100 dark:bg-blue-800 rounded">[2]</code> to insert values from column 2</li>
                                <li>• Use <code class="px-2 py-0.5 bg-blue-100 dark:bg-blue-800 rounded">[3]</code>, <code class="px-2 py-0.5 bg-blue-100 dark:bg-blue-800 rounded">[4]</code>, etc. for other columns</li>
                                <li>• <strong>Phone numbers can be in ANY column</strong> - system auto-detects them!</li>
                            </ul>
                            <div class="mt-3 p-3 bg-blue-100 dark:bg-blue-800 rounded-lg">
                                <p class="text-xs font-semibold text-blue-900 dark:text-blue-200 mb-2">Example Messages:</p>
                                <p class="text-sm text-blue-900 dark:text-blue-200 font-mono mb-2">"Dear [1], Happy New Year! Your event is on [3]."</p>
                                <p class="text-sm text-blue-900 dark:text-blue-200 font-mono">"Hello [2], reminder: [3] at [4]"</p>
                            </div>
                            <p class="mt-2 text-xs text-blue-700 dark:text-blue-400"><strong>Tip:</strong> Put phone numbers in any column. Names, dates, or other data can be in any other column. Use [1], [2], [3], etc. to reference them in your message.</p>
                        </div>
                    </div>

                    <!-- From Contacts -->
                    <div x-show="recipientType === 'contacts'" x-cloak>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Select Contacts
                        </label>
                        @if($contacts->count() > 0)
                        <div class="border-2 border-gray-300 dark:border-gray-600 rounded-xl p-4 max-h-96 overflow-y-auto">
                            <div class="space-y-2">
                                @foreach($contacts as $contact)
                                <label class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                                    <input type="checkbox" name="contact_ids[]" value="{{ $contact->id }}" class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                    <div class="ml-3 flex-1">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $contact->name ?: 'No Name' }}
                                        </div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                                            {{ $contact->phone_number }}
                                        </div>
                                    </div>
                                    @if($contact->group)
                                    <span class="ml-2 px-2 py-1 text-xs bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300 rounded-full">
                                        {{ $contact->group }}
                                    </span>
                                    @endif
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="border-2 border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                You don't have any saved contacts yet.
                            </p>
                            <a href="{{ route('user.sms.contacts.index') }}" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Contacts Now
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- By Group -->
                    <div x-show="recipientType === 'group'" x-cloak>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Select Group
                        </label>
                        @if(count($groups) > 0)
                        <select name="group" class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">Choose a group...</option>
                            @foreach($groups as $group)
                                <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                        @else
                        <div class="border-2 border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                No contact groups found.
                            </p>
                            <a href="{{ route('user.sms.contacts.index') }}" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Import Contacts with Groups
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Estimated Cost -->
                    <div class="mt-6 p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border-2 border-purple-200 dark:border-purple-800 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-purple-900 dark:text-purple-300">Estimated Cost</p>
                                <p class="text-xs text-purple-700 dark:text-purple-400">Based on current recipients and message length</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-black text-purple-600 dark:text-purple-400" x-text="totalCredits"></p>
                                <p class="text-xs text-purple-700 dark:text-purple-400">Credits</p>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-purple-200 dark:border-purple-800 grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-purple-700 dark:text-purple-400">Recipients:</span>
                                <span class="font-bold text-purple-900 dark:text-purple-300" x-text="recipientCount"></span>
                            </div>
                            <div>
                                <span class="text-purple-700 dark:text-purple-400">Credits/SMS:</span>
                                <span class="font-bold text-purple-900 dark:text-purple-300" x-text="creditsPerSms"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Options -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border-2 border-indigo-100 dark:border-indigo-900/50 overflow-hidden">
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
                            <div class="p-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 transition-all duration-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-gray-900 dark:text-white">Send Now</span>
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Send immediately</p>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="schedule_type" value="later" x-model="scheduleType" class="peer sr-only">
                            <div class="p-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 transition-all duration-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-gray-900 dark:text-white">Schedule for Later</span>
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
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
                               class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               min="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('user.sms.campaigns.index') }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <span x-text="scheduleType === 'later' ? 'Schedule Campaign' : 'Send Bulk SMS'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function bulkSmsForm() {
    // Check URL parameter for recipient type
    const urlParams = new URLSearchParams(window.location.search);
    const typeParam = urlParams.get('type');
    const defaultType = typeParam === 'excel' ? 'excel' : 'manual';

    return {
        message: '{{ old('message') }}',
        recipientType: '{{ old('recipient_type') }}' || defaultType,
        scheduleType: '{{ old('schedule_type', 'now') }}',
        manualNumbers: '{{ old('recipients') }}',
        recipientCount: 0,

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

        countManualNumbers() {
            // Split by newline or comma and filter empty values
            const numbers = this.manualNumbers
                .split(/[\n,]+/)
                .map(n => n.trim())
                .filter(n => n.length > 0);
            this.recipientCount = numbers.length;
        },

        init() {
            this.countManualNumbers();
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
