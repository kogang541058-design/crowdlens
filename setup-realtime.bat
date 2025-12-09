@echo off
echo ========================================
echo CrowdLens Real-Time Notifications Setup
echo ========================================
echo.

echo [1/5] Clearing Laravel caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo ✓ Caches cleared

echo.
echo [2/5] Checking Pusher package...
php artisan package:discover
echo ✓ Packages discovered

echo.
echo [3/5] Verifying .env configuration...
findstr /C:"PUSHER_APP_KEY" .env >nul
if %errorlevel% equ 0 (
    echo ✓ Pusher credentials found in .env
) else (
    echo ✗ WARNING: Pusher credentials not found in .env
    echo.
    echo Please add these lines to your .env file:
    echo PUSHER_APP_ID=your_app_id
    echo PUSHER_APP_KEY=your_app_key
    echo PUSHER_APP_SECRET=your_app_secret
    echo PUSHER_APP_CLUSTER=mt1
    echo.
    echo Get credentials from: https://dashboard.pusher.com/
    echo.
    pause
)

echo.
echo [4/5] Checking Laravel Echo installation...
if exist "node_modules\laravel-echo" (
    echo ✓ Laravel Echo installed
) else (
    echo ⚠ Laravel Echo not found, installing...
    call npm install laravel-echo pusher-js
)

echo.
echo [5/5] Building frontend assets...
echo This may take a minute...
call npm run build
if %errorlevel% equ 0 (
    echo ✓ Assets built successfully
) else (
    echo ⚠ Build failed - you may need to run: npm run dev
)

echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Make sure XAMPP Apache is running
echo 2. Open admin page: http://localhost/crowdlens/public/admin/reports
echo 3. Open browser console (F12) to see connection logs
echo 4. Submit a test report from user dashboard
echo 5. Watch the magic happen! ✨
echo.
echo For detailed instructions, see REALTIME_NOTIFICATION_SETUP.md
echo.
pause
