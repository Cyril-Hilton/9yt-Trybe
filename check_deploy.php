<?php
echo "--- DEPLOYMENT CHECK ---\n";
echo "Current Git Commit: " . trim(shell_exec('git rev-parse --short HEAD')) . "\n";
echo "Current Time: " . date('Y-m-d H:i:s') . "\n\n";

// Check 1: HTTPS Force
$file = 'app/Providers/AppServiceProvider.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    echo "[AppServiceProvider]\n";
    if (strpos($content, "request()->getHost(), '9yttrybe.com'") !== false) {
        echo "✅ HTTPS Logic: UPDATED (Domain check added)\n";
    } elseif (strpos($content, "forceScheme") !== false) {
        echo "⚠️ HTTPS Logic: PARTIAL (Old forceScheme check)\n";
    } else {
        echo "❌ HTTPS Logic: MISSING\n";
    }
} else {
    echo "❌ AppServiceProvider not found.\n";
}
echo "\n";

// Check 2: Venues Fallback & Cache Bust
$file2 = 'app/Http/Controllers/NearbyVenuesController.php';
if (file_exists($file2)) {
    $content = file_get_contents($file2);
    echo "[NearbyVenuesController]\n";
    
    if (strpos($content, 'nearby:v2:') !== false) {
        echo "✅ Cache Key: UPDATED (v2)\n";
    } else {
        echo "❌ Cache Key: LEGACY (May serve cached errors)\n";
    }

    if (strpos($content, 'getFallbackVenues') !== false) {
        echo "✅ Fallback Method: PRESENT\n";
    } else {
        echo "❌ Fallback Method: MISSING\n";
    }
} else {
    echo "❌ NearbyVenuesController not found.\n";
}
echo "\n";

// Check 3: Assets
echo "[Assets]\n";
echo "Slide 1 Video: " . (file_exists('public/ui/sliders/slide1.mp4') ? "✅ Found" : "❌ MISSING") . "\n";

echo "\n--- END CHECK ---\n";
