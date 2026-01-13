@extends('layouts.app')

@section('title', 'Resend Campaign - ' . $campaign->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('user.sms.campaigns.show', $campaign->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold mb-4 inline-block">
                ← Back to Campaign
            </a>
            <h1 class="text-4xl font-bold gradient-text mb-2">Resend Campaign</h1>
            <p class="text-gray-600 dark:text-gray-400">Edit and resend: {{ $campaign->name }}</p>
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

        <!-- Info Box -->
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-800 rounded-xl p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 dark:text-blue-100 mb-2">Resend Campaign</h3>
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        You can edit the recipients and message before resending. The campaign will be sent as a new campaign with the same content.
                    </p>
                </div>
            </div>
        </div>

        <!-- Resend Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                <h2 class="text-xl font-bold text-white">Campaign Details</h2>
            </div>

            <form method="POST" action="{{ route('user.sms.campaigns.send') }}" class="p-6 space-y-6">
                @csrf

                <!-- Hidden fields required by send controller -->
                <input type="hidden" name="recipient_type" value="manual">
                <input type="hidden" name="schedule_type" value="now">

                <!-- Campaign Name (Read-only info) -->
                <div class="bg-gray-50 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Original Campaign</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $campaign->name }}</p>
                </div>

                <!-- Campaign Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Campaign Name *
                    </label>
                    <input type="text"
                           name="campaign_name"
                           value="{{ old('campaign_name', $campaign->name . ' (Resent)') }}"
                           required
                           maxlength="255"
                           placeholder="e.g., Conference Reminder"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('campaign_name') border-red-500 @enderror">
                    @error('campaign_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Recipients -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Recipients *
                    </label>
                    <textarea name="recipients"
                              rows="4"
                              required
                              placeholder="0241234567, 0551234567, 0201234567"
                              class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('recipients') border-red-500 @enderror">{{ old('recipients', $recipientsText) }}</textarea>
                    @error('recipients')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">You can edit, add, or remove phone numbers. Separate with commas or newlines.</p>
                </div>

                <!-- Message -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Message *
                    </label>
                    <textarea name="message"
                              rows="6"
                              required
                              maxlength="1000"
                              class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('message') border-red-500 @enderror"
                              placeholder="Type your message here...">{{ old('message', $campaign->message) }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum 1000 characters. SMS will be split if longer than 160 characters.</p>
                </div>

                <!-- Sender ID -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Sender ID (Optional)
                    </label>
                    <select name="sender_id" class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('sender_id') border-red-500 @enderror">
                        <option value="">9yt Trybe (Default)</option>
                        @foreach($senderIds as $senderId)
                            <option value="{{ $senderId->sender_id }}" {{ old('sender_id', $campaign->sender_id) == $senderId->sender_id ? 'selected' : '' }}>
                                {{ $senderId->sender_id }}
                            </option>
                        @endforeach
                    </select>
                    @error('sender_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Your approved sender IDs or use the default</p>
                </div>

                <!-- SMS Credit Balance -->
                <div class="bg-indigo-50 dark:bg-indigo-900/20 border-2 border-indigo-200 dark:border-indigo-800 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-indigo-600 dark:text-indigo-400 font-semibold">Your SMS Credit Balance</p>
                            <p class="text-2xl font-black text-indigo-900 dark:text-indigo-100">{{ number_format($creditBalance->balance) }} Credits</p>
                        </div>
                        <svg class="w-12 h-12 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                    <a href="{{ route('user.sms.campaigns.show', $campaign->id) }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Send Campaign
                    </button>
                </div>
            </form>
        </div>

        <!-- Original Campaign Stats -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Original Campaign Stats</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Recipients</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($campaign->total_recipients) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Delivered</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($campaign->total_delivered) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Failed</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($campaign->total_failed) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
