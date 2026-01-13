@extends('layouts.admin')

@section('title', 'Send Single SMS')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Send Single SMS
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Send an instant SMS to a single recipient</p>
                </div>
                <a href="{{ route('admin.sms.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Balance Card -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl shadow-xl p-6 text-white">
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

        <!-- Send SMS Form -->
        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    SMS Details
                </h2>
            </div>

            <form method="POST" action="{{ route('admin.sms.send-single.store') }}" class="p-6">
                @csrf

                <!-- Phone Number -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Phone Number *
                    </label>
                    <input type="text"
                           name="phone_number"
                           value="{{ old('phone_number') }}"
                           placeholder="e.g., 0241234567 or 233241234567"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('phone_number') border-red-500 @enderror"
                           required>
                    @error('phone_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Enter phone number with or without country code</p>
                </div>

                <!-- Sender ID -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
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
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        <strong>Super Admin Privilege:</strong> You can type ANY sender ID without approval (max 15 characters)
                    </p>
                </div>

                <!-- Message -->
                <div class="mb-6" x-data="{ message: '{{ old('message') }}', maxLength: 1000 }">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
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
                            <strong>Estimated Credits:</strong>
                            <span x-text="Math.ceil(message.length / 160) || 1">1</span> credit(s)
                        </p>
                        <p class="text-xs text-blue-600 mt-1">SMS are charged per 160 characters (or 70 for Unicode)</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.sms.dashboard') }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Send SMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

        <!-- Help Section -->
        <div class="mt-6 bg-indigo-50 border-2 border-indigo-200 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-indigo-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tips for Sending SMS
            </h3>
            <ul class="space-y-2 text-sm text-indigo-800">
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Keep your message clear and concise for better readability</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Standard SMS supports up to 160 characters per message</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Special characters and emojis may reduce the character limit to 70</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Use a custom Sender ID to increase brand recognition</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
