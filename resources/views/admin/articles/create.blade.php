@extends('layouts.admin')

@section('title', 'Create Article')

@section('content')
<div class="p-8 max-w-4xl">
    <h1 class="text-3xl font-bold text-white mb-6">Create Article</h1>

    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="bg-gray-800 rounded-xl p-6 border border-gray-700 space-y-6">
        @csrf

        <div>
            <label class="block text-sm text-gray-300 mb-2">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:ring-2 focus:ring-indigo-500">
            @error('title')<p class="text-sm text-red-400 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-300 mb-2">Type</label>
                <select name="type" class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600">
                    <option value="blog" {{ old('type', 'blog') === 'blog' ? 'selected' : '' }}>Blog</option>
                    <option value="news" {{ old('type', 'blog') === 'news' ? 'selected' : '' }}>News</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-2">Category</label>
                <input type="text" name="category" value="{{ old('category') }}"
                       class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600"
                       placeholder="how-to, whats-on">
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-300 mb-2">Summary</label>
            <textarea name="summary" rows="3" required
                      class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600">{{ old('summary') }}</textarea>
            @error('summary')<p class="text-sm text-red-400 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm text-gray-300 mb-2">Content</label>
            <textarea name="content" rows="10" required
                      class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600">{{ old('content') }}</textarea>
            @error('content')<p class="text-sm text-red-400 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm text-gray-300 mb-2">Cover Image</label>
            <input type="file" name="image" class="w-full text-gray-300">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-300 mb-2">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                       class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600">
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-2">Meta Description</label>
                <textarea name="meta_description" rows="2"
                          class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600">{{ old('meta_description') }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-300 mb-2">Source Name</label>
                <input type="text" name="source_name" value="{{ old('source_name', '9yt !Trybe') }}"
                       class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600">
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-2">Source URL</label>
                <input type="url" name="source_url" value="{{ old('source_url', url('/')) }}"
                       class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_published" id="is_published" class="h-4 w-4">
            <label for="is_published" class="text-sm text-gray-300">Publish now</label>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Create</button>
        </div>
    </form>
</div>
@endsection
