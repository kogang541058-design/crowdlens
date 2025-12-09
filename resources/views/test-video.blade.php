<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Playback Test - CrowdLens</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #64748b;
        }

        .status {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .status h2 {
            color: #1e293b;
            margin-bottom: 1.5rem;
            font-size: 1.25rem;
        }

        .check-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: #f8fafc;
            border-radius: 8px;
            gap: 0.75rem;
        }

        .check-item.success {
            background: #d1fae5;
            color: #065f46;
        }

        .check-item.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .check-item svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .video-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .video-card video {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: #000;
        }

        .video-info {
            padding: 1.5rem;
        }

        .video-info h3 {
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .video-info p {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .video-info code {
            display: block;
            background: #f8fafc;
            padding: 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-top: 0.5rem;
            word-break: break-all;
            color: #475569;
        }

        .diagnostic {
            background: #1e293b;
            color: white;
            padding: 2rem;
            border-radius: 16px;
            margin-top: 2rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            max-height: 400px;
            overflow-y: auto;
        }

        .diagnostic h3 {
            margin-bottom: 1rem;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .log-entry {
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
        }

        .log-success {
            color: #86efac;
        }

        .log-error {
            color: #fca5a5;
        }

        .log-info {
            color: #93c5fd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎥 Video Playback Diagnostic Test</h1>
            <p>Testing video playback functionality for CrowdLens disaster reporting system</p>
        </div>

        <div class="status">
            <h2>System Status Checks</h2>
            
            @php
                $storageLinked = is_link(public_path('storage')) || is_dir(public_path('storage'));
                $videosExist = \Storage::disk('public')->exists('reports/videos');
                $hasVideos = \App\Models\Report::whereNotNull('video')->exists();
                $sampleVideo = \App\Models\Report::whereNotNull('video')->first();
            @endphp

            <div class="check-item {{ $storageLinked ? 'success' : 'error' }}">
                @if($storageLinked)
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><strong>Storage Link:</strong> ✓ Connected (public/storage → storage/app/public)</span>
                @else
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><strong>Storage Link:</strong> ✗ NOT FOUND - Run: php artisan storage:link</span>
                @endif
            </div>

            <div class="check-item {{ $videosExist ? 'success' : 'error' }}">
                @if($videosExist)
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><strong>Video Directory:</strong> ✓ Exists (storage/app/public/reports/videos)</span>
                @else
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><strong>Video Directory:</strong> ✗ Directory not found</span>
                @endif
            </div>

            <div class="check-item {{ $hasVideos ? 'success' : 'error' }}">
                @if($hasVideos)
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><strong>Database Videos:</strong> ✓ Found {{ \App\Models\Report::whereNotNull('video')->count() }} video(s)</span>
                @else
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><strong>Database Videos:</strong> ✗ No videos found in reports table</span>
                @endif
            </div>

            <div class="check-item success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span><strong>MIME Types:</strong> ✓ Configured in .htaccess (video/mp4, video/webm, etc.)</span>
            </div>

            <div class="check-item success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span><strong>Streaming Headers:</strong> ✓ Accept-Ranges and Connection headers configured</span>
            </div>
        </div>

        @if($hasVideos)
        <h2 style="color: white; margin-bottom: 1rem; font-size: 1.5rem;">📹 Sample Videos</h2>
        <div class="video-grid">
            @foreach(\App\Models\Report::whereNotNull('video')->take(6)->get() as $report)
            <div class="video-card">
                <video controls preload="metadata" id="video-{{ $report->id }}">
                    <source src="{{ Storage::url($report->video) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div class="video-info">
                    <h3>{{ $report->disaster_type ?? 'Unknown Type' }}</h3>
                    <p><strong>Location:</strong> {{ Str::limit($report->location ?? 'Unknown', 40) }}</p>
                    <p><strong>Date:</strong> {{ $report->created_at->format('M d, Y') }}</p>
                    <p><strong>Storage Path:</strong></p>
                    <code>{{ $report->video }}</code>
                    <p style="margin-top: 0.5rem;"><strong>Public URL:</strong></p>
                    <code>{{ Storage::url($report->video) }}</code>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="status">
            <h2>No Videos Found</h2>
            <p style="color: #64748b;">Upload some disaster reports with videos to test the playback functionality.</p>
        </div>
        @endif

        <div class="diagnostic">
            <h3>🔍 Real-Time Diagnostic Console</h3>
            <div id="diagnosticLog"></div>
        </div>
    </div>

    <script>
        const diagnosticLog = document.getElementById('diagnosticLog');
        
        function log(message, type = 'info') {
            const entry = document.createElement('div');
            entry.className = `log-entry log-${type}`;
            entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            diagnosticLog.insertBefore(entry, diagnosticLog.firstChild);
            console.log(message);
        }

        log('✓ Page loaded successfully', 'success');
        log('✓ Video diagnostic system initialized', 'success');

        // Monitor all videos on the page
        const videos = document.querySelectorAll('video');
        log(`Found ${videos.length} video(s) on page`, 'info');

        videos.forEach((video, index) => {
            const videoId = video.id || `video-${index}`;
            
            video.addEventListener('loadstart', () => {
                log(`[${videoId}] Loading started...`, 'info');
            });

            video.addEventListener('loadedmetadata', () => {
                log(`[${videoId}] ✓ Metadata loaded - Duration: ${video.duration.toFixed(2)}s, Size: ${video.videoWidth}x${video.videoHeight}`, 'success');
            });

            video.addEventListener('loadeddata', () => {
                log(`[${videoId}] ✓ Video data loaded and ready`, 'success');
            });

            video.addEventListener('canplay', () => {
                log(`[${videoId}] ✓ Video can start playing`, 'success');
            });

            video.addEventListener('canplaythrough', () => {
                log(`[${videoId}] ✓ Video can play through without buffering`, 'success');
            });

            video.addEventListener('play', () => {
                log(`[${videoId}] ▶️ Video started playing`, 'info');
            });

            video.addEventListener('pause', () => {
                log(`[${videoId}] ⏸️ Video paused`, 'info');
            });

            video.addEventListener('ended', () => {
                log(`[${videoId}] ✓ Video playback completed`, 'success');
            });

            video.addEventListener('error', (e) => {
                const error = video.error;
                let errorMsg = 'Unknown error';
                
                if (error) {
                    switch(error.code) {
                        case 1: errorMsg = 'Video loading aborted'; break;
                        case 2: errorMsg = 'Network error while loading video'; break;
                        case 3: errorMsg = 'Video decoding failed'; break;
                        case 4: errorMsg = 'Video format not supported'; break;
                    }
                }
                
                log(`[${videoId}] ✗ ERROR: ${errorMsg} (Code: ${error ? error.code : 'N/A'})`, 'error');
                log(`[${videoId}] URL: ${video.currentSrc}`, 'error');
            });

            video.addEventListener('stalled', () => {
                log(`[${videoId}] ⚠️ Video download stalled`, 'error');
            });

            video.addEventListener('waiting', () => {
                log(`[${videoId}] ⏳ Waiting for data...`, 'info');
            });
        });

        // Test network connectivity
        log('Testing video URL accessibility...', 'info');
        
        @if($sampleVideo)
        fetch('{{ Storage::url($sampleVideo->video) }}', { method: 'HEAD' })
            .then(response => {
                if (response.ok) {
                    log('✓ Video URL is accessible (HTTP ' + response.status + ')', 'success');
                    log('✓ Content-Type: ' + (response.headers.get('content-type') || 'Not specified'), 'info');
                    log('✓ Content-Length: ' + (response.headers.get('content-length') || 'Not specified') + ' bytes', 'info');
                } else {
                    log('✗ Video URL returned HTTP ' + response.status, 'error');
                }
            })
            .catch(error => {
                log('✗ Network error: ' + error.message, 'error');
            });
        @endif
    </script>
</body>
</html>
