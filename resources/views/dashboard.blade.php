<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Dashboard - Davao City Reports</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Pusher & Laravel Echo CDN -->
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
            min-height: 100vh;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .navbar-brand h1 {
            font-size: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }

        .navbar-brand p {
            font-size: 0.875rem;
            color: #64748b;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-name {
            font-weight: 600;
            color: #1e293b;
        }

        .logout-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .welcome-text {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
        }

        .welcome-icon {
            width: 32px;
            height: 32px;
            background: #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .submit-report-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .submit-report-btn:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .filter-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            color: #475569;
        }

        .filter-select {
            padding: 0.5rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: white;
            color: #334155;
            font-size: 0.875rem;
            cursor: pointer;
            min-width: 200px;
        }

        .filter-select:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .map-heading {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .map-icon {
            width: 24px;
            height: 24px;
        }

        .map-container {
            background: white;
            border-radius: 16px;
            padding: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
            height: 600px;
            min-height: 500px;
        }

        .reports-section {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .reports-heading {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2rem;
        }

        .reports-icon {
            width: 24px;
            height: 24px;
        }

        .empty-reports {
            text-align: center;
            padding: 3rem 2rem;
            color: #94a3b8;
        }

        .empty-reports svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            opacity: 0.5;
            stroke: currentColor;
        }

        .empty-reports p {
            font-size: 1rem;
            color: #64748b;
        }

        .reports-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
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
            border-bottom: 2px solid #e2e8f0;
        }

        .reports-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.875rem;
        }

        .reports-table tbody tr:hover {
            background: #f8fafc;
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

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h2 {
            font-size: 1.5rem;
            color: #1e293b;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748b;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close-modal:hover {
            background: #f1f5f9;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #334155;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: border-color 0.2s;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-file {
            padding: 0.5rem;
        }

        .location-input-wrapper {
            position: relative;
            display: flex;
            gap: 0.5rem;
        }

        .location-input-wrapper .form-input {
            flex: 1;
        }

        .gps-button {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .gps-button:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
            transform: translateY(-1px);
        }

        .gps-button:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        .gps-button svg {
            width: 20px;
            height: 20px;
        }

        .btn-submit {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            width: 100%;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #2563eb;
        }

        .success-message {
            background: #d1fae5;
            color: #065f46;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .warning-message {
            background: #fef3c7;
            color: #92400e;
            padding: 0.75rem;
            border-radius: 8px;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            display: none;
            border-left: 4px solid #f59e0b;
        }

        .warning-message.show {
            display: block;
        }

        .file-size-info {
            background: #dbeafe;
            color: #1e40af;
            padding: 0.75rem;
            border-radius: 8px;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            display: none;
            border-left: 4px solid #3b82f6;
        }

        .file-size-info.show {
            display: block;
        }

        .location-suggestions {
            position: absolute;
            background: white;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            margin-top: 0.25rem;
            max-height: 200px;
            overflow-y: auto;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
        }

        .location-suggestion-item {
            padding: 0.75rem;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
        }

        .location-suggestion-item:hover {
            background: #f8fafc;
        }

        .location-suggestion-item:last-child {
            border-bottom: none;
        }

        #map {
            width: 100%;
            height: 100%;
            border-radius: 16px;
        }

        .map-controls {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            display: flex;
            gap: 0.5rem;
        }

        .map-control-btn {
            background: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .map-control-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .map-control-btn.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .map-legend {
            position: absolute;
            bottom: 2rem;
            left: 1rem;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        .map-legend h3 {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
            font-size: 0.75rem;
            color: #64748b;
        }

        .legend-marker {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .marker-pending { background: #f59e0b; }
        .marker-in-progress { background: #3b82f6; }
        .marker-resolved { background: #10b981; }
        .marker-mine { background: #8b5cf6; }

        .welcome-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .welcome-card h2 {
            font-size: 2rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .welcome-card p {
            color: #64748b;
            font-size: 1.125rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon svg {
            width: 24px;
            height: 24px;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .stat-icon.yellow {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .stat-icon.purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .actions-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .actions-card h3 {
            font-size: 1.5rem;
            color: #1e293b;
            margin-bottom: 1.5rem;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .action-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .action-btn svg {
            width: 20px;
            height: 20px;
        }

        .action-btn.secondary {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .action-btn.secondary:hover {
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
        }

        .reports-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .reports-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .reports-header h3 {
            font-size: 1.5rem;
            color: #1e293b;
        }

        .filter-btn {
            background: #f1f5f9;
            color: #475569;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            background: #e2e8f0;
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
            white-space: nowrap;
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

        .status-in-progress {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-resolved {
            background: #d1fae5;
            color: #065f46;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #94a3b8;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 1.125rem;
        }

        /* Notification Bell Styles */
        .notification-bell {
            position: relative;
            background: white;
            border: none;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .notification-bell:hover {
            background: #f8fafc;
            transform: scale(1.05);
        }

        .notification-bell svg {
            width: 22px;
            height: 22px;
            color: #64748b;
        }

        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: white;
            font-size: 0.625rem;
            font-weight: 700;
            padding: 0.125rem 0.375rem;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
            display: none;
            animation: bounceIn 0.3s ease-out;
        }

        .notification-badge.show {
            display: block;
        }

        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        /* Real-time Notification Popup */
        .realtime-notification {
            position: fixed;
            top: 80px;
            right: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 1rem 1.5rem;
            min-width: 350px;
            max-width: 400px;
            z-index: 10000;
            animation: slideInRight 0.4s ease-out;
            border-left: 4px solid #3b82f6;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .realtime-notification h4 {
            margin: 0 0 0.5rem 0;
            color: #1e293b;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .realtime-notification p {
            margin: 0;
            color: #64748b;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .realtime-notification .close-notification {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 1.25rem;
            line-height: 1;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .realtime-notification .close-notification:hover {
            color: #64748b;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-brand">
            <div>
                <h1>Davao City Reports</h1>
                <p>Citizen Portal</p>
            </div>
        </div>
        <div class="user-info">
            <button class="notification-bell" onclick="toggleNotifications()" title="Notifications">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="notification-badge" id="userNotificationBadge">0</span>
            </button>
            <span class="user-name">{{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <!-- Success Alert Popup -->
        @if(session('success'))
        <div id="successAlert" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 10000; min-width: 400px; max-width: 500px; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3); padding: 1.5rem; animation: slideDown 0.3s ease-out;">
            <div style="margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1.125rem; color: #1e293b; font-weight: 600;">Success!</h3>
            </div>
            <div style="margin-bottom: 1.5rem; color: #64748b; font-size: 0.9375rem; line-height: 1.5;">
                {{ session('success') }}
            </div>
            <div style="text-align: right;">
                <button onclick="closeSuccessAlert()" style="background: #3b82f6; color: white; border: none; padding: 0.5rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 0.875rem; min-width: 80px;">
                    OK
                </button>
            </div>
        </div>
        <div id="successAlertOverlay" onclick="closeSuccessAlert()" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); z-index: 9999;"></div>
        <style>
            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateX(-50%) translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0);
                }
            }
        </style>
        <script>
            function closeSuccessAlert() {
                document.getElementById('successAlert').style.display = 'none';
                document.getElementById('successAlertOverlay').style.display = 'none';
            }
        </script>
        @endif

        <!-- Header Section -->
        <div class="header-section">
            <div class="welcome-text">
                <div class="welcome-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                Welcome, {{ Auth::user()->name }}!
            </div>
            <button class="submit-report-btn" onclick="openReportModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Submit New Report
            </button>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-label">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter by Disaster Type:
            </div>
            <select class="filter-select" id="disasterFilter">
                <option value="">All Disasters</option>
                @foreach($disasterTypes as $type)
                    <option value="{{ $type->name }}">{{ $type->icon }} {{ $type->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Map Heading -->
        <div class="map-heading">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="map-icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            Reports Map
        </div>

        <!-- Map Container -->
        <div class="map-container">
            <div id="map"></div>
        </div>

        <!-- Your Recent Reports Section -->
        <div class="reports-section">
            <div class="reports-heading">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="reports-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Recent Reports
            </div>
            
            <!-- Reports Table -->
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>
                            Type of Disaster
                            <br>
                            <select class="disaster-type-select" id="userReportsFilter" onchange="filterUserReports()">
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
                            <select class="disaster-type-select" id="userStatusFilter" onchange="filterUserReports()">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                                <option value="unverified">Unverified</option>
                            </select>
                        </th>
                        <th>
                            Action Status
                            <br>
                            <select class="disaster-type-select" id="userActionStatusFilter" onchange="filterUserReports()">
                                <option value="">All</option>
                                <option value="solved">Solved</option>
                                <option value="in_progress">In Progress</option>
                            </select>
                        </th>
                        <th>Image</th>
                        <th>Video</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr class="user-report-row" data-disaster-type="{{ $report->disaster_type }}" data-status="{{ $report->status }}" data-action-status="{{ $report->solved ? 'solved' : ($report->responses()->where('action_type', 'in_progress')->exists() ? 'in_progress' : '') }}">
                        <td>{{ $report->disaster_type }}</td>
                        <td>{{ Str::limit($report->description, 50) }}</td>
                        <td>{{ $report->created_at->format('M d, Y') }}</td>
                        <td>{{ $report->created_at->format('h:i A') }}</td>
                        <td>{{ $report->user->name }}</td>
                        <td>{{ $report->location ?? $report->latitude . ', ' . $report->longitude }}</td>
                        <td>
                            @if($report->status === 'pending')
                                <span class="status-badge status-pending">Pending</span>
                            @elseif($report->status === 'verified')
                                <span class="status-badge" style="background: #d1fae5; color: #065f46;">Verified</span>
                            @else
                                <span class="status-badge" style="background: #fee2e2; color: #991b1b;">❌ Unverified</span>
                            @endif
                        </td>
                        <td>
                            @if($report->solved)
                                <span class="status-badge" style="background: #d1fae5; color: #065f46;">Solved</span>
                            @elseif($report->responses()->where('action_type', 'in_progress')->exists())
                                <span class="status-badge status-in-progress"> In Progress</span>
                            @else
                                <span style="color: #94a3b8; font-size: 0.875rem;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($report->image)
                                <a href="javascript:void(0)" onclick="showMedia('{{ asset('storage/' . $report->image) }}', 'image')" class="view-link">View</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($report->video)
                                <a href="javascript:void(0)" onclick="showMedia('{{ asset('storage/' . $report->video) }}', 'video')" class="view-link">View</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <!-- Empty state row -->
                    <tr>
                        <td colspan="10">
                            <div class="empty-reports">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p>You haven't submitted any reports yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Report Modal -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Submit New Report</h2>
                <button class="close-modal" onclick="closeReportModal()">&times;</button>
            </div>

            @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Type of Disaster *</label>
                    <select name="disaster_type" class="form-select" required>
                        <option value="">Select disaster type</option>
                        @foreach($disasterTypes as $type)
                            <option value="{{ $type->name }}">{{ $type->icon }} {{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('disaster_type')
                        <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Description *</label>
                    <textarea name="description" class="form-textarea" placeholder="Describe the situation..." required></textarea>
                    @error('description')
                        <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Location (type address or click on map) *</label>
                    <div class="location-input-wrapper">
                        <input type="text" name="location" id="locationInput" class="form-input" placeholder="e.g., Davao City Hall, Roxas Avenue" autocomplete="off">
                        <button type="button" id="gpsButton" class="gps-button" onclick="getCurrentLocation()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Use GPS
                        </button>
                    </div>
                    <input type="hidden" name="latitude" id="latitudeInput" required>
                    <input type="hidden" name="longitude" id="longitudeInput" required>
                    <small style="color: #64748b; font-size: 0.75rem;">Current: <span id="currentCoords">Not selected</span></small>
                    @error('latitude')
                        <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Upload Image (optional)</label>
                    <input type="file" name="image" class="form-input form-file" accept="image/*">
                    <small style="color: #64748b; font-size: 0.75rem;">Max file size: 10MB</small>
                    @error('image')
                        <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Upload Video (optional)</label>
                    <input type="file" name="video" id="videoInput" class="form-input form-file" accept="video/*">
                    <small style="color: #64748b; font-size: 0.75rem;">
                        Accepted formats: MP4, AVI, MOV, WMV | Max size: 200MB
                    </small>
                    <div id="videoSizeInfo" class="file-size-info">
                        <strong>📹</strong> <span id="videoSizeText"></span>
                    </div>
                    @error('video')
                        <div class="warning-message show" style="background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444;">
                            <strong>❌ Upload Rejected:</strong> {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">Submit Report</button>
            </form>
        </div>
    </div>

    <!-- Media Modal -->
    <div id="mediaModal" class="media-modal">
        <button class="media-modal-close" onclick="closeMediaModal()">×</button>
        <div class="media-modal-content" id="mediaContent"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Media Modal Functions
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

        // Modal functions - must be defined before use
        function openReportModal() {
            const modal = document.getElementById('reportModal');
            if (modal) {
                modal.classList.add('active');
                console.log('Modal opened');
            } else {
                console.error('Modal not found');
            }
        }

        function closeReportModal() {
            const modal = document.getElementById('reportModal');
            if (modal) {
                modal.classList.remove('active');
            }
        }

        // Get user's current GPS location - defined globally
        window.getCurrentLocation = function() {
            const gpsButton = document.getElementById('gpsButton');
            const currentCoords = document.getElementById('currentCoords');
            
            console.log('GPS button clicked');
            
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser');
                return;
            }
            
            // Disable button and show loading state
            gpsButton.disabled = true;
            gpsButton.innerHTML = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke-width="4" stroke="currentColor" fill="none" opacity="0.25"></circle><path d="M4 12a8 8 0 018-8" stroke-width="4" stroke-linecap="round"></path></svg> Getting...`;
            
            console.log('Requesting geolocation...');
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    console.log('GPS position received:', position);
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    console.log('Lat:', lat, 'Lng:', lng);
                    
                    // Update hidden inputs
                    document.getElementById('latitudeInput').value = lat;
                    document.getElementById('longitudeInput').value = lng;
                    
                    // Update coordinates display
                    currentCoords.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                    currentCoords.style.color = '#10b981';
                    
                    // Reset button first
                    gpsButton.disabled = false;
                    gpsButton.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> Use GPS';
                    
                    // Reverse geocode to get address
                    console.log('Fetching address from Nominatim...');
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            console.log('Nominatim response status:', response.status);
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Address data:', data);
                            const locationInputField = document.getElementById('locationInput');
                            let addressText = '';
                            
                            if (data.display_name) {
                                addressText = data.display_name;
                            } else if (data.address) {
                                // Build address from components
                                const parts = [];
                                if (data.address.road) parts.push(data.address.road);
                                if (data.address.suburb || data.address.neighbourhood) parts.push(data.address.suburb || data.address.neighbourhood);
                                if (data.address.city || data.address.municipality) parts.push(data.address.city || data.address.municipality);
                                if (data.address.country) parts.push(data.address.country);
                                addressText = parts.join(', ') || `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                            } else {
                                addressText = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                            }
                            
                            // Fill location field
                            locationInputField.value = addressText;
                            console.log('✅ Address set:', addressText);
                            
                            // Now place marker on map with the address
                            // Access the setMarker function from the DOMContentLoaded scope
                            const event = new CustomEvent('placeMarker', {
                                detail: { lat: lat, lng: lng, address: addressText }
                            });
                            window.dispatchEvent(event);
                        })
                        .catch(error => {
                            console.error('❌ Reverse geocoding error:', error);
                            // Fallback: use coordinates as location
                            const addressText = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                            document.getElementById('locationInput').value = addressText;
                            
                            // Still place marker
                            const event = new CustomEvent('placeMarker', {
                                detail: { lat: lat, lng: lng, address: addressText }
                            });
                            window.dispatchEvent(event);
                        });
                },
                function(error) {
                    console.error('Geolocation error:', error);
                    let errorMessage = 'Unable to get your location. ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage += 'Please enable location permissions in your browser.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage += 'Location information is unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage += 'Location request timed out. Please try again.';
                            break;
                        default:
                            errorMessage += 'An unknown error occurred.';
                            break;
                    }
                    alert(errorMessage);
                    
                    // Reset button
                    gpsButton.disabled = false;
                    gpsButton.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> Use GPS';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        }

        // Video file validation (size and format)
        const videoInput = document.getElementById('videoInput');
        const videoSizeInfo = document.getElementById('videoSizeInfo');
        const videoSizeText = document.getElementById('videoSizeText');
        const MAX_VIDEO_SIZE = 200 * 1024 * 1024; // 200MB in bytes
        
        // Accepted video formats
        const ACCEPTED_VIDEO_FORMATS = [
            'video/mp4',
            'video/avi',
            'video/x-msvideo',
            'video/quicktime',
            'video/x-ms-wmv'
        ];

        if (videoInput) {
            videoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (file) {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    const fileType = file.type;
                    const fileExtension = file.name.split('.').pop().toLowerCase();
                    
                    // Always show file size info
                    videoSizeInfo.classList.add('show');
                    
                    // Check file format first
                    const isFormatAccepted = ACCEPTED_VIDEO_FORMATS.includes(fileType) || 
                                            ['mp4', 'avi', 'mov', 'wmv'].includes(fileExtension);
                    
                    if (!isFormatAccepted) {
                        // Show error for unsupported format
                        videoSizeInfo.style.background = '#fee2e2';
                        videoSizeInfo.style.color = '#991b1b';
                        videoSizeInfo.style.borderLeftColor = '#ef4444';
                        videoSizeText.innerHTML = `❌ <strong>Format Not Accepted:</strong> ${fileExtension.toUpperCase()} files are not supported.<br><small>Please use: MP4, AVI, MOV, or WMV format (H.264 codec recommended)</small>`;
                        
                        // Clear the input
                        videoInput.value = '';
                        
                        return;
                    }
                    
                    // Check file size
                    if (file.size > MAX_VIDEO_SIZE) {
                        // Show error for oversized file
                        videoSizeInfo.style.background = '#fef3c7';
                        videoSizeInfo.style.color = '#92400e';
                        videoSizeInfo.style.borderLeftColor = '#f59e0b';
                        videoSizeText.innerHTML = `⚠️ <strong>File Too Large:</strong> ${fileSizeMB}MB exceeds the 200MB limit.<br><small>This file will be rejected upon submission.</small>`;
                    } else if (fileSizeMB > 50) {
                        // Show info for large files
                        videoSizeInfo.style.background = '#dbeafe';
                        videoSizeInfo.style.color = '#1e40af';
                        videoSizeInfo.style.borderLeftColor = '#3b82f6';
                        videoSizeText.innerHTML = `ℹ️ Video: ${fileSizeMB}MB (${fileExtension.toUpperCase()}). Upload may take some time.`;
                    } else {
                        // Show success for normal files
                        videoSizeInfo.style.background = '#d1fae5';
                        videoSizeInfo.style.color = '#065f46';
                        videoSizeInfo.style.borderLeftColor = '#10b981';
                        videoSizeText.innerHTML = `✓ Video ready: ${fileSizeMB}MB (${fileExtension.toUpperCase()}).<br><small>Note: Videos with H.265/HEVC codec will be rejected. Use H.264 codec for best compatibility.</small>`;
                    }
                } else {
                    videoSizeInfo.classList.remove('show');
                }
            });
        }

        // Filter user reports table - Global function for onchange events
        window.filterUserReports = function() {
            const disasterFilter = document.getElementById('userReportsFilter');
            const statusFilter = document.getElementById('userStatusFilter');
            const actionStatusFilter = document.getElementById('userActionStatusFilter');
            
            if (!disasterFilter || !statusFilter || !actionStatusFilter) {
                console.error('Filter elements not found');
                return;
            }
            
            const disasterValue = disasterFilter.value;
            const statusValue = statusFilter.value;
            const actionValue = actionStatusFilter.value;
            const rows = document.querySelectorAll('.user-report-row');
            
            rows.forEach(row => {
                const disasterType = row.getAttribute('data-disaster-type');
                const status = row.getAttribute('data-status');
                const actionStatus = row.getAttribute('data-action-status');
                
                const matchesDisaster = disasterValue === '' || disasterType === disasterValue;
                const matchesStatus = statusValue === '' || status === statusValue;
                const matchesAction = actionValue === '' || actionStatus === actionValue;
                
                if (matchesDisaster && matchesStatus && matchesAction) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Close modal when clicking outside
            const modal = document.getElementById('reportModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeReportModal();
                    }
                });
            }

            // Davao City boundaries (approximate)
            const davaoCityBounds = L.latLngBounds(
                L.latLng(6.90, 125.25),  // Southwest corner
                L.latLng(7.50, 125.70)   // Northeast corner
            );

            // Initialize map centered on Davao City
            const map = L.map('map', {
                center: [7.1907, 125.4553],
                zoom: 13,
                maxBounds: davaoCityBounds,
                maxBoundsViscosity: 1.0,
                minZoom: 11,
                maxZoom: 18
            });

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Marker for selected location in the form
            let selectedMarker = null;

            // Function to add/update marker when location is selected
            function setMarker(lat, lng, locationName) {
                console.log('setMarker called with:', lat, lng, locationName);
                
                if (selectedMarker) {
                    map.removeLayer(selectedMarker);
                }
                
                selectedMarker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: 'custom-marker',
                        html: '<div style="background-color: #ef4444; width: 30px; height: 30px; border-radius: 50%; border: 4px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); animation: pulse 2s infinite;"></div><style>@keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); }}</style>',
                        iconSize: [30, 30],
                        iconAnchor: [15, 15]
                    })
                }).addTo(map);
                
                selectedMarker.bindPopup(`<b>📍 Selected Location</b><br>${locationName || 'Your Report Location'}`).openPopup();
                map.setView([lat, lng], 15);
                
                // Update form fields
                document.getElementById('latitudeInput').value = lat;
                document.getElementById('longitudeInput').value = lng;
                document.getElementById('currentCoords').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                
                console.log('Marker placed successfully!');
            }

            // Listen for GPS marker placement event
            window.addEventListener('placeMarker', function(e) {
                const { lat, lng, address } = e.detail;
                setMarker(lat, lng, address);
            });

            // Geocoding - automatically search and place marker as user types
            let searchTimeout;
            const locationInput = document.getElementById('locationInput');

            if (locationInput) {
                locationInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    if (query.length < 3) {
                        return;
                    }
                    
                    // Wait 1 second after user stops typing
                    searchTimeout = setTimeout(async () => {
                        try {
                            console.log('Searching for:', query);
                            
                            // Search with bias towards Davao City, Philippines
                            const response = await fetch(
                                `https://nominatim.openstreetmap.org/search?` +
                                `format=json&q=${encodeURIComponent(query + ', Philippines')}&` +
                                `limit=1`
                            );
                            
                            const results = await response.json();
                            
                            if (results.length > 0) {
                                const result = results[0];
                                const lat = parseFloat(result.lat);
                                const lon = parseFloat(result.lon);
                                
                                console.log('Location found:', result.display_name);
                                
                                // Automatically place marker on map
                                setMarker(lat, lon, result.display_name);
                            } else {
                                console.log('No location found for:', query);
                            }
                        } catch (error) {
                            console.error('Geocoding error:', error);
                        }
                    }, 1000); // 1 second delay
                });
            }

            // Custom marker icons for different statuses
        const createIcon = (color) => {
            return L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
        };

        const myReportIcon = createIcon('#8b5cf6');
        const pendingIcon = createIcon('#f59e0b');
        const inProgressIcon = createIcon('#3b82f6');
        const resolvedIcon = createIcon('#10b981');

        // Sample report data (replace with actual data from your database) dahdahdaskd
        const sampleReports = [
            {
                id: 1,
                title: 'Pothole on Main Street',
                status: 'pending',
                lat: 7.1907,
                lng: 125.4553,
                description: 'Large pothole causing traffic issues',
                isMine: false
            },
            {
                id: 2,
                title: 'Broken Streetlight',
                status: 'in-progress',
                lat: 7.1950,
                lng: 125.4600,
                description: 'Streetlight not working for 3 days',
                isMine: false
            },
            {
                id: 3,
                title: 'Illegal Dumping',
                status: 'resolved',
                lat: 7.1850,
                lng: 125.4500,
                description: 'Waste dumped near residential area',
                isMine: false
            }
        ];
        const temp = 123;
        // Get disaster types and verified reports from database
        // `disasterTypes` is an array of objects like { id, name, icon, color, is_active }
        const disasterTypes = @json($disasterTypes);

        // Get verified reports from database
        const verifiedReports = @json($verifiedReports);
        let allMarkers = []; // Store all markers for filtering
        // Add markers for all verified reports, using the disaster type icon/color when available
        if (verifiedReports && verifiedReports.length > 0) {
            verifiedReports.forEach(report => {
                const lat = parseFloat(report.latitude);
                const lng = parseFloat(report.longitude);

                // Find the disaster type object for this report
                const dt = disasterTypes ? disasterTypes.find(d => d.name === report.disaster_type) : null;
                const color = (dt && dt.color) ? dt.color : '#10b981'; // fallback green
                const emoji = (dt && dt.icon) ? dt.icon : '📍';

                // Create a marker icon that displays the disaster emoji inside a colored circle
                const typeIcon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: ${color}; width: 30px; height: 30px; border-radius: 50%; border: 4px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16px;">${emoji}</div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15],
                    popupAnchor: [0, -15]
                });

                const marker = L.marker([lat, lng], { icon: typeIcon }).addTo(map);

                // Store marker with disaster type for filtering
                marker.disasterType = report.disaster_type;
                allMarkers.push(marker);

                const isMyReport = report.user_id === {{ auth()->id() }};

                marker.bindPopup(`
                    <div style="padding: 0.75rem; min-width: 250px;">
                        <h4 style="margin: 0 0 0.5rem 0; color: #1e293b; font-size: 1rem; font-weight: 600;">
                            ${report.disaster_type.charAt(0).toUpperCase() + report.disaster_type.slice(1)}
                        </h4>
                        <p style="margin: 0 0 0.5rem 0; color: #64748b; font-size: 0.875rem;">
                            ${report.description.length > 100 ? report.description.substring(0, 100) + '...' : report.description}
                        </p>
                        <div style="margin-bottom: 0.5rem;">
                            <small style="color: #94a3b8; font-size: 0.75rem;">
                                📍 ${report.location || lat.toFixed(6) + ', ' + lng.toFixed(6)}
                            </small>
                        </div>
                        <div style="margin-bottom: 0.5rem;">
                            <small style="color: #94a3b8; font-size: 0.75rem;">
                                👤 Reported by: ${isMyReport ? 'You' : report.user.name}
                            </small>
                        </div>
                        <div style="margin-bottom: 0.5rem;">
                            <small style="color: #94a3b8; font-size: 0.75rem;">
                                📅 ${new Date(report.created_at).toLocaleDateString()}
                            </small>
                        </div>
                        ${report.image ? '<a href="javascript:void(0)" onclick="showMedia(\'/storage/' + report.image + '\', \'image\')" style="display: inline-block; margin-right: 0.5rem; padding: 0.25rem 0.5rem; background: #3b82f6; color: white; text-decoration: none; border-radius: 4px; font-size: 0.75rem; cursor: pointer;">📷 View Image</a>' : ''}
                        ${report.video ? '<a href="javascript:void(0)" onclick="showMedia(\'/storage/' + report.video + '\', \'video\')" style="display: inline-block; padding: 0.25rem 0.5rem; background: #8b5cf6; color: white; text-decoration: none; border-radius: 4px; font-size: 0.75rem; cursor: pointer;">🎥 View Video</a>' : ''}
                        <div style="margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px solid #e2e8f0;">
                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: ${isMyReport ? '#f3e8ff' : '#d1fae5'}; color: ${isMyReport ? '#6b21a8' : '#065f46'}; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                ${isMyReport ? 'Your Report' : 'Verified'}
                            </span>
                        </div>
                    </div>
                `);
            });
        }

        // Add click handler to map for creating new reports
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            // Reverse geocode to get address
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    const address = data.display_name || `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                    const locationInputField = document.getElementById('locationInput');
                    if (locationInputField) {
                        locationInputField.value = address;
                        setMarker(lat, lng, address);
                    }
                })
                .catch(error => {
                    console.error('Reverse geocoding error:', error);
                    const locationInputField = document.getElementById('locationInput');
                    if (locationInputField) {
                        locationInputField.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        setMarker(lat, lng, null);
                    }
                });
        });

        // Close media modal when clicking outside or pressing Escape
        const mediaModal = document.getElementById('mediaModal');
        if (mediaModal) {
            mediaModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeMediaModal();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMediaModal();
            }
        });

        // Filter reports by disaster type
        function filterReportsByType() {
            const filterValue = document.getElementById('disasterFilter').value;
            
            allMarkers.forEach(marker => {
                if (filterValue === '' || marker.disasterType === filterValue) {
                    marker.addTo(map); // Show marker
                } else {
                    map.removeLayer(marker); // Hide marker
                }
            });
        }

        // Attach event listeners to filter dropdowns
        const disasterFilterEl = document.getElementById('disasterFilter');
        if (disasterFilterEl) {
            disasterFilterEl.addEventListener('change', filterReportsByType);
        }

        // ========================================
        // REAL-TIME NOTIFICATION SYSTEM (User Side)
        // ========================================
        
        let userNotificationCount = 0;
        const pusherKey = '{{ config("broadcasting.connections.pusher.key") }}';
        const pusherCluster = '{{ config("broadcasting.connections.pusher.options.cluster") }}';
        const userId = {{ auth()->id() }};
        
        // Initialize Laravel Echo
        if (pusherKey && pusherKey !== 'your_app_key') {
            console.log('🔌 Initializing Echo for user notifications...');
            
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: pusherCluster,
                forceTLS: true,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }
            });

            // Listen for admin responses on user's private channel
            window.Echo.private(`user.${userId}`)
                .listen('.admin.responded', (data) => {
                    console.log('📩 Admin responded to your report:', data);
                    
                    // Show notification popup
                    showUserNotification(data);
                    
                    // Update badge
                    userNotificationCount++;
                    const badge = document.getElementById('userNotificationBadge');
                    if (badge) {
                        badge.textContent = userNotificationCount;
                        badge.classList.add('show');
                    }
                    
                    // Play notification sound
                    playNotificationSound();
                    
                    // Reload page to show updated report
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                });

            // Connection monitoring
            window.Echo.connector.pusher.connection.bind('connected', function() {
                console.log('✓ User Echo connected successfully');
            });
            
            window.Echo.connector.pusher.connection.bind('error', function(err) {
                console.error('✗ User Echo connection error:', err);
            });
        } else {
            console.log('🔄 Using polling fallback for user notifications');
            
            // Polling fallback - check for new responses every 3 seconds
            async function checkForNewResponses() {
                try {
                    console.log('🔍 Checking for new admin responses...');
                    const response = await fetch('/user/check-responses', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (!response.ok) {
                        console.error('Response check failed:', response.status);
                        return;
                    }
                    
                    const data = await response.json();
                    console.log('Response data:', data);
                    
                    if (data.has_new_response) {
                        console.log('📥 New admin response detected via polling!');
                        
                        // Show notification for each new response
                        if (data.latest_response) {
                            showUserNotification({
                                disaster_type: data.latest_response.disaster_type || 'Report',
                                response_message: data.latest_response.response_message,
                                action_type: data.latest_response.action_type,
                                responded_at: data.latest_response.responded_at
                            });
                            
                            // Update badge
                            userNotificationCount++;
                            const badge = document.getElementById('userNotificationBadge');
                            if (badge) {
                                badge.textContent = userNotificationCount;
                                badge.classList.add('show');
                            }
                            
                            // Play sound
                            playNotificationSound();
                            
                            // Reload after 3 seconds
                            setTimeout(() => {
                                console.log('🔄 Reloading page to show updated report...');
                                location.reload();
                            }, 3000);
                        }
                    }
                } catch (error) {
                    console.error('Error checking for responses:', error);
                }
            }
            
            // Start polling immediately and then every 3 seconds
            checkForNewResponses();
            setInterval(checkForNewResponses, 3000);
        }
        
        // Toggle notifications function
        window.toggleNotifications = function() {
            // Reset badge counter
            userNotificationCount = 0;
            const badge = document.getElementById('userNotificationBadge');
            if (badge) {
                badge.classList.remove('show');
                badge.textContent = '0';
            }
            
            // Scroll to reports section
            const reportsSection = document.querySelector('.reports-section');
            if (reportsSection) {
                reportsSection.scrollIntoView({ behavior: 'smooth' });
            }
        };

        /**
         * Show real-time notification popup for admin response
         */
        function showUserNotification(data) {
            const notification = document.createElement('div');
            notification.className = 'realtime-notification';
            notification.innerHTML = `
                <button class="close-notification" onclick="this.parentElement.remove()">×</button>
                <h4>🔔 Admin Response Received</h4>
                <p><strong>${data.disaster_type}</strong></p>
                <p style="margin-top: 0.5rem;">${data.response_message}</p>
                <p style="margin-top: 0.5rem; color: #94a3b8; font-size: 0.75rem;">
                    Status: <strong>${data.action_type === 'solved' ? 'Solved' : data.action_type === 'in_progress' ? 'In Progress' : 'Updated'}</strong>
                </p>
            `;
            
            document.body.appendChild(notification);
            
            // Auto-remove after 8 seconds
            setTimeout(() => {
                notification.style.animation = 'slideInRight 0.3s ease-out reverse';
                setTimeout(() => notification.remove(), 300);
            }, 8000);
        }

        /**
         * Play notification sound
         */
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

        }); // Close DOMContentLoaded
    </script>
</body>
</html>
php