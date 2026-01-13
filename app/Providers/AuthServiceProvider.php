<?php

namespace App\Providers;

use App\Models\Conference;
use App\Models\Event;
use App\Policies\ConferencePolicy;
use App\Policies\EventPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Conference::class => ConferencePolicy::class,
        Event::class => EventPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
