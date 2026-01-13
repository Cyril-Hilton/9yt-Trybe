<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NewsService
{
    public function getArticles(?string $query = null): array
    {
        $query = $query ?: config('services.news.default_query');
        $provider = config('services.news.provider', 'gnews');
        $cacheMinutes = (int) config('services.news.cache_minutes', 60);
        $cacheKey = 'news:' . $provider . ':' . Str::slug($query);

        return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($provider, $query) {
            if ($provider === 'newsapi') {
                $newsApi = $this->fetchFromNewsApi($query);
                if (!empty($newsApi)) {
                    return $newsApi;
                }
            }

            if ($provider === 'gnews') {
                $gnews = $this->fetchFromGnews($query);
                if (!empty($gnews)) {
                    return $gnews;
                }
            }

            return $this->fetchFromRssFeeds();
        });
    }

    public function warmCache(?string $query = null): array
    {
        $query = $query ?: config('services.news.default_query');
        $provider = config('services.news.provider', 'gnews');
        $cacheMinutes = (int) config('services.news.cache_minutes', 60);
        $cacheKey = 'news:' . $provider . ':' . Str::slug($query);

        Cache::forget($cacheKey);

        return $this->getArticles($query);
    }

    private function fetchFromGnews(string $query): array
    {
        $apiKey = config('services.news.gnews.api_key');
        $baseUrl = rtrim(config('services.news.gnews.base_url', 'https://gnews.io/api/v4'), '/');
        $language = config('services.news.language', 'en');
        $max = (int) config('services.news.max_results', 20);

        if (empty($apiKey)) {
            return [];
        }

        $response = Http::timeout(10)->get("{$baseUrl}/search", [
            'q' => $query,
            'lang' => $language,
            'max' => $max,
            'token' => $apiKey,
        ]);

        if (!$response->ok()) {
            return [];
        }

        $payload = $response->json();
        $articles = $payload['articles'] ?? [];

        return collect($articles)->map(function (array $article) {
            return [
                'title' => $article['title'] ?? '',
                'description' => $article['description'] ?? '',
                'url' => $article['url'] ?? '',
                'image' => $article['image'] ?? '',
                'source' => $article['source']['name'] ?? 'Unknown',
                'published_at' => $article['publishedAt'] ?? '',
                'author' => $article['source']['name'] ?? 'Unknown',
            ];
        })->values()->all();
    }

    private function fetchFromNewsApi(string $query): array
    {
        $apiKey = config('services.news.newsapi.api_key');
        $baseUrl = rtrim(config('services.news.newsapi.base_url', 'https://newsapi.org/v2'), '/');
        $language = config('services.news.language', 'en');
        $max = (int) config('services.news.max_results', 20);

        if (empty($apiKey)) {
            return [];
        }

        $response = Http::timeout(10)->get("{$baseUrl}/everything", [
            'q' => $query,
            'language' => $language,
            'pageSize' => $max,
            'sortBy' => 'publishedAt',
            'apiKey' => $apiKey,
        ]);

        if (!$response->ok()) {
            return [];
        }

        $payload = $response->json();
        $articles = $payload['articles'] ?? [];

        return collect($articles)->map(function (array $article) {
            return [
                'title' => $article['title'] ?? '',
                'description' => $article['description'] ?? '',
                'url' => $article['url'] ?? '',
                'image' => $article['urlToImage'] ?? '',
                'source' => $article['source']['name'] ?? 'Unknown',
                'published_at' => $article['publishedAt'] ?? '',
                'author' => $article['author'] ?? 'Unknown',
            ];
        })->values()->all();
    }

    private function fetchFromRssFeeds(): array
    {
        $feeds = config('services.news.rss_feeds', []);
        $max = (int) config('services.news.max_results', 20);

        if (empty($feeds) || !is_array($feeds)) {
            $feeds = [
                'https://www.vogue.com/rss',
                'https://www.gq.com/feed/rss',
                'https://www.vanityfair.com/feed/rss',
                'https://www.hypebeast.com/feed',
                'https://www.elle.com/rss/all.xml',
            ];
        }

        $articles = [];

        foreach ($feeds as $feedUrl) {
            try {
                $response = Http::timeout(10)->get($feedUrl);
                if (!$response->ok()) {
                    continue;
                }

                $xml = @simplexml_load_string($response->body());
                if (!$xml) {
                    continue;
                }

                $items = $xml->channel->item ?? [];
                foreach ($items as $item) {
                    $title = (string) ($item->title ?? '');
                    $link = (string) ($item->link ?? '');
                    $description = strip_tags((string) ($item->description ?? ''));
                    $publishedAt = (string) ($item->pubDate ?? '');
                    $sourceName = (string) ($xml->channel->title ?? 'RSS');

                    $image = '';
                    $media = $item->children('http://search.yahoo.com/mrss/');
                    if ($media && isset($media->content)) {
                        $attributes = $media->content->attributes();
                        $image = (string) ($attributes['url'] ?? '');
                    }

                    if (empty($image) && isset($item->enclosure)) {
                        $enclosureAttributes = $item->enclosure->attributes();
                        $image = (string) ($enclosureAttributes['url'] ?? '');
                    }

                    $articles[] = [
                        'title' => $title,
                        'description' => $description,
                        'url' => $link,
                        'image' => $image,
                        'source' => $sourceName,
                        'published_at' => $publishedAt,
                        'author' => $sourceName,
                    ];
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return collect($articles)
            ->unique('url')
            ->sortByDesc('published_at')
            ->take($max)
            ->values()
            ->all();
    }
}
