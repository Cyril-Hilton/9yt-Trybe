@extends('layouts.app')

@section('title', '9yt !Trybe Blog - Guides & What\'s On')
@section('meta_title', '9yt !Trybe Blog - Guides & What\'s On')
@section('meta_description', 'How-tos, growth playbooks, and what\'s happening near you on 9yt !Trybe.')

@section('content')
<section class="py-10 sm:py-14 lg:py-20 bg-white dark:bg-black">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 font-semibold">Guides & What\'s On</p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white">9yt !Trybe Blog</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-300 max-w-2xl">
                    Practical playbooks for growth, plus weekly roundups of what\'s happening in your city.
                </p>
            </div>
            <form class="flex w-full sm:w-auto gap-2" method="get" action="{{ route('blog.index') }}">
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search blog..."
                    class="w-full sm:w-64 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-cyan-500"
                >
                <select name="category" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                    <option value="">All categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <button class="px-4 py-2 rounded-lg bg-cyan-600 text-white font-semibold hover:bg-cyan-700 transition">
                    Search
                </button>
            </form>
        </div>

        @if($articles->count() === 0)
            <div class="mt-12 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-10 text-center">
                <p class="text-gray-700 dark:text-gray-300">No posts yet. Check back soon.</p>
            </div>
        @else
            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($articles as $article)
                    <article class="rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition">
                        @if($article->image_url)
                            <a href="{{ route('blog.show', $article->slug) }}">
                                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-48 object-cover">
                            </a>
                        @endif
                        <div class="p-5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 font-semibold">
                                    {{ $article->category ?: 'Blog' }}
                                </span>
                                <span class="text-gray-400">
                                    {{ $article->published_at ? $article->published_at->format('M d, Y') : '' }}
                                </span>
                            </div>
                            <h2 class="mt-2 text-lg font-bold text-gray-900 dark:text-white">
                                <a href="{{ route('blog.show', $article->slug) }}" class="hover:text-cyan-600 dark:hover:text-cyan-400">
                                    {{ $article->title }}
                                </a>
                            </h2>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                                {{ $article->description }}
                            </p>
                            <div class="mt-4 text-xs text-gray-500">
                                {{ $article->author ?? '9yt !Trybe' }}
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
