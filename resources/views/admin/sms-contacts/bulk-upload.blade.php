@extends('layouts.admin')

@section('title', 'Bulk Upload SMS Contacts')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Bulk Upload SMS Contacts</h1>
                <p class="text-gray-400">Upload multiple contacts from an Excel file</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upload Form -->
        <div class="lg:col-span-2">
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">Upload Excel File</h2>

                <form action="{{ route('admin.sms-contacts.bulk-upload.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        <!-- File Upload -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                Excel File *
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-600 border-dashed rounded-lg hover:border-indigo-500 transition">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-400">
                                        <label for="file-upload" class="relative cursor-pointer bg-gray-700 rounded-md font-medium text-indigo-400 hover:text-indigo-300 focus-within:outline-none px-3 py-1">
                                            <span>Upload a file</span>
                                            <input id="file-upload" name="excel_file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-400">
                                        Excel or CSV
                                    </p>
                                </div>
                            </div>
                            @error('excel_file')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Group (Optional) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                Assign Group to All (Optional)
                            </label>
                            <input type="text" name="group" value="{{ old('group') }}"
                                   placeholder="e.g., Tech Conference 2025"
                                   class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('group') border-red-500 @enderror">
                            @error('group')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Assign all uploaded contacts to this group (overrides group column in file)</p>
                        </div>

                        <!-- Duplicate Handling Info -->
                        <div class="bg-yellow-900/30 border border-yellow-700 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div class="text-sm text-yellow-200">
                                    <p class="font-semibold mb-1">Automatic Duplicate Prevention</p>
                                    <p>Phone numbers that already exist in your database will be automatically skipped. You'll see a report of imported vs. skipped contacts after upload.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Upload & Import
                            </button>
                            <a href="{{ route('admin.sms-contacts.index') }}" class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Instructions & Download Sample -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Download Sample -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-bold text-white mb-4">Download Sample File</h3>
                <p class="text-sm text-gray-400 mb-4">Use this template to format your contact data correctly.</p>
                <a href="{{ route('admin.sms-contacts.sample-download') }}" class="block w-full px-4 py-3 bg-indigo-600 text-white text-center rounded-lg font-semibold hover:bg-indigo-700 transition">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    Download CSV Template
                </a>
            </div>

            <!-- Instructions -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-bold text-white mb-4">File Format Instructions</h3>
                <div class="space-y-4 text-sm text-gray-300">
                    <div>
                        <p class="font-semibold text-white mb-2">Required Columns:</p>
                        <ul class="list-disc list-inside space-y-1 text-gray-400">
                            <li><span class="font-mono text-indigo-400">phone_number</span> - Must be first column</li>
                        </ul>
                    </div>

                    <div>
                        <p class="font-semibold text-white mb-2">Optional Columns:</p>
                        <ul class="list-disc list-inside space-y-1 text-gray-400">
                            <li><span class="font-mono text-indigo-400">name</span> - Contact's full name</li>
                            <li><span class="font-mono text-indigo-400">email</span> - Email address</li>
                            <li><span class="font-mono text-indigo-400">group</span> - Category/Group name</li>
                        </ul>
                    </div>

                    <div class="border-t border-gray-700 pt-4">
                        <p class="font-semibold text-white mb-2">Important Notes:</p>
                        <ul class="space-y-2 text-gray-400">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>First row should contain column headers</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Phone numbers must be Ghanaian format</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Duplicates are automatically skipped</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Supports .xlsx, .xls, and .csv files</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Example Format -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-bold text-white mb-4">Example Data</h3>
                <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                    <table class="text-xs text-gray-300 font-mono">
                        <thead>
                            <tr class="text-indigo-400">
                                <th class="text-left pr-4">phone_number</th>
                                <th class="text-left pr-4">name</th>
                                <th class="text-left pr-4">email</th>
                                <th class="text-left">group</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-400">
                            <tr>
                                <td class="pr-4">0241234567</td>
                                <td class="pr-4">John Doe</td>
                                <td class="pr-4">john@ex.com</td>
                                <td>Tech</td>
                            </tr>
                            <tr>
                                <td class="pr-4">0551234567</td>
                                <td class="pr-4">Jane Smith</td>
                                <td class="pr-4">jane@ex.com</td>
                                <td>Medical</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
