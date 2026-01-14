<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NearbyVenuesController extends Controller
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_api_key');
    }

    /**
     * Search nearby venues by category
     */
    public function searchNearby(Request $request)
    {
        // Increase execution time for this API call to handle multiple requests
        set_time_limit(120);

        if (!$this->apiKey) {
            return response()->json([
                'places' => [],
                'total_results' => 0,
                'error' => 'Google Places API key not configured.',
            ]);
        }

        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'category' => 'required|in:club,restaurant,lounge,arcade,hotel,lodging',
            'radius' => 'nullable|integer|min:100|max:50000',
        ]);

        $cacheKey = sprintf(
            'nearby:%s:%s:%s:%s',
            round($validated['lat'], 2),
            round($validated['lng'], 2),
            $validated['category'],
            $validated['radius'] ?? 50000
        );

        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response()->json($cached);
        }

        $response = $this->fetchFromGooglePlaces($validated);
        $payload = $response->getData(true);
        Cache::put($cacheKey, $payload, now()->addMinutes(5));

        return $response;
    }

    /**
     * Fetch from Google Places API (Using legacy nearbysearch - more compatible)
     */
    private function fetchFromGooglePlaces($params)
    {
        $searchConfigs = array_slice($this->getSearchConfigs($params['category']), 0, 3);
        $radius = $params['radius'] ?? 50000; // 50km for better coverage

        $userLat = $params['lat'];
        $userLng = $params['lng'];

        // Use legacy Places API nearbysearch endpoint
        $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';

        $allPlaces = [];
        $seenPlaceIds = [];

        // Execute multiple searches to get comprehensive results
        foreach ($searchConfigs as $config) {
            $nextPageToken = null;
            $searchPlaces = [];
            $pagesFetched = 0;
            $maxPages = 1; // Limit to 1 page per search to avoid timeout

            // Fetch pages for this search configuration
            do {
                // Add next page token if available (must wait before using it)
                if ($nextPageToken) {
                    sleep(2); // Required delay by Google API before using page token
                    $requestParams = [
                        'pagetoken' => $nextPageToken,
                        'key' => $this->apiKey
                    ];
                } else {
                    // First page request
                    $requestParams = [
                        'location' => "{$userLat},{$userLng}",
                        'radius' => $radius,
                        'key' => $this->apiKey
                    ];

                    // Add type or keyword based on config
                    if (isset($config['type'])) {
                        $requestParams['type'] = $config['type'];
                    }
                    if (isset($config['keyword'])) {
                        $requestParams['keyword'] = $config['keyword'];
                    }
                }

                $response = Http::timeout(8)->get($url, $requestParams);

                if ($response->failed()) {
                    \Log::error('Google Places API HTTP Error', [
                        'http_status' => $response->status(),
                        'category' => $params['category'],
                        'config' => $config,
                        'error_body' => $response->body()
                    ]);
                    break;
                }

                $data = $response->json();

                if (($data['status'] ?? null) === 'REQUEST_DENIED' || ($data['status'] ?? null) === 'INVALID_REQUEST') {
                    $errorMessage = $data['error_message'] ?? 'Google Places request denied.';
                    \Log::error('Google Places API Request Denied', [
                        'api_status' => $data['status'] ?? 'unknown',
                        'error_message' => $errorMessage,
                        'category' => $params['category'],
                        'config' => $config
                    ]);
                    return response()->json([
                        'places' => [],
                        'total_results' => 0,
                        'error' => $errorMessage,
                        'billing_off' => $this->isBillingDisabled($errorMessage),
                    ]);
                }

                if (isset($data['error_message'])) {
                    \Log::error('Google Places API Response Error', [
                        'api_status' => $data['status'] ?? 'unknown',
                        'error_message' => $data['error_message'],
                        'category' => $params['category'],
                        'config' => $config
                    ]);
                    break;
                }

                // Log if we got results for this search
                $resultCount = count($data['results'] ?? []);
                if ($resultCount > 0) {
                    \Log::debug('Search config returned results', [
                        'category' => $params['category'],
                        'config' => $config,
                        'result_count' => $resultCount,
                        'has_next_page' => isset($data['next_page_token']),
                        'page' => $pagesFetched + 1
                    ]);
                }

                $results = $data['results'] ?? [];
                $searchPlaces = array_merge($searchPlaces, $results);

                // Get next page token if available
                $nextPageToken = $data['next_page_token'] ?? null;
                $pagesFetched++;

            } while ($nextPageToken && $pagesFetched < $maxPages);

            // Deduplicate and merge results
            foreach ($searchPlaces as $place) {
                $placeId = $place['place_id'] ?? null;
                if ($placeId && !in_array($placeId, $seenPlaceIds)) {
                    $seenPlaceIds[] = $placeId;
                    $allPlaces[] = $place;
                }
            }
        }

        \Log::info('Google Places API Results', [
            'category' => $params['category'],
            'total_unique_places' => count($allPlaces),
            'searches_performed' => count($searchConfigs),
            'location' => "{$userLat},{$userLng}",
            'radius_km' => $radius / 1000
        ]);

        // Calculate distance for each place
        $placesWithDistance = collect($allPlaces)->map(function ($place) use ($userLat, $userLng, $params) {
            $placeLat = $place['geometry']['location']['lat'] ?? 0;
            $placeLng = $place['geometry']['location']['lng'] ?? 0;

            $distance = $this->calculateDistance($userLat, $userLng, $placeLat, $placeLng);

            return [
                'id' => $place['place_id'] ?? null,
                'name' => $place['name'] ?? 'Unknown',
                'address' => $place['vicinity'] ?? ($place['formatted_address'] ?? ''),
                'latitude' => $placeLat,
                'longitude' => $placeLng,
                'rating' => $place['rating'] ?? null,
                'user_ratings_total' => $place['user_ratings_total'] ?? 0,
                'price_level' => $place['price_level'] ?? null,
                'phone' => null,
                'maps_url' => "https://www.google.com/maps/place/?q=place_id:" . ($place['place_id'] ?? ''),
                'photo_reference' => isset($place['photos'][0]['photo_reference']) ? $place['photos'][0]['photo_reference'] : null,
                'distance_km' => round($distance, 2),
                'is_open_now' => $place['opening_hours']['open_now'] ?? null,
                'category' => $params['category'],
            ];
        });

        // Apply category-based prioritization then sort by distance
        $priorityCategories = ['club', 'lodging', 'hotel'];

        if (in_array($params['category'], $priorityCategories)) {
            // For priority categories, sort by distance directly
            $placesWithDistance = $placesWithDistance->sortBy('distance_km');
        } else {
            // For other categories, also sort by distance
            $placesWithDistance = $placesWithDistance->sortBy('distance_km');
        }

        $placesWithDistance = $placesWithDistance->values()->toArray();

        return response()->json([
            'places' => $placesWithDistance,
            'total_results' => count($placesWithDistance)
        ]);
    }

    /**
     * Get search configurations for comprehensive results
     * Each category can have multiple search strategies
     * Using multiple types and keywords to maximize location discovery
     */
    private function getSearchConfigs($category)
    {
        return match($category) {
            'club' => [
                ['type' => 'night_club'],
                ['type' => 'bar', 'keyword' => 'nightclub'],
                ['keyword' => 'dance club'],
                ['keyword' => 'disco'],
                ['keyword' => 'nightlife'],
            ],
            'restaurant' => [
                ['type' => 'restaurant'],
                ['type' => 'cafe'],
                ['type' => 'meal_takeaway'],
                ['keyword' => 'food'],
                ['keyword' => 'eatery'],
            ],
            'lounge' => [
                ['type' => 'bar'],
                ['type' => 'night_club', 'keyword' => 'lounge'],
                ['keyword' => 'cocktail lounge'],
                ['keyword' => 'hookah lounge'],
                ['keyword' => 'shisha lounge'],
            ],
            'arcade' => [
                ['type' => 'amusement_center'],
                ['type' => 'bowling_alley'],
                ['keyword' => 'arcade games'],
                ['keyword' => 'game center'],
                ['keyword' => 'gaming'],
                ['keyword' => 'recreation'],
                ['keyword' => 'fun center'],
                ['keyword' => 'entertainment'],
            ],
            'hotel' => [
                ['type' => 'lodging'],
                ['type' => 'hotel'],
                ['keyword' => 'hotel'],
            ],
            'lodging' => [
                ['type' => 'lodging'],
                ['keyword' => 'airbnb'],
                ['keyword' => 'guest house'],
                ['keyword' => 'vacation rental'],
                ['keyword' => 'short stay'],
                ['keyword' => 'apartment'],
            ],
            default => [
                ['type' => 'restaurant']
            ]
        };
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get photo URL from reference (Legacy API)
     */
    public function getPhoto($photoReference)
    {
        $url = "https://maps.googleapis.com/maps/api/place/photo";
        $url .= "?maxwidth=800&photo_reference={$photoReference}&key={$this->apiKey}";

        return redirect($url);
    }

    /**
     * Get user location from IP (using existing IP Geolocation API)
     */
    public function getLocationFromIP(Request $request)
    {
        $ip = $request->ip();
        $ipGeoKey = config('services.ip_geolocation.api_key');

        // Use ipgeolocation.io API
        $response = Http::timeout(5)->get("https://api.ipgeolocation.io/ipgeo?apiKey={$ipGeoKey}&ip={$ip}");

        if ($response->successful()) {
            $data = $response->json();
            return response()->json([
                'latitude' => $data['latitude'] ?? 5.6037,
                'longitude' => $data['longitude'] ?? -0.1870,
                'city' => $data['city'] ?? 'Accra',
                'country' => $data['country_name'] ?? 'Ghana'
            ]);
        }

        // Default to Accra, Ghana
        return response()->json([
            'latitude' => 5.6037,
            'longitude' => -0.1870,
            'city' => 'Accra',
            'country' => 'Ghana'
        ]);
    }

    private function isBillingDisabled(string $errorMessage): bool
    {
        $message = strtolower($errorMessage);

        if (!str_contains($message, 'billing')) {
            return false;
        }

        return str_contains($message, 'not been enabled')
            || str_contains($message, 'billing account')
            || str_contains($message, 'billing must be enabled')
            || str_contains($message, 'activate billing')
            || str_contains($message, 'enable billing');
    }
}
