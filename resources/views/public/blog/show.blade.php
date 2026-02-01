@extends('layouts.app')

@section('title', $article->title . ' - Blog')
@section('meta_title', $article->meta_title ?: $article->title)
@section('meta_description', $article->meta_description ?: Str::limit(strip_tags($article->description), 155))

@section('og_type', 'article')
@section('og_title', $article->meta_title ?: $article->title)
@section('og_description', $article->meta_description ?: Str::limit(strip_tags($article->description), 155))
@section('og_image', $article->image_url ?: asset('ui/logo/9yt-trybe-logo-light.png'))

@section('twitter_title', $article->meta_title ?: $article->title)
@section('twitter_description', $article->meta_description ?: Str::limit(strip_tags($article->description), 155))
@section('twitter_image', $article->image_url ?: asset('ui/logo/9yt-trybe-logo-light.png'))

@push('head')
@php
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $article->title,
        'description' => $article->meta_description ?: Str::limit(strip_tags($article->description), 155),
        'image' => [$article->image_url ?: asset('ui/logo/9yt-trybe-logo-light.png')],
        'author' => [
            '@type' => 'Person',
            'name' => $article->author ?? '9yt !Trybe',
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => '9yt !Trybe',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('ui/logo/9yt-trybe-logo-light.png'),
            ],
        ],
        'mainEntityOfPage' => url()->current(),
    ];

    if ($article->published_at) {
        $schema['datePublished'] = $article->published_at->toIso8601String();
    }

    if ($article->updated_at) {
        $schema['dateModified'] = $article->updated_at->toIso8601String();
    }
@endphp
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<section class="bg-white dark:bg-black">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 font-semibold">
                {{ $article->category ?: 'Blog' }}
            </p>
            <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white">
                {{ $article->title }}
            </h1>
            <div class="mt-3 text-sm text-gray-500 flex flex-wrap gap-2">
                <span>{{ $article->author ?? '9yt !Trybe' }}</span>
                <span>•</span>
                <span>{{ $article->published_at ? $article->published_at->format('M d, Y') : '' }}</span>
            </div>
        </div>

        @if($article->image_url)
            <div class="mb-8">
                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full rounded-2xl object-cover">
            </div>
        @endif

        <div class="prose max-w-none text-gray-800 dark:text-gray-200">
            <p class="text-lg text-gray-700 dark:text-gray-300">{{ $article->description }}</p>
            <div class="mt-6">
                {!! nl2br(e($article->content)) !!}
            </div>
        </div>

        <div class="mt-10 flex items-center justify-between">
            <a href="{{ route('blog.index') }}" class="text-cyan-600 dark:text-cyan-400 font-semibold hover:text-cyan-700 dark:hover:text-cyan-300">
                ? Back to blog
            </a>
            <button onclick="navigator.clipboard.writeText(window.location.href)" class="text-sm px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900">
                Copy link
            </button>
        </div>

        @if($related->count() > 0)
            <div class="mt-12">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Related posts</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($related as $item)
                        <a href="{{ route('blog.show', $item->slug) }}" class="block rounded-xl border border-gray-200 dark:border-gray-800 p-4 hover:shadow-md transition">
                            <p class="text-xs uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-400 font-semibold">
                                {{ $item->category ?: 'Blog' }}
                            </p>
                            <p class="mt-2 font-semibold text-gray-900 dark:text-white">
                                {{ $item->title }}
                            </p>
                            <p class="mt-2 text-xs text-gray-500">
                                {{ $item->published_at ? $item->published_at->format('M d, Y') : '' }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
