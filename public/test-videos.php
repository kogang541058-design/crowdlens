<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Tester - CrowdLens</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #0f172a;
            color: white;
            padding: 2rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            margin-bottom: 1rem;
            color: #3b82f6;
        }
        .video-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .video-card {
            background: #1e293b;
            border-radius: 8px;
            padding: 1rem;
            transition: transform 0.2s;
        }
        .video-card:hover {
            transform: translateY(-4px);
        }
        .video-wrapper {
            background: #000;
            border-radius: 4px;
            margin-bottom: 0.75rem;
            position: relative;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            overflow: hidden;
        }
        video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .video-info {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.5rem;
        }
        .status {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        .status.success {
            background: #10b981;
            color: white;
        }
        .status.error {
            background: #ef4444;
            color: white;
        }
        .status.loading {
            background: #f59e0b;
            color: white;
        }
        .filename {
            font-size: 0.875rem;
            color: white;
            margin-bottom: 0.5rem;
            word-break: break-all;
        }
        .actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .btn {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 0.75rem;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        .btn-secondary {
            background: #64748b;
            color: white;
        }
        .summary {
            background: #1e293b;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .summary-item {
            text-align: center;
        }
        .summary-value {
            font-size: 2rem;
            font-weight: bold;
            color: #3b82f6;
        }
        .summary-label {
            font-size: 0.875rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎥 Video Playback Tester</h1>
        <p style="color: #94a3b8; margin-bottom: 1rem;">Testing all video files in storage to identify playback issues</p>
        
        <div class="summary">
            <h2 style="margin-bottom: 1rem;">Summary</h2>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-value" id="totalCount">0</div>
                    <div class="summary-label">Total</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value" style="color: #10b981;" id="successCount">0</div>
                    <div class="summary-label">Playable</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value" style="color: #ef4444;" id="errorCount">0</div>
                    <div class="summary-label">Errors</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value" style="color: #f59e0b;" id="loadingCount">0</div>
                    <div class="summary-label">Loading</div>
                </div>
            </div>
        </div>

        <div class="video-list" id="videoList"></div>
    </div>

    <script>
        const videos = <?php
            $videoDir = __DIR__ . '/storage/app/public/reports/videos';
            $videos = [];
            
            if (is_dir($videoDir)) {
                $files = glob($videoDir . '/*.mp4');
                foreach ($files as $file) {
                    $filename = basename($file);
                    $videos[] = [
                        'filename' => $filename,
                        'url' => '/storage/reports/videos/' . $filename,
                        'size' => filesize($file)
                    ];
                }
            }
            
            echo json_encode($videos);
        ?>;

        let stats = {
            total: videos.length,
            success: 0,
            error: 0,
            loading: videos.length
        };

        function updateStats() {
            document.getElementById('totalCount').textContent = stats.total;
            document.getElementById('successCount').textContent = stats.success;
            document.getElementById('errorCount').textContent = stats.error;
            document.getElementById('loadingCount').textContent = stats.loading;
        }

        function formatBytes(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        function createVideoCard(videoData) {
            const card = document.createElement('div');
            card.className = 'video-card';
            card.innerHTML = `
                <div class="filename">${videoData.filename}</div>
                <div class="video-wrapper">
                    <video 
                        controls 
                        preload="metadata"
                        data-filename="${videoData.filename}">
                        <source src="${videoData.url}" type="video/mp4; codecs='avc1.42E01E, mp4a.40.2'">
                        <source src="${videoData.url}" type="video/mp4">
                    </video>
                </div>
                <div class="video-info">Size: ${formatBytes(videoData.size)}</div>
                <span class="status loading">Loading...</span>
                <div class="actions">
                    <a href="${videoData.url}" class="btn btn-primary" download>Download</a>
                    <a href="${videoData.url}" class="btn btn-secondary" target="_blank">Open</a>
                </div>
            `;
            
            const video = card.querySelector('video');
            const status = card.querySelector('.status');
            
            video.addEventListener('loadedmetadata', function() {
                console.log('✓ Loaded:', videoData.filename, `${video.videoWidth}x${video.videoHeight}`, `${video.duration.toFixed(1)}s`);
                status.textContent = `✓ ${video.videoWidth}x${video.videoHeight} • ${video.duration.toFixed(1)}s`;
                status.className = 'status success';
                stats.success++;
                stats.loading--;
                updateStats();
            });
            
            video.addEventListener('canplay', function() {
                console.log('✓ Can play:', videoData.filename);
            });
            
            video.addEventListener('error', function(e) {
                console.error('✗ Error:', videoData.filename, video.error);
                
                let errorMsg = 'Playback error';
                if (video.error) {
                    switch(video.error.code) {
                        case 1: errorMsg = 'Aborted'; break;
                        case 2: errorMsg = 'Network error'; break;
                        case 3: errorMsg = 'Decode error'; break;
                        case 4: errorMsg = 'Format not supported'; break;
                    }
                }
                
                status.textContent = `✗ ${errorMsg}`;
                status.className = 'status error';
                stats.error++;
                stats.loading--;
                updateStats();
            });
            
            return card;
        }

        const videoList = document.getElementById('videoList');
        
        if (videos.length === 0) {
            videoList.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #94a3b8; padding: 2rem;">No videos found in storage</p>';
        } else {
            videos.forEach(videoData => {
                videoList.appendChild(createVideoCard(videoData));
            });
        }
        
        updateStats();
    </script>
</body>
</html>
