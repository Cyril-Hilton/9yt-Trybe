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

            @if(!empty($aiDigest))
                <div class="mt-6 rounded-2xl border border-cyan-200 dark:border-cyan-800 bg-cyan-50/40 dark:bg-cyan-900/20 p-6">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-cyan-700 dark:text-cyan-300 font-semibold">AI Digest</p>
                        @if(!empty($aiDigest['topics']))
                            <div class="flex flex-wrap gap-2 text-xs">
                                @foreach($aiDigest['topics'] as $topic)
                                    <span class="px-2 py-1 rounded-full bg-white/70 dark:bg-black/40 text-cyan-700 dark:text-cyan-200 border border-cyan-200/70 dark:border-cyan-700/60">
                                        {{ $topic }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <h2 class="mt-3 text-xl font-bold text-gray-900 dark:text-white">{{ $aiDigest['headline'] ?? '' }}</h2>
                    @if(!empty($aiDigest['bullets']))
                        <ul class="mt-3 space-y-2 text-sm text-gray-700 dark:text-gray-200">
                            @foreach($aiDigest['bullets'] as $bullet)
                                <li class="flex gap-2">
                                    <span class="mt-1 w-2 h-2 rounded-full bg-cyan-500"></span>
                                    <span>{{ $bullet }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            @if(empty($articles))
                <div class="mt-12 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-10 text-center">
                    <div class="mx-auto w-16 h-16 text-gray-400 mb-4">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 font-medium">News is being updated.</p>
                    <p class="mt-2 text-sm text-gray-500">Check back shortly for the latest fashion and lifestyle updates.</p>
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
