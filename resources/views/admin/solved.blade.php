<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Solved Reports - Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f8fafc;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .sidebar-header p {
            color: #94a3b8;
            font-size: 0.875rem;
        }

        .nav-menu {
            list-style: none;
            padding: 1.5rem 0;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.3s;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link.active {
            background: rgba(59, 130, 246, 0.2);
            color: white;
            border-left: 3px solid #3b82f6;
        }

        .nav-link svg {
            width: 20px;
            height: 20px;
            margin-right: 0.75rem;
        }

        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 2rem;
        }

        .top-bar {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .top-bar h1 {
            font-size: 1.75rem;
            color: #1e293b;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-name {
            color: #64748b;
            font-size: 0.875rem;
        }

        .logout-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        .reports-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .reports-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .reports-header h2 {
            font-size: 1.5rem;
            color: #1e293b;
        }

        .add-btn {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.2s;
        }

        .add-btn:hover {
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #94a3b8;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 1.125rem;
        }

        .disaster-type-select {
            padding: 0.5rem;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            background: white;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .image-preview, .video-preview {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }

        .view-link {
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.875rem;
            cursor: pointer;
        }

        .view-link:hover {
            text-decoration: underline;
        }

        /* Media Modal Styles */
        .media-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }

        .media-modal.active {
            display: flex;
        }

        .media-modal-content {
            position: relative;
            max-width: 95%;
            max-height: 95%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .media-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10001;
            transition: background 0.3s;
        }

        .media-modal-close:hover {
            background: rgba(255, 0, 0, 0.9);
        }

        .media-modal img {
            max-width: 90vw;
            max-height: 90vh;
            width: auto;
            height: auto;
            display: block;
            border-radius: 8px;
        }

        .media-modal video {
            max-width: 90vw;
            max-height: 90vh;
            width: auto;
            height: auto;
            display: block;
            border-radius: 8px;
        }

        .reports-table {
            width: 100%;
            border-collapse: collapse;
        }

        .reports-table thead {
            background: #f8fafc;
        }

        .reports-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #475569;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .reports-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .reports-table tbody tr:hover {
            background: #f8fafc;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-verified {
            background: #d1fae5;
            color: #065f46;
        }

        .status-btn {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .status-btn:hover {
            transform: scale(1.05);
        }

        .btn-unverified {
            background: #f59e0b;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-unverified:hover {
            transform: scale(1.05);
            background: #d97706;
        }

        /* Notification Bell Styles */
        .notification-bell {
            position: relative;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: background 0.3s;
        }

        .notification-bell:hover {
            background: rgba(59, 130, 246, 0.1);
        }

        .notification-bell svg {
            width: 24px;
            height: 24px;
            color: #64748b;
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            display: none;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        .notification-badge.show {
            display: flex;
        }

        /* Notification Dropdown Panel */
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            width: 360px;
            max-height: 480px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
        }

        .notification-dropdown.show {
            display: flex;
        }

        .notification-dropdown-header {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 700;
            font-size: 1.25rem;
            color: #111827;
        }

        .notification-dropdown-body {
            overflow-y: auto;
            max-height: 400px;
        }

        .notification-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .notification-item:hover {
            background-color: #f9fafb;
        }

        .notification-item.unread {
            background-color: #eff6ff;
        }

        .notification-item.unread:hover {
            background-color: #dbeafe;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-icon svg {
            width: 20px;
            height: 20px;
            color: white;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            color: #111827;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .notification-text {
            color: #6b7280;
            font-size: 0.8125rem;
            line-height: 1.4;
        }

        .notification-time {
            color: #9ca3af;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .notification-empty {
            padding: 3rem 1.5rem;
            text-align: center;
            color: #9ca3af;
        }

        .notification-empty svg {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem;
            opacity: 0.5;
        }

        /* Real-time notification popup */
        .realtime-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            max-width: 400px;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Portal</h2>
            <p>Davao City Reports</p>
        </div>
        <nav>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.map') }}" class="nav-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Map
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.reports') }}" class="nav-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users') }}" class="nav-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.solved') }}" class="nav-link active">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Solved
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Settings
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h1>Solved Reports</h1>
            <div class="admin-info">
                <div class="notification-bell" style="position: relative;">
                    <button onclick="toggleNotificationDropdown(event)" title="Notifications" style="background: none; border: none; cursor: pointer; padding: 0.5rem; display: flex; align-items: center; position: relative;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="notificationBadge" class="notification-badge">0</span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-dropdown-header">
                            Notifications
                        </div>
                        <div class="notification-dropdown-body" id="notificationList">
                            <div class="notification-empty">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <div>No new notifications</div>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="admin-name">{{ Auth::guard('admin')->user()->name }}</span>
                <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>

        <div class="reports-container">
            @if(session('success'))
            <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
                {{ session('success') }}
            </div>
            @endif

            <div class="reports-header">
                <h2>Recent Reports</h2>
            </div>

            <table class="reports-table">
                <thead>
                    <tr>
                        <th>
                            Disaster
                            <br>
                            <select class="disaster-type-select" id="adminDisasterFilter" onchange="filterAdminReports()">
                                <option value="">All</option>
                                @foreach($disasterTypes as $type)
                                    <option value="{{ $type->name }}">{{ $type->icon }} {{ $type->name }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>User</th>
                        <th>Location</th>
                        <th>Solved By</th>
                        <th>Solved At</th>
                        <th>Solved</th>
                    </tr>
                </thead>
                <tbody>
                    @if($solvedReports->count() > 0)
                        @foreach($solvedReports as $solved)
                        <tr class="report-row" data-disaster-type="{{ $solved->report->disaster_type }}">
                            <td>
                                <span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                    {{ ucfirst($solved->report->disaster_type) }}
                                </span>
                            </td>
                            <td style="max-width: 300px;">{{ Str::limit($solved->report->description, 100) }}</td>
                            <td>{{ $solved->report->created_at->format('M d, Y') }}</td>
                            <td>{{ $solved->report->user->name }}</td>
                            <td>
                                @if($solved->report->location)
                                    {{ Str::limit($solved->report->location, 50) }}
                                @else
                                    {{ number_format($solved->report->latitude, 6) }}, {{ number_format($solved->report->longitude, 6) }}
                                @endif
                            </td>
                            <td>{{ $solved->admin->name }}</td>
                            <td>{{ $solved->solved_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <span style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600; display: inline-block;">
                                    Solved
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p>No solved reports yet</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Media Modal -->
    <div id="mediaModal" class="media-modal">
        <button class="media-modal-close" onclick="closeMediaModal()">×</button>
        <div class="media-modal-content" id="mediaContent"></div>
    </div>

    <script>
        function showMedia(url, type) {
            console.log('Opening media:', type, url);
            const modal = document.getElementById('mediaModal');
            const content = document.getElementById('mediaContent');
            
            if (type === 'image') {
                content.innerHTML = `<img src="${url}" alt="Report Image" style="max-width: 90vw; max-height: 90vh; width: auto; height: auto; border-radius: 8px;" onerror="console.error('Failed to load image:', this.src)">`;
            } else if (type === 'video') {
                content.innerHTML = `<video src="${url}" controls autoplay style="max-width: 90vw; max-height: 90vh; width: auto; height: auto; border-radius: 8px;" onerror="console.error('Failed to load video:', this.src)">Your browser does not support the video tag.</video>`;
            }
            
            modal.classList.add('active');
        }

        function closeMediaModal() {
            const modal = document.getElementById('mediaModal');
            const content = document.getElementById('mediaContent');
            modal.classList.remove('active');
            
            // Stop video playback if exists
            const video = content.querySelector('video');
            if (video) {
                video.pause();
                video.currentTime = 0;
            }
            
            content.innerHTML = '';
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('mediaModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeMediaModal();
                    }
                });
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMediaModal();
            }
        });

        // Filter reports by disaster type
        function filterAdminReports() {
            const filterValue = document.getElementById('adminDisasterFilter').value;
            const rows = document.querySelectorAll('.report-row');
            
            rows.forEach(row => {
                const disasterType = row.getAttribute('data-disaster-type');
                if (filterValue === '' || disasterType === filterValue) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Poll for new reports every 5 seconds
        let notificationCount = 0;
        let notificationsList = [];
        let lastCheckedReportId = 0;

        // Load notifications from database on page load
        function loadNotifications() {
            fetch('{{ route('admin.notifications.get') }}')
                .then(response => response.json())
                .then(data => {
                    notificationsList = data.notifications.map(notif => ({
                        id: notif.report_id,
                        disaster_type: notif.disaster_type,
                        user_name: notif.user_name,
                        time_ago: notif.time_ago,
                        read: notif.is_read,
                        notification_id: notif.id
                    }));
                    
                    // Set lastCheckedReportId to the highest report ID to avoid showing old reports
                    if (notificationsList.length > 0) {
                        lastCheckedReportId = Math.max(...notificationsList.map(n => n.id));
                    }
                    
                    updateNotificationBadge();
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        // Load notifications on page load
        loadNotifications();

        // Track shown notifications using localStorage
        function hasShownNotification(reportId) {
            const shown = localStorage.getItem('shown_notifications') || '[]';
            const shownIds = JSON.parse(shown);
            return shownIds.includes(reportId);
        }

        function markNotificationAsShown(reportId) {
            const shown = localStorage.getItem('shown_notifications') || '[]';
            let shownIds = JSON.parse(shown);
            shownIds.push(reportId);
            // Keep only last 100 notifications to prevent storage overflow
            if (shownIds.length > 100) {
                shownIds = shownIds.slice(-100);
            }
            localStorage.setItem('shown_notifications', JSON.stringify(shownIds));
        }

        function showRealtimeNotification(report) {
            // Check if already shown
            if (hasShownNotification(report.id)) {
                return;
            }

            const notification = document.createElement('div');
            notification.className = 'realtime-notification';
            notification.innerHTML = `
                <svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <div>
                    <div style="font-weight: 600; margin-bottom: 0.25rem;">🚨 New Report Submitted!</div>
                    <div style="font-size: 0.875rem; opacity: 0.95;">${report.disaster_type || 'Report'} - ${report.user_name}</div>
                </div>
            `;
            
            document.body.appendChild(notification);
            playNotificationSound();
            markNotificationAsShown(report.id);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        function playNotificationSound() {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 800;
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.5);
            } catch (e) {
                console.warn('Could not play notification sound:', e);
            }
        }

        function toggleNotificationDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
            
            if (dropdown.classList.contains('show')) {
                populateNotifications();
                // Mark all notifications as read when opening dropdown
                markAllAsRead();
            }
        }

        function markAllAsRead() {
            // Mark all unread notifications as read in the list
            notificationsList.forEach(notif => {
                if (!notif.read) {
                    notif.read = true;
                }
            });
            
            // Update badge to show 0
            updateNotificationBadge();
            
            // Set flag in localStorage to persist across page reloads and sync across all pages
            localStorage.setItem('notificationsBadgeReset', 'true');
            
            // Mark all as read in database
            fetch('/admin/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).catch(error => console.error('Error marking all as read:', error));
        }

        function populateNotifications() {
            const listContainer = document.getElementById('notificationList');
            
            if (notificationsList.length === 0) {
                listContainer.innerHTML = `
                    <div class="notification-empty">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <div>No new notifications</div>
                    </div>
                `;
                return;
            }

            listContainer.innerHTML = notificationsList.map(notif => `
                <div class="notification-item ${notif.read ? '' : 'unread'}" onclick="viewReport(${notif.id})">
                    <div class="notification-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">New Report Submitted</div>
                        <div class="notification-text">${notif.disaster_type} - ${notif.user_name}</div>
                        <div class="notification-time">${notif.time_ago}</div>
                    </div>
                </div>
            `).join('');
        }

        function viewReport(reportId) {
            // Mark as read
            const notif = notificationsList.find(n => n.id === reportId);
            if (notif && !notif.read) {
                notif.read = true;
                
                // Mark as read in database
                if (notif.notification_id) {
                    fetch(`/admin/notifications/${notif.notification_id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }).catch(error => console.error('Error marking notification as read:', error));
                }
            }
            
            // Close dropdown
            document.getElementById('notificationDropdown').classList.remove('show');
            
            // Store report ID to show modal after redirect
            sessionStorage.setItem('openReportModal', reportId);
            
            // Redirect to reports page
            window.location.href = '{{ route('admin.reports') }}';
        }

        function updateNotificationBadge() {
            const unreadCount = notificationsList.filter(n => !n.read).length;
            notificationCount = unreadCount;
            const notificationBadge = document.getElementById('notificationBadge');
            notificationBadge.textContent = unreadCount;
            if (unreadCount > 0) {
                notificationBadge.classList.add('show');
            } else {
                notificationBadge.classList.remove('show');
            }
        }

        // Listen for storage changes to sync badge across pages
        window.addEventListener('storage', function(e) {
            if (e.key === 'notificationsBadgeReset') {
                // Reset badge to 0 when another page marks all as read
                notificationsList.forEach(notif => notif.read = true);
                updateNotificationBadge();
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationDropdown');
            const bell = document.querySelector('.notification-bell');
            if (dropdown && bell && !bell.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        function checkNewReports() {
            fetch(`{{ route('admin.reports.check-new') }}?since=${lastCheckedReportId}`)
                .then(response => response.json())
                .then(data => {
                    // Show notification popup for new reports only
                    if (data.new_reports && data.new_reports.length > 0) {
                        // Clear the badge reset flag since there's a new notification
                        localStorage.removeItem('notificationsBadgeReset');
                        
                        data.new_reports.forEach(report => {
                            // Only process if this report hasn't been shown yet
                            if (!hasShownNotification(report.id)) {
                                showRealtimeNotification(report);
                                playNotificationSound();
                                
                                // Add to notifications list only if not already there
                                const existingNotif = notificationsList.find(n => n.id === report.id);
                                if (!existingNotif) {
                                    notificationsList.unshift({
                                        id: report.id,
                                        disaster_type: report.disaster_type_name,
                                        user_name: report.user_name,
                                        time_ago: 'Just now',
                                        read: false
                                    });
                                    
                                    // Keep only last 50 notifications
                                    if (notificationsList.length > 50) {
                                        notificationsList = notificationsList.slice(0, 50);
                                    }
                                }
                                
                                // Update lastCheckedReportId
                                if (report.id > lastCheckedReportId) {
                                    lastCheckedReportId = report.id;
                                }
                            }
                        });
                        
                        updateNotificationBadge();
                    }
                })
                .catch(error => console.error('Error checking reports:', error));
        }

        // Check immediately and then every 5 seconds
        checkNewReports();
        setInterval(checkNewReports, 5000);
    </script>
</body>
</html>
