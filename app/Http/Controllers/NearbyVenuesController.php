<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NearbyVenuesController extends Controller
{
    private $provider;
    private $mapsEnabled;
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_api_key');
        $this->provider = config('services.maps.provider', 'osm');
        $this->mapsEnabled = config('services.maps.enabled', true);
    }

    /**
     * Search nearby venues by category
     */
    public function searchNearby(Request $request)
    {
        // Increase execution time for this API call to handle multiple requests
        set_time_limit(120);

        if (!$this->mapsEnabled) {
            return response()->json([
                'places' => [],
                'total_results' => 0,
                'error' => 'Map functionality is currently disabled.',
            ]);
        }

        if ($this->provider === 'google' && !$this->apiKey) {
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

        if ($this->provider === 'google') {
            $response = $this->fetchFromGooglePlaces($validated);
        } else {
            $response = $this->fetchFromOSM($validated);
        }

        $payload = $response->getData(true);
        Cache::put($cacheKey, $payload, now()->addMinutes(15)); // Cache for 15 mins for OSM

        return $response;
    }

    /**
     * Fetch from OpenStreetMap using Overpass API
     */
    private function fetchFromOSM($params)
    {
        $userLat = $params['lat'];
        $userLng = $params['lng'];
        $radiusKm = ($params['radius'] ?? 50000) / 1000;
        $radiusMeters = $params['radius'] ?? 50000;

        // Map categories to OSM tags
        $tagConfigs = $this->getOSMTagConfigs($params['category']);

        // List of Overpass API servers for redundancy
        $overpassServers = [
            'https://overpass-api.de/api/interpreter',
            'https://overpass.kumi.systems/api/interpreter',
            'https://overpass.n.ey.pw/api/interpreter',
            'https://overpass.be/api/interpreter'
        ];

        $allPlaces = [];
        $lastException = null;

        // Try servers until one works
        foreach ($overpassServers as $overpassUrl) {
            try {
                // Build Overpass QL query
                $query = "[out:json][timeout:25];\n(\n";
                foreach ($tagConfigs as $config) {
                    $tagValue = $config['value'];
                    $tagName = $config['name'];
                    $query .= "  node[\"{$tagName}\"=\"{$tagValue}\"](around:{$radiusMeters}, {$userLat}, {$userLng});\n";
                    $query .= "  way[\"{$tagName}\"=\"{$tagValue}\"](around:{$radiusMeters}, {$userLat}, {$userLng});\n";
                    $query .= "  relation[\"{$tagName}\"=\"{$tagValue}\"](around:{$radiusMeters}, {$userLat}, {$userLng});\n";
                }
                $query .= ");\nout center;";

                $response = Http::timeout(15)->asForm()->post($overpassUrl, ['data' => $query]);

                if ($response->failed()) {
                    continue; // Try next server
                }

                $data = $response->json();
                $elements = $data['elements'] ?? [];

                foreach ($elements as $element) {
                    $lat = $element['lat'] ?? ($element['center']['lat'] ?? null);
                    $lon = $element['lon'] ?? ($element['center']['lon'] ?? null);

                    if ($lat === null || $lon === null) continue;

                    $distance = $this->calculateDistance($userLat, $userLng, $lat, $lon);
                    $tags = $element['tags'] ?? [];
                    
                    // Construct address from tags
                    $addressParts = [];
                    if (isset($tags['addr:street'])) $addressParts[] = $tags['addr:street'] . (isset($tags['addr:housenumber']) ? ' ' . $tags['addr:housenumber'] : '');
                    if (isset($tags['addr:suburb'])) $addressParts[] = $tags['addr:suburb'];
                    if (isset($tags['addr:city'])) $addressParts[] = $tags['addr:city'];
                    
                    $address = implode(', ', $addressParts);
                    if (empty($address)) $address = $params['category'] . ' near ' . $userLat . ', ' . $userLng;

                    $allPlaces[] = [
                        'id' => $element['type'] . '/' . $element['id'],
                        'name' => $tags['name'] ?? 'Unnamed ' . ucfirst($params['category']),
                        'address' => $address,
                        'latitude' => $lat,
                        'longitude' => $lon,
                        'rating' => isset($tags['rating']) ? (float)$tags['rating'] : null,
                        'user_ratings_total' => 0,
                        'price_level' => null,
                        'phone' => $tags['phone'] ?? ($tags['contact:phone'] ?? null),
                        'maps_url' => "https://www.openstreetmap.org/" . $element['type'] . "/" . $element['id'],
                        'photo_reference' => null,
                        'photo_url' => $this->getCategoryPlaceholderImage($params['category'], $element['id']),
                        'distance_km' => round($distance, 2),
                        'is_open_now' => null,
                        'category' => $params['category'],
                    ];
                }

                $sortedPlaces = collect($allPlaces)->sortBy('distance_km')->values()->toArray();

                return response()->json([
                    'places' => $sortedPlaces,
                    'total_results' => count($sortedPlaces),
                    'provider' => 'osm'
                ]);

            } catch (\Exception $e) {
                $lastException = $e;
                continue; // Try next server
            }
        }

        // if we get here, all servers failed
        \Log::error('OSM/Overpass Error (All servers failed): ' . ($lastException ? $lastException->getMessage() : 'Unknown error'));
        return response()->json([
            'places' => [],
            'total_results' => 0,
            'error' => 'Failed to fetch venues from OpenStreetMap servers.'
        ]);
    }

    private function fetchFromGooglePlaces($params)
    {
        $configs = $this->getSearchConfigs($params['category']);
        $allPlaces = [];
        $photoReferences = [];

        foreach ($configs as $config) {
            $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json";
            $query = [
                'location' => "{$params['lat']},{$params['lng']}",
                'radius' => $params['radius'] ?? 50000,
                'key' => $this->apiKey,
            ];

            if (isset($config['type'])) $query['type'] = $config['type'];
            if (isset($config['keyword'])) $query['keyword'] = $config['keyword'];

            try {
                $response = Http::get($url, $query);
                
                if ($response->successful()) {
                    $results = $response->json()['results'] ?? [];
                    
                    foreach ($results as $place) {
                        if (!isset($allPlaces[$place['place_id']])) {
                            $distance = $this->calculateDistance(
                                $params['lat'], 
                                $params['lng'], 
                                $place['geometry']['location']['lat'], 
                                $place['geometry']['location']['lng']
                            );

                            $allPlaces[$place['place_id']] = [
                                'id' => $place['place_id'],
                                'name' => $place['name'],
                                'address' => $place['vicinity'] ?? '',
                                'latitude' => $place['geometry']['location']['lat'],
                                'longitude' => $place['geometry']['location']['lng'],
                                'rating' => $place['rating'] ?? null,
                                'user_ratings_total' => $place['user_ratings_total'] ?? 0,
                                'price_level' => $place['price_level'] ?? null,
                                'photo_reference' => $place['photos'][0]['photo_reference'] ?? null,
                                'distance_km' => round($distance, 2),
                                'is_open_now' => $place['opening_hours']['open_now'] ?? null,
                                'category' => $params['category'],
                                'maps_url' => "https://www.google.com/maps/search/?api=1&query=".urlencode($place['name'])."&query_place_id=".$place['place_id']
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Google Places Error: ' . $e->getMessage());
            }
        }

        $sortedPlaces = collect($allPlaces)->sortBy('distance_km')->values()->toArray();

        return response()->json([
            'places' => $sortedPlaces,
            'total_results' => count($sortedPlaces),
            'provider' => 'google'
        ]);
    }

    private function getOSMTagConfigs($category)
    {
        return match($category) {
            'club' => [
                ['name' => 'amenity', 'value' => 'nightclub']
            ],
            'restaurant' => [
                ['name' => 'amenity', 'value' => 'restaurant'],
                ['name' => 'amenity', 'value' => 'cafe']
            ],
            'lounge' => [
                ['name' => 'amenity', 'value' => 'bar'],
                ['name' => 'amenity', 'value' => 'pub']
            ],
            'arcade' => [
                ['name' => 'leisure', 'value' => 'amusement_arcade']
            ],
            'hotel' => [
                ['name' => 'tourism', 'value' => 'hotel']
            ],
            'lodging' => [
                ['name' => 'tourism', 'value' => 'guest_house'],
                ['name' => 'tourism', 'value' => 'hostel'],
                ['name' => 'tourism', 'value' => 'apartment']
            ],
            default => [
                ['name' => 'amenity', 'value' => 'restaurant']
            ]
        };
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

    private function getCategoryPlaceholderImage($category, $seed = null): string
    {
        $placeholders = [
            'club' => [
                'https://images.unsplash.com/photo-1566737236500-c8ac43014a67?w=800&q=80',
                'https://images.unsplash.com/photo-1570872626485-d8ffea69f463?w=800&q=80',
                'https://images.unsplash.com/photo-1545128485-c400e7702796?w=800&q=80',
                'https://images.unsplash.com/photo-1571266028243-e4733b0f0bb1?w=800&q=80',
                'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=800&q=80',
            ],
            'restaurant' => [
                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&q=80',
                'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=800&q=80',
                'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&q=80',
                'https://images.unsplash.com/photo-1514315384763-ba401779410f?w=800&q=80',
                'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=800&q=80',
                'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=800&q=80',
            ],
            'lounge' => [
                'https://images.unsplash.com/photo-1572116469696-31de0f17cc34?w=800&q=80',
                'https://images.unsplash.com/photo-1574096079513-d8259312b785?w=800&q=80',
                'https://images.unsplash.com/photo-1560624052-449f5ddf0c31?w=800&q=80',
                'https://images.unsplash.com/photo-1597075095400-b31046162232?w=800&q=80',
            ],
            'arcade' => [
                'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=800&q=80',
                'https://images.unsplash.com/photo-1511884642898-4c92249e20b6?w=800&q=80',
                'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=800&q=80',
                'https://images.unsplash.com/photo-1627850604058-52e40de1b8ed?w=800&q=80',
            ],
            'hotel' => [
                'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80',
                'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800&q=80',
                'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&q=80',
                'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800&q=80',
            ],
            'lodging' => [
                'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
                'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
                'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800&q=80',
                'https://images.unsplash.com/photo-1560185007-cde436f6a4d0?w=800&q=80',
            ],
            'default' => [
                'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=800&q=80',
                'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=800&q=80',
                'https://images.unsplash.com/photo-1514525253344-a812ca920d0f?w=800&q=80',
            ]
        ];

        $images = $placeholders[$category] ?? $placeholders['default'];
        
        // Use seed to consistently pick the same image for the same venue
        if ($seed !== null) {
            // Simple hash of the seed to an index
            $index = abs(crc32($seed)) % count($images);
            return $images[$index];
        }

        return $images[0];
    }
}
