@extends('layouts.admin')

@section('title', 'Gallery Management')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Gallery Management</h1>
                <p class="text-gray-400 text-sm sm:text-base">Manage event gallery images</p>
            </div>
            <a href="{{ route('admin.gallery.create') }}" class="px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition shadow-lg flex items-center justify-center text-sm sm:text-base whitespace-nowrap">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Image
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-600 bg-opacity-20 border border-green-600 rounded-lg">
        <p class="text-green-400 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @php
            $total = \App\Models\GalleryImage::count();
            $active = \App\Models\GalleryImage::where('is_active', true)->count();
            $inactive = \App\Models\GalleryImage::where('is_active', false)->count();
        @endphp
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700">
            <p class="text-gray-400 text-sm mb-1">Total Images</p>
            <p class="text-3xl font-bold text-white">{{ $total }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6">
            <p class="text-green-100 text-sm mb-1">Active</p>
            <p class="text-3xl font-bold text-white">{{ $active }}</p>
        </div>
        <div class="bg-gradient-to-br from-gray-600 to-gray-700 rounded-xl p-6">
            <p class="text-gray-100 text-sm mb-1">Inactive</p>
            <p class="text-3xl font-bold text-white">{{ $inactive }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-xl p-4 mb-6 border border-gray-700">
        <form action="{{ route('admin.gallery.index') }}" method="GET" class="flex flex-wrap gap-4">
            <select name="category" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="all" {{ $category === 'all' ? 'selected' : '' }}>All Categories</option>
                <option value="new" {{ $category === 'new' ? 'selected' : '' }}>New Events</option>
                <option value="old" {{ $category === 'old' ? 'selected' : '' }}>Old Events</option>
            </select>

            <select name="status" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Filter
            </button>

            @if(request()->hasAny(['category', 'status']) && (request('category') !== 'all' || request('status') !== 'all'))
            <a href="{{ route('admin.gallery.index') }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Images Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($images as $image)
        <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700 hover:border-indigo-500 transition group">
            <div class="aspect-square relative overflow-hidden bg-gray-900">
                <img src="{{ $image->image_url }}" alt="{{ $image->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                <div class="absolute top-2 right-2 flex gap-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $image->is_active ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300' }}">
                        {{ $image->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-600 text-white">
                        {{ ucfirst($image->category) }}
                    </span>
                </div>
            </div>
            <div class="p-4">
                <h3 class="text-white font-semibold mb-1 truncate">{{ $image->title }}</h3>
                @if($image->description)
                <p class="text-gray-400 text-sm mb-3 line-clamp-2">{{ $image->description }}</p>
                @endif
                <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                    <span>Order: {{ $image->order }}</span>
                    <span>{{ $image->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.gallery.edit', $image) }}" class="flex-1 px-3 py-2 bg-indigo-600 text-white text-center rounded-lg hover:bg-indigo-700 transition text-sm">
                        Edit
                    </a>
                    <form action="{{ route('admin.gallery.toggle-active', $image) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 {{ $image->is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition text-sm">
                            {{ $image->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.gallery.destroy', $image) }}" method="POST" onsubmit="return confirm('Delete this image? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-20">
            <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-400 mb-2">No gallery images found</h3>
            <p class="text-gray-500 mb-6">Get started by adding your first gallery image</p>
            <a href="{{ route('admin.gallery.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add First Image
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($images->hasPages())
    <div class="mt-8">
        {{ $images->links() }}
    </div>
    @endif
</div>
@endsection
