<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Event Search API ===\n\n";

try {
    // Test the exact query used by searchEvents method
    $events = App\Models\Event::where('is_external', false)
        ->with(['company:id,company_name', 'tickets' => function($q) {
            $q->where('is_active', true);
        }])
        ->orderBy('start_date', 'desc')
        ->get(['id', 'title', 'company_id', 'start_date']);

    echo "✓ Query successful! Found " . $events->count() . " events\n\n";

    foreach ($events->take(3) as $event) {
        echo "Event: {$event->title}\n";
        echo "  Company: " . ($event->company ? $event->company->company_name : 'N/A') . "\n";
        echo "  Tickets: " . $event->tickets->count() . "\n";
        
        if ($event->tickets->count() > 0) {
            foreach ($event->tickets as $ticket) {
                echo "    - {$ticket->name}: GHS {$ticket->price}\n";
            }
        }
        echo "\n";
    }

    echo "✓ API should work correctly!\n";

} catch (\Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
