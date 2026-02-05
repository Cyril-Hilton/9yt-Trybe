@extends('layouts.company')

@section('title', 'Resend Campaign')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('organization.sms.campaigns.show', $campaign->id) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold mb-4 inline-block">
                ← Back to Campaign
            </a>
            <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Resend Campaign
            </h1>
            <p class="mt-2 text-gray-600">Edit and resend: {{ $campaign->name }}</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-2 border-green-200 rounded-xl p-4">
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-xl p-4">
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Info Box -->
        <div class="mb-6 bg-blue-50 border-2 border-blue-200 rounded-2xl p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Resend Campaign</h3>
                    <p class="text-sm text-blue-800">
                        You can edit the recipients and message before resending. The campaign will be sent as a new campaign with the same content.
                    </p>
                </div>
            </div>
        </div>

        <!-- Resend Form -->
        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                <h2 class="text-xl font-bold text-white">Campaign Details</h2>
            </div>

            <form method="POST" action="{{ route('organization.sms.campaigns.send-single.store') }}" class="p-6 space-y-6">
                @csrf

                <!-- Campaign Name (Read-only info) -->
                <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-4">
                    <p class="text-sm text-gray-600 mb-1">Original Campaign</p>
                    <p class="text-lg font-bold text-gray-900">{{ $campaign->name }}</p>
                </div>

                <!-- Recipients -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Recipients *
                    </label>
                    <textarea name="phone_number"
                              rows="4"
                              required
                              placeholder="0241234567, 0551234567, 0201234567"
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('phone_number') border-red-500 @enderror">{{ old('phone_number', $recipientsText) }}</textarea>
                    @error('phone_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">You can edit, add, or remove phone numbers. Separate with commas.</p>
                </div>

                <!-- Message -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Message *
                    </label>
                    <textarea name="message"
                              rows="6"
                              required
                              maxlength="1000"
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('message') border-red-500 @enderror"
                              placeholder="Type your message here...">{{ old('message', $campaign->message) }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Maximum 1000 characters. SMS will be split if longer than 160 characters.</p>
                </div>

                <!-- Sender ID -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Sender ID (Optional)
                    </label>
                    <select name="sender_id" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('sender_id') border-red-500 @enderror">
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
                    <p class="mt-1 text-xs text-gray-500">Your approved sender IDs or use the default</p>
                </div>

                <!-- SMS Credit Balance -->
                <div class="bg-indigo-50 border-2 border-indigo-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-indigo-600 font-semibold">Your SMS Credit Balance</p>
                            <p class="text-2xl font-black text-indigo-900">{{ number_format((float) $creditBalanceValue) }} Credits</p>
                        </div>
                        <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200">
                    <a href="{{ route('organization.sms.campaigns.show', $campaign->id) }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
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
        <div class="mt-6 bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Original Campaign Stats</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Total Recipients</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($campaign->total_recipients) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Delivered</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($campaign->total_delivered) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Failed</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($campaign->total_failed) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
