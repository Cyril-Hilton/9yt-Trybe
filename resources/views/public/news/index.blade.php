@extends('layouts.app')

@section('title', '9yt !Trybe News - Fashion, Lifestyle & Entertainment')
@section('meta_title', '9yt !Trybe News - Fashion, Lifestyle & Entertainment')
@section('meta_description', 'Latest fashion, lifestyle, entertainment, and exploration news curated for the 9yt !Trybe community.')
@section('meta_keywords', 'fashion news, lifestyle news, entertainment news, exploration, trends, 9yt trybe')

@section('content')
    <section class="py-10 sm:py-14 lg:py-20 bg-white dark:bg-black">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 font-semibold">Live Updates</p>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white">Fashion, Lifestyle & Entertainment</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-300 max-w-2xl">
                        Fresh headlines from credible sources. We curate and credit every article.
                    </p>
                </div>
                <form class="flex w-full sm:w-auto gap-2" method="get" action="{{ route('news.index') }}">
                    <input
                        type="text"
                        name="q"
                        value="{{ $query }}"
                        placeholder="Search news..."
                        class="w-full sm:w-72 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-cyan-500"
                    >
                    <button class="px-4 py-2 rounded-lg bg-cyan-600 text-white font-semibold hover:bg-cyan-700 transition">
                        Search
                    </button>
                </form>
            </div>

            <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                Source attribution is required. Articles link to the original publishers.
            </div>

            @if(empty($articles))
                <div class="mt-12 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-10 text-center">
                    <p class="text-gray-700 dark:text-gray-300">No articles yet. Add your API key and try again.</p>
                    <p class="mt-2 text-sm text-gray-500">Set `NEWS_PROVIDER` and a matching API key in `.env`.</p>
                </div>
            @else
                <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($articles as $article)
                        <article class="rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition">
                            @if(!empty($article['image']))
                                <a href="{{ $article['url'] }}" target="_blank" rel="noopener">
                                    <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="w-full h-48 object-cover">
                                </a>
                            @endif
                            <div class="p-5">
                                <p class="text-xs uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 font-semibold">
                                    {{ $article['source'] ?? 'Source' }}
                                </p>
                                <h2 class="mt-2 text-lg font-bold text-gray-900 dark:text-white">
                                    <a href="{{ $article['url'] }}" target="_blank" rel="noopener" class="hover:text-cyan-600 dark:hover:text-cyan-400">
                                        {{ $article['title'] }}
                                    </a>
                                </h2>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                                    {{ $article['description'] }}
                                </p>
                                <div class="mt-4 text-xs text-gray-500 flex items-center justify-between">
                                    <span>{{ $article['author'] ?? 'Editorial' }}</span>
                                    <span>{{ $article['published_at'] ? \Carbon\Carbon::parse($article['published_at'])->format('M d, Y') : '' }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
