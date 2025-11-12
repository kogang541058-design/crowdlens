<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Dashboard | {{ config('app.name', 'Laravel') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: #f8fafc;
                min-height: 100vh;
            }
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                width: 260px;
                background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
                color: white;
                padding: 1.5rem 0;
                overflow-y: auto;
                z-index: 100;
            }
            .logo {
                padding: 0 1.5rem 1.5rem;
                border-bottom: 1px solid rgba(255,255,255,.1);
                margin-bottom: 1rem;
            }
            .logo h1 {
                font-size: 1.25rem;
                font-weight: 800;
                color: white;
                margin-bottom: .25rem;
            }
            .logo p {
                font-size: .8rem;
                color: #94a3b8;
            }
            .nav-menu {
                list-style: none;
                padding: 0 .75rem;
            }
            .nav-item {
                margin-bottom: .25rem;
            }
            .nav-link {
                display: flex;
                align-items: center;
                gap: .75rem;
                padding: .75rem 1rem;
                color: #cbd5e1;
                text-decoration: none;
                border-radius: 10px;
                transition: all 0.2s;
                font-size: .95rem;
            }
            .nav-link:hover, .nav-link.active {
                background: rgba(255,255,255,.1);
                color: white;
            }
            .nav-link svg {
                width: 20px;
                height: 20px;
            }
            .main-content {
                margin-left: 260px;
                min-height: 100vh;
            }
            .header {
                background: white;
                border-bottom: 1px solid #e2e8f0;
                padding: 1.25rem 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: sticky;
                top: 0;
                z-index: 50;
                box-shadow: 0 1px 3px rgba(0,0,0,.05);
            }
            .header h2 {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1e293b;
            }
            .user-menu {
                display: flex;
                align-items: center;
                gap: 1rem;
            }
            .user-info {
                text-align: right;
            }
            .user-info .name {
                font-weight: 600;
                color: #1e293b;
                font-size: .95rem;
            }
            .user-info .role {
                font-size: .8rem;
                color: #64748b;
            }
            .btn-logout {
                padding: .5rem 1rem;
                background: #ef4444;
                color: white;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
                font-size: .9rem;
            }
            .btn-logout:hover {
                background: #dc2626;
            }
            .container {
                padding: 2rem;
            }
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 1.5rem;
                margin-bottom: 2rem;
            }
            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            @media (max-width: 768px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                }
            }
            .stat-card {
                background: white;
                padding: 1.75rem;
                border-radius: 16px;
                box-shadow: 0 1px 3px rgba(0,0,0,.08);
                border: 1px solid #e2e8f0;
                position: relative;
                overflow: hidden;
            }
            .stat-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            }
            .stat-card.success::before {
                background: linear-gradient(90deg, #10b981, #059669);
            }
            .stat-card.warning::before {
                background: linear-gradient(90deg, #f59e0b, #d97706);
            }
            .stat-card.danger::before {
                background: linear-gradient(90deg, #ef4444, #dc2626);
            }
            .stat-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 1rem;
            }
            .stat-title {
                font-size: .9rem;
                color: #64748b;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .05em;
            }
            .stat-icon {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            }
            .stat-card.success .stat-icon {
                background: linear-gradient(135deg, #10b981, #059669);
            }
            .stat-card.warning .stat-icon {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }
            .stat-card.danger .stat-icon {
                background: linear-gradient(135deg, #ef4444, #dc2626);
            }
            .stat-icon svg {
                width: 24px;
                height: 24px;
                color: white;
            }
            .stat-number {
                font-size: 2.5rem;
                font-weight: 800;
                color: #1e293b;
                line-height: 1;
                margin-bottom: .5rem;
            }
            .stat-change {
                font-size: .85rem;
                color: #10b981;
                font-weight: 600;
            }
            .card {
                background: white;
                border-radius: 16px;
                box-shadow: 0 1px 3px rgba(0,0,0,.08);
                border: 1px solid #e2e8f0;
                margin-bottom: 1.5rem;
            }
            .card-header {
                padding: 1.5rem 2rem;
                border-bottom: 1px solid #e2e8f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .card-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: #1e293b;
            }
            .card-body {
                padding: 2rem;
            }
            .btn-primary {
                padding: .65rem 1.25rem;
                background: linear-gradient(135deg, #3b82f6, #8b5cf6);
                color: white;
                border: none;
                border-radius: 10px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
                font-size: .9rem;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: .5rem;
            }
            .btn-primary:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(59,130,246,.3);
            }
            .table {
                width: 100%;
                border-collapse: collapse;
            }
            .table thead {
                background: #f8fafc;
            }
            .table th {
                padding: 1rem 1.5rem;
                text-align: left;
                font-size: .85rem;
                font-weight: 700;
                color: #475569;
                text-transform: uppercase;
                letter-spacing: .05em;
                border-bottom: 2px solid #e2e8f0;
            }
            .table td {
                padding: 1rem 1.5rem;
                border-bottom: 1px solid #e2e8f0;
                color: #475569;
            }
            .table tbody tr:hover {
                background: #f8fafc;
            }
            .badge {
                display: inline-block;
                padding: .35rem .75rem;
                border-radius: 6px;
                font-size: .8rem;
                font-weight: 600;
            }
            .badge-success {
                background: #dcfce7;
                color: #166534;
            }
            .badge-warning {
                background: #fef3c7;
                color: #92400e;
            }
            .badge-danger {
                background: #fee2e2;
                color: #991b1b;
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
                opacity: .5;
            }
        </style>
    </head>
    <body>
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h1>Admin Portal</h1>
                <p>Davao City Reports</p>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link active">
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
                    <a href="#" class="nav-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Settings
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <h2>Dashboard Overview</h2>
                <div class="user-menu">
                    <div class="user-info">
                        <div class="name">{{ auth('admin')->user()->name }}</div>
                        <div class="role">Administrator</div>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn-logout">Logout</button>
                    </form>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="container">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-title">Total Users</div>
                                <div class="stat-number">{{ $totalUsers }}</div>
                                <div class="stat-change">↑ Active users</div>
                            </div>
                            <div class="stat-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card success">
                        <div class="stat-header">
                            <div>
                                <div class="stat-title">Total Reports</div>
                                <div class="stat-number">{{ $totalReports }}</div>
                                <div class="stat-change">{{ $totalReports > 0 ? 'Total submissions' : 'No reports yet' }}</div>
                            </div>
                            <div class="stat-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card warning">
                        <div class="stat-header">
                            <div>
                                <div class="stat-title">Pending</div>
                                <div class="stat-number">{{ $pendingReports }}</div>
                                <div class="stat-change">{{ $pendingReports > 0 ? 'Needs attention' : 'All clear' }}</div>
                            </div>
                            <div class="stat-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card success">
                        <div class="stat-header">
                            <div>
                                <div class="stat-title">Verified Reports</div>
                                <div class="stat-number">{{ $verifiedReports }}</div>
                                <div class="stat-change">{{ $verifiedReports > 0 ? 'Completed' : 'No verified yet' }}</div>
                            </div>
                            <div class="stat-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card success">
                        <div class="stat-header">
                            <div>
                                <div class="stat-title">Solved</div>
                                <div class="stat-number">{{ $solvedReports }}</div>
                                <div class="stat-change">{{ $solvedReports > 0 ? 'Resolved issues' : 'No solved yet' }}</div>
                            </div>
                            <div class="stat-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card danger">
                        <div class="stat-header">
                            <div>
                                <div class="stat-title">Unsolved</div>
                                <div class="stat-number">{{ $unsolvedReports }}</div>
                                <div class="stat-change">{{ $unsolvedReports > 0 ? 'Needs resolution' : 'All resolved' }}</div>
                            </div>
                            <div class="stat-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>

