<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time System Test - CrowdLens</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Pusher & Laravel Echo via CDN -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
    
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
            max-width: 900px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        h1 {
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 2rem;
        }

        .status-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin-bottom: 0.5rem;
            background: #f8fafc;
            border-radius: 8px;
            gap: 0.75rem;
        }

        .status-item.success {
            background: #d1fae5;
            color: #065f46;
        }

        .status-item.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-item.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .icon {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }

        .log-console {
            background: #1e293b;
            color: #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 1rem;
        }

        .log-entry {
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
        }

        .log-success { color: #86efac; }
        .log-error { color: #fca5a5; }
        .log-warning { color: #fde047; }
        .log-info { color: #93c5fd; }

        .btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #2563eb;
        }

        .btn:disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }

        .code-block {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>🔍 Real-Time System Test</h1>
            <p class="subtitle">Testing Pusher + Laravel Echo integration</p>

            <div id="statusContainer">
                <div class="status-item" id="statusEcho">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Checking Laravel Echo...</span>
                </div>

                <div class="status-item" id="statusPusher">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Checking Pusher connection...</span>
                </div>

                <div class="status-item" id="statusChannel">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Checking channel subscription...</span>
                </div>

                <div class="status-item" id="statusCredentials">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Checking Pusher credentials...</span>
                </div>
            </div>

            <div style="margin-top: 2rem;">
                <button class="btn" id="testBtn" onclick="testBroadcast()">
                    🧪 Trigger Test Event
                </button>
            </div>
        </div>

        <div class="card">
            <h2 style="color: #1e293b; margin-bottom: 1rem;">📊 Real-Time Console</h2>
            <div class="log-console" id="logConsole"></div>
        </div>

        <div class="card">
            <h2 style="color: #1e293b; margin-bottom: 1rem;">⚙️ Configuration</h2>
            <p><strong>Pusher App Key:</strong></p>
            <div class="code-block">{{ config('broadcasting.connections.pusher.key') ?: 'NOT SET' }}</div>
            
            <p style="margin-top: 1rem;"><strong>Pusher Cluster:</strong></p>
            <div class="code-block">{{ config('broadcasting.connections.pusher.options.cluster') ?: 'NOT SET' }}</div>
            
            <p style="margin-top: 1rem;"><strong>Broadcast Driver:</strong></p>
            <div class="code-block">{{ config('broadcasting.default') }}</div>
        </div>
    </div>

    <script>
        const logConsole = document.getElementById('logConsole');
        const statusEcho = document.getElementById('statusEcho');
        const statusPusher = document.getElementById('statusPusher');
        const statusChannel = document.getElementById('statusChannel');
        const statusCredentials = document.getElementById('statusCredentials');

        function log(message, type = 'info') {
            const entry = document.createElement('div');
            entry.className = `log-entry log-${type}`;
            entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            logConsole.insertBefore(entry, logConsole.firstChild);
            console.log(message);
        }

        function updateStatus(element, success, message) {
            element.className = `status-item ${success ? 'success' : 'error'}`;
            element.innerHTML = `
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${success 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                    }
                </svg>
                <span>${message}</span>
            `;
        }

        // Initialize Laravel Echo with Pusher
        log('Initializing Laravel Echo...', 'info');
        
        try {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: '{{ config("broadcasting.connections.pusher.key") }}',
                cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                forceTLS: true,
                encrypted: true,
            });
            log('✓ Echo initialized', 'success');
        } catch (e) {
            log('✗ Echo initialization failed: ' + e.message, 'error');
        }

        // Test Echo
        log('Starting system tests...', 'info');

        if (typeof window.Echo !== 'undefined') {
            updateStatus(statusEcho, true, '✓ Laravel Echo loaded successfully');
            log('✓ Laravel Echo is available', 'success');

            // Check Pusher connection
            if (window.Echo.connector && window.Echo.connector.pusher) {
                const pusher = window.Echo.connector.pusher;
                
                log('✓ Pusher client initialized', 'success');
                log(`Pusher version: ${Pusher.VERSION}`, 'info');
                log(`App key: ${pusher.key}`, 'info');
                log(`Cluster: ${pusher.config.cluster}`, 'info');

                // Check credentials
                if (pusher.key && pusher.key !== 'your_app_key') {
                    updateStatus(statusCredentials, true, '✓ Pusher credentials configured');
                    log('✓ Pusher credentials look valid', 'success');
                } else {
                    updateStatus(statusCredentials, false, '✗ Pusher credentials not set (using default)');
                    log('✗ Please set PUSHER_APP_KEY in .env file', 'error');
                }

                // Monitor connection
                pusher.connection.bind('connected', function() {
                    updateStatus(statusPusher, true, '✓ Connected to Pusher');
                    log('✓ Successfully connected to Pusher', 'success');
                });

                pusher.connection.bind('disconnected', function() {
                    updateStatus(statusPusher, false, '✗ Disconnected from Pusher');
                    log('✗ Disconnected from Pusher', 'error');
                });

                pusher.connection.bind('error', function(err) {
                    updateStatus(statusPusher, false, '✗ Pusher connection error');
                    log('✗ Pusher error: ' + JSON.stringify(err), 'error');
                });

                pusher.connection.bind('state_change', function(states) {
                    log(`Connection state: ${states.previous} → ${states.current}`, 'info');
                });

                // Subscribe to test channel
                try {
                    const channel = window.Echo.channel('admin-notifications');
                    
                    channel.listen('.report.submitted', (event) => {
                        log('🔔 RECEIVED EVENT: ' + JSON.stringify(event), 'success');
                        alert('✅ Real-time event received!\n\n' + JSON.stringify(event, null, 2));
                    });

                    updateStatus(statusChannel, true, '✓ Subscribed to admin-notifications channel');
                    log('✓ Subscribed to admin-notifications channel', 'success');
                    log('Listening for .report.submitted events...', 'info');
                } catch (e) {
                    updateStatus(statusChannel, false, '✗ Failed to subscribe to channel');
                    log('✗ Channel subscription error: ' + e.message, 'error');
                }

            } else {
                updateStatus(statusPusher, false, '✗ Pusher not initialized');
                log('✗ Pusher client not available', 'error');
            }
        } else {
            updateStatus(statusEcho, false, '✗ Laravel Echo not loaded');
            log('✗ Laravel Echo not found - run: npm install && npm run build', 'error');
        }

        function testBroadcast() {
            log('Triggering test broadcast...', 'info');
            document.getElementById('testBtn').disabled = true;
            document.getElementById('testBtn').textContent = 'Sending...';

            fetch('/admin/test-broadcast', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                log('✓ Test event sent to Pusher', 'success');
                log('Wait for event to be received...', 'info');
                document.getElementById('testBtn').disabled = false;
                document.getElementById('testBtn').textContent = '🧪 Trigger Test Event';
            })
            .catch(err => {
                log('✗ Failed to send test event: ' + err.message, 'error');
                document.getElementById('testBtn').disabled = false;
                document.getElementById('testBtn').textContent = '🧪 Trigger Test Event';
            });
        }
    </script>
</body>
</html>
