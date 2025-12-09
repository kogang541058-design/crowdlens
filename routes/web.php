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

// Video test page (accessible to admin only for diagnostics)
Route::get('/test-video', function () {
    return view('test-video');
})->middleware('auth')->name('test.video');

// Real-time system test page
Route::get('/test-realtime', function () {
    return view('test-realtime');
})->middleware('auth')->name('test.realtime');

// Report routes
Route::middleware('auth')->group(function () {
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/user/check-responses', [HomeController::class, 'checkResponses'])->name('user.check-responses');
});

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Protected admin routes
    Route::middleware(['auth:admin', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/map', [AdminAuthController::class, 'map'])->name('map');
        Route::get('/reports', [AdminAuthController::class, 'reports'])->name('reports');
        Route::get('/reports/count', [AdminAuthController::class, 'getReportCount'])->name('reports.count');
        Route::get('/reports/new', [AdminAuthController::class, 'getNewReports'])->name('reports.new');
        Route::get('/reports/check-new', [AdminAuthController::class, 'checkNewReports'])->name('reports.check-new');
        Route::get('/notifications', [AdminAuthController::class, 'getNotifications'])->name('notifications.get');
        Route::post('/notifications/{id}/read', [AdminAuthController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [AdminAuthController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
        Route::patch('/reports/{report}/verify', [AdminAuthController::class, 'verifyReport'])->name('reports.verify');
        Route::post('/reports/{report}/respond', [AdminAuthController::class, 'respondToReport'])->name('reports.respond');
        Route::post('/reports/{report}/mark-solved', [AdminAuthController::class, 'markSolved'])->name('reports.markSolved');

        Route::delete('/reports/{report}', [AdminAuthController::class, 'deleteReport'])->name('reports.delete');
        Route::get('/users', [AdminAuthController::class, 'users'])->name('users');
        Route::get('/solved', [AdminAuthController::class, 'solved'])->name('solved');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});
