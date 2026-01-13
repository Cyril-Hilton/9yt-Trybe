<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule: Send congratulatory emails for completed events
// Runs daily at 10:00 AM Ghana time
Schedule::command('events:send-congratulatory-emails')
        ->dailyAt('10:00')
        ->timezone('Africa/Accra')
        ->emailOutputOnFailure(config('mail.from.address'));

// Schedule: Refresh news cache to keep headlines fresh
Schedule::command('news:refresh-cache')
        ->hourly()
        ->timezone('Africa/Accra');
