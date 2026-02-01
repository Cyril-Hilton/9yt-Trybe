<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventLike;
use App\Models\EventView;
use App\Models\OrganizationFollower;
use App\Services\AI\AiContentService;
use App\Services\News\NewsService;
use App\Services\SEO\AiLandingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\SEO\AiTranslationService;

class EventController extends Controller
{
    public function home(Request $request, NewsService $newsService, AiContentService $aiContent)
    {
        $query = Event::approved()->with('company');

        // Filter by region
        if ($request->filled('region')) {
            $query->where('region', $request->input('region'));
        }

        // Filter by date range
        if ($request->filled('filter')) {
            $filter = $request->input('filter');
            $now = now();

            switch ($filter) {
                case 'today':
                    $query->whereDate('start_date', $now->toDateString());
                    break;
                case 'this_weekend':
                    $startOfWeekend = $now->copy()->next('Saturday')->startOfDay();
                    $endOfWeekend = $now->copy()->next('Sunday')->endOfDay();
                    $query->whereBetween('start_date', [$startOfWeekend, $endOfWeekend]);
                    break;
            }
        }

        // Get upcoming events ordered by date
        $events = $query->upcoming()
            ->orderBy('start_date', 'asc')
            ->limit(16)
            ->get();

        // 16 regions in Ghana (aligned with front-end location mapping)
        $regions = [
            'Greater Accra',
            'Ashanti',
            'Central',
            'Western',
            'Western North',
            'Eastern',
            'Volta',
            'Oti',
            'Northern',
            'Savannah',
            'North East',
            'Upper East',
            'Upper West',
            'Bono',
            'Bono East',
            'Ahafo',
        ];

        $newsArticles = $newsService->getArticles();
        $newsDigest = $aiContent->generateNewsDigest($newsArticles, null);

        return view('welcome', compact('events', 'regions', 'newsArticles', 'newsDigest'));
    }

