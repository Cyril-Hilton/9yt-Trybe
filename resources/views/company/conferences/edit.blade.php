@extends('layouts.company')

@section('title', 'Edit Conference')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('organization.conferences.show', $conference) }}" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block">
            ← Back to Conference
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Conference</h1>
        <p class="mt-2 text-gray-600">{{ $conference->title }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('organization.conferences.update', $conference) }}" method="POST">
            @csrf
            @method('PUT')

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
                    value="{{ old('title', $conference->title) }}"
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
                >{{ old('description', $conference->description) }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
<!-- ADD IMAGE MANAGEMENT -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Logo Upload/Update -->
                <!-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Conference Logo
                    </label>
                    
                    @if($conference->hasLogo())
                    <div class="mb-3">
                        <img src="{{ $conference->logo_url }}" alt="Current logo" class="h-20 rounded border">
                        <button type="button" onclick="if(confirm('Remove logo?')) document.getElementById('remove-logo-form').submit();" 
                                class="mt-2 text-sm text-red-600 hover:text-red-800">
                            Remove Logo
                        </button>
                    </div>
                    @endif
                    
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                    <span>{{ $conference->hasLogo() ? 'Change logo' : 'Upload logo' }}</span>
                                    <input id="logo" name="logo" type="file" class="sr-only" accept="image/*" onchange="previewImage(this, 'logo-preview')">
                                </label>
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

                <!-- Header Image Upload/Update -->
                <!-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Header Image
                    </label>
                    
                    @if($conference->hasHeaderImage())
                    <div class="mb-3">
                        <img src="{{ $conference->header_image_url }}" alt="Current header" class="h-32 w-full object-cover rounded border">
                        <button type="button" onclick="if(confirm('Remove header image?')) document.getElementById('remove-header-form').submit();" 
                                class="mt-2 text-sm text-red-600 hover:text-red-800">
                            Remove Header
                        </button>
                    </div>
                    @endif
                    
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="header_image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                    <span>{{ $conference->hasHeaderImage() ? 'Change header' : 'Upload header' }}</span>
                                    <input id="header_image" name="header_image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this, 'header-preview')">
                                </label>
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
                    value="{{ old('venue', $conference->venue) }}"
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
                        value="{{ old('start_date', $conference->start_date->format('Y-m-d\TH:i')) }}"
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
                        value="{{ old('end_date', $conference->end_date->format('Y-m-d\TH:i')) }}"
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
                <p class="text-sm text-gray-600 mb-4">Set to 0 for unlimited. Current registrations won't be affected.</p>
                
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
                            value="{{ old('online_limit', $conference->online_limit) }}"
                            class="w-full px-4 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('online_limit') border-red-500 @enderror"
                            placeholder="0 for unlimited"
                        >
                        <p class="mt-2 text-xs text-blue-700">
                            Current: {{ $conference->online_count }} registrations
                        </p>
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
                            value="{{ old('in_person_limit', $conference->in_person_limit) }}"
                            class="w-full px-4 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('in_person_limit') border-red-500 @enderror"
                            placeholder="0 for unlimited"
                        >
                        <p class="mt-2 text-xs text-red-700">
                            Current: {{ $conference->in_person_count }} registrations
                        </p>
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
                    <option value="active" {{ old('status', $conference->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $conference->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="closed" {{ old('status', $conference->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-600">
                    Set to "Active" to allow new registrations. "Inactive" conferences won't accept new registrations.
                </p>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <!-- Delete Button -->
                <button 
                    type="button"
                    onclick="if(confirm('Are you sure you want to delete this conference? All registrations will be permanently deleted. This action cannot be undone.')) { document.getElementById('delete-form').submit(); }"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                >
                    Delete Conference
                </button>

                <div class="flex space-x-4">
                    <a href="{{ route('organization.conferences.show', $conference) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Update Conference
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Form (Hidden) -->
        <form id="delete-form" action="{{ route('organization.conferences.destroy', $conference) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<!-- Hidden forms for removing images -->
<form id="remove-logo-form" action="{{ route('organization.conferences.remove-logo', $conference) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<form id="remove-header-form" action="{{ route('organization.conferences.remove-header', $conference) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

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
