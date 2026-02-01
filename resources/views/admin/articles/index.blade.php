@extends('layouts.admin')

@section('title', 'Articles')

@section('content')
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Articles</h1>
            <p class="text-gray-400">Manage blog and news content</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            New Article
        </a>
    </div>

    <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Published</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($articles as $article)
                <tr class="hover:bg-gray-750 transition">
                    <td class="px-6 py-4 text-sm text-white">
                        <div class="font-semibold">{{ $article->title }}</div>
                        <div class="text-xs text-gray-400">{{ Str::limit($article->description, 80) }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-300">
                        {{ $article->type ?? 'news' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-300">
                        {{ $article->category ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $article->is_published ? 'bg-green-900 text-green-200' : 'bg-yellow-900 text-yellow-200' }}">
                            {{ $article->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">
                        {{ $article->published_at ? $article->published_at->format('M d, Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="{{ route('admin.articles.edit', $article) }}" class="text-indigo-400 hover:text-indigo-300 mr-3">Edit</a>
                        <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline" onsubmit="return confirm('Delete this article?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">No articles yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $articles->links() }}
    </div>
</div>
@endsection
