<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check for ticket-related tables
$tables = DB::select("SHOW TABLES LIKE '%ticket%'");
echo "Ticket-related tables:\n";
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    echo "\n=== $tableName ===\n";
    $columns = DB::select("DESCRIBE $tableName");
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
}
