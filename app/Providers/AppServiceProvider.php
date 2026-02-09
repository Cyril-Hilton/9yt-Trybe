<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Conference;
use App\Policies\ConferencePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use App\Models\Article;
use App\Observers\ArticleObserver;
use App\Models\Event;
use App\Observers\EventObserver;
use App\Models\Poll;
use App\Observers\PollObserver;
use App\Models\Survey;
use App\Observers\SurveyObserver;
use App\Observers\ConferenceObserver;
use App\Models\ShopProduct;
use App\Observers\ShopProductObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // SSL Bypass for local development (Fix cURL error 60)
        if ($this->app->environment('local')) {
            \Illuminate\Support\Facades\Http::macro('local', function () {
                return \Illuminate\Support\Facades\Http::withoutVerifying();
            });
            
            // Alternatively, for all requests in local:
            // This is more aggressive but fixes all third party packages using the Http facade
            // However, macros don't override the base 'get', 'post' etc. 
            // So I will recommend the user to use 'Http::withoutVerifying()' in services 
            // OR I can set it in Guzzle's global defaults if I really wanted to.
            // For now, I'll modify the specific services.
        }
        
        // Observers
        Article::observe(ArticleObserver::class);
        Event::observe(EventObserver::class);
        Poll::observe(PollObserver::class);
        Survey::observe(SurveyObserver::class);
        Conference::observe(ConferenceObserver::class);
        ShopProduct::observe(ShopProductObserver::class);

        if ($this->app->environment('production') || str_contains(request()->getHost(), '9yttrybe.com')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
        }
    }
}
