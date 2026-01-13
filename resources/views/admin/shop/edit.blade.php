@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Edit Product</h1>
                <p class="text-gray-400">Update product information</p>
            </div>
            <a href="{{ route('admin.shop.index') }}" class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Products
            </a>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl border border-gray-700 p-8">
        <form action="{{ route('admin.shop.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Product Name -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('name') border-red-500 @enderror"
                           placeholder="Enter product name">
                    @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Price (GH₵) *</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                           class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('price') border-red-500 @enderror"
                           placeholder="0.00">
                    @error('price')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Stock Quantity *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                           class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('stock') border-red-500 @enderror"
                           placeholder="0">
                    @error('stock')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Description *</label>
                    <textarea name="description" rows="4" required
                              class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('description') border-red-500 @enderror"
                              placeholder="Enter product description">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Image -->
                @if($product->image)
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Current Image</label>
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 rounded-lg object-cover border-2 border-gray-600">
                    </div>
                </div>
                @endif

                <!-- Product Image -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        {{ $product->image ? 'Change Image' : 'Product Image' }}
                    </label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input type="file" name="image" accept="image/*" id="imageInput"
                                   class="w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none @error('image') border-red-500 @enderror"
                                   onchange="previewImage(event)">
                            @error('image')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Accepted formats: JPG, PNG, GIF, WEBP</p>
                        </div>
                        <div id="imagePreview" class="hidden">
                            <img id="preview" class="w-24 h-24 rounded-lg object-cover border-2 border-gray-600">
                        </div>
                    </div>
                </div>

                <!-- Status Info -->
                <div class="lg:col-span-2 bg-gray-900 rounded-lg p-4 border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-300">Product Status</p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    @if($product->status === 'approved') bg-green-900 text-green-200
                                    @elseif($product->status === 'pending') bg-yellow-900 text-yellow-200
                                    @elseif($product->status === 'rejected') bg-red-900 text-red-200
                                    @endif">
                                    {{ ucfirst($product->status) }}
                                </span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $product->is_active ? 'bg-blue-900 text-blue-200' : 'bg-gray-700 text-gray-300' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-700">
                <a href="{{ route('admin.shop.index') }}" class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition shadow-lg">
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
