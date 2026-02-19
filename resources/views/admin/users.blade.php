<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Users - Admin Dashboard</title>
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

        .users-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .users-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .users-header h2 {
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

        .manage-menu button:hover {
            background: #f8fafc !important;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table thead {
            background: #f8fafc;
        }

        .users-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #475569;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .users-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .users-table tbody tr:hover {
            background: #f8fafc;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #d1fae5;
            color: #065f46;
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
                    <a href="{{ route('admin.users') }}" class="nav-link active">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.barangay') }}" class="nav-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Barangay
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
            <h1>Users Management</h1>
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

        <div class="users-container">
            <div class="users-header">
                <h2>All Users</h2>
                <button class="add-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add User
                </button>
            </div>

            @if($users->count() > 0)
            <table class="users-table">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>
                            EMAIL
                            <button onclick="toggleEmailMasking()" id="emailToggleBtn" title="Show/Hide emails" style="margin-left: 0.5rem; background: transparent; color: #6366f1; border: none; padding: 0.25rem; cursor: pointer; font-size: 1rem; display: inline-flex; align-items: center; vertical-align: middle;">
                                <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg id="eyeOffIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </th>
                        <th>REGISTERED</th>
                        <th>STATUS</th>
                        <th style="text-align: center;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>
                            <span class="email-masked">
                                @php
                                    $email = $user->email;
                                    $atPos = strpos($email, '@');
                                    if ($atPos !== false) {
                                        $localPart = substr($email, 0, $atPos);
                                        $domain = substr($email, $atPos);
                                        $visibleChars = min(3, strlen($localPart));
                                        $maskedEmail = substr($localPart, 0, $visibleChars) . str_repeat('*', strlen($localPart) - $visibleChars) . $domain;
                                        echo $maskedEmail;
                                    } else {
                                        echo $email;
                                    }
                                @endphp
                            </span>
                            <span class="email-full" style="display: none;">{{ $user->email }}</span>
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($user->isBlocked())
                                <span class="status-badge" style="background-color: #fee2e2; color: #dc2626; cursor: help;" title="Reason: {{ $user->block_reason }}">Blocked</span>
                            @else
                                <span class="status-badge" style="background-color: #dcfce7; color: #16a34a;">Active</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="position: relative; display: inline-block;">
                                <button onclick="toggleManageMenu({{ $user->id }})" style="background: #3b82f6; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                    Manage
                                </button>
                                <div id="manageMenu{{ $user->id }}" class="manage-menu" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 0.25rem; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); z-index: 1000; min-width: 150px; overflow: hidden;">
                                    <button onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}'); toggleManageMenu({{ $user->id }})" style="width: 100%; background: white; border: none; padding: 0.75rem 1rem; cursor: pointer; font-size: 0.875rem; text-align: left; display: flex; align-items: center; gap: 0.5rem; color: #334155; border-bottom: 1px solid #f1f5f9;">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </button>
                                    @if($user->isBlocked())
                                    <form method="POST" action="{{ route('admin.users.block', $user->id) }}" onsubmit="return confirm('Are you sure you want to unblock this user?');" style="margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="unblock">
                                        <button type="submit" style="width: 100%; background: white; border: none; padding: 0.75rem 1rem; cursor: pointer; font-size: 0.875rem; text-align: left; display: flex; align-items: center; gap: 0.5rem; color: #16a34a;">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Unblock
                                        </button>
                                    </form>
                                    @else
                                    <button onclick="openBlockModal({{ $user->id }}, '{{ $user->name }}'); toggleManageMenu({{ $user->id }})" style="width: 100%; background: white; border: none; padding: 0.75rem 1rem; cursor: pointer; font-size: 0.875rem; text-align: left; display: flex; align-items: center; gap: 0.5rem; color: #ef4444;">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        Block
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <p>No users registered yet</p>
            </div>
            @endif
        </div>
    </div>

    <script>
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

        // Add User Modal Functions
        function openAddUserModal() {
            document.getElementById('addUserModal').style.display = 'flex';
            // Clear previous errors
            document.getElementById('addUserErrors').style.display = 'none';
            document.getElementById('addUserErrorList').innerHTML = '';
        }

        function closeAddUserModal() {
            document.getElementById('addUserModal').style.display = 'none';
            document.getElementById('addUserForm').reset();
            document.getElementById('addUserErrors').style.display = 'none';
            document.getElementById('addUserErrorList').innerHTML = '';
        }

        // Toggle Manage Menu
        function toggleManageMenu(userId) {
            const menu = document.getElementById('manageMenu' + userId);
            const allMenus = document.querySelectorAll('.manage-menu');
            
            // Close all other menus
            allMenus.forEach(m => {
                if (m !== menu) {
                    m.style.display = 'none';
                }
            });
            
            // Toggle current menu
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }

        // Toggle Email Masking
        function toggleEmailMasking() {
            const maskedEmails = document.querySelectorAll('.email-masked');
            const fullEmails = document.querySelectorAll('.email-full');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            
            maskedEmails.forEach(masked => {
                if (masked.style.display === 'none') {
                    masked.style.display = '';
                } else {
                    masked.style.display = 'none';
                }
            });
            
            fullEmails.forEach(full => {
                if (full.style.display === 'none') {
                    full.style.display = '';
                } else {
                    full.style.display = 'none';
                }
            });
            
            // Toggle icon
            if (eyeIcon.style.display === 'none') {
                eyeIcon.style.display = '';
                eyeOffIcon.style.display = 'none';
            } else {
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = '';
            }
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.manage-menu') && !event.target.closest('button')) {
                document.querySelectorAll('.manage-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
            }
        });

        // Edit User Modal Functions
        function editUser(userId, userName, userEmail) {
            document.getElementById('editUserModal').style.display = 'flex';
            document.getElementById('editUserId').value = userId;
            document.getElementById('editUserName').value = userName;
            document.getElementById('editUserEmail').value = userEmail;
            document.getElementById('editUserForm').action = `/admin/users/${userId}`;
            
            // Clear previous errors
            document.getElementById('editUserErrors').style.display = 'none';
            document.getElementById('editUserErrorList').innerHTML = '';
        }

        function closeEditUserModal() {
            document.getElementById('editUserModal').style.display = 'none';
            document.getElementById('editUserForm').reset();
            document.getElementById('editUserErrors').style.display = 'none';
            document.getElementById('editUserErrorList').innerHTML = '';
        }

        // Block User Modal Functions
        function openBlockModal(userId, userName) {
            document.getElementById('blockUserModal').style.display = 'flex';
            document.getElementById('blockUserName').textContent = userName;
            document.getElementById('blockUserForm').action = `/admin/users/${userId}/block`;
        }

        function closeBlockModal() {
            document.getElementById('blockUserModal').style.display = 'none';
            document.getElementById('blockUserForm').reset();
        }

        // Add AJAX form submission for Edit User
        document.addEventListener('DOMContentLoaded', function() {
            const editForm = document.getElementById('editUserForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Clear previous errors
                    document.getElementById('editUserErrors').style.display = 'none';
                    document.getElementById('editUserErrorList').innerHTML = '';
                    
                    const password = this.querySelector('input[name="password"]').value;
                    const passwordConfirm = this.querySelector('input[name="password_confirmation"]').value;
                    const errors = [];
                    
                    // Client-side validation
                    if (password || passwordConfirm) {
                        if (!password || !passwordConfirm) {
                            errors.push('Both password fields are required if changing password');
                        } else {
                            if (password.length < 8) {
                                errors.push('Password must be at least 8 characters long');
                            }
                            if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                                errors.push('Password must contain at least one special character (!@#$%^&*(),.?":{}|<>)');
                            }
                            if (password !== passwordConfirm) {
                                errors.push('Passwords do not match');
                            }
                        }
                    }
                    
                    if (errors.length > 0) {
                        const errorList = document.getElementById('editUserErrorList');
                        errorList.innerHTML = errors.map(error => `<li>${error}</li>`).join('');
                        document.getElementById('editUserErrors').style.display = 'block';
                        return false;
                    }
                    
                    // Submit via AJAX
                    const formData = new FormData(this);
                    const userId = document.getElementById('editUserId').value;
                    
                    fetch(`/admin/users/${userId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.errors) {
                            // Show server-side validation errors
                            const errorList = document.getElementById('editUserErrorList');
                            const errorMessages = Object.values(data.errors).flat();
                            errorList.innerHTML = errorMessages.map(error => `<li>${error}</li>`).join('');
                            document.getElementById('editUserErrors').style.display = 'block';
                        } else if (data.success) {
                            // Success - reload page to show updated data
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        // Show error message
                        const errorList = document.getElementById('editUserErrorList');
                        errorList.innerHTML = '<li>An error occurred. Please try again.</li>';
                        document.getElementById('editUserErrors').style.display = 'block';
                    });
                    
                    return false;
                });
            }
        });
        
        // Auto-reopen Edit User modal if there are server-side validation errors
        @if($errors->any() && old('_form') === 'edit_user')
            document.getElementById('editUserModal').style.display = 'flex';
            document.getElementById('editUserId').value = '{{ old('user_id') }}';
            document.getElementById('editUserName').value = '{{ old('name') }}';
            document.getElementById('editUserEmail').value = '{{ old('email') }}';
            document.getElementById('editUserForm').action = `/admin/users/{{ old('user_id') }}`;
        @endif
        
        // Add AJAX form submission for Add User
        document.addEventListener('DOMContentLoaded', function() {
            const addForm = document.getElementById('addUserForm');
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Clear previous errors
                    document.getElementById('addUserErrors').style.display = 'none';
                    document.getElementById('addUserErrorList').innerHTML = '';
                    
                    // Submit via AJAX
                    const formData = new FormData(this);
                    
                    fetch('{{ route('admin.users.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.errors) {
                            // Show server-side validation errors
                            const errorList = document.getElementById('addUserErrorList');
                            const errorMessages = Object.values(data.errors).flat();
                            errorList.innerHTML = errorMessages.map(error => `<li>${error}</li>`).join('');
                            document.getElementById('addUserErrors').style.display = 'block';
                        } else if (data.success) {
                            // Success - reload page to show new user
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        // Show error message
                        const errorList = document.getElementById('addUserErrorList');
                        errorList.innerHTML = '<li>An error occurred. Please try again.</li>';
                        document.getElementById('addUserErrors').style.display = 'block';
                    });
                    
                    return false;
                });
            }
        });
        
        // Add event listener to Add User button
        document.querySelector('.add-btn').addEventListener('click', openAddUserModal);
    </script>

    <!-- Add User Modal -->
    <div id="addUserModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 2rem; max-width: 500px; width: 90%; position: relative;">
            <button onclick="closeAddUserModal()" style="position: absolute; top: 1rem; right: 1rem; background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; color: #64748b;">×</button>
            
            <h2 style="font-size: 1.5rem; color: #1e293b; margin-bottom: 1.5rem;">Add New User</h2>
            
            <!-- Error Messages -->
            <div id="addUserErrors" style="display: none; background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <ul id="addUserErrorList" style="margin: 0; padding-left: 1.5rem;"></ul>
            </div>
            
            <form id="addUserForm" method="POST" action="{{ route('admin.users.store') }}" style="display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Name *</label>
                    <input type="text" name="name" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Email *</label>
                    <input type="email" name="email" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Password *</label>
                    <input type="password" name="password" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                    <small style="color: #64748b; font-size: 0.75rem;">Minimum 8 characters</small>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Confirm Password *</label>
                    <input type="password" name="password_confirmation" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="button" onclick="closeAddUserModal()" style="flex: 1; background: #f1f5f9; color: #64748b; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; border: none;">
                        Cancel
                    </button>
                    <button type="submit" style="flex: 1; background: #10b981; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; border: none;">
                        Add User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 2rem; max-width: 500px; width: 90%; position: relative;">
            <button onclick="closeEditUserModal()" style="position: absolute; top: 1rem; right: 1rem; background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; color: #64748b;">×</button>
            
            <h2 style="font-size: 1.5rem; color: #1e293b; margin-bottom: 1.5rem;">Edit User</h2>
            
            <!-- Error Messages -->
            <div id="editUserErrors" style="display: none; background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <ul id="editUserErrorList" style="margin: 0; padding-left: 1.5rem;"></ul>
            </div>
            
            @if($errors->any() && old('_form') === 'edit_user')
            <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <form id="editUserForm" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="user_id">
                <input type="hidden" name="_form" value="edit_user">
                
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Name *</label>
                    <input type="text" id="editUserName" name="name" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Email *</label>
                    <input type="email" id="editUserEmail" name="email" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">New Password</label>
                    <input type="password" name="password" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                    <small style="color: #64748b; font-size: 0.75rem;">Leave blank to keep current password</small>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Confirm New Password</label>
                    <input type="password" name="password_confirmation" minlength="8" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="button" onclick="closeEditUserModal()" style="flex: 1; background: #f1f5f9; color: #64748b; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; border: none;">
                        Cancel
                    </button>
                    <button type="submit" style="flex: 1; background: #3b82f6; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; border: none;">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Block User Modal -->
    <div id="blockUserModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
            <div style="padding: 1.5rem; border-bottom: 1px solid #e2e8f0;">
                <h3 style="margin: 0; color: #1e293b; font-size: 1.25rem; font-weight: 700;">Block User</h3>
            </div>
            
            <form method="POST" id="blockUserForm" style="padding: 1.5rem;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="block">
                
                <div style="margin-bottom: 1.5rem;">
                    <p style="color: #64748b; margin-bottom: 1rem;">You are about to block: <strong id="blockUserName" style="color: #1e293b;"></strong></p>
                    
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Select Reason for Blocking</label>
                    <select name="block_reason" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; background-color: white; margin-bottom: 1rem;">
                        <option value="">Choose a reason...</option>
                        <option value="Spam reports">Spam reports</option>
                        <option value="Always invalid reports">Always invalid reports</option>
                        <option value="Abusive behavior">Abusive behavior</option>
                        <option value="Fake information">Fake information</option>
                        <option value="Other">Other</option>
                    </select>

                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155;">Block Duration</label>
                    <select name="block_duration" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; background-color: white;">
                        <option value="">Select duration...</option>
                        <option value="1">1 Day</option>
                        <option value="3">3 Days</option>
                        <option value="7">1 Week</option>
                        <option value="14">2 Weeks</option>
                        <option value="30">1 Month</option>
                        <option value="permanent">Permanent</option>
                    </select>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" onclick="closeBlockModal()" style="flex: 1; background: #f1f5f9; color: #64748b; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; border: none;">
                        Cancel
                    </button>
                    <button type="submit" style="flex: 1; background: #ef4444; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; border: none;">
                        Block User
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
