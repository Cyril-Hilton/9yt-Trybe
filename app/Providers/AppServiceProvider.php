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
        Gate::policy(Conference::class, ConferencePolicy::class);
        Article::observe(ArticleObserver::class);
        Event::observe(EventObserver::class);
        Poll::observe(PollObserver::class);
        Survey::observe(SurveyObserver::class);
        Conference::observe(ConferenceObserver::class);
        ShopProduct::observe(ShopProductObserver::class);

        if ($this->app->environment('production') || str_contains(request()->getHost(), '9yttrybe.com')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
