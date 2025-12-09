<?php
/**
 * Video Codec Checker
 * 
 * This script checks video files in storage and identifies which ones
 * may have playback issues in browsers.
 * 
 * Usage: php check-videos.php
 */

$storageDir = __DIR__ . '/storage/app/public/reports/videos';

if (!is_dir($storageDir)) {
    die("Storage directory not found: $storageDir\n");
}

echo "Checking videos in: $storageDir\n";
echo str_repeat('=', 80) . "\n\n";

$videos = glob($storageDir . '/*.mp4');
$total = count($videos);
$problematic = [];

foreach ($videos as $index => $videoPath) {
    $filename = basename($videoPath);
    $size = filesize($videoPath);
    $sizeMB = round($size / 1024 / 1024, 2);
    
    echo sprintf("[%d/%d] %s (%.2f MB)\n", $index + 1, $total, $filename, $sizeMB);
    
    // Check if file can be read
    $handle = @fopen($videoPath, 'rb');
    if (!$handle) {
        echo "  ❌ ERROR: Cannot open file\n";
        $problematic[] = $filename;
        continue;
    }
    
    // Read first 12 bytes to check MP4 signature
    $header = fread($handle, 12);
    fclose($handle);
    
    // Check for valid MP4 file type box (ftyp)
    $ftypPos = strpos($header, 'ftyp');
    if ($ftypPos === false) {
        echo "  ⚠️  WARNING: Invalid MP4 header - may not play in browsers\n";
        $problematic[] = $filename;
    } else {
        // Check brand
        $brand = substr($header, $ftypPos + 4, 4);
        echo "  ℹ️  Brand: $brand\n";
        
        // Check for common browser-compatible brands
        $compatibleBrands = ['isom', 'mp41', 'mp42', 'avc1', 'iso2', 'iso5'];
        $isCompatible = false;
        foreach ($compatibleBrands as $compatBrand) {
            if (strpos($brand, $compatBrand) !== false) {
                $isCompatible = true;
                break;
            }
        }
        
        if ($isCompatible) {
            echo "  ✓ Likely browser-compatible\n";
        } else {
            echo "  ⚠️  WARNING: May have compatibility issues\n";
            $problematic[] = $filename;
        }
    }
    
    echo "\n";
}

echo str_repeat('=', 80) . "\n";
echo "Summary:\n";
echo "  Total videos: $total\n";
echo "  Potentially problematic: " . count($problematic) . "\n";

if (count($problematic) > 0) {
    echo "\n⚠️  Videos that may need re-encoding:\n";
    foreach ($problematic as $file) {
        echo "  - $file\n";
    }
    
    echo "\n";
    echo "To fix these videos, you need to re-encode them with H.264 codec.\n";
    echo "You can use FFmpeg (download from https://ffmpeg.org/):\n\n";
    echo "  ffmpeg -i input.mp4 -c:v libx264 -preset fast -crf 23 -c:a aac -b:a 128k output.mp4\n\n";
}
