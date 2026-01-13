@extends('layouts.app')

@section('title', 'Request Sender ID')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('user.sms.sender-ids.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold mb-4 inline-block">
                ← Back to Sender IDs
            </a>
            <h1 class="text-4xl font-bold gradient-text mb-2">📝 Request New Sender ID</h1>
            <p class="text-gray-600 dark:text-gray-400">Submit a new Sender ID for admin approval</p>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg">
                <p class="text-sm text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Info Cards -->
        <div class="space-y-4 mb-8">
            <!-- What is a Sender ID -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-6 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-2">What is a Sender ID?</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-400">A Sender ID is the name that appears as the sender when recipients receive your SMS messages. It helps recipients identify who sent the message. Mnotify limits sender IDs to 11 characters.</p>
                    </div>
                </div>
            </div>

            <!-- Guidelines -->
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 p-6 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-green-800 dark:text-green-300 mb-2">Sender ID Guidelines</h4>
                        <ul class="text-sm text-green-700 dark:text-green-400 space-y-1 list-disc list-inside">
                            <li>Maximum 11 characters (Mnotify requirement)</li>
                            <li>Only letters, numbers, and spaces allowed (A-Z, 0-9, spaces)</li>
                            <li>Use a recognizable name related to your business or organization</li>
                            <li>Examples: 9yt Trybe, MyShop, TechInfo, InfoDesk</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Approval Process -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-6 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-yellow-800 dark:text-yellow-300 mb-2">Approval Process</h4>
                        <p class="text-sm text-yellow-700 dark:text-yellow-400">Your Sender ID request will be reviewed by an admin. You will receive notification once it's approved or if it's rejected. Only approved Sender IDs can be used to send SMS messages.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <form action="{{ route('user.sms.sender-ids.store') }}" method="POST" x-data="{
                senderId: '{{ old('sender_id') }}',
                purpose: '{{ old('purpose') }}',
                get isValidSenderId() {
                    return this.senderId.length > 0 && this.senderId.length <= 11 && /^[A-Za-z0-9\s]+$/.test(this.senderId);
                }
            }">
                @csrf

                <!-- Sender ID Input -->
                <div class="mb-6">
                    <label for="sender_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Sender ID <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="sender_id"
                        name="sender_id"
                        x-model="senderId"
                        maxlength="11"
                        required
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition uppercase font-mono text-lg @error('sender_id') border-red-500 @enderror"
                        placeholder="9yt Trybe"
                        value="{{ old('sender_id') }}"
                    >

                    <!-- Character Counter -->
                    <div class="mt-2 flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400" x-text="senderId.length + '/11 characters'"></span>
                        <div class="flex items-center space-x-2">
                            <span x-show="isValidSenderId" class="text-green-600 dark:text-green-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Valid format
                            </span>
                            <span x-show="senderId.length > 0 && !isValidSenderId" class="text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Invalid format
                            </span>
                        </div>
                    </div>

                    @error('sender_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Letters, numbers, and spaces allowed (A-Z, 0-9, spaces). Maximum 11 characters.
                    </p>
                </div>

                <!-- Purpose Textarea -->
                <div class="mb-8">
                    <label for="purpose" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Purpose / Description <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="purpose"
                        name="purpose"
                        x-model="purpose"
                        rows="5"
                        maxlength="500"
                        required
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition @error('purpose') border-red-500 @enderror"
                        placeholder="Explain how you plan to use this Sender ID. For example: 'For sending promotional messages and order notifications to our customers.'"
                    >{{ old('purpose') }}</textarea>

                    <!-- Character Counter -->
                    <div class="mt-2 flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400" x-text="purpose.length + '/500 characters'"></span>
                    </div>

                    @error('purpose')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Provide a clear explanation of how you intend to use this Sender ID. This helps admins review your request.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('user.sms.sender-ids.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        x-bind:disabled="!isValidSenderId || purpose.length < 10"
                    >
                        Submit Request
                    </button>
                </div>
            </form>
        </div>

        <!-- Additional Help -->
        <div class="mt-8 bg-gray-50 dark:bg-gray-800/50 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">💡 Need Help?</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                If you're unsure about what Sender ID to choose or have questions about the approval process, consider these tips:
            </p>
            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-disc list-inside">
                <li>Use your company or organization name</li>
                <li>Keep it short and memorable</li>
                <li>Avoid generic names that might confuse recipients</li>
                <li>You can request multiple Sender IDs for different purposes</li>
                <li>Admin review typically takes 24-48 hours</li>
            </ul>
        </div>
    </div>
</div>

<style>
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
[x-cloak] { display: none !important; }
</style>
@endsection
