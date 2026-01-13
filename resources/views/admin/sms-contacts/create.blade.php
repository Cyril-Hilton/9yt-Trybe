@extends('layouts.admin')

@section('title', 'Add SMS Contact')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Add SMS Contact</h1>
                <p class="text-gray-400">Add a single contact to your database</p>
            </div>
            <a href="{{ route('admin.sms-contacts.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                ← Back to Contacts
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-900/50 border border-green-700 text-green-200 px-6 py-4 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-900/50 border border-red-700 text-red-200 px-6 py-4 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 max-w-3xl">
        <form action="{{ route('admin.sms-contacts.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Phone Number -->
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">
                        Phone Number *
                    </label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" required
                           placeholder="e.g., 0241234567"
                           class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('phone_number') border-red-500 @enderror">
                    @error('phone_number')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-400">Ghanaian phone number format</p>
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">
                        Name (Optional)
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="e.g., John Doe"
                           class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">
                        Email (Optional)
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="e.g., john@example.com"
                           class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Group -->
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">
                        Group (Optional)
                    </label>
                    <input type="text" name="group" value="{{ old('group') }}"
                           placeholder="e.g., Tech Conference Attendees, Medical Professionals"
                           class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('group') border-red-500 @enderror">
                    @error('group')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-400">Categorize contacts for easy filtering</p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">
                        Notes (Optional)
                    </label>
                    <textarea name="notes" rows="4"
                              placeholder="Any additional information about this contact..."
                              class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror>
                </div>

                <!-- Info Box -->
                <div class="bg-indigo-900/30 border border-indigo-700 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-indigo-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-indigo-200">
                            <p class="font-semibold mb-1">Duplicate Prevention</p>
                            <p>If this phone number already exists in the database, the system will prevent adding it again.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4 border-t border-gray-700">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition flex-1">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Contact
                    </button>
                    <a href="{{ route('admin.sms-contacts.index') }}" class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Links -->
    <div class="mt-6 max-w-3xl">
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
            <p class="text-sm text-gray-400 mb-2">Need to add multiple contacts at once?</p>
            <a href="{{ route('admin.sms-contacts.bulk-upload') }}" class="text-indigo-400 hover:text-indigo-300 font-medium flex items-center">
                Use Bulk Upload
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection
