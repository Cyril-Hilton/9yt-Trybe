<?php

namespace App\Services\News;

use App\Models\Article;
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
            $localArticles = $this->fetchLocalArticles($query);
            
            // If provider is local, don't even try external to save time/resources
            if ($provider === 'local') {
                return $localArticles;
            }

            $externalArticles = [];
            try {
                if ($provider === 'x') {
                    $externalArticles = $this->fetchFromX($query);
                } elseif ($provider === 'newsapi') {
                    $externalArticles = $this->fetchFromNewsApi($query);
                } elseif ($provider === 'gnews') {
                    $externalArticles = $this->fetchFromGnews($query);
                }
            } catch (\Throwable $e) {
                // Fail silently for external providers to keep the site running
                report($e);
            }

            if (empty($externalArticles)) {
                $externalArticles = $this->fetchFromRssFeeds();
            }

            // Merge local and external articles, prioritizing local
            return array_merge($localArticles, $externalArticles);
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

        $externalArticles = [];
        $url = "{$baseUrl}/search";
        $params = [
            'q' => $query,
            'lang' => $language,
            'max' => $max,
            'token' => $apiKey,
        ];

        try {
            $response = Http::timeout(5)->get($url, $params);
            
            if ($response->successful()) {
                $articles = $response->json()['articles'] ?? [];
                
                foreach ($articles as $article) {
                    $externalArticles[] = [
                        'title' => $article['title'] ?? '',
                        'description' => $article['description'] ?? '',
                        'url' => $article['url'] ?? '',
                        'image' => $article['image'] ?? $article['urlToImage'] ?? '',
                        'source' => $article['source']['name'] ?? 'GNews',
                        'published_at' => $article['publishedAt'] ?? now()->toIso8601String(),
                        'author' => $article['source']['name'] ?? 'Editorial',
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Failed to fetch from GNews: " . $e->getMessage());
        }

        return $externalArticles;
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

        try {
            $response = Http::timeout(5)->get("{$baseUrl}/everything", [
                'q' => $query,
                'language' => $language,
                'pageSize' => $max,
                'sortBy' => 'publishedAt',
                'apiKey' => $apiKey,
            ]);
        } catch (\Throwable $e) {
            return [];
        }

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

    private function fetchFromX(string $query): array
    {
        $bearerToken = config('services.x.bearer_token');
        if (empty($bearerToken)) {
            return [];
        }

        $language = config('services.news.language', 'en');
        $maxResults = max(5, min(100, (int) config('services.news.max_results', 20)));
        $baseQuery = $query ?: config('services.news.x.default_query', 'entertainment OR culture OR pop culture OR music OR film OR celebrity');
        $searchQuery = trim("{$baseQuery} -is:retweet lang:{$language}");

        try {
            $response = Http::withToken($bearerToken)
                ->acceptJson()
                ->timeout(5)
                ->get('https://api.twitter.com/2/tweets/search/recent', [
                    'query' => $searchQuery,
                    'max_results' => $maxResults,
                    'tweet.fields' => 'created_at,author_id',
                    'expansions' => 'author_id',
                    'user.fields' => 'name,username,profile_image_url',
                ]);
        } catch (\Throwable $e) {
            return [];
        }

        if (!$response->ok()) {
            return [];
        }

        $payload = $response->json();
        $tweets = $payload['data'] ?? [];
        $users = collect($payload['includes']['users'] ?? [])->keyBy('id');

        return collect($tweets)->map(function (array $tweet) use ($users) {
            $user = $users->get($tweet['author_id'], []);
            $username = $user['username'] ?? 'twitter';
            $text = trim(preg_replace('/\s+/', ' ', $tweet['text'] ?? ''));

            return [
                'title' => Str::limit($text, 90),
                'description' => Str::limit($text, 160),
                'url' => "https://twitter.com/{$username}/status/{$tweet['id']}",
                'image' => $user['profile_image_url'] ?? '',
                'source' => $user['name'] ?? 'X',
                'published_at' => $tweet['created_at'] ?? now()->toIso8601String(),
                'author' => $user['name'] ?? $username,
            ];
        })->unique('url')->values()->all();
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

        // Limit feeds to process to prevent timeout (max 5 feeds per hit)
        // Shuffle to get varied content on different cache refreshes
        $feedsToProcess = collect($feeds)->shuffle()->take(5)->all();
        $articles = [];
        $startTime = microtime(true);

        foreach ($feedsToProcess as $feedUrl) {
            // Check if we are running out of time (30s limit - 5s buffer)
            if (microtime(true) - $startTime > 25) {
                break;
            }

            try {
                // Reduced timeout per feed to 1.5 seconds
                $response = Http::timeout(1.5)->get($feedUrl);
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

    private function fetchLocalArticles(?string $query = null): array
    {
        try {
            $queryBuilder = Article::where('is_published', true);

            if ($query) {
                $defaultQuery = trim(config('services.news.default_query'));
                $currentQuery = trim($query);

                // If query matches default, return all published articles
                if ($currentQuery === $defaultQuery || empty($currentQuery)) {
                    return $queryBuilder->orderBy('published_at', 'desc')
                        ->limit(20)
                        ->get()
                        ->map(function ($article) {
                            return [
                                'title' => $article->title,
                                'description' => $article->description,
                                'url' => route('events.index'), // Fallback if no news details page
                                'image' => $article->image_url ?? '',
                                'source' => $article->source_name,
                                'published_at' => $article->published_at ? $article->published_at->toIso8601String() : now()->toIso8601String(),
                                'author' => $article->author,
                            ];
                        })
                        ->toArray();
                }

                // Handle composite queries (e.g., "fashion OR lifestyle")
                if (str_contains($currentQuery, ' OR ')) {
                    $terms = explode(' OR ', $currentQuery);
                    $queryBuilder->where(function ($q) use ($terms) {
                        foreach ($terms as $term) {
                            $term = trim($term);
                            $q->orWhere('title', 'like', "%{$term}%")
                              ->orWhere('description', 'like', "%{$term}%")
                              ->orWhere('content', 'like', "%{$term}%");
                        }
                    });
                } else {
                    $queryBuilder->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%")
                          ->orWhere('content', 'like', "%{$query}%");
                    });
                }
            }

            return $queryBuilder->orderBy('published_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($article) {
                    return [
                        'title' => $article->title,
                        'description' => $article->description,
                        'url' => route('events.index'), // Or a dedicated article page if exists
                        'image' => $article->image_url ?? '', // Use accessor
                        'source' => $article->source_name,
                        'published_at' => $article->published_at ? $article->published_at->toIso8601String() : now()->toIso8601String(),
                        'author' => $article->author,
                    ];
                })
                ->toArray();
        } catch (\Throwable $e) {
            report($e);
            return [];
        }
    }
}
