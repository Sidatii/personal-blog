<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\CertificationController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SettingsController;
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

    // Project management
    Route::resource('projects', ProjectController::class)->except(['show'])->names([
        'index' => 'admin.projects.index',
        'create' => 'admin.projects.create',
        'store' => 'admin.projects.store',
        'edit' => 'admin.projects.edit',
        'update' => 'admin.projects.update',
        'destroy' => 'admin.projects.destroy',
    ]);

    // Certification management
    Route::resource('certifications', CertificationController::class)->except(['show'])->names([
        'index'   => 'admin.certifications.index',
        'create'  => 'admin.certifications.create',
        'store'   => 'admin.certifications.store',
        'edit'    => 'admin.certifications.edit',
        'update'  => 'admin.certifications.update',
        'destroy' => 'admin.certifications.destroy',
    ]);

    // Contact submissions management
    Route::prefix('contacts')->name('admin.contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/{contact}', [ContactController::class, 'show'])->name('show');
        Route::post('/{contact}/mark-as-read', [ContactController::class, 'markAsRead'])->name('mark-as-read');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
    });

    // Comment moderation
    Route::prefix('comments')->name('admin.comments.')->group(function () {
        Route::get('/', [CommentController::class, 'index'])->name('index');
        Route::post('/{comment}/approve', [CommentController::class, 'approve'])->name('approve');
        Route::post('/{comment}/spam', [CommentController::class, 'spam'])->name('spam');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');
    });

    // Activity log
    Route::get('/activity', [ActivityController::class, 'index'])->name('admin.activity.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');

    // About page editor
    Route::get('about', [AboutController::class, 'index'])->name('admin.about.index');
    Route::put('about', [AboutController::class, 'update'])->name('admin.about.update');

    // Image uploads
    Route::post('images', [ImageController::class, 'store'])->name('admin.images.store');
    Route::delete('images', [ImageController::class, 'destroy'])->name('admin.images.destroy');

    // Blog post image management
    Route::prefix('posts')->name('admin.posts.')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::get('/{id}', [PostController::class, 'show'])->name('show');
        Route::post('/{id}/images', [PostController::class, 'uploadImage'])->name('images.store');
    });
});
