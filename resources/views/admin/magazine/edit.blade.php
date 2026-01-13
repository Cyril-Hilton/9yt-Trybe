@extends('layouts.admin')

@section('title', 'Edit Magazine Image')

@section('content')
<div class="p-8 max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Edit Magazine Image</h1>
        <p class="text-gray-400">Update magazine image details</p>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-600 bg-opacity-20 border border-red-600 rounded-lg">
        <ul class="list-disc list-inside text-red-400">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.magazine.update', $image) }}" method="POST" enctype="multipart/form-data" class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <!-- Current Image Preview -->
            <div>
                <label class="block text-white font-semibold mb-2">Current Image</label>
                <div class="aspect-video max-w-md bg-gray-900 rounded-lg overflow-hidden">
                    <img src="{{ $image->image_url }}" alt="{{ $image->title }}" class="w-full h-full object-cover">
                </div>
            </div>

            <div>
                <label class="block text-white font-semibold mb-2">Title *</label>
                <input type="text" name="title" value="{{ old('title', $image->title) }}" required
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-white font-semibold mb-2">Replace Image <span class="text-gray-400 text-sm font-normal">(Optional, JPG/PNG/WEBP)</span></label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-white font-semibold mb-2">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('description', $image->description) }}</textarea>
            </div>

            <div>
                <label class="block text-white font-semibold mb-2">Display Order</label>
                <input type="number" name="order" value="{{ old('order', $image->order) }}" min="0"
                       class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <p class="text-gray-400 text-sm mt-1">Lower numbers appear first</p>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $image->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-indigo-600 bg-gray-700 border-gray-600 rounded focus:ring-indigo-500">
                <label for="is_active" class="ml-2 text-white">Active (visible on public gallery)</label>
            </div>
        </div>

        <div class="flex gap-4 mt-8">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition shadow-lg">
                Update Image
            </button>
            <a href="{{ route('admin.magazine.index') }}" class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
