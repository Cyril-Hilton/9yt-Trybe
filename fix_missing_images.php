<?php
/**
 * 9yt !Trybe Image Repair Script
 * Purpose: Update database records pointing to non-existent local files with Unsplash URLs.
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

// 1. Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Image Repair Started ---\n";

$unsplashPool = [
    'concert' => [
        'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=1200&q=80',
        'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=1200&q=80',
        'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=1200&q=80'
    ],
    'conference' => [
        'https://images.unsplash.com/photo-1540575861501-7ad060e29ad3?w=1200&q=80',
        'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=1200&q=80',
        'https://images.unsplash.com/photo-1475721027187-402ec7575763?w=1200&q=80'
    ],
    'workshop' => [
        'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=1200&q=80',
        'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&q=80'
    ],
    'party' => [
        'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=1200&q=80',
        'https://images.unsplash.com/photo-1514525253344-f81f3f746a15?w=1200&q=80'
    ],
    'default' => [
        'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=1200&q=80',
        'https://images.unsplash.com/photo-1496333039240-42118e26bc7d?w=1200&q=80'
    ]
];

function getRandomImage($type, $pool) {
    $images = isset($pool[$type]) ? $pool[$type] : $pool['default'];
    return $images[array_rand($images)];
}

// 2. Fix Events Banner Images
$events = DB::table('events')->get();
$updatedCount = 0;
$publicPath = storage_path('app/public/');

foreach ($events as $event) {
    $banner = $event->banner_image;
    
    if (!$banner) continue;

    $isUrl = str_starts_with($banner, 'http');
    $isLocalMissing = !$isUrl && !file_exists($publicPath . $banner);

    if ($isLocalMissing) {
        $newImage = getRandomImage($event->event_type, $unsplashPool);
        
        DB::table('events')->where('id', $event->id)->update([
            'banner_image' => $newImage,
            'updated_at' => now()
        ]);
        
        echo "[Event ID: {$event->id}] Updated missing local banner '{$banner}' to Unsplash URL.\n";
        $updatedCount++;
    }
}

echo "\nTotal events updated: $updatedCount\n";

// 3. Fix Secondary Image Tables
$imageTables = ['event_images', 'gallery_images'];
foreach ($imageTables as $tableName) {
    if (!Schema::hasTable($tableName)) {
        echo "Table '{$tableName}' does not exist, skipping.\n";
        continue;
    }
    
    $images = DB::table($tableName)->get();
    $imgUpdatedCount = 0;

    foreach ($images as $img) {
        $path = $img->image_path; // Corrected: Both tables use 'image_path'
        if (!$path) continue;

        $isUrl = str_starts_with($path, 'http');
        $isLocalMissing = !$isUrl && !file_exists($publicPath . $path);

        if ($isLocalMissing) {
            // Find parent event to get type if possible (mostly for event_images)
            $type = 'default';
            if (isset($img->event_id)) {
                $event = DB::table('events')->where('id', $img->event_id)->first();
                $type = $event ? $event->event_type : 'default';
            }
            
            $newImage = getRandomImage($type, $unsplashPool);
            
            DB::table($tableName)->where('id', $img->id)->update([
                'image_path' => $newImage,
                'updated_at' => now()
            ]);
            
            echo "[Table: {$tableName}, ID: {$img->id}] Updated missing '{$path}' to URL.\n";
            $imgUpdatedCount++;
        }
    }
    echo "Total {$tableName} updated: $imgUpdatedCount\n";
}

// 4. Fix News Articles
if (Schema::hasTable('news_articles')) {
    $articles = DB::table('news_articles')->get();
    $artUpdatedCount = 0;

    foreach ($articles as $art) {
        if ($art->image_path && !str_contains($art->image_path, '/') && !str_starts_with($art->image_path, 'http')) {
            DB::table('news_articles')->where('id', $art->id)->update([
                'image_path' => getRandomImage('default', $unsplashPool),
                'updated_at' => now()
            ]);
            $artUpdatedCount++;
        }
    }
    echo "Total news_articles updated: $artUpdatedCount\n";
}

echo "\n--- Repair Complete ---\n";
