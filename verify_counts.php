<?php
use App\Models\User;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventTicket;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Users: " . User::count() . "\n";
echo "Companies: " . Company::count() . "\n";
echo "Events: " . Event::count() . "\n";
echo "Tickets: " . EventTicket::count() . "\n";
