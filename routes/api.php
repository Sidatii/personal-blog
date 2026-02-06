<?php

use App\Http\Controllers\ThemeController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Theme endpoints for Rose Pine dark/light mode toggle
|
*/

Route::post('/theme/toggle', [ThemeController::class, 'toggle'])
    ->name('theme.toggle');

Route::get('/theme/status', [ThemeController::class, 'status'])
    ->name('theme.status');

Route::post('/webhooks/github', [WebhookController::class, 'handle'])
    ->name('webhooks.github');
