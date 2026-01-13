@extends('layouts.company')

@section('title', 'Request Sender ID')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Request Sender ID
                    </h1>
                    <p class="mt-2 text-gray-600">Submit a sender ID for admin approval</p>
                </div>
                <a href="{{ route('organization.sms.sender-ids.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Sender IDs
                </a>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6 mb-8">
            <div class="flex items-start">
                <svg class="w-8 h-8 text-blue-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Sender ID Requirements</h3>
                    <ul class="space-y-1 text-sm text-blue-800">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span><strong>Length:</strong> Maximum 11 characters (Mnotify requirement)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span><strong>Characters:</strong> Letters, numbers, and spaces allowed (A-Z, 0-9, spaces)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span><strong>Approval:</strong> All sender IDs must be approved by admin before use</span>
                        </li>
                    </ul>
                    <div class="mt-3 pt-3 border-t border-blue-200">
                        <p class="text-sm font-semibold text-blue-900 mb-1">Good Examples:</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-white rounded-lg text-xs font-mono text-blue-900 border border-blue-300">9yt Trybe</span>
                            <span class="px-3 py-1 bg-white rounded-lg text-xs font-mono text-blue-900 border border-blue-300">MyShop</span>
                            <span class="px-3 py-1 bg-white rounded-lg text-xs font-mono text-blue-900 border border-blue-300">TechInfo</span>
                            <span class="px-3 py-1 bg-white rounded-lg text-xs font-mono text-blue-900 border border-blue-300">InfoDesk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Sender ID Details
                </h2>
            </div>

            <form method="POST" action="{{ route('organization.sms.sender-ids.store') }}" class="p-6 space-y-6" x-data="{ senderId: '{{ old('sender_id') }}' }">
                @csrf

                <!-- Sender ID -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Sender ID *
                    </label>
                    <input type="text"
                           name="sender_id"
                           x-model="senderId"
                           @input="senderId = senderId.toUpperCase()"
                           placeholder="e.g., 9YT TRYBE"
                           maxlength="11"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-lg @error('sender_id') border-red-500 @enderror"
                           required>
                    @error('sender_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                        <span>Letters, numbers, and spaces allowed</span>
                        <span x-text="senderId.length + ' / 11'">0 / 11</span>
                    </div>

                    <!-- Preview -->
                    <div class="mt-3 p-4 bg-gray-50 border-2 border-gray-200 rounded-xl">
                        <p class="text-xs text-gray-600 mb-2">Preview (how it will appear on recipient's phone):</p>
                        <div class="bg-white rounded-lg shadow-md p-4 max-w-xs">
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-900 mb-1" x-text="senderId || 'SENDER_ID'">SENDER_ID</p>
                                    <div class="bg-indigo-100 rounded-lg p-2">
                                        <p class="text-sm text-gray-800">Your SMS message will appear here...</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Just now</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purpose -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Purpose / Use Case *
                    </label>
                    <textarea name="purpose"
                              rows="4"
                              placeholder="Explain how you plan to use this sender ID (e.g., For promotional messages to customers, For order notifications, For appointment reminders)"
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('purpose') border-red-500 @enderror"
                              required
                              maxlength="500">{{ old('purpose') }}</textarea>
                    @error('purpose')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Maximum 500 characters. Be clear and specific to help with approval.</p>
                </div>

                <!-- Terms -->
                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1 text-sm text-yellow-800">
                            <p class="font-bold mb-1">Important Notice</p>
                            <ul class="space-y-1 list-disc list-inside">
                                <li>Your sender ID request will be reviewed by an administrator</li>
                                <li>Approval typically takes 1-2 business days</li>
                                <li>You will be notified once your request is approved or rejected</li>
                                <li>Rejected sender IDs can be resubmitted with corrections</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200">
                    <a href="{{ route('organization.sms.sender-ids.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
