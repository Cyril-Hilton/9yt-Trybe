@extends('layouts.company')

@section('title', 'Send Bulk Email')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('organization.conferences.show', $conference) }}" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block">
            ← Back to Conference
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Send Bulk Email</h1>
        <p class="mt-2 text-gray-600">{{ $conference->title }}</p>
    </div>

    <!-- Statistics Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">Current Registrations</h3>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-3xl font-bold text-blue-600">{{ $conference->registrations()->count() }}</p>
                <p class="text-sm text-blue-700 mt-1">Total</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-blue-600">{{ $conference->onlineRegistrations()->count() }}</p>
                <p class="text-sm text-blue-700 mt-1">Online</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-blue-600">{{ $conference->inPersonRegistrations()->count() }}</p>
                <p class="text-sm text-blue-700 mt-1">In-Person</p>
            </div>
        </div>
    </div>

    <!-- Email Form -->
    <div class="bg-white rounded-lg shadow p-6">
       <form action="{{ route('organization.conferences.send-bulk-email', $conference) }}" method="POST">
            @csrf

            <!-- Filter Recipients -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Send to <span class="text-red-500">*</span>
                </label>
                
                <div class="space-y-3">
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                        <input type="radio" name="filter" value="all" required checked
                            class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                        <div class="ml-4">
                            <span class="block font-medium text-gray-900">All Registrants</span>
                            <span class="block text-sm text-gray-600">Send to everyone ({{ $conference->registrations()->count() }} people)</span>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                        <input type="radio" name="filter" value="online" required
                            class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                        <div class="ml-4">
                            <span class="block font-medium text-gray-900">Online Attendees Only</span>
                            <span class="block text-sm text-gray-600">Send to online registrants ({{ $conference->onlineRegistrations()->count() }} people)</span>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                        <input type="radio" name="filter" value="in_person" required
                            class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                        <div class="ml-4">
                            <span class="block font-medium text-gray-900">In-Person Attendees Only</span>
                            <span class="block text-sm text-gray-600">Send to in-person registrants ({{ $conference->inPersonRegistrations()->count() }} people)</span>
                        </div>
                    </label>
                </div>

                @error('filter')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject -->
            <div class="mb-6">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                    Email Subject <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="subject" 
                    id="subject" 
                    required
                    value="{{ old('subject') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('subject') border-red-500 @enderror"
                    placeholder="e.g., Important Update About {{ $conference->title }}"
                >
                @error('subject')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message -->
            <div class="mb-6">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                    Email Message <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="message" 
                    id="message" 
                    rows="10"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('message') border-red-500 @enderror"
                    placeholder="Write your message here..."
                >{{ old('message') }}</textarea>
                @error('message')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-600">
                    <span class="flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <span>Tip: Keep your message clear and concise. Each recipient will receive a personalized email with their name.</span>
                    </span>
                </p>
            </div>

            <!-- Warning Notice -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="font-medium text-yellow-900">Important Notice</h4>
                        <p class="text-sm text-yellow-800 mt-1">
                            This will send an email to all selected registrants. Please review your message carefully before sending. 
                            Emails will be queued and sent in the background.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('organization.conferences.show', $conference) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button 
                    type="submit"
                    onclick="return confirm('Are you sure you want to send this email to all selected registrants?')"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                >
                    Send Bulk Email
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Section -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📧 Email Preview</h3>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="border-b border-gray-200 pb-4 mb-4">
                <p class="text-sm text-gray-600">From: {{ auth()->guard('company')->user()->name }}</p>
                <p class="text-sm text-gray-600">To: [Recipient Name] &lt;[recipient@email.com]&gt;</p>
                <p class="text-sm text-gray-600">Subject: <span id="preview-subject" class="font-medium">[Your subject will appear here]</span></p>
            </div>
            <div class="prose max-w-none">
                <p class="text-gray-700">Dear <strong>[Recipient Name]</strong>,</p>
                <div id="preview-message" class="mt-4 text-gray-700 whitespace-pre-line">[Your message will appear here]</div>
                <div class="mt-6 pt-6 border-t border-gray-200 text-sm text-gray-600">
                    <p>Best regards,<br><strong>{{ auth()->guard('company')->user()->name }}</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Live preview
document.getElementById('subject').addEventListener('input', function(e) {
    document.getElementById('preview-subject').textContent = e.target.value || '[Your subject will appear here]';
});

document.getElementById('message').addEventListener('input', function(e) {
    document.getElementById('preview-message').textContent = e.target.value || '[Your message will appear here]';
});
</script>
@endsection