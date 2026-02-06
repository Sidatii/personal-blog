<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Blog Routes
Route::get('/blog', [BlogController::class, 'index'])->name('posts.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('posts.show');

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
