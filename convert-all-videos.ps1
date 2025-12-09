# Convert All Videos to H.264 Format
# PowerShell version

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "Converting All Videos to H.264 Format" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Check if FFmpeg is installed
$ffmpegPath = $null

# Try common locations
$possiblePaths = @(
    "C:\ffmpeg\bin\ffmpeg.exe",
    "C:\Program Files\ffmpeg\bin\ffmpeg.exe",
    "C:\Program Files (x86)\ffmpeg\bin\ffmpeg.exe"
)

foreach ($path in $possiblePaths) {
    if (Test-Path $path) {
        $ffmpegPath = $path
        break
    }
}

# Check if in PATH
if (-not $ffmpegPath) {
    $pathFFmpeg = Get-Command ffmpeg -ErrorAction SilentlyContinue
    if ($pathFFmpeg) {
        $ffmpegPath = $pathFFmpeg.Source
    }
}

if (-not $ffmpegPath) {
    Write-Host "ERROR: FFmpeg is not installed or not in PATH" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please install FFmpeg first:" -ForegroundColor Yellow
    Write-Host "1. Download from: https://www.gyan.dev/ffmpeg/builds/ffmpeg-release-essentials.zip"
    Write-Host "2. Extract to C:\ffmpeg"
    Write-Host "3. Add C:\ffmpeg\bin to your system PATH"
    Write-Host "4. Restart PowerShell and run this script again"
    Write-Host ""
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host "FFmpeg found at: $ffmpegPath" -ForegroundColor Green
Write-Host "Starting conversion..." -ForegroundColor Green
Write-Host ""

# Navigate to videos directory
$videosPath = Join-Path $PSScriptRoot "storage\app\public\reports\videos"
if (-not (Test-Path $videosPath)) {
    Write-Host "ERROR: Videos directory not found: $videosPath" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

Set-Location $videosPath

# Get all MP4 files
$videos = Get-ChildItem -Filter "*.mp4"
$count = 0
$success = 0
$failed = 0

foreach ($video in $videos) {
    $count++
    Write-Host "[$count] Converting: $($video.Name)" -ForegroundColor Cyan
    
    $tempOutput = "temp_$($video.Name)"
    
    # Convert video
    $arguments = @(
        "-i", "`"$($video.FullName)`"",
        "-c:v", "libx264",
        "-preset", "fast",
        "-crf", "23",
        "-profile:v", "baseline",
        "-level", "3.0",
        "-pix_fmt", "yuv420p",
        "-c:a", "aac",
        "-b:a", "128k",
        "-movflags", "+faststart",
        "-y", "`"$tempOutput`""
    )
    
    $process = Start-Process -FilePath $ffmpegPath -ArgumentList $arguments -NoNewWindow -Wait -PassThru -RedirectStandardOutput "nul" -RedirectStandardError "nul"
    
    if ($process.ExitCode -eq 0 -and (Test-Path $tempOutput)) {
        # Replace original with converted
        Remove-Item $video.FullName -Force
        Rename-Item $tempOutput $video.Name
        Write-Host "    [OK] Converted successfully" -ForegroundColor Green
        $success++
    } else {
        Write-Host "    [FAILED] Conversion failed" -ForegroundColor Red
        # Clean up temp file if exists
        if (Test-Path $tempOutput) {
            Remove-Item $tempOutput -Force
        }
        $failed++
    }
    Write-Host ""
}

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "Conversion Complete!" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "Total videos: $count"
Write-Host "Successfully converted: $success" -ForegroundColor Green
Write-Host "Failed: $failed" -ForegroundColor $(if ($failed -gt 0) { "Red" } else { "Green" })
Write-Host ""
Write-Host "All videos are now in browser-compatible H.264 format!" -ForegroundColor Green
Write-Host ""
Read-Host "Press Enter to exit"
