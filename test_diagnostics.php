<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DIAGNOSTICS ===\n\n";

// 1. Test Database Connection
echo "1. Testing Database Connection...\n";
try {
    DB::connection()->getPdo();
    echo "   ✓ Database connection successful\n";
    echo "   Database: " . DB::connection()->getDatabaseName() . "\n\n";
} catch (\Exception $e) {
    echo "   ✗ Database connection FAILED: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 2. Check if Events table exists and has data
echo "2. Checking Events...\n";
try {
    $eventCount = DB::table('events')->count();
    echo "   Total events in database: $eventCount\n";
    
    if ($eventCount > 0) {
        $nonExternalCount = DB::table('events')->where('is_external', false)->count();
        echo "   Non-external events: $nonExternalCount\n";
        
        // Show first 3 events
        $events = DB::table('events')
            ->where('is_external', false)
            ->limit(3)
            ->get(['id', 'title', 'start_date']);
        
        echo "\n   Sample events:\n";
        foreach ($events as $event) {
            echo "   - [{$event->id}] {$event->title} ({$event->start_date})\n";
        }
    } else {
        echo "   ⚠ No events found in database!\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ✗ Error checking events: " . $e->getMessage() . "\n\n";
}

// 3. Test PhpSpreadsheet
echo "3. Testing PhpSpreadsheet...\n";
try {
    if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
        echo "   ✓ PhpSpreadsheet is installed\n";
    } else {
        echo "   ✗ PhpSpreadsheet NOT found\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Test Event Search API simulation
echo "4. Simulating Event Search API...\n";
try {
    $events = DB::table('events')
        ->where('is_external', false)
        ->orderBy('start_date', 'desc')
        ->get(['id', 'title', 'company_id', 'ticket_price_general', 'ticket_price_vip', 'start_date']);
    
    echo "   Query successful, found " . $events->count() . " events\n";
    
    if ($events->count() > 0) {
        echo "   ✓ API should work correctly\n";
    } else {
        echo "   ⚠ No events to return\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Query failed: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. Check Paystack Configuration
echo "5. Checking Paystack Configuration...\n";
$paystackKey = config('paystack.secretKey');
if (!empty($paystackKey)) {
    echo "   ✓ Paystack secret key is set\n";
} else {
    echo "   ✗ Paystack secret key is missing\n";
}
echo "\n";

echo "=== END DIAGNOSTICS ===\n";
