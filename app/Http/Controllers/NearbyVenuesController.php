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
            // Auto-fallback to OSM when Google key is missing
            $this->provider = 'osm';
        }

        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'category' => 'required|in:club,restaurant,lounge,arcade,hotel,lodging',
            'radius' => 'nullable|integer|min:100|max:50000',
        ]);

        $cacheKey = sprintf(
            'nearby:v2:%s:%s:%s:%s',
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
            $payload = $response->getData(true);

            if ($this->shouldFallbackToOsm($payload)) {
                $fallbackResponse = $this->fetchFromOSM($validated);
                $fallbackPayload = $fallbackResponse->getData(true);
                $fallbackPayload['fallback_from'] = 'google';
                $response = response()->json($fallbackPayload);
            }
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

                $response = Http::timeout(15)
                    ->when(app()->environment('local'), fn($h) => $h->withoutVerifying())
                    ->asForm()
                    ->post($overpassUrl, ['data' => $query]);

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
                    if (isset($tags['addr:street'])) {
                        $street = $tags['addr:street'];
                        if (isset($tags['addr:housenumber'])) {
                            $street .= ' ' . $tags['addr:housenumber'];
                        }
                        $addressParts[] = $street;
                    }
                    
                    if (isset($tags['addr:suburb'])) $addressParts[] = $tags['addr:suburb'];
                    if (isset($tags['addr:neighbourhood'])) $addressParts[] = $tags['addr:neighbourhood'];
                    if (isset($tags['addr:city'])) $addressParts[] = $tags['addr:city'];
                    if (isset($tags['addr:postcode'])) $addressParts[] = $tags['addr:postcode'];
                    
                    $address = implode(', ', $addressParts);
                    if (empty($address)) {
                        // Better fallback for address
                        $address = ($tags['amenity'] ?? $tags['tourism'] ?? $tags['leisure'] ?? $params['category']) . ' near ' . round($lat, 4) . ', ' . round($lon, 4);
                    }

                    $venueName = $tags['name'] ?? 'Unnamed ' . ucfirst($params['category']);

                    $allPlaces[] = [
                        'id' => $element['type'] . '/' . $element['id'],
                        'name' => $venueName,
                        'address' => $address,
                        'latitude' => $lat,
                        'longitude' => $lon,
                        'rating' => isset($tags['rating']) ? (float)$tags['rating'] : null,
                        'user_ratings_total' => 0,
                        'price_level' => null,
                        'phone' => $tags['phone'] ?? ($tags['contact:phone'] ?? null),
                        'maps_url' => "https://www.google.com/maps/search/?api=1&query=" . urlencode($venueName . " @ " . $lat . "," . $lon),
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
        
        // Return fallback venues so the UI is not empty
        $fallbackVenues = $this->getFallbackVenues($params['category']);
        
        return response()->json([
            'places' => $fallbackVenues,
            'total_results' => count($fallbackVenues),
            'provider' => 'fallback'
        ]);
    }

    private function fetchFromGooglePlaces($params)
    {
        $configs = $this->getSearchConfigs($params['category']);
        $allPlaces = [];
        $photoReferences = [];
        $lastStatus = null;
        $lastErrorMessage = null;

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
                $response = Http::timeout(8) // Set 8s timeout per request
                    ->when(app()->environment('local'), fn($h) => $h->withoutVerifying())
                    ->get($url, $query);
                
                if ($response->successful()) {
                    $payload = $response->json();
                    $status = $payload['status'] ?? null;
                    $lastStatus = $status ?? $lastStatus;
                    $lastErrorMessage = $payload['error_message'] ?? $lastErrorMessage;

                    if (!empty($status) && $status !== 'OK' && $status !== 'ZERO_RESULTS') {
                        continue;
                    }

                    $results = $payload['results'] ?? [];
                    
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
                $lastErrorMessage = $e->getMessage();
            }
        }

        $sortedPlaces = collect($allPlaces)->sortBy('distance_km')->values()->toArray();

        return response()->json([
            'places' => $sortedPlaces,
            'total_results' => count($sortedPlaces),
            'provider' => 'google',
            'status' => $lastStatus,
            'error_message' => $lastErrorMessage,
        ]);
    }

    private function shouldFallbackToOsm(array $payload): bool
    {
        $total = (int) ($payload['total_results'] ?? 0);
        if ($total > 0) {
            return false;
        }

        $status = strtoupper((string) ($payload['status'] ?? ''));
        $errorMessage = (string) ($payload['error_message'] ?? '');

        if ($this->isBillingDisabled($errorMessage)) {
            return true;
        }

        if (in_array($status, ['REQUEST_DENIED', 'OVER_QUERY_LIMIT', 'INVALID_REQUEST'], true)) {
            return true;
        }

        if ($status === 'ZERO_RESULTS') {
            return true;
        }

        return $errorMessage !== '';
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
        ],
        'restaurant' => [
            ['type' => 'restaurant'],
            ['type' => 'cafe'],
            ['keyword' => 'food'],
        ],
        'lounge' => [
            ['type' => 'bar', 'keyword' => 'lounge'],
            ['keyword' => 'cocktail lounge'],
        ],
        'arcade' => [
            ['type' => 'amusement_center'],
            ['keyword' => 'arcade games'],
        ],
        'hotel' => [
            ['type' => 'lodging', 'keyword' => 'hotel'],
        ],
        'lodging' => [
            ['type' => 'lodging'],
            ['keyword' => 'airbnb'],
            ['keyword' => 'guest house'],
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
        $response = Http::timeout(5)
            ->when(app()->environment('local'), fn($h) => $h->withoutVerifying())
            ->get("https://api.ipgeolocation.io/ipgeo?apiKey={$ipGeoKey}&ip={$ip}");

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

    /**
     * Get fallback venues for demo/offline purposes
     */
    private function getFallbackVenues($category)
    {
        // Default hardcoded venues for Accra (Top Locations)
        $venues = [
            [
                'id' => 'fallback_1',
                'name' => 'Twist Night Club',
                'address' => 'Labone, Accra, Ghana',
                'latitude' => 5.5760,
                'longitude' => -0.1683,
                'rating' => 4.5,
                'user_ratings_total' => 350,
                'price_level' => 3,
                'distance_km' => 2.5,
                'is_open_now' => true,
                'category' => 'club',
                'maps_url' => 'https://maps.google.com/?q=Twist+Night+Club+Accra',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('club', 'twist'),
            ],
            [
                'id' => 'fallback_2',
                'name' => 'Sandbox Beach Club',
                'address' => 'Labadi, Accra',
                'latitude' => 5.5647,
                'longitude' => -0.1624,
                'rating' => 4.4,
                'user_ratings_total' => 520,
                'price_level' => 4,
                'distance_km' => 3.1,
                'is_open_now' => true,
                'category' => 'club',
                'maps_url' => 'https://maps.google.com/?q=Sandbox+Beach+Club',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('club', 'sandbox'),
            ],
            [
                'id' => 'fallback_9',
                'name' => 'Bloom Bar',
                'address' => 'Oxford Street, Osu, Accra',
                'latitude' => 5.5583,
                'longitude' => -0.1818,
                'rating' => 4.6,
                'user_ratings_total' => 1200,
                'price_level' => 3,
                'distance_km' => 0.5,
                'is_open_now' => true,
                'category' => 'club',
                'maps_url' => 'https://maps.google.com/?q=Bloom+Bar+Accra',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('club', 'bloom'),
            ],
            [
                'id' => 'fallback_10',
                'name' => 'Front/Back',
                'address' => 'Osu, Accra',
                'latitude' => 5.5562,
                'longitude' => -0.1834,
                'rating' => 4.7,
                'user_ratings_total' => 280,
                'price_level' => 4,
                'distance_km' => 0.6,
                'is_open_now' => true,
                'category' => 'club',
                'maps_url' => 'https://maps.google.com/?q=Front+Back+Accra',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('club', 'frontback'),
            ],
            [
                'id' => 'fallback_11',
                'name' => 'Carbon Night Club',
                'address' => 'Airport Residential Area, Accra',
                'latitude' => 5.6015,
                'longitude' => -0.1822,
                'rating' => 4.4,
                'user_ratings_total' => 150,
                'price_level' => 5,
                'distance_km' => 1.4,
                'is_open_now' => true,
                'category' => 'club',
                'maps_url' => 'https://maps.google.com/?q=Carbon+Night+Club+Accra',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('club', 'carbon'),
            ],
            [
                'id' => 'fallback_12',
                'name' => 'Ace Tantra Night Club',
                'address' => 'Oxford Street, Osu, Accra',
                'latitude' => 5.5570,
                'longitude' => -0.1820,
                'rating' => 4.3,
                'user_ratings_total' => 410,
                'price_level' => 4,
                'distance_km' => 0.4,
                'is_open_now' => true,
                'category' => 'club',
                'maps_url' => 'https://maps.google.com/?q=Ace+Tantra+Accra',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('club', 'ace'),
            ],
            [
                'id' => 'fallback_13',
                'name' => 'The Alley',
                'address' => 'North Labone, Accra',
                'latitude' => 5.5720,
                'longitude' => -0.1710,
                'rating' => 4.5,
                'user_ratings_total' => 180,
                'price_level' => 3,
                'distance_km' => 2.2,
                'is_open_now' => true,
                'category' => 'lounge',
                'maps_url' => 'https://maps.google.com/?q=The+Alley+Accra',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('lounge', 'alley'),
            ],
            [
                'id' => 'fallback_3',
                'name' => 'Santoku',
                'address' => 'Villaggio Vista, North Airport Rd, Accra',
                'latitude' => 5.6025,
                'longitude' => -0.1837,
                'rating' => 4.7,
                'user_ratings_total' => 120,
                'price_level' => 4,
                'distance_km' => 1.5,
                'is_open_now' => true,
                'category' => 'restaurant',
                'maps_url' => 'https://maps.google.com/?q=Santoku+Accra',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('restaurant', 'santoku'),
            ],
            [
                'id' => 'fallback_4',
                'name' => 'Skybar 25',
                'address' => 'Alto Tower, Villaggio Vista, Accra',
                'latitude' => 5.6025,
                'longitude' => -0.1837,
                'rating' => 4.6,
                'user_ratings_total' => 210,
                'price_level' => 4,
                'distance_km' => 1.5,
                'is_open_now' => true,
                'category' => 'lounge',
                'maps_url' => 'https://maps.google.com/?q=Skybar+25+Accra',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('lounge', 'skybar'),
            ],
             [
                'id' => 'fallback_5',
                'name' => 'Kempinski Hotel Gold Coast City',
                'address' => 'Gamel Abdul Nasser Ave, Accra',
                'latitude' => 5.5517,
                'longitude' => -0.1923,
                'rating' => 4.8,
                'user_ratings_total' => 850,
                'price_level' => 5,
                'distance_km' => 4.2,
                'is_open_now' => true,
                'category' => 'hotel',
                'maps_url' => 'https://maps.google.com/?q=Kempinski+Accra',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('hotel', 'kempinski'),
            ],
            [
                'id' => 'fallback_6',
                'name' => 'Potbelly Shack',
                'address' => 'East Legon, Accra',
                'latitude' => 5.6356,
                'longitude' => -0.1601,
                'rating' => 4.2,
                'user_ratings_total' => 300,
                'price_level' => 2,
                'distance_km' => 1.8,
                'is_open_now' => true,
                'category' => 'restaurant',
                'maps_url' => 'https://maps.google.com/?q=Potbelly+Shack+Accra',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('restaurant', 'potbelly'),
            ],
            [
                 'id' => 'fallback_7',
                'name' => 'Game It Up',
                'address' => 'Accra Mall, Accra',
                'latitude' => 5.6200,
                'longitude' => -0.1700,
                'rating' => 4.3,
                'user_ratings_total' => 150,
                'price_level' => 2,
                'distance_km' => 2.0,
                'is_open_now' => true,
                'category' => 'arcade',
                'maps_url' => 'https://maps.google.com/?q=Accra+Mall',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('arcade', 'gameitup'),
            ],
            [
                'id' => 'fallback_8',
                'name' => 'The Apartment',
                'address' => 'Osu, Accra',
                'latitude' => 5.5500,
                'longitude' => -0.1800,
                'rating' => 4.1,
                'user_ratings_total' => 90,
                'price_level' => 3,
                'distance_km' => 3.5,
                'is_open_now' => true,
                'category' => 'lodging',
                'maps_url' => 'https://maps.google.com/?q=The+Apartment+Accra',
                'photo_reference' => null,
                'photo_url' => $this->getCategoryPlaceholderImage('lodging', 'apartment'),
            ]
        ];

        // Filter: match category exactly
        $filtered = array_filter($venues, function($v) use ($category) {
            return $v['category'] === $category;
        });

        // If no items match, return nothing (UI has empty state)
        return array_values($filtered);
    }
}
