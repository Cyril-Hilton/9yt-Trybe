<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Company;
use App\Models\Category;
use App\Models\ShopProduct;
use App\Models\Poll;
use App\Models\Article;
use App\Models\Contestant;
use App\Models\TeamMember;
use App\Models\Survey;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Services\Search\AiSearchService;

class SearchController extends Controller
{
    /**
     * Global search across all content types
     */
    public function index(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', 'all');

        if (empty($query)) {
            return view('search.results', [
                'query' => '',
                'events' => collect(),
                'companies' => collect(),
                'categories' => collect(),
                'products' => collect(),
                'polls' => collect(),
                'contestants' => collect(),
                'speakers' => collect(),
                'surveys' => collect(),
                'conferences' => collect(),
                'blogs' => collect(),
                'totalResults' => 0,
                'type' => $type,
                'suggestions' => [],
                'aiSearch' => null,
                'searchQuery' => '',
            ]);
        }

        $aiSearch = app(AiSearchService::class)->rewrite($query);
        $searchQuery = $aiSearch['corrected_query'] ?? $query;
        $terms = $this->buildSearchTerms($searchQuery, $aiSearch['synonyms'] ?? []);

        // Normalize query for better matching
        $normalizedQuery = $this->normalizeQuery($searchQuery);
        $searchTerms = $this->getSearchTerms($searchQuery);

        // Search Events - comprehensive fuzzy search across all relevant fields
        $events = collect();
        $fuzzyVariations = $this->getFuzzyVariations($searchQuery);

        if ($type === 'all' || $type === 'events') {
            $events = Event::where('status', 'approved')
                ->where(function ($q) use ($terms) {
                    $this->applyTermSearch($q, ['title', 'summary', 'venue_name'], $terms);
                    $q->orWhereHas('categories', function ($cq) use ($terms) {
                        $this->applyTermSearch($cq, ['name', 'slug', 'description'], $terms);
                    });
                    $q->orWhereHas('company', function ($cq) use ($terms) {
                        $this->applyTermSearch($cq, ['name', 'description'], $terms);
                    });
                })
                ->with(['company:id,name', 'categories:id,name,slug'])
                ->latest('start_date')
                ->take($type === 'all' ? 12 : 24)
                ->get();
        }

        // Search Companies/Organizers - search name and description
        $companies = collect();
        if ($type === 'all' || $type === 'organizers') {
            $companies = Company::where(function ($q) {
                    $q->where('is_suspended', false)
                      ->orWhereNull('is_suspended');
                })
                ->whereNotNull('slug')
                ->where('slug', '!=', '')
                ->where(function ($q) use ($terms) {
                    $this->applyTermSearch($q, ['name', 'description'], $terms);
                })
                ->withCount([
                    'events as events_count' => function ($q) {
                        $q->approved();
                    },
                ])
                ->latest()
                ->take($type === 'all' ? 6 : 12)
                ->get();
        }

        // Search Categories
        $categories = collect();
        if ($type === 'all' || $type === 'categories') {
            $categories = Category::where('is_active', true)
                ->where(function ($q) use ($terms) {
                    $this->applyTermSearch($q, ['name', 'description', 'slug'], $terms);
                })
                ->withCount('events')
                ->orderBy('order')
                ->take(12)
                ->get();
        }

        // Search Shop Products - extended to description
        $products = collect();
        if ($type === 'all' || $type === 'products') {
            $products = ShopProduct::where('status', 'approved')
                ->where('is_active', true)
                ->where(function ($q) use ($terms) {
                    $this->applyTermSearch($q, ['name', 'description', 'slug', 'category', 'color', 'size'], $terms);
                })
                ->latest()
                ->take($type === 'all' ? 8 : 16)
                ->get();
        }

        // Search Polls
        $polls = collect();
        if ($type === 'all' || $type === 'polls') {
            $polls = Poll::where('status', 'active')
                ->where(function ($q) use ($terms) {
                    $this->applyTermSearch($q, ['title', 'description', 'slug'], $terms);
                })
                ->with('company:id,name')
                ->withCount('contestants')
                ->latest()
                ->take($type === 'all' ? 6 : 12)
                ->get();
        }

        // Search Contestants
        $contestants = collect();
        if ($type === 'all' || $type === 'contestants') {
            try {
                $contestants = Contestant::where('status', 'active')
                    ->where(function ($q) use ($terms) {
                        $this->applyTermSearch($q, ['name', 'bio'], $terms);
                    })
                    ->whereHas('poll', function ($q) {
                        $q->where('status', 'active');
                    })
                    ->with('poll:id,title,slug')
                    ->orderBy('total_votes', 'desc')
                    ->take($type === 'all' ? 6 : 12)
                    ->get();
            } catch (\Exception $e) {
                // Skip contestants if there's an error
            }
        }

        // Search Speakers/Team Members
        $speakers = collect();
        if ($type === 'all' || $type === 'speakers') {
            $speakers = TeamMember::where('status', 'approved')
                ->where(function ($q) use ($terms) {
                    $this->applyTermSearch($q, ['full_name', 'title', 'role', 'job_description'], $terms);
                })
                ->take($type === 'all' ? 6 : 12)
                ->get();
        }

        // Search Surveys
        $surveys = collect();
        if ($type === 'all' || $type === 'surveys') {
            $surveys = Survey::where('status', 'active')
                ->where(function ($q) use ($terms) {
                    $this->applyTermSearch($q, ['title', 'description', 'slug'], $terms);
                })
                ->with('company:id,name')
                ->latest()
                ->take($type === 'all' ? 6 : 12)
                ->get();
        }

        // Search Conferences
        $conferences = collect();
        if ($type === 'all' || $type === 'conferences') {
            $conferences = Conference::where('status', 'active')
                ->where(function ($q) use ($terms) {
                    $this->applyTermSearch($q, ['title', 'description', 'venue', 'slug'], $terms);
                })
                ->with('company:id,name')
                ->withCount('registrations')
                ->latest('start_date')
                ->take($type === 'all' ? 6 : 12)
                ->get();
        }

        // Search Blog Articles
        $blogs = collect();
        if ($type === 'all' || $type === 'blogs') {
            $blogs = Article::where('type', 'blog')
                ->where('is_published', true)
                ->where(function ($q) use ($terms) {
                    $this->applyTermSearch($q, ['title', 'description', 'content', 'category'], $terms);
                })
                ->latest('published_at')
                ->take($type === 'all' ? 6 : 12)
                ->get();
        }

        $totalResults = $events->count() + $companies->count() + $categories->count()
            + $products->count() + $polls->count() + $contestants->count() + $speakers->count()
            + $surveys->count() + $conferences->count() + $blogs->count();

        // Generate suggestions if no results found
        $suggestions = [];
        if ($totalResults === 0) {
            $suggestions = $this->generateSuggestions($query);
        }

        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'events' => $events,
                'companies' => $companies,
                'categories' => $categories,
                'products' => $products,
                'polls' => $polls,
                'contestants' => $contestants,
                'speakers' => $speakers,
                'surveys' => $surveys,
                'conferences' => $conferences,
                'blogs' => $blogs,
                'totalResults' => $totalResults,
                'suggestions' => $suggestions,
            ]);
        }

        return view('search.results', [
            'query' => $query,
            'searchQuery' => $searchQuery,
            'aiSearch' => $aiSearch,
            'events' => $events,
            'companies' => $companies,
            'categories' => $categories,
            'products' => $products,
            'polls' => $polls,
            'contestants' => $contestants,
            'speakers' => $speakers,
            'surveys' => $surveys,
            'conferences' => $conferences,
            'blogs' => $blogs,
            'totalResults' => $totalResults,
            'type' => $type,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Quick search for autocomplete (AJAX only)
     */
    public function quickSearch(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if ($query === '' || strlen($query) < 2) {
            return response()->json([
                'suggestions' => [],
            ]);
        }

        $suggestions = collect();
        $queryLower = strtolower($query);
        $isShortQuery = strlen($query) <= 2;
        $primaryTake = $isShortQuery ? 5 : 8;
        $secondaryTake = $isShortQuery ? 3 : 5;
        
        // Use fuzzy variations for broader matching
        $variations = $this->getFuzzyVariations($query);
        $likeAny = '%' . $query . '%';

        $staticPageRoutes = [
            ['route' => 'home', 'icon' => 'home', 'title' => 'Home'],
            ['route' => 'events.index', 'icon' => 'calendar', 'title' => 'Events'],
            ['route' => 'shop.index', 'icon' => 'shopping-bag', 'title' => 'Shop'],
            ['route' => 'organizers.index', 'icon' => 'building', 'title' => 'Organizers'],
            ['route' => 'about', 'icon' => 'info', 'title' => 'About'],
            ['route' => 'contact', 'icon' => 'mail', 'title' => 'Contact'],
        ];

        $staticPages = collect($staticPageRoutes)
            ->filter(fn($page) => Route::has($page['route']))
            ->map(fn($page) => [
                'type' => 'page',
                'icon' => $page['icon'],
                'title' => $page['title'],
                'url' => route($page['route']),
            ])
            ->values()
            ->all();

        $pageMatches = collect($staticPages)->filter(function ($page) use ($queryLower) {
            return str_contains(strtolower($page['title']), $queryLower);
        })->map(function ($page) {
            return [
                'type' => 'page',
                'icon' => $page['icon'],
                'id' => $page['title'],
                'title' => $page['title'],
                'subtitle' => 'Main Page',
                'url' => $page['url'],
                'image' => null,
            ];
        });

        $suggestions = $suggestions->concat($pageMatches);

        // Get events with fuzzy logic
        try {
            $events = Event::where('status', 'approved')
                ->where(function ($q) use ($variations) {
                    foreach ($variations as $var) {
                        $q->orWhere('title', 'LIKE', "%{$var}%")
                          ->orWhere('venue_name', 'LIKE', "%{$var}%");
                    }
                })
                ->select('id', 'title', 'slug', 'start_date', 'banner_image', 'venue_name')
                ->with('company:id,name')
                ->latest('start_date')
                ->take($primaryTake)
                ->get()
                ->map(function ($event) {
                    return [
                        'type' => 'event',
                        'icon' => 'calendar',
                        'id' => $event->id,
                        'title' => $event->title,
                        'subtitle' => ($event->company->name ?? $event->venue_name ?? '') . ($event->start_date ? ' • ' . $event->start_date->format('M d') : ''),
                        'url' => route('events.show', $event->slug),
                        'image' => $event->banner_image ? $event->banner_url : null,
                    ];
                });
            $suggestions = $suggestions->concat($events);
        } catch (\Exception $e) { }

        // Get companies with fuzzy logic
        try {
            $companies = Company::where(function ($q) {
                    $q->where('is_suspended', false)->orWhereNull('is_suspended');
                })
                ->whereNotNull('slug')
                ->where('slug', '!=', '')
                ->where(function ($q) use ($variations) {
                    foreach ($variations as $var) {
                        $q->orWhere('name', 'LIKE', "%{$var}%");
                    }
                })
                ->select('id', 'name', 'logo', 'slug')
                ->take($primaryTake)
                ->get()
                ->map(function ($company) {
                    return [
                        'type' => 'organizer',
                        'icon' => 'building',
                        'id' => $company->id,
                        'title' => $company->name,
                        'subtitle' => 'Organizer',
                        'url' => route('organizers.show', $company->slug),
                        'image' => $company->logo ? asset('storage/' . $company->logo) : null,
                    ];
                });
            $suggestions = $suggestions->concat($companies);
        } catch (\Exception $e) { }

        // Get categories
        try {
            $categories = Category::where('is_active', true)
                ->where(function ($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%");
                })
                ->select('id', 'name', 'slug')
                ->withCount('events')
                ->take($secondaryTake)
                ->get()
                ->map(function ($category) {
                    return [
                        'type' => 'category',
                        'icon' => 'tag',
                        'id' => $category->id,
                        'title' => $category->name,
                        'subtitle' => $category->events_count . ' events',
                        'url' => route('categories.show', $category->slug),
                    ];
                });
            $suggestions = $suggestions->concat($categories);
        } catch (\Exception $e) { }

        // Get products
        try {
            $products = ShopProduct::where('status', 'approved')
                ->where('is_active', true)
                ->where(function ($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%");
                })
                ->select('id', 'name', 'slug', 'image_path', 'price')
                ->take($secondaryTake)
                ->get()
                ->map(function ($product) {
                    return [
                        'type' => 'product',
                        'icon' => 'shopping-bag',
                        'id' => $product->id,
                        'title' => $product->name,
                        'subtitle' => 'GH₵ ' . number_format($product->price, 2),
                        'url' => route('shop.show', $product->slug),
                        'image' => $product->image_path ? asset('storage/' . $product->image_path) : null,
                    ];
                });
            $suggestions = $suggestions->concat($products);
        } catch (\Exception $e) { }

        // Add "see all results" action
        if ($suggestions->count() > 0) {
            $suggestions->push([
                'type' => 'action',
                'icon' => 'search',
                'id' => 0,
                'title' => 'See all results for "' . $query . '"',
                'subtitle' => 'Press Enter or click here',
                'url' => route('search', ['q' => $query]),
                'image' => null,
            ]);
        }

        return response()->json([
            'suggestions' => $suggestions->values(),
            'query' => $query,
        ]);
    }

    /**
     * Normalize query for better matching
     */
    private function normalizeQuery(string $query): string
    {
        return trim(strtolower($query));
    }

    /**
     * Get individual search terms
     */
    private function getSearchTerms(string $query): array
    {
        $terms = explode(' ', $query);
        return array_filter($terms, fn($term) => strlen(trim($term)) >= 2);
    }

    /**
     * Build expanded search terms (query + synonyms + fuzzy)
     */
    private function buildSearchTerms(string $query, array $synonyms = []): array
    {
        $terms = array_merge(
            [$query],
            $this->getSearchTerms($query),
            $synonyms,
            $this->getFuzzyVariations($query)
        );

        $terms = array_values(array_filter(array_map(function ($term) {
            $term = trim((string) $term);
            return strlen($term) >= 2 ? $term : null;
        }, $terms)));

        $terms = array_values(array_unique($terms));

        return array_slice($terms, 0, 14);
    }

    private function applyTermSearch($query, array $columns, array $terms): void
    {
        if (empty($terms)) {
            return;
        }

        $query->where(function ($q) use ($columns, $terms) {
            foreach ($terms as $term) {
                $like = '%' . $term . '%';
                $q->orWhere(function ($sub) use ($columns, $like) {
                    foreach ($columns as $column) {
                        $sub->orWhere($column, 'LIKE', $like);
                    }
                });
            }
        });
    }

    /**
     * Create fuzzy variations of search query for better matching
     * Handles cases like "ZAAMA DISCO" matching "ZAAMADISCO"
     */
    private function getFuzzyVariations(string $query): array
    {
        $variations = [];
        $query = strtolower(trim($query));

        // Original query
        $variations[] = $query;

        // Remove all spaces
        $noSpaces = str_replace(' ', '', $query);
        if ($noSpaces !== $query) {
            $variations[] = $noSpaces;
        }

        // Remove special characters
        $alphanumeric = preg_replace('/[^a-z0-9]/', '', $query);
        if ($alphanumeric && $alphanumeric !== $query && $alphanumeric !== $noSpaces) {
            $variations[] = $alphanumeric;
        }

        // Split and get individual words
        $words = preg_split('/[\s\-_]+/', $query);
        foreach ($words as $word) {
            if (strlen($word) >= 3) {
                $variations[] = $word;
            }
        }

        return array_unique(array_filter($variations));
    }

    /**
     * Build a fuzzy search query that matches variations
     */
    private function buildFuzzyQuery($eloquentQuery, string $column, string $searchQuery)
    {
        $variations = $this->getFuzzyVariations($searchQuery);

        $eloquentQuery->where(function($q) use ($column, $variations) {
            foreach ($variations as $variation) {
                // Standard LIKE search
                $q->orWhereRaw("LOWER({$column}) LIKE ?", ["%{$variation}%"]);
                // Also search with spaces removed from database value
                $q->orWhereRaw("LOWER(REPLACE({$column}, ' ', '')) LIKE ?", ["%{$variation}%"]);
            }
        });

        return $eloquentQuery;
    }

    /**
     * Generate suggestions when no results found
     */
    private function generateSuggestions(string $query): array
    {
        $suggestions = [];

        // Try to find similar content using partial matching
        $terms = $this->getSearchTerms($query);

        foreach ($terms as $term) {
            if (strlen($term) < 3) continue;

            // Find similar events - search ALL
            $similarEvents = Event::where(function ($q) use ($term) {
                    $q->where('title', 'LIKE', "%{$term}%")
                      ->orWhere('summary', 'LIKE', "%{$term}%");
                })
                ->select('title')
                ->take(2)
                ->pluck('title')
                ->toArray();

            foreach ($similarEvents as $title) {
                $suggestions[] = [
                    'type' => 'event',
                    'text' => $title,
                    'url' => route('search', ['q' => $title]),
                ];
            }

            // Find similar categories
            $similarCategories = Category::where('is_active', true)
                ->where('name', 'LIKE', "%{$term}%")
                ->select('name', 'slug')
                ->take(2)
                ->get();

            foreach ($similarCategories as $category) {
                $suggestions[] = [
                    'type' => 'category',
                    'text' => $category->name,
                    'url' => route('events.index', ['category' => $category->slug]),
                ];
            }
        }

        // Get popular searches / trending content as fallback suggestions
        if (empty($suggestions)) {
            $popularEvents = Event::orderBy('views_count', 'desc')
                ->select('title')
                ->take(3)
                ->pluck('title')
                ->toArray();

            foreach ($popularEvents as $title) {
                $suggestions[] = [
                    'type' => 'popular',
                    'text' => $title,
                    'url' => route('search', ['q' => $title]),
                ];
            }

            $popularCategories = Category::where('is_active', true)
                ->withCount('events')
                ->orderBy('events_count', 'desc')
                ->select('name', 'slug')
                ->take(3)
                ->get();

            foreach ($popularCategories as $category) {
                $suggestions[] = [
                    'type' => 'category',
                    'text' => $category->name,
                    'url' => route('events.index', ['category' => $category->slug]),
                ];
            }
        }

        // Remove duplicates and limit
        $suggestions = collect($suggestions)->unique('text')->take(6)->values()->toArray();

        return $suggestions;
    }
}
