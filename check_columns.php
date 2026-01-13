<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = DB::select("DESCRIBE events");
echo "Events table columns:\n";
foreach ($columns as $col) {
    echo "  - {$col->Field} ({$col->Type})\n";
}
