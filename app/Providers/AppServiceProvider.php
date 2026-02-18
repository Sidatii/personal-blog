<?php

namespace App\Providers;

use App\Events\PostPublished;
use App\Listeners\SendNewsletterOnPostPublished;
use App\Models\Setting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Use Tailwind pagination views
        Paginator::defaultView('vendor.pagination.tailwind');

        // Share portfolio settings with all views (for footer, etc.)
        View::composer(['components.footer', 'layouts.app'], function ($view) {
            $raw = Setting::where('key', 'like', 'about.%')->pluck('value', 'key');

            $view->with('portfolioSettings', [
                'github_url' => $raw['about.github_url'] ?? config('portfolio.social.github', ''),
                'twitter_url' => $raw['about.twitter_url'] ?? config('portfolio.social.twitter', ''),
                'linkedin_url' => $raw['about.linkedin_url'] ?? config('portfolio.social.linkedin', ''),
            ]);
        });

        // Configure rate limiting for comments
        RateLimiter::for('comments', function ($request) {
            return Limit::perHour(config('comments.throttle_per_hour', 5))
                ->by($request->ip());
        });

        // Configure rate limiting for reactions
        RateLimiter::for('reactions', function ($request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Newsletter dispatch on new post published
        Event::listen(PostPublished::class, SendNewsletterOnPostPublished::class);
    }
}
