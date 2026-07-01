<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\BarangayAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Models\Report;
use App\Jobs\ProcessPrediction;

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
    
    // User settings routes
    Route::get('/settings', [HomeController::class, 'settings'])->name('settings');
    Route::put('/settings/profile', [HomeController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [HomeController::class, 'updatePassword'])->name('settings.password');

    // User, Admin, and Barangay profile and password update
    Route::patch('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');

    Route::get('/user/notifications', [HomeController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/user/notifications/mark-read', [HomeController::class, 'markAllRead'])->name('notifications.mark-read');
});

Route::middleware('auth:admin,barangay')->group(function () {
    
    Route::get('/staff-settings', [ProfileController::class, 'staffSettings'])->name('staff.settings');
    
    Route::post('/reports/{report}/run-ai', 
        [ReportController::class, 'runPrediction'])
        ->name('reports.run-ai');
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
        Route::get('/reports/barangay-updates', [AdminAuthController::class, 'pollBarangayUpdates'])->name('reports.barangay-updates');
        Route::get('/notifications', [AdminAuthController::class, 'getNotifications'])->name('notifications.get');
        Route::post('/notifications/{id}/read', [AdminAuthController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [AdminAuthController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
        Route::patch('/reports/{report}/verify', [AdminAuthController::class, 'verifyReport'])->name('reports.verify');
        Route::post('/reports/{report}/respond', [AdminAuthController::class, 'respondToReport'])->name('reports.respond');
        Route::post('/reports/{report}/mark-solved', [AdminAuthController::class, 'markSolved'])->name('reports.markSolved');

        Route::delete('/reports/{report}', [AdminAuthController::class, 'deleteReport'])->name('reports.delete');
        Route::get('/users', [AdminAuthController::class, 'users'])->name('users');
        Route::post('/users', [AdminAuthController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}', [AdminAuthController::class, 'updateUser'])->name('users.update');
        Route::patch('/users/{user}/block', [AdminAuthController::class, 'blockUser'])->name('users.block');
        Route::get('/barangay', [AdminAuthController::class, 'barangay'])->name('barangay');
        Route::post('/barangay', [AdminAuthController::class, 'storeBarangay'])->name('barangay.store');
        Route::put('/barangay/{id}', [AdminAuthController::class, 'updateBarangay'])->name('barangay.update');
        Route::delete('/barangay/{id}', [AdminAuthController::class, 'deleteBarangay'])->name('barangay.delete');
        Route::get('/solved', [AdminAuthController::class, 'solved'])->name('solved');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Route::post('/reports/{report}/run-ai', function (Report $report) {
        //     ProcessPrediction::dispatch($report);
        // })->name('reports.run-ai'); 
        
    });

});

// Barangay routes
Route::prefix('barangay')->name('barangay.')->group(function () {
    // Protected barangay routes
    Route::middleware(['auth:barangay'])->group(function () {
        Route::get('/dashboard', [BarangayAuthController::class, 'dashboard'])
            ->name('dashboard');
        Route::get('/reports', [BarangayAuthController::class, 'reports'])
            ->name('reports');
        Route::get('/reports/poll', [BarangayAuthController::class, 'pollReports'])
            ->name('reports.poll');
        Route::get('/notifications', [BarangayAuthController::class, 'getNotifications'])
            ->name('notifications.get');
        Route::patch('/reports/{report}/action', [BarangayAuthController::class, 'updateActionStatus'])
            ->name('reports.action');
        Route::post('/logout', [BarangayAuthController::class, 'logout'])
            ->name('logout');
    });
});


