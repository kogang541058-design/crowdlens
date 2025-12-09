<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reports - Admin Dashboard</title>
    
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
            padding: 0.4rem 0.5rem;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            background: white;
            font-size: 0.75rem;
            margin-top: 0.5rem;
            min-width: 120px;
            display: block;
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
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #475569;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            vertical-align: top;
            line-height: 1.2;
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

        .badge-solved {
            background: #d1fae5;
            color: #065f46;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-in-progress {
            background: #dbeafe;
            color: #1e40af;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-verified {
            background: #d1fae5;
            color: #065f46;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-unverified {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
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

        /* Report Detail Modal */
        .report-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }

        .report-modal-overlay.show {
            display: flex;
        }

        .report-modal {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .report-modal-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .report-modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .report-modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 1.5rem;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .report-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .report-modal-body {
            padding: 2rem;
            overflow-y: auto;
            max-height: calc(90vh - 140px);
        }

        .report-detail-row {
            margin-bottom: 1.5rem;
        }

        .report-detail-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .report-detail-value {
            color: #1f2937;
            font-size: 1rem;
            line-height: 1.6;
        }

        .report-media-preview img,
        .report-media-preview video {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .report-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .report-row:hover {
            background-color: #f9fafb;
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

        .notification-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
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
                    <a href="{{ route('admin.reports') }}" class="nav-link active">
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
                    <a href="{{ route('admin.solved') }}" class="nav-link">
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
            <h1>Reports Management</h1>
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
                <h2 style="font-size: 1.5rem; font-weight: 600; color: #1e293b; margin: 0;">Recent Reports</h2>
            </div>

            <table class="reports-table">
                <thead>
                    <tr>
                        <th>
                            Type of Disaster
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
                        <th>Time</th>
                        <th>User</th>
                        <th>Location</th>
                        <th>
                            Status
                            <br>
                            <select class="disaster-type-select" id="statusFilter" onchange="filterAdminReports()">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                                <option value="unverified">Unverified</option>
                            </select>
                        </th>
                        <th>
                            Action Status
                            <br>
                            <select class="disaster-type-select" id="actionStatusFilter" onchange="filterAdminReports()">
                                <option value="">All</option>
                                <option value="solved">Solved</option>
                                <option value="in_progress">In Progress</option>
                            </select>
                        </th>
                        <th style="display: none;">Image</th>
                        <th style="display: none;">Video</th>
                        <th style="display: none;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($reports->count() > 0)
                        @foreach($reports as $report)
                        <tr class="report-row" 
                            data-id="{{ $report->id }}"
                            data-disaster-type="{{ $report->disaster_type }}" 
                            data-status="{{ $report->status }}" 
                            data-action-status="{{ $report->solved ? 'solved' : ($report->responses()->where('action_type', 'in_progress')->exists() ? 'in_progress' : '') }}"
                            data-description="{{ $report->description }}"
                            data-user="{{ $report->user->name }}"
                            data-location="{{ $report->location ?: number_format($report->latitude, 6) . ', ' . number_format($report->longitude, 6) }}"
                            data-date="{{ $report->created_at->format('M d, Y') }}"
                            data-time="{{ $report->created_at->format('h:i A') }}"
                            data-image="{{ $report->image ? Storage::url($report->image) : '' }}"
                            data-video="{{ $report->video ? Storage::url($report->video) : '' }}">
                            <td>
                                <span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                    {{ ucfirst($report->disaster_type) }}
                                </span>
                            </td>
                            <td style="max-width: 300px;">{{ Str::limit($report->description, 100) }}</td>
                            <td>{{ $report->created_at->format('M d, Y') }}</td>
                            <td>{{ $report->created_at->format('h:i A') }}</td>
                            <td>{{ $report->user->name }}</td>
                            <td>
                                @if($report->location)
                                    {{ Str::limit($report->location, 50) }}
                                @else
                                    {{ number_format($report->latitude, 6) }}, {{ number_format($report->longitude, 6) }}
                                @endif
                            </td>
                            <td>
                                @if($report->status === 'pending')
                                    <span style="background: #fef3c7; color: #92400e; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600; display: inline-block;">
                                        Pending
                                    </span>
                                @elseif($report->status === 'verified')
                                    <span style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600; display: inline-block;">
                                        Verified
                                    </span>
                                @else
                                    <span style="background: #fee2e2; color: #991b1b; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600; display: inline-block;">
                                        Unverified
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($report->solved)
                                    <span style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600; display: inline-block;">
                                        Solved
                                    </span>
                                @elseif($report->responses()->where('action_type', 'in_progress')->exists())
                                    <span style="background: #dbeafe; color: #1e40af; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600; display: inline-block;">
                                        In Progress
                                    </span>
                                @else
                                    <span style="color: #94a3b8; font-size: 0.875rem;">-</span>
                                @endif
                            </td>
                            <td style="display: none;">
                                @if($report->image)
                                    <a href="javascript:void(0)" class="view-link view-media" data-url="{{ Storage::url($report->image) }}" data-type="image" onclick="event.stopPropagation()">View</a>
                                @else
                                    <span style="color: #94a3b8;">N/A</span>
                                @endif
                            </td>
                            <td style="display: none;">
                                @if($report->video)
                                    <a href="javascript:void(0)" class="view-link view-media" data-url="{{ Storage::url($report->video) }}" data-type="video" onclick="event.stopPropagation()">View</a>
                                @else
                                    <span style="color: #94a3b8;">N/A</span>
                                @endif
                            </td>
                            <td style="display: none;">
                                <button class="respond-btn" data-id="{{ $report->id }}" data-type="{{ $report->disaster_type }}" data-description="{{ $report->description }}" data-location="{{ $report->location }}" data-status="{{ $report->status }}" onclick="event.stopPropagation()" style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none;">
                                    📝 Respond
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p>No reports submitted yet</p>
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

    <!-- Report Detail Modal -->
    <div class="report-modal-overlay" id="reportDetailModal" onclick="closeReportDetailModal(event)">
        <div class="report-modal" onclick="event.stopPropagation()">
            <div class="report-modal-header">
                <h3>Report Details</h3>
                <button class="report-modal-close" onclick="closeReportDetailModal()">&times;</button>
            </div>
            <div class="report-modal-body" id="reportDetailContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Respond Modal -->
    <div id="respondModal" class="media-modal" style="align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 2rem; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
            <button onclick="closeRespondModal()" style="position: absolute; top: 1rem; right: 1rem; background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; color: #64748b;">×</button>
            
            <h2 style="font-size: 1.5rem; color: #1e293b; margin-bottom: 1.5rem;">Respond to Report</h2>
            
            <div id="reportDetails" style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <p style="margin: 0.25rem 0; color: #64748b; font-size: 0.875rem;"><strong>Type:</strong> <span id="modalDisasterType"></span></p>
                <p style="margin: 0.25rem 0; color: #64748b; font-size: 0.875rem;"><strong>Description:</strong> <span id="modalDescription"></span></p>
                <p style="margin: 0.25rem 0; color: #64748b; font-size: 0.875rem;"><strong>Location:</strong> <span id="modalLocation"></span></p>
            </div>

            <form id="respondForm" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                <input type="hidden" name="status" id="hiddenStatus">
                
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Status *</label>
                    <select id="statusSelect" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                        <option value="">Select status</option>
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="unverified">Unverified</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Action Type</label>
                    <select name="action_type" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                        <option value="">Select action type</option>
                        <option value="solved">Mark as Solved</option>
                        <option value="in_progress">In Progress</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Response Message *</label>
                    <textarea name="response_message" required rows="4" placeholder="Enter your response to this report..." style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; resize: vertical;"></textarea>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Notes (optional)</label>
                    <textarea name="notes" rows="3" placeholder="Additional notes..." style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; resize: vertical;"></textarea>
                </div>

                <button type="submit" style="background: #10b981; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; width: 100%;">
                    Submit Response
                </button>
            </form>
        </div>
    </div>

    <script>
        function showMedia(url, type) {
            console.log('Opening media:', type, url);
            const modal = document.getElementById('mediaModal');
            const content = document.getElementById('mediaContent');
            content.innerHTML = '';
            
            if (type === 'image') {
                const img = document.createElement('img');
                img.src = url;
                img.alt = 'Report Image';
                img.style.cssText = 'max-width: 90vw; max-height: 90vh; width: auto; height: auto; border-radius: 8px;';
                img.onerror = function() {
                    console.error('Failed to load image:', url);
                    content.innerHTML = `<div style="color: white; text-align: center; padding: 2rem;">Failed to load image.<br><a href="${url}" target="_blank" style="color: #3b82f6; text-decoration: underline;">Open in new tab</a></div>`;
                };
                content.appendChild(img);
            } else if (type === 'video') {
                content.innerHTML = '';
                
                // Create video player directly
                const video = document.createElement('video');
                video.controls = true;
                video.preload = 'auto';
                video.playsInline = true;
                video.src = url;
                video.style.cssText = 'max-width: 90vw; max-height: 90vh; width: 100%; height: auto; border-radius: 8px; background: #000;';
                
                video.addEventListener('loadedmetadata', function() {
                    console.log('✓ Video loaded:', url, video.videoWidth + 'x' + video.videoHeight, video.duration + 's');
                });
                
                content.appendChild(video);
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

        function openRespondModal(reportId, disasterType, description, location, currentStatus) {
            const modal = document.getElementById('respondModal');
            const form = document.getElementById('respondForm');
            const statusSelect = document.getElementById('statusSelect');
            const hiddenStatus = document.getElementById('hiddenStatus');
            
            // Update report details
            document.getElementById('modalDisasterType').textContent = disasterType;
            document.getElementById('modalDescription').textContent = description;
            document.getElementById('modalLocation').textContent = location;
            
            // Update form action
            form.action = `/admin/reports/${reportId}/respond`;
            
            // Handle status field based on current status
            if (currentStatus === 'verified' || currentStatus === 'unverified') {
                // Auto-fill and disable status field if already verified/unverified
                statusSelect.value = currentStatus;
                statusSelect.disabled = true;
                statusSelect.style.background = '#f1f5f9';
                statusSelect.style.cursor = 'not-allowed';
                // Set hidden field with the status value
                hiddenStatus.value = currentStatus;
            } else {
                // Enable status selection for pending reports
                statusSelect.disabled = false;
                statusSelect.value = '';
                statusSelect.style.background = 'white';
                statusSelect.style.cursor = 'pointer';
                hiddenStatus.value = '';
            }
            
            // Show modal
            modal.classList.add('active');
            modal.style.display = 'flex';
        }

        function closeRespondModal() {
            const modal = document.getElementById('respondModal');
            modal.classList.remove('active');
            modal.style.display = 'none';
            
            // Reset form
            document.getElementById('respondForm').reset();
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const mediaModal = document.getElementById('mediaModal');
            const respondModal = document.getElementById('respondModal');
            
            if (mediaModal) {
                mediaModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeMediaModal();
                    }
                });
            }

            if (respondModal) {
                respondModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeRespondModal();
                    }
                });
            }

            // Check if we need to auto-open a report modal from notification click
            const reportIdToOpen = sessionStorage.getItem('openReportModal');
            if (reportIdToOpen) {
                sessionStorage.removeItem('openReportModal');
                
                // Wait a bit for the page to fully load
                setTimeout(() => {
                    const reportRow = document.querySelector(`.report-row[data-id="${reportIdToOpen}"]`);
                    if (reportRow) {
                        // Scroll to the row
                        reportRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        // Open the modal
                        openReportDetailModal(reportRow);
                    }
                }, 300);
            }

            // Event delegation for view-media links (works for existing and dynamically added rows)
            document.body.addEventListener('click', function(e) {
                if (e.target.classList.contains('view-media')) {
                    e.preventDefault();
                    e.stopPropagation();
                    const url = e.target.getAttribute('data-url');
                    const type = e.target.getAttribute('data-type');
                    if (url && type) {
                        showMedia(url, type);
                    }
                }
            });

            // Event delegation for respond buttons (works for existing and dynamically added rows)
            document.body.addEventListener('click', function(e) {
                if (e.target.classList.contains('respond-btn')) {
                    e.stopPropagation();
                    const id = e.target.getAttribute('data-id');
                    const type = e.target.getAttribute('data-type');
                    const description = e.target.getAttribute('data-description');
                    const location = e.target.getAttribute('data-location');
                    const status = e.target.getAttribute('data-status');
                    if (id) {
                        openRespondModal(id, type, description, location, status);
                    }
                }
            });

            // Event delegation for report row clicks (open modal when clicking row, but not buttons/links)
            document.body.addEventListener('click', function(e) {
                // Check if click is on a report row
                const row = e.target.closest('.report-row');
                if (row && !e.target.closest('.view-media') && !e.target.closest('.respond-btn')) {
                    openReportDetailModal(row);
                }
            });

            // Sync status select with hidden input when changed
            const statusSelect = document.getElementById('statusSelect');
            const hiddenStatus = document.getElementById('hiddenStatus');
            if (statusSelect && hiddenStatus) {
                statusSelect.addEventListener('change', function() {
                    hiddenStatus.value = this.value;
                });
            }

            // Handle form submission with AJAX for success popup
            const respondForm = document.getElementById('respondForm');
            if (respondForm) {
                respondForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const actionUrl = this.action;
                    
                    // Submit form via AJAX
                    fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            // Close modal
                            closeRespondModal();
                            
                            // Show success popup
                            showSuccessPopup('Response submitted successfully!');
                            
                            // Reload page after 2 seconds to show updated data
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            alert('Failed to submit response. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error submitting response:', error);
                        alert('An error occurred. Please try again.');
                    });
                });
            }
        });

        // Show success popup
        function showSuccessPopup(message) {
            const popup = document.createElement('div');
            popup.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
                z-index: 10001;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                animation: slideInRight 0.3s ease-out;
                font-weight: 600;
                font-size: 0.9375rem;
            `;
            popup.innerHTML = `
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>${message}</span>
            `;
            
            document.body.appendChild(popup);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                popup.style.animation = 'slideInRight 0.3s ease-out reverse';
                setTimeout(() => popup.remove(), 300);
            }, 3000);
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMediaModal();
                closeRespondModal();
            }
        });

        // Filter reports by disaster type, status, and action status
        function filterAdminReports() {
            const disasterFilter = document.getElementById('adminDisasterFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const actionStatusFilter = document.getElementById('actionStatusFilter').value;
            const rows = document.querySelectorAll('.report-row');
            
            rows.forEach(row => {
                const disasterType = row.getAttribute('data-disaster-type');
                const status = row.getAttribute('data-status');
                const actionStatus = row.getAttribute('data-action-status');
                
                const matchesDisaster = disasterFilter === '' || disasterType === disasterFilter;
                const matchesStatus = statusFilter === '' || status === statusFilter;
                const matchesAction = actionStatusFilter === '' || actionStatus === actionStatusFilter;
                
                if (matchesDisaster && matchesStatus && matchesAction) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // ========================================
        // REAL-TIME NOTIFICATION SYSTEM (PUSHER + LARAVEL ECHO)
        // ========================================

        console.log('🚀 Initializing Real-Time Notification System...');
        console.log('Pusher Key:', '{{ config("broadcasting.connections.pusher.key") }}');
        console.log('Pusher Cluster:', '{{ config("broadcasting.connections.pusher.options.cluster") }}');

        // Check if Pusher credentials are set
        const pusherKey = '{{ config("broadcasting.connections.pusher.key") }}';
        if (pusherKey === 'your_app_key' || !pusherKey) {
            console.warn('⚠️ PUSHER CREDENTIALS NOT SET!');
            console.warn('Real-time notifications are disabled. To enable:');
            console.warn('1. Go to https://dashboard.pusher.com/');
            console.warn('2. Create an account and app (free)');
            console.warn('3. Copy credentials to .env file');
            console.warn('4. Run: php artisan config:clear');
            console.warn('5. Refresh this page');
        } else {
            console.log('✓ Pusher credentials found');
        }

        // Initialize Laravel Echo with Pusher
        try {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                forceTLS: true,
                encrypted: true,
            });
            console.log('✓ Laravel Echo initialized successfully');
        } catch (error) {
            console.error('❌ Failed to initialize Echo:', error);
        }

        // Initialize notification count
        let notificationCount = 0;
        const notificationBadge = document.getElementById('notificationBadge');
        let notificationsList = [];

        // Load notifications from database on page load
        function loadNotifications() {
            fetch('{{ route('admin.notifications.get') }}')
                .then(response => response.json())
                .then(data => {
                    // Get last badge reset timestamp from localStorage
                    const lastResetTime = localStorage.getItem('notificationsBadgeReset');
                    
                    notificationsList = data.notifications.map(notif => ({
                        id: notif.report_id,
                        disaster_type: notif.disaster_type,
                        user_name: notif.user_name,
                        time_ago: notif.time_ago,
                        read: lastResetTime ? true : notif.is_read, // Mark as read if badge was reset
                        notification_id: notif.id
                    }));
                    updateNotificationBadge();
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        // Load notifications on page load
        loadNotifications();

        // Listen for new report submissions on the admin-notifications channel
        if (window.Echo) {
            console.log('✓ Subscribing to admin-notifications channel...');
            
            const channel = window.Echo.channel('admin-notifications');
            
            channel.listen('.report.submitted', (event) => {
                console.log('🔔 NEW REPORT RECEIVED:', event);
                
                // Clear the badge reset flag since there's a new notification
                localStorage.removeItem('notificationsBadgeReset');
                
                // Show real-time notification popup
                showRealtimeNotification(event);
                
                // Add to notifications list
                notificationsList.unshift({
                    id: event.id,
                    disaster_type: event.disaster_type_name,
                    user_name: event.user_name,
                    time_ago: 'Just now',
                    read: false
                });
                
                // Keep only last 50 notifications
                if (notificationsList.length > 50) {
                    notificationsList = notificationsList.slice(0, 50);
                }
                
                // Update notification badge
                updateNotificationBadge();
                
                // Add new report to the table automatically
                addReportToTable(event);
                
                // Play notification sound
                playNotificationSound();
            });
            
            console.log('✓ Listening for .report.submitted events on admin-notifications channel');
        } else {
            console.warn('⚠️ Laravel Echo not loaded - Real-time notifications disabled');
        }

        /**
         * Show real-time notification popup
         */
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
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; flex-shrink: 0;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <div>
                    <strong>New Report Submitted!</strong>
                    <br>
                    <small>${report.disaster_type_name} - ${report.location || 'Location unavailable'}</small>
                </div>
            `;
            
            document.body.appendChild(notification);
            markNotificationAsShown(report.id);
            
            // Remove notification after 5 seconds with slide-out animation
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        /**
         * Add new report to the table dynamically
         */
        function addReportToTable(report) {
            const tbody = document.querySelector('.reports-table tbody');
            if (!tbody) return;
            
            // Create new table row
            const row = document.createElement('tr');
            row.className = 'report-row';
            row.setAttribute('data-id', report.id);
            row.setAttribute('data-disaster-type', report.disaster_type);
            row.setAttribute('data-status', report.status);
            row.setAttribute('data-action-status', report.action_status || '');
            row.setAttribute('data-description', report.description);
            row.setAttribute('data-user', report.user_name);
            row.setAttribute('data-location', report.location || '');
            row.setAttribute('data-date', report.formatted_date);
            row.setAttribute('data-time', report.formatted_time);
            row.setAttribute('data-image', report.image || '');
            row.setAttribute('data-video', report.video || '');
            
            // Build status badge
            let statusBadge = '';
            if (report.status === 'pending') {
                statusBadge = '<span class="badge-pending">Pending</span>';
            } else if (report.status === 'verified') {
                statusBadge = '<span class="badge-verified">Verified</span>';
            } else if (report.status === 'unverified') {
                statusBadge = '<span class="badge-unverified">Unverified</span>';
            }
            
            // Build action status badge
            let actionBadge = '<span style="color: #94a3b8; font-size: 0.875rem;">-</span>';
            if (report.action_status === 'solved') {
                actionBadge = '<span class="badge-solved">Solved</span>';
            } else if (report.action_status === 'in_progress') {
                actionBadge = '<span class="badge-in-progress">In Progress</span>';
            }
            
            // Build image cell
            let imageCell = '<span style="color: #94a3b8;">N/A</span>';
            if (report.image) {
                imageCell = `<a href="javascript:void(0)" class="view-link view-media" data-url="${report.image}" data-type="image">View</a>`;
            }
            
            // Build video cell
            let videoCell = '<span style="color: #94a3b8;">N/A</span>';
            if (report.video) {
                videoCell = `<a href="javascript:void(0)" class="view-link view-media" data-url="${report.video}" data-type="video">View</a>`;
            }
            
            // Populate row HTML
            row.innerHTML = `
                <td>
                    <span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                        ${report.disaster_type_name}
                    </span>
                </td>
                <td style="max-width: 300px;">${report.description.substring(0, 100)}${report.description.length > 100 ? '...' : ''}</td>
                <td>${report.formatted_date}</td>
                <td>${report.formatted_time}</td>
                <td>${report.user_name}</td>
                <td>${report.location || 'N/A'}</td>
                <td>${statusBadge}</td>
                <td>${actionBadge}</td>
                <td style="display: none;">${imageCell}</td>
                <td style="display: none;">${videoCell}</td>
                <td style="display: none;">
                    <button class="respond-btn" data-id="${report.id}" data-type="${report.disaster_type}" data-description="${report.description.replace(/"/g, '&quot;')}" data-location="${report.location || 'N/A'}" data-status="${report.status}" style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none;">
                        📝 Respond
                    </button>
                </td>
            `;
            
            // Add highlight effect for new row
            row.style.backgroundColor = '#fef3c7';
            
            // Insert at the top of the table
            tbody.insertBefore(row, tbody.firstChild);
            
            // Remove highlight after 3 seconds
            setTimeout(() => {
                row.style.transition = 'background-color 1s ease';
                row.style.backgroundColor = '';
            }, 3000);
        }

        /**
         * Play notification sound
         */
        function playNotificationSound() {
            try {
                // Simple notification beep using Web Audio API
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

        // Notification dropdown management
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
                <div class="notification-item ${notif.read ? '' : 'unread'}\" onclick="viewReport(${notif.id})">
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
            
            // Find and highlight the report row
            const rows = document.querySelectorAll('.report-row');
            rows.forEach(row => {
                const respondBtn = row.querySelector('.respond-btn');
                if (respondBtn && respondBtn.getAttribute('data-id') == reportId) {
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    row.style.backgroundColor = '#fef3c7';
                    setTimeout(() => {
                        row.style.transition = 'background-color 1s ease';
                        row.style.backgroundColor = '';
                    }, 2000);
                }
            });
            
            // Update notification count
            updateNotificationBadge();
        }

        function updateNotificationBadge() {
            const unreadCount = notificationsList.filter(n => !n.read).length;
            notificationCount = unreadCount;
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

        function toggleNotificationDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
            
            if (dropdown.classList.contains('show')) {
                populateNotifications();
            }
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
            if (notif) {
                notif.read = true;
            }
            
            // Close dropdown
            document.getElementById('notificationDropdown').classList.remove('show');
            
            // Find and highlight the report row
            const rows = document.querySelectorAll('.report-row');
            rows.forEach(row => {
                const viewButtons = row.querySelectorAll('[onclick]');
                viewButtons.forEach(btn => {
                    const onclick = btn.getAttribute('onclick');
                    if (onclick && onclick.includes(reportId)) {
                        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        row.style.backgroundColor = '#fef3c7';
                        setTimeout(() => {
                            row.style.transition = 'background-color 1s ease';
                            row.style.backgroundColor = '';
                        }, 2000);
                    }
                });
            });
            
            // Update notification count
            updateNotificationBadge();
        }

        function updateNotificationBadge() {
            const unreadCount = notificationsList.filter(n => !n.read).length;
            notificationCount = unreadCount;
            notificationBadge.textContent = unreadCount;
            if (unreadCount > 0) {
                notificationBadge.style.display = 'block';
            } else {
                notificationBadge.style.display = 'none';
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationDropdown');
            const bell = document.querySelector('.notification-bell');
            if (dropdown && !bell.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Log Echo connection status
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            window.Echo.connector.pusher.connection.bind('connected', function() {
                console.log('✓ Pusher connected successfully');
            });
            
            window.Echo.connector.pusher.connection.bind('disconnected', function() {
                console.warn('⚠️ Pusher disconnected');
            });
            
            window.Echo.connector.pusher.connection.bind('error', function(err) {
                console.error('✗ Pusher connection error:', err);
            });
        }

        // ========================================
        // POLLING FALLBACK (when Pusher not configured)
        // ========================================
        
        let lastReportId = null;
        let pollingInterval = null;
        
        // Get the highest report ID currently displayed
        function getLatestReportId() {
            const rows = document.querySelectorAll('.report-row');
            if (rows.length === 0) return 0;
            
            const ids = Array.from(rows).map(row => {
                const respondBtn = row.querySelector('.respond-btn');
                return respondBtn ? parseInt(respondBtn.getAttribute('data-id')) : 0;
            });
            
            return Math.max(...ids);
        }
        
        // Check for new reports via API
        async function checkForNewReports() {
            try {
                const response = await fetch('{{ route("admin.reports.check-new") }}?since=' + lastReportId);
                const data = await response.json();
                
                if (data.new_reports && data.new_reports.length > 0) {
                    console.log('📥 Found ' + data.new_reports.length + ' new report(s)');
                    
                    data.new_reports.forEach(report => {
                        // Show notification
                        showRealtimeNotification(report);
                        
                        // Update badge
                        notificationCount++;
                        notificationBadge.textContent = notificationCount;
                        notificationBadge.classList.add('show');
                        
                        // Add to table
                        addReportToTable(report);
                        
                        // Play sound
                        playNotificationSound();
                    });
                    
                    // Update last report ID
                    lastReportId = getLatestReportId();
                }
            } catch (error) {
                console.error('Error checking for new reports:', error);
            }
        }
        
        // Start polling if Pusher not configured
        if (pusherKey === 'your_app_key' || !pusherKey) {
            console.log('🔄 Starting polling fallback (checking every 3 seconds)');
            lastReportId = getLatestReportId();
            
            // Check for new reports every 3 seconds
            pollingInterval = setInterval(checkForNewReports, 3000);
        } else {
            console.log('✓ Using Pusher real-time notifications (no polling needed)');
        }

        // Report Detail Modal Functions
        function openReportDetailModal(row) {
            const reportData = {
                id: row.getAttribute('data-id'),
                disasterType: row.getAttribute('data-disaster-type'),
                description: row.getAttribute('data-description'),
                user: row.getAttribute('data-user'),
                location: row.getAttribute('data-location'),
                date: row.getAttribute('data-date'),
                time: row.getAttribute('data-time'),
                status: row.getAttribute('data-status'),
                actionStatus: row.getAttribute('data-action-status'),
                imageUrl: row.getAttribute('data-image'),
                videoUrl: row.getAttribute('data-video')
            };

            // Get status and action status badges from the row
            const cells = row.querySelectorAll('td');
            const statusBadge = cells[6].innerHTML;
            const actionBadge = cells[7].innerHTML;

            // Populate modal content
            const modalContent = document.getElementById('reportDetailContent');
            modalContent.innerHTML = `
                <div class="report-detail-row">
                    <div class="report-detail-label">Disaster Type</div>
                    <div class="report-detail-value">
                        <span style="background: #dbeafe; color: #1e40af; padding: 0.5rem 1rem; border-radius: 12px; font-weight: 600;">
                            ${reportData.disasterType.charAt(0).toUpperCase() + reportData.disasterType.slice(1)}
                        </span>
                    </div>
                </div>
                
                <div class="report-detail-row">
                    <div class="report-detail-label">Description</div>
                    <div class="report-detail-value">${reportData.description}</div>
                </div>
                
                <div class="report-detail-row">
                    <div class="report-detail-label">Reported By</div>
                    <div class="report-detail-value">${reportData.user}</div>
                </div>
                
                <div class="report-detail-row">
                    <div class="report-detail-label">Location</div>
                    <div class="report-detail-value">${reportData.location}</div>
                </div>
                
                <div class="report-detail-row">
                    <div class="report-detail-label">Date & Time</div>
                    <div class="report-detail-value">${reportData.date} at ${reportData.time}</div>
                </div>
                
                <div class="report-detail-row">
                    <div class="report-detail-label">Status</div>
                    <div class="report-detail-value">${statusBadge}</div>
                </div>
                
                <div class="report-detail-row">
                    <div class="report-detail-label">Action Status</div>
                    <div class="report-detail-value">${actionBadge}</div>
                </div>
                
                ${reportData.imageUrl ? `
                    <div class="report-detail-row">
                        <div class="report-detail-label">Image</div>
                        <div class="report-media-preview">
                            <img src="${reportData.imageUrl}" alt="Report Image" onclick="openImageFullscreen('${reportData.imageUrl}')" style="cursor: pointer;">
                        </div>
                    </div>
                ` : ''}
                
                ${reportData.videoUrl ? `
                    <div class="report-detail-row">
                        <div class="report-detail-label">Video</div>
                        <div class="report-media-preview">
                            <video controls src="${reportData.videoUrl}"></video>
                        </div>
                    </div>
                ` : ''}
                
                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <button onclick="closeReportDetailModal(); openRespondModal(${reportData.id}, '${reportData.disasterType}', \`${reportData.description.replace(/`/g, '\\`')}\`, '${reportData.location}', '${reportData.status}');" style="flex: 1; background: #10b981; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; border: none;">
                        📝 Respond to Report
                    </button>
                    <button onclick="closeReportDetailModal()" style="flex: 1; background: #6b7280; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; border: none;">
                        Close
                    </button>
                </div>
            `;

            // Show modal
            document.getElementById('reportDetailModal').classList.add('show');
        }

        function closeReportDetailModal(event) {
            if (event && event.target !== event.currentTarget) return;
            
            // Stop any playing videos in the modal
            const modalContent = document.getElementById('reportDetailContent');
            const videos = modalContent.querySelectorAll('video');
            videos.forEach(video => {
                video.pause();
                video.currentTime = 0;
            });
            
            document.getElementById('reportDetailModal').classList.remove('show');
        }

        // Track if report modal was open before viewing image
        let reportModalWasOpen = false;

        function openImageFullscreen(imageUrl) {
            // Remember that report modal was open
            reportModalWasOpen = document.getElementById('reportDetailModal').classList.contains('show');
            
            // Hide report modal temporarily
            if (reportModalWasOpen) {
                document.getElementById('reportDetailModal').classList.remove('show');
            }
            
            // Show image in media modal
            showMedia(imageUrl, 'image');
        }

        // Override closeMediaModal to restore report modal
        const originalCloseMediaModal = closeMediaModal;
        closeMediaModal = function() {
            originalCloseMediaModal();
            
            // Restore report modal if it was open
            if (reportModalWasOpen) {
                setTimeout(() => {
                    document.getElementById('reportDetailModal').classList.add('show');
                    reportModalWasOpen = false;
                }, 100);
            }
        };
    </script>
</body>
</html>
