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
        \App\Models\Poll::class => \App\Policies\PollPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
