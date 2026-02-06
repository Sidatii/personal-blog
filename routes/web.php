<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Blog Routes
Route::get('/blog', [BlogController::class, 'index'])->name('posts.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('posts.show');

// Feed Routes (RSS/Atom)
Route::feeds();

// Sitemap Route
Route::get('/sitemap.xml', function () {
    $sitemapPath = public_path('sitemap.xml');

    // If static sitemap exists, serve it
    if (file_exists($sitemapPath)) {
        return response(file_get_contents($sitemapPath))
            ->header('Content-Type', 'application/xml');
    }

    // Otherwise, generate on demand
    $sitemap = \Spatie\Sitemap\Sitemap::create();

    // Add homepage
    $sitemap->add(
        \Spatie\Sitemap\Tags\Url::create('/')
            ->setLastModificationDate(now())
            ->setChangeFrequency(\Spatie\Sitemap\Tags\Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(1.0)
    );

    // Add blog index
    $sitemap->add(
        \Spatie\Sitemap\Tags\Url::create('/blog')
            ->setLastModificationDate(now())
            ->setChangeFrequency(\Spatie\Sitemap\Tags\Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.9)
    );

    // Add all published posts
    $posts = \App\Models\Post::published()
        ->orderBy('published_at', 'desc')
        ->get();

    foreach ($posts as $index => $post) {
        $priority = max(0.5, 0.8 - ($index / max($posts->count(), 1)) * 0.3);

        $sitemap->add(
            \Spatie\Sitemap\Tags\Url::create(route('posts.show', $post))
                ->setLastModificationDate($post->updated_at)
                ->setChangeFrequency(\Spatie\Sitemap\Tags\Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority($priority)
        );
    }

    return response($sitemap->toXml())
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/health', function () {
    $checks = [
        'database' => false,
        'cache' => false,
        'queue' => false,
    ];

    // Check database connection
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $checks['database'] = true;
    } catch (\Throwable $e) {
        // Database unavailable
    }

    // Check cache driver
    try {
        \Illuminate\Support\Facades\Cache::store()->put('health-check', true, 10);
        \Illuminate\Support\Facades\Cache::store()->forget('health-check');
        $checks['cache'] = true;
    } catch (\Throwable $e) {
        // Cache unavailable
    }

    // Check queue table accessible
    try {
        \Illuminate\Support\Facades\DB::table('jobs')->count();
        $checks['queue'] = true;
    } catch (\Throwable $e) {
        // Queue table unavailable
    }

    $healthy = $checks['database'] && $checks['cache'] && $checks['queue'];

    return response()->json([
        'status' => $healthy ? 'ok' : 'degraded',
        'checks' => $checks,
        'timestamp' => now()->toISOString(),
    ], $healthy ? 200 : 503);
})->name('health');
