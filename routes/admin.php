<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\LogoutController;
use Illuminate\Support\Facades\Route;

// Guest routes (not authenticated)
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login.post');
});

// Authenticated admin routes
Route::middleware('admin')->group(function () {
    Route::get('/', fn() => view('admin.dashboard'))->name('admin.dashboard');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('admin.logout');
});
