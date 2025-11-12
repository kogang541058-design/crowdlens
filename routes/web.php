<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;

use App\Http\Controllers\ReportController;

// Public home with Login & Register forms
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected dashboard
Route::get('/dashboard', [HomeController::class, 'dashboard'])
    ->middleware('auth')
    ->name('dashboard');

// Report routes
Route::middleware('auth')->group(function () {
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Protected admin routes
    Route::middleware(['auth:admin', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/map', [AdminAuthController::class, 'map'])->name('map');
        Route::get('/reports', [AdminAuthController::class, 'reports'])->name('reports');
        Route::patch('/reports/{report}/verify', [AdminAuthController::class, 'verifyReport'])->name('reports.verify');
        Route::post('/reports/{report}/mark-solved', [AdminAuthController::class, 'markSolved'])->name('reports.markSolved');
        Route::post('/reports/{report}/mark-unsolved', [AdminAuthController::class, 'markUnsolved'])->name('reports.markUnsolved');
        Route::delete('/reports/{report}', [AdminAuthController::class, 'deleteReport'])->name('reports.delete');
        Route::get('/users', [AdminAuthController::class, 'users'])->name('users');
        Route::get('/solved', [AdminAuthController::class, 'solved'])->name('solved');
        Route::get('/unsolved', [AdminAuthController::class, 'unsolved'])->name('unsolved');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});
