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

// Schedule: Send due scheduled SMS campaigns (runs every minute)
Schedule::command('sms:send-scheduled-campaigns')
        ->everyMinute()
        ->timezone(config('app.timezone'))
        ->withoutOverlapping();

// Schedule: Daily AI SEO refresh (events + polls)
Schedule::command('seo:refresh --only-missing')
        ->dailyAt('02:30')
        ->timezone('Africa/Accra')
        ->withoutOverlapping();

// Schedule: Daily AI blog generation (drafts by default)
Schedule::command('blog:generate-ai')
        ->dailyAt('03:10')
        ->timezone('Africa/Accra')
        ->withoutOverlapping();

// Schedule: AI enrichment (tags + FAQs)
Schedule::command('ai:enrich-content --only-missing')
        ->dailyAt('02:50')
        ->timezone('Africa/Accra')
        ->withoutOverlapping();

// Schedule: AI social snippets for upcoming events
Schedule::command('ai:growth-social-snippets')
        ->dailyAt('04:00')
        ->timezone('Africa/Accra')
        ->withoutOverlapping();

// Schedule: Weekly AI growth digest + organizer tips
Schedule::command('ai:growth-digest')
        ->weeklyOn((int) config('services.ai.growth.digest_day', 1), '04:20')
        ->timezone('Africa/Accra')
        ->withoutOverlapping();

Schedule::command('ai:growth-organizer-tips')
        ->weeklyOn((int) config('services.ai.growth.digest_day', 1), '04:40')
        ->timezone('Africa/Accra')
        ->withoutOverlapping();
