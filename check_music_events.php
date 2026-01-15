<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Event;
use App\Models\Category;

$categorySlug = 'music';
$category = Category::where('slug', $categorySlug)->first();

echo "Category: " . ($category ? $category->name : "Not found") . "\n";

$events = Event::approved()
    ->inCategory($categorySlug)
    ->upcoming()
    ->get();

echo "Found " . $events->count() . " events in music category.\n";
foreach ($events as $event) {
    echo "- " . $event->title . " (Status: " . $event->status . ")\n";
}
