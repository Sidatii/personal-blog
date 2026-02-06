<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Eloquent\PostRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\TagRepository;
use App\Services\Content\MarkdownParser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(TagRepository::class, function ($app) {
            return new TagRepository(new \App\Models\Tag());
        });

        // Service bindings
        $this->app->bind(MarkdownParser::class, function ($app) {
            return new MarkdownParser();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
