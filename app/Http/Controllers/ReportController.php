<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Admin;
use App\Models\Notification;
use App\Events\ReportSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Store a newly created report.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'disaster_type' => 'required|string',
            'description' => 'required|string|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location' => 'nullable|string',
            'image' => 'nullable|image|max:10240', // 10MB max
            'video' => 'nullable|mimetypes:video/*|max:204800', // 200MB max
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending'; // Always set status to pending for new reports

        // Handle file uploads
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('reports/images', 'public');
        }

        if ($request->hasFile('video')) {
            $videoFile = $request->file('video');
            
            // Check if FFmpeg is available for validation
            $ffmpegPath = $this->findFFmpeg();
            
            if ($ffmpegPath) {
                // Validate video codec before accepting
                $tempPath = $videoFile->getRealPath();
                $validation = $this->validateVideoCodec($tempPath, $ffmpegPath);
                
                if (!$validation['valid']) {
                    return back()->withErrors(['video' => $validation['message']])->withInput();
                }
            }
            
            $videoPath = $videoFile->store('reports/videos', 'public');
            $validated['video'] = $videoPath;
            
            // Convert video to browser-compatible format if needed
            if ($ffmpegPath) {
                $this->convertVideoToH264($videoPath);
            }
        }

        $report = Report::create($validated);

        // Create notifications for all admins
        $admins = Admin::all();
        $disasterTypeName = ucfirst($report->disaster_type);
        $userName = Auth::user()->name;
        
        foreach ($admins as $admin) {
            Notification::create([
                'admin_id' => $admin->id,
                'report_id' => $report->id,
                'type' => 'new_report',
                'message' => "New {$disasterTypeName} report submitted by {$userName}",
                'is_read' => false,
            ]);
        }

        // Broadcast the event to admin channel
        \Log::info('Broadcasting ReportSubmitted event for report ID: ' . $report->id);
        broadcast(new ReportSubmitted($report->load('user')))->toOthers();
        \Log::info('Broadcast completed for report ID: ' . $report->id);

        return redirect()->back()->with('success', 'Report submitted successfully!');
    }

    /**
     * Get user's reports
     */
    public function index()
    {
        $reports = Report::where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json($reports);
    }
    
    /**
     * Validate video codec compatibility
     */
    private function validateVideoCodec($filePath, $ffmpegPath)
    {
        // Use ffprobe (comes with FFmpeg) to check video codec
        $ffprobePath = str_replace('ffmpeg.exe', 'ffprobe.exe', $ffmpegPath);
        
        if (!file_exists($ffprobePath)) {
            // If ffprobe not found, allow upload (will be converted anyway)
            return ['valid' => true];
        }
        
        $command = sprintf(
            '"%s" -v error -select_streams v:0 -show_entries stream=codec_name -of default=noprint_wrappers=1:nokey=1 "%s" 2>&1',
            $ffprobePath,
            $filePath
        );
        
        $codec = trim(shell_exec($command));
        
        \Log::info('Detected video codec: ' . $codec);
        
        // Reject incompatible codecs
        $incompatibleCodecs = ['hevc', 'h265', 'vp9', 'av1'];
        
        if (in_array(strtolower($codec), $incompatibleCodecs)) {
            return [
                'valid' => false,
                'message' => "Video codec '{$codec}' is not supported. Please upload a video in H.264 format (standard MP4). You can convert it at freeconvert.com/video-converter"
            ];
        }
        
        // Check if video has valid video stream
        if (empty($codec)) {
            return [
                'valid' => false,
                'message' => 'Invalid video file. No video stream detected. Please upload a valid video file.'
            ];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Convert video to browser-compatible H.264 format
     */
    private function convertVideoToH264($videoPath)
    {
        // Run conversion in background using PHP exec
        $fullPath = storage_path('app/public/' . $videoPath);
        
        if (!file_exists($fullPath)) {
            \Log::error('Video file not found for conversion: ' . $fullPath);
            return;
        }
        
        // Check if FFmpeg is available
        $ffmpegPath = $this->findFFmpeg();
        
        if (!$ffmpegPath) {
            \Log::warning('FFmpeg not found - video will not be converted. Install FFmpeg for automatic conversion.');
            // Video uploaded as-is, may not play in all browsers
            return;
        }
        
        $tempPath = storage_path('app/public/reports/videos/temp_' . basename($videoPath));
        
        // Convert to H.264 baseline profile (maximum compatibility)
        $command = sprintf(
            '"%s" -i "%s" -c:v libx264 -preset fast -crf 23 -profile:v baseline -level 3.0 -pix_fmt yuv420p -c:a aac -b:a 128k -movflags +faststart -y "%s" 2>&1',
            $ffmpegPath,
            $fullPath,
            $tempPath
        );
        
        \Log::info('Converting video to H.264: ' . basename($videoPath));
        
        // Execute conversion
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($tempPath)) {
            // Replace original with converted version
            @unlink($fullPath);
            rename($tempPath, $fullPath);
            \Log::info('Video converted successfully: ' . basename($videoPath));
        } else {
            \Log::error('Video conversion failed: ' . implode("\n", $output));
            // Clean up temp file if exists
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }
    
    /**
     * Find FFmpeg executable
     */
    private function findFFmpeg()
    {
        // Try common Windows locations
        $paths = [
            'C:\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\Program Files\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\Program Files (x86)\\ffmpeg\\bin\\ffmpeg.exe',
        ];
        
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        // Check if in PATH
        $output = shell_exec('where ffmpeg 2>nul');
        if (!empty($output)) {
            return trim(explode("\n", $output)[0]);
        }
        
        return null;
    }
}
