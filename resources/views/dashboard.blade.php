<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Davao City Reports</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
            height: calc(100vh - 380px);
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
            padding: 0.5rem;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            background: white;
            font-size: 0.875rem;
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
            <span class="user-name">{{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
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
                        <th>ID</th>
                        <th>
                            Type of Disaster
                            <br>
                            <select class="disaster-type-select" id="userReportsFilter">
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
                        <th>Image</th>
                        <th>Video</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr class="user-report-row" data-disaster-type="{{ $report->disaster_type }}">
                        <td>#{{ $report->id }}</td>
                        <td>{{ $report->disaster_type }}</td>
                        <td>{{ Str::limit($report->description, 50) }}</td>
                        <td>{{ $report->created_at->format('M d, Y') }}</td>
                        <td>{{ $report->created_at->format('h:i A') }}</td>
                        <td>{{ $report->user->name }}</td>
                        <td>{{ $report->location ?? $report->latitude . ', ' . $report->longitude }}</td>
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
                        <td colspan="9">
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
                    <input type="text" name="location" id="locationInput" class="form-input" placeholder="e.g., Davao City Hall, Roxas Avenue" autocomplete="off">
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
                    <input type="file" name="video" class="form-input form-file" accept="video/*">
                    <small style="color: #64748b; font-size: 0.75rem;">Max file size: 100MB (Please wait for upload to complete)</small>
                    @error('video')
                        <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span>
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
                                ${isMyReport ? '👤 Your Report' : '✓ Verified'}
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

        // Filter user reports table
        function filterUserReports() {
            const filterValue = document.getElementById('userReportsFilter').value;
            const rows = document.querySelectorAll('.user-report-row');
            
            rows.forEach(row => {
                const disasterType = row.getAttribute('data-disaster-type');
                if (filterValue === '' || disasterType === filterValue) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Attach event listeners to filter dropdowns
        const disasterFilterEl = document.getElementById('disasterFilter');
        if (disasterFilterEl) {
            disasterFilterEl.addEventListener('change', filterReportsByType);
        }

        const userReportsFilterEl = document.getElementById('userReportsFilter');
        if (userReportsFilterEl) {
            userReportsFilterEl.addEventListener('change', filterUserReports);
        }

        }); // Close DOMContentLoaded
    </script>
</body>
</html>
php