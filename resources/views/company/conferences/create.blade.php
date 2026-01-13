@extends('layouts.company')

@section('title', 'Create Conference')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('organization.conferences.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block">
            ← Back to Conferences
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Create New Conference</h1>
        <p class="mt-2 text-gray-600">Set up your conference details and registration form</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('organization.conferences.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Conference Title <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    required
                    value="{{ old('title') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('title') border-red-500 @enderror"
                    placeholder="e.g., Annual Tech Conference 2024"
                >
                @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('description') border-red-500 @enderror"
                    placeholder="Describe your conference..."
                >{{ old('description') }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Uploads -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Logo Upload -->
                <!-- <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                        Conference Logo
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                    <span>Upload logo</span>
                                    <input id="logo" name="logo" type="file" class="sr-only" accept="image/*" onchange="previewImage(this, 'logo-preview')">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF</p>
                        </div>
                    </div>
                    <div id="logo-preview" class="mt-2 hidden">
                        <img src="" alt="Logo preview" class="h-20 mx-auto rounded">
                    </div>
                    @error('logo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div> -->

                <!-- Header Image Upload -->
                <!-- <div>
                    <label for="header_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Header Image
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="header_image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                    <span>Upload header</span>
                                    <input id="header_image" name="header_image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this, 'header-preview')">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF</p>
                        </div>
                    </div>
                    <div id="header-preview" class="mt-2 hidden">
                        <img src="" alt="Header preview" class="h-32 w-full object-cover rounded">
                    </div>
                    @error('header_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div> -->
            </div>

            <!-- Venue -->
            <div class="mb-6">
                <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">
                    Venue (for In-Person attendees)
                </label>
                <input 
                    type="text" 
                    name="venue" 
                    id="venue"
                    value="{{ old('venue') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('venue') border-red-500 @enderror"
                    placeholder="e.g., Grand Hotel, 123 Main Street, City"
                >
                @error('venue')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date & Time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Date & Time <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        name="start_date" 
                        id="start_date" 
                        required
                        value="{{ old('start_date') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('start_date') border-red-500 @enderror"
                    >
                    @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        End Date & Time <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        name="end_date" 
                        id="end_date" 
                        required
                        value="{{ old('end_date') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('end_date') border-red-500 @enderror"
                    >
                    @error('end_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Attendance Limits -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance Limits</h3>
                <p class="text-sm text-gray-600 mb-4">Set to 0 for unlimited registration</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <label for="online_limit" class="block text-sm font-medium text-blue-900 mb-2">
                            Online Limit <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="online_limit" 
                            id="online_limit" 
                            required
                            min="0"
                            value="{{ old('online_limit', 0) }}"
                            class="w-full px-4 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('online_limit') border-red-500 @enderror"
                            placeholder="0 for unlimited"
                        >
                        @error('online_limit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <label for="in_person_limit" class="block text-sm font-medium text-red-900 mb-2">
                            In-Person Limit <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="in_person_limit" 
                            id="in_person_limit" 
                            required
                            min="0"
                            value="{{ old('in_person_limit', 0) }}"
                            class="w-full px-4 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('in_person_limit') border-red-500 @enderror"
                            placeholder="0 for unlimited"
                        >
                        @error('in_person_limit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select 
                    name="status" 
                    id="status" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('status') border-red-500 @enderror"
                >
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-600">
                    Set to "Active" to allow registrations. "Inactive" conferences won't accept new registrations.
                </p>
            </div>

            <!-- Info Box -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="font-medium text-blue-900">Custom Form Fields</h4>
                        <p class="text-sm text-blue-800 mt-1">
                            After creating this conference, you'll be able to add custom fields to your registration form using our Form Builder. 
                            Default fields (Name, Email, Phone) are always included.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('organization.conferences.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Create Conference
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const img = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
