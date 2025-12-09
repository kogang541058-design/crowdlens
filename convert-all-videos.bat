@echo off
echo ============================================
echo Converting All Videos to H.264 Format
echo ============================================
echo.

REM Check if FFmpeg is installed
where ffmpeg >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: FFmpeg is not installed or not in PATH
    echo.
    echo Please install FFmpeg first:
    echo 1. Download from: https://www.gyan.dev/ffmpeg/builds/ffmpeg-release-essentials.zip
    echo 2. Extract to C:\ffmpeg
    echo 3. Add C:\ffmpeg\bin to your system PATH
    echo 4. Restart this script
    echo.
    pause
    exit /b 1
)

echo FFmpeg found! Starting conversion...
echo.

cd /d "%~dp0storage\app\public\reports\videos"

set count=0
set success=0
set failed=0

for %%F in (*.mp4) do (
    set /a count+=1
    echo [!count!] Converting: %%F
    
    REM Create temp output file
    set "output=temp_%%F"
    
    REM Convert video
    ffmpeg -i "%%F" -c:v libx264 -preset fast -crf 23 -profile:v baseline -level 3.0 -pix_fmt yuv420p -c:a aac -b:a 128k -movflags +faststart -y "!output!" >nul 2>&1
    
    if exist "!output!" (
        REM Replace original with converted
        del "%%F"
        ren "!output!" "%%F"
        echo     [OK] Converted successfully
        set /a success+=1
    ) else (
        echo     [FAILED] Conversion failed
        set /a failed+=1
    )
    echo.
)

echo ============================================
echo Conversion Complete!
echo ============================================
echo Total videos: %count%
echo Successfully converted: %success%
echo Failed: %failed%
echo.
echo All videos are now in browser-compatible H.264 format!
echo.
pause