    public function index(Request $request)
    {
        $query = Event::approved()->with('company');
        $category = null;
        $invalidCategory = false;

        // Search by keyword
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhere('venue_name', 'like', "%{$search}%")
                    ->orWhere('venue_address', 'like', "%{$search}%");
            });
        }

        // Filter by location
        if ($request->filled('location')) {
            $location = $request->input('location');
            $query->where(function ($q) use ($location) {
                $q->where('venue_address', 'like', "%{$location}%")
                    ->orWhere('venue_name', 'like', "%{$location}%");
            });
        }

        // Filter by date
        if ($request->filled('date')) {
            $date = $request->input('date');
            $query->whereDate('start_date', $date);
        }

        // Filter by location type
        if ($request->filled('location_type')) {
            $query->where('location_type', $request->input('location_type'));
        }

        // Filter by price
        if ($request->filled('price_filter')) {
            $priceFilter = $request->input('price_filter');
            if ($priceFilter === 'free') {
                $query->whereHas('tickets', function ($q) {
                    $q->where('type', 'free')->where('is_active', true);
                });
            } elseif ($priceFilter === 'paid') {
                $query->whereHas('tickets', function ($q) {
                    $q->where('type', 'paid')->where('is_active', true);
                });
            }
        }

        // Filter by category (Eventbrite-style)
        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $category = Category::active()->where('slug', $categorySlug)->first();

            if ($category) {
                $query->inCategory($category->slug);
            } else {
                $invalidCategory = true;
                $category = Category::make([
                    'slug' => $categorySlug,
                    'name' => ucwords(str_replace('-', ' ', $categorySlug)),
                ]);
            }
        }

        // Sorting
        $sort = $request->input('sort', 'date');
        switch ($sort) {
            case 'popular':
                $query->trending();
                break;
            case 'date':
            default:
                $query->upcoming()->orderBy('start_date', 'asc');
                break;
        }

        $events = $invalidCategory ? collect() : $query->paginate(12);

        // Get trending events for sidebar
        $trendingEvents = Event::approved()
            ->with('company')
            ->trending()
            ->limit(5)
            ->get();

        // Get upcoming events grouped by month for calendar
        $upcomingEventDates = Event::approved()
            ->upcoming()
            ->selectRaw('DATE(start_date) as event_date, COUNT(*) as event_count')
            ->groupBy('event_date')
            ->get()
            ->pluck('event_count', 'event_date');

        // Get all event categories for filter
        $categories = \App\Models\Category::active()->get();

        $categoryIntro = null;
        $categoryMeta = null;
        if ($category && !$invalidCategory) {
            $landing = app(AiLandingService::class)
                ->generateCategoryLanding($category->name, $events->pluck('title')->take(8)->all());

            $fallbackTitle = $category->meta_title ?: ($category->name . ' Events | 9yt !Trybe');
            $fallbackDescription = $category->meta_description
                ?: 'Browse upcoming ' . $category->name . ' events, tickets, and experiences on 9yt !Trybe.';

            $categoryMeta = [
                'headline' => $landing['headline'] ?? ($category->name . ' Events'),
                'intro' => $landing['intro'] ?? '',
                'meta_title' => $landing['meta_title'] ?? $fallbackTitle,
                'meta_description' => $landing['meta_description'] ?? $fallbackDescription,
            ];

            $translator = app(AiTranslationService::class);
            $lang = $translator->resolveLanguage($request->query('lang'));
            if ($lang !== 'en') {
                $translated = $translator->translateMeta(
                    $categoryMeta['meta_title'],
                    $categoryMeta['meta_description'],
                    $lang
                );
                $categoryMeta['meta_title'] = $translated['meta_title'];
                $categoryMeta['meta_description'] = $translated['meta_description'];
                $categoryMeta['intro'] = $translator->translateText($categoryMeta['intro'], $lang);
                $categoryMeta['headline'] = $translator->translateText($categoryMeta['headline'], $lang);
            }

            $categoryIntro = $categoryMeta['intro'];
        }

        return view('public.events.index', compact(
            'events',
            'trendingEvents',
            'upcomingEventDates',
            'categories',
            'category',
            'invalidCategory',
            'categoryIntro',
            'categoryMeta'
        ));
    }

    public function category(Request $request, string $slug)
    {
        $request->merge(['category' => $slug]);

        return $this->index($request);
    }

    public function show(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)
            ->approved()
            ->with(['company', 'tickets.section', 'images', 'videos', 'faqs'])
            ->firstOrFail();

        // Track view
        $this->trackView($event);

        // Check if user has liked this event
        $userLiked = false;
        if (Auth::check()) {
            $userLiked = EventLike::where('event_id', $event->id)
                ->where('user_id', Auth::id())
                ->exists();
        }

        // Check if user follows this organization
        $userFollowing = false;
        if (Auth::check()) {
            $userFollowing = OrganizationFollower::where('company_id', $event->company_id)
                ->where('user_id', Auth::id())
                ->exists();
        }

        // SOCIAL PROOF DATA (Conversion Booster - increases conversions by 15-30%)

        // Total attendees/tickets sold
        $totalAttendees = \App\Models\EventAttendee::where('event_id', $event->id)
            ->where('status', '!=', 'cancelled')
            ->count();

        // Calculate total tickets remaining across all ticket types
        $ticketsRemaining = 0;
        $lowestRemainingTicket = null;
        foreach ($event->tickets as $ticket) {
            if ($ticket->quantity !== null) {
                $remaining = $ticket->quantity - $ticket->sold;
                $ticketsRemaining += $remaining;

                // Track lowest remaining for scarcity messaging
                if ($remaining > 0 && ($lowestRemainingTicket === null || $remaining < $lowestRemainingTicket)) {
                    $lowestRemainingTicket = $remaining;
                }
            }
        }

        // Recent purchase activity (last 24 hours)
        $recentPurchases = \App\Models\EventOrder::where('event_id', $event->id)
            ->where('payment_status', 'completed')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        // Popularity indicators
        $isPopular = $totalAttendees > 50 || $recentPurchases > 10;
        $isTrending = $recentPurchases > 5;
        $isAlmostSoldOut = $lowestRemainingTicket !== null && $lowestRemainingTicket <= 10;

        // Get related events from same organizer
        $relatedEvents = Event::approved()
            ->where('company_id', $event->company_id)
            ->where('id', '!=', $event->id)
            ->upcoming()
            ->limit(3)
            ->get();

        $metaOverrides = null;
        $translator = app(AiTranslationService::class);
        $lang = $translator->resolveLanguage($request->query('lang'));
        if ($lang !== 'en') {
            $baseTitle = $event->meta_title ?: ($event->title . ' - Book Tickets');
            $baseDescription = $event->meta_description ?: Str::limit(strip_tags($event->summary ?? $event->overview ?? ('Book tickets for ' . $event->title)), 155);
            $metaOverrides = $translator->translateMeta($baseTitle, $baseDescription, $lang);
        }

        return view('public.events.show', compact(
            'event',
            'userLiked',
            'userFollowing',
            'relatedEvents',
            'totalAttendees',
            'ticketsRemaining',
            'lowestRemainingTicket',
            'recentPurchases',
            'isPopular',
            'isTrending',
            'isAlmostSoldOut',
            'metaOverrides'
        ));
    }

    public function like(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->approved()->firstOrFail();

        $userId = Auth::id();
        $sessionId = session()->getId();

        $existingLike = EventLike::where('event_id', $event->id)
            ->where(function ($q) use ($userId, $sessionId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('session_id', $sessionId);
                }
            })
            ->first();

        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $event->decrementLikes();
            $liked = false;
        } else {
            // Like
            EventLike::create([
                'event_id' => $event->id,
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
            ]);
            $event->incrementLikes();
            $liked = true;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'liked' => $liked,
                'likes_count' => $event->fresh()->likes_count,
            ]);
        }

        return back();
    }

    public function followOrganization(Request $request, Event $event)
    {
        if (!Auth::check()) {
            return redirect()->route('organization.login')
                ->with('error', 'Please login to follow organizations.');
        }

        $userId = Auth::id();
        $email = Auth::user()->email;

        $existing = OrganizationFollower::where('company_id', $event->company_id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            // Unfollow
            $existing->delete();
            $following = false;
            $message = 'Unfollowed ' . $event->company->name;
        } else {
            // Follow
            OrganizationFollower::create([
                'company_id' => $event->company_id,
                'user_id' => $userId,
                'email' => $email,
                'email_notifications' => true,
            ]);
            $following = true;
            $message = 'Now following ' . $event->company->name;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'following' => $following,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    protected function trackView(Event $event)
    {
        $userId = Auth::id();
        $sessionId = session()->getId();
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();

        // Don't track multiple views from same user/session within 1 hour
        $recentView = EventView::where('event_id', $event->id)
            ->where(function ($q) use ($userId, $sessionId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('session_id', $sessionId);
                }
            })
            ->where('created_at', '>', now()->subHour())
            ->exists();

        if (!$recentView) {
            EventView::create([
                'event_id' => $event->id,
                'user_id' => $userId,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            $event->incrementViews();
        }
    }

    public function calendar(Request $request)
    {
        // Get year and month from request or default to current
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        // Create date object for the selected month
        $date = now()->setYear($year)->setMonth($month)->startOfMonth();

        // Get all approved events for the selected month
        $events = Event::approved()
            ->with('company')
            ->whereBetween('start_date', [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth()
            ])
            ->get()
            ->groupBy(function ($event) {
                return $event->start_date->format('Y-m-d');
            });

        // Get trending events for sidebar
        $trendingEvents = Event::approved()
            ->trending()
            ->with('company')
            ->limit(4)
            ->get();

        return view('public.events.calendar', compact('events', 'trendingEvents', 'date'));
    }
}
