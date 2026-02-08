<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectController;
use Illuminate\Support\Facades\Route;

// Guest routes (not authenticated)
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login.post');
});

// Authenticated admin routes
Route::middleware('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('admin.logout');

    // Post management
    Route::resource('posts', PostController::class)->names([
        'index' => 'admin.posts.index',
        'create' => 'admin.posts.create',
        'store' => 'admin.posts.store',
        'show' => 'admin.posts.show',
        'edit' => 'admin.posts.edit',
        'update' => 'admin.posts.update',
        'destroy' => 'admin.posts.destroy',
    ]);

    // Project management
    Route::resource('projects', ProjectController::class)->except(['show'])->names([
        'index' => 'admin.projects.index',
        'create' => 'admin.projects.create',
        'store' => 'admin.projects.store',
        'edit' => 'admin.projects.edit',
        'update' => 'admin.projects.update',
        'destroy' => 'admin.projects.destroy',
    ]);

    // Contact submissions management
    Route::prefix('contacts')->name('admin.contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/{contact}', [ContactController::class, 'show'])->name('show');
        Route::post('/{contact}/mark-as-read', [ContactController::class, 'markAsRead'])->name('mark-as-read');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
    });

    // Activity log
    Route::get('/activity', [ActivityController::class, 'index'])->name('admin.activity.index');
});
