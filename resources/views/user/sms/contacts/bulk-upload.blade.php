@extends('layouts.app')

@section('title', 'Bulk Upload SMS Contacts')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Bulk Upload SMS Contacts</h1>
                    <p class="text-gray-600 dark:text-gray-400">Import multiple contacts from Excel/CSV file</p>
                </div>
                <a href="{{ route('user.sms.contacts.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    ← Back to Contacts
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-lg">
            <p class="text-sm text-green-800 dark:text-green-300">{{ session('success') }}</p>
            @if(session('upload_errors'))
                <details class="mt-2">
                    <summary class="cursor-pointer text-sm text-green-700 dark:text-green-400 font-medium">View Errors</summary>
                    <ul class="mt-2 list-disc list-inside text-xs text-green-700 dark:text-green-400">
                        @foreach(session('upload_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg">
            <p class="text-sm text-red-800 dark:text-red-300">{{ session('error') }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Upload Form -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Upload Excel File</h2>

                    <form action="{{ route('user.sms.contacts.bulk-store') }}" method="POST" enctype="multipart/form-data" x-data="{ fileName: '' }">
                        @csrf

                        <div class="space-y-6">
                            <!-- File Upload -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Select Excel/CSV File *
                                </label>
                                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-indigo-500 dark:hover:border-indigo-400 transition">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <input type="file" name="excel_file" id="excel_file" accept=".csv,.xlsx,.xls" required class="hidden"
                                           @change="fileName = $event.target.files[0]?.name || ''">
                                    <label for="excel_file" class="mt-2 cursor-pointer">
                                        <span class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                            Choose File
                                        </span>
                                    </label>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400" x-text="fileName || 'No file chosen'"></p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">Supported: CSV, XLSX, XLS</p>
                                </div>
                                @error('excel_file')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Group (Optional) -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Assign to Group (Optional)
                                </label>
                                <input type="text" name="group" value="{{ old('group') }}"
                                       placeholder="e.g., VIP Customers, Event Attendees"
                                       class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    All contacts in this file will be assigned to this group. Leave blank to use groups from the Excel file (column 4).
                                </p>
                                @error('group')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Download Sample -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-1">Need a template?</p>
                                        <p class="text-sm text-blue-700 dark:text-blue-300 mb-2">Download our sample Excel file to see the correct format</p>
                                        <a href="{{ route('user.sms.contacts.download-sample') }}" class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Download Sample CSV
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition flex-1">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    Upload & Import Contacts
                                </button>
                                <a href="{{ route('user.sms.contacts.index') }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Instructions Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 sticky top-8">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 Instructions</h3>

                    <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">File Format</h4>
                            <p class="mb-2">Your Excel/CSV file should have these columns:</p>
                            <ol class="list-decimal list-inside space-y-1 text-xs">
                                <li><strong>Phone Number</strong> (Required)</li>
                                <li><strong>Name</strong> (Optional)</li>
                                <li><strong>Email</strong> (Optional)</li>
                                <li><strong>Group</strong> (Optional)</li>
                            </ol>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Example Data</h4>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded p-3 overflow-x-auto">
                                <table class="text-xs font-mono">
                                    <thead>
                                        <tr class="text-gray-700 dark:text-gray-300">
                                            <th class="pr-2">Phone</th>
                                            <th class="pr-2">Name</th>
                                            <th class="pr-2">Email</th>
                                            <th>Group</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 dark:text-gray-400">
                                        <tr>
                                            <td class="pr-2">024123...</td>
                                            <td class="pr-2">John</td>
                                            <td class="pr-2">john@...</td>
                                            <td>VIP</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2">055456...</td>
                                            <td class="pr-2">Jane</td>
                                            <td class="pr-2">jane@...</td>
                                            <td>VIP</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Important Notes</h4>
                            <ul class="space-y-1 text-xs">
                                <li class="flex items-start">
                                    <span class="text-indigo-600 dark:text-indigo-400 mr-2">✓</span>
                                    <span>Duplicate phone numbers will be automatically skipped</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-indigo-600 dark:text-indigo-400 mr-2">✓</span>
                                    <span>First row with headers will be skipped automatically</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-indigo-600 dark:text-indigo-400 mr-2">✓</span>
                                    <span>Phone numbers must be 10-20 characters</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-indigo-600 dark:text-indigo-400 mr-2">✓</span>
                                    <span>No file size limit</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    // Alpine.js is already loaded from the layout
});
</script>
@endsection
