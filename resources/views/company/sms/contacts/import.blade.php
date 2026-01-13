@extends('layouts.company')

@section('title', 'Import Contacts')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Import Contacts
                    </h1>
                    <p class="mt-2 text-gray-600">Upload contacts from CSV or paste phone numbers</p>
                </div>
                <a href="{{ route('organization.sms.contacts.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Contacts
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('organization.sms.contacts.import.store') }}" enctype="multipart/form-data" x-data="{ importType: 'csv' }">
            @csrf

            <!-- Import Method Selection -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Choose Import Method
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="import_type" value="csv" x-model="importType" class="peer sr-only" required checked>
                            <div class="p-6 border-2 border-gray-300 rounded-xl hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all duration-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="font-bold text-gray-900 text-lg">CSV Upload</span>
                                    </div>
                                    <svg class="w-6 h-6 text-indigo-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600">Upload a CSV file with phone numbers, names, emails, and groups</p>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="import_type" value="text" x-model="importType" class="peer sr-only">
                            <div class="p-6 border-2 border-gray-300 rounded-xl hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all duration-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span class="font-bold text-gray-900 text-lg">Paste Numbers</span>
                                    </div>
                                    <svg class="w-6 h-6 text-indigo-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600">Copy and paste phone numbers (one per line or comma-separated)</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- CSV Upload -->
            <div x-show="importType === 'csv'" x-cloak class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload CSV File
                    </h2>
                </div>

                <div class="p-6 space-y-6">
                    <!-- CSV File Upload -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Select CSV File *
                        </label>
                        <input type="file"
                               name="csv_file"
                               accept=".csv,.txt"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('csv_file') border-red-500 @enderror">
                        @error('csv_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Accepted formats: .csv, .txt</p>
                    </div>

                    <!-- Sample CSV Download -->
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-blue-900 mb-2">CSV Format Guide</h4>
                                <p class="text-sm text-blue-800 mb-3">Your CSV file should have the following columns (in order):</p>
                                <div class="bg-white rounded-lg p-3 mb-3">
                                    <code class="text-xs text-gray-900 font-mono">Phone Number, Name, Email, Group</code>
                                </div>
                                <p class="text-sm text-blue-800 mb-2">Example:</p>
                                <div class="bg-white rounded-lg p-3 mb-3">
                                    <code class="text-xs text-gray-700 font-mono">
                                        0241234567,John Doe,john@example.com,Customers<br>
                                        233551234567,Jane Smith,jane@example.com,Staff<br>
                                        0201234567,Bob Johnson,bob@example.com,VIP
                                    </code>
                                </div>
                                <a href="{{ route('organization.sms.contacts.sample-csv') }}" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download Sample CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Text Paste -->
            <div x-show="importType === 'text'" x-cloak class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Paste Phone Numbers
                    </h2>
                </div>

                <div class="p-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Phone Numbers (one per line or comma-separated) *
                        </label>
                        <textarea name="contacts_text"
                                  rows="10"
                                  placeholder="0241234567&#10;0551234567&#10;233201234567&#10;or&#10;0241234567, 0551234567, 233201234567"
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-sm @error('contacts_text') border-red-500 @enderror">{{ old('contacts_text') }}</textarea>
                        @error('contacts_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Paste phone numbers separated by new lines or commas</p>
                    </div>
                </div>
            </div>

            <!-- Group Assignment -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Group Assignment (Optional)
                    </h2>
                </div>

                <div class="p-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Assign to Group
                        </label>
                        <input type="text"
                               name="group"
                               value="{{ old('group') }}"
                               placeholder="e.g., Customers, Staff, December Promo"
                               list="groups"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <datalist id="groups">
                            @foreach($groups as $group)
                                <option value="{{ $group }}">
                            @endforeach
                        </datalist>
                        <p class="mt-1 text-xs text-gray-500">
                            <span x-show="importType === 'csv'">This will be used only if a contact doesn't have a group specified in the CSV</span>
                            <span x-show="importType === 'text'">All imported contacts will be assigned to this group</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('organization.sms.contacts.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Import Contacts
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
