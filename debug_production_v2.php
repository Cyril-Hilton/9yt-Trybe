<?php
// Load Laravel Bootstrap
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- PRODUCTION DEBUG V2 ---\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// 1. Check Slider 2
echo "[Slider Assets]\n";
$slide1 = public_path('ui/sliders/slide1.mp4');
$slide2 = public_path('ui/sliders/slide2.mp4');

if (file_exists($slide1)) {
    echo "✅ Slide 1: FOUND (" . round(filesize($slide1)/1024/1024, 2) . " MB)\n";
} else {
    echo "❌ Slide 1: MISSING\n";
}

if (file_exists($slide2)) {
    echo "✅ Slide 2: FOUND (" . round(filesize($slide2)/1024/1024, 2) . " MB)\n";
} else {
    echo "❌ Slide 2: MISSING (This explains why only 1 slider loads)\n";
}
echo "\n";

// 2. Test Venues Backend Logic
echo "[Venues API Test]\n";
try {
    $controller = new \App\Http\Controllers\NearbyVenuesController();
    $request = \Illuminate\Http\Request::create('/api/nearby-venues', 'GET', [
        'lat' => 5.6037,
        'lng' => -0.1870,
        'category' => 'club',
        'radius' => 50000
    ]);
    
    // Manually force the controller to run logic
    $response = $controller->searchNearby($request);
    $data = $response->getData(true);
    
    echo "Provider: " . ($data['provider'] ?? 'unknown') . "\n";
    echo "Venues Returned: " . count($data['places'] ?? []) . "\n";
    
    if (count($data['places'] ?? []) > 0) {
        $first = $data['places'][0];
        echo "Example Venue: " . ($first['name'] ?? 'Unknown') . "\n";
        echo "✅ Backend Logic is WORKING. If venues don't show, it's a browser/JS issue.\n";
    } else {
        echo "❌ Backend returned 0 venues. Fallback logic failed.\n";
    }

} catch (\Exception $e) {
    echo "❌ Exception testing API: " . $e->getMessage() . "\n";
    if (str_contains($e->getMessage(), 'cURL')) {
        echo "   (This cURL error is expected if external API is failing, but fallback should have caught it if implemented correctly)\n";
    }
}

echo "\n--- END DEBUG ---\n";
