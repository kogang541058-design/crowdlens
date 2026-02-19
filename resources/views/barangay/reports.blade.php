<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - {{ $barangay->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Pusher & Laravel Echo via CDN -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f8fafc;
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header h2 { font-size: 1.5rem; margin-bottom: 0.25rem; }
        .sidebar-header p  { color: rgba(255,255,255,0.8); font-size: 0.875rem; }

        .nav-menu { list-style: none; padding: 1.5rem 0; }
        .nav-item  { margin-bottom: 0.5rem; }
        .nav-link  {
            display: flex; align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        .nav-link:hover  { background: rgba(255,255,255,0.1); color: white; }
        .nav-link.active { background: rgba(255,255,255,0.2); color: white; border-left: 3px solid white; }
        .nav-link svg    { width: 20px; height: 20px; margin-right: 0.75rem; }

        /* ── Main ── */
        .main-content { margin-left: 260px; flex: 1; padding: 2rem; }

        .top-bar {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .top-bar h1 { font-size: 1.75rem; color: #1e293b; }

        .barangay-info { display: flex; align-items: center; gap: 1rem; }
        .barangay-name { color: #64748b; font-size: 0.875rem; }

        .logout-btn {
            background: #ef4444; color: white; border: none;
            padding: 0.5rem 1rem; border-radius: 6px;
            cursor: pointer; font-size: 0.875rem; transition: background 0.3s;
        }
        .logout-btn:hover { background: #dc2626; }

        /* ── Filter bar ── */
        .filter-bar {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .filter-bar label { font-size: 0.875rem; color: #64748b; font-weight: 600; }
        .filter-select {
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #374151;
            background: #f8fafc;
            cursor: pointer;
        }
        .filter-select:focus { outline: none; border-color: #667eea; }

        /* ── Notification Bell ── */
        .notification-bell-wrap { position: relative; }
        .notification-bell-wrap button {
            background: none; border: none; cursor: pointer;
            padding: 0.5rem; border-radius: 50%;
            display: flex; align-items: center;
            transition: background 0.2s;
        }
        .notification-bell-wrap button:hover { background: rgba(102,126,234,0.1); }
        .notification-bell-wrap svg { width: 24px; height: 24px; color: #64748b; }
        .notif-badge {
            position: absolute; top: 0; right: 0;
            background: #ef4444; color: white;
            border-radius: 50%; width: 20px; height: 20px;
            font-size: 0.7rem; font-weight: 700;
            display: none; align-items: center; justify-content: center;
            border: 2px solid white;
        }
        .notif-badge.show { display: flex; }
        .notif-dropdown {
            position: absolute; top: calc(100% + 0.5rem); right: 0;
            width: 340px; max-height: 460px;
            background: white; border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            display: none; flex-direction: column; z-index: 9999; overflow: hidden;
        }
        .notif-dropdown.show { display: flex; }
        .notif-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 700; font-size: 1rem; color: #111827;
        }
        .notif-body { overflow-y: auto; max-height: 380px; }
        .notif-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex; gap: 0.75rem; align-items: flex-start;
            cursor: default; transition: background 0.2s;
        }
        .notif-item.unread { background: #eff6ff; }
        .notif-item:hover { background: #f9fafb; }
        .notif-icon {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg,#667eea,#764ba2);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .notif-icon svg { width: 18px; height: 18px; color: white; }
        .notif-content { flex: 1; }
        .notif-title { font-weight: 600; color: #111827; font-size: 0.875rem; margin-bottom: 0.2rem; }
        .notif-text  { color: #6b7280; font-size: 0.8rem; line-height: 1.4; }
        .notif-time  { color: #9ca3af; font-size: 0.75rem; margin-top: 0.2rem; }
        .notif-empty { padding: 3rem 1.5rem; text-align: center; color: #9ca3af; }
        .notif-empty svg { width: 44px; height: 44px; margin: 0 auto 0.75rem; opacity: 0.5; }

        /* ── Realtime toast ── */
        @keyframes slideInRight  { from { transform: translateX(120%); opacity:0; } to { transform: translateX(0); opacity:1; } }
        @keyframes slideOutRight { from { transform: translateX(0); opacity:1; } to { transform: translateX(120%); opacity:0; } }
        .rt-toast {
            position: fixed; bottom: 1.5rem; right: 1.5rem;
            background: white; border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            padding: 1rem 1.25rem;
            display: flex; gap: 0.75rem; align-items: center;
            z-index: 99999; max-width: 320px;
            animation: slideInRight 0.3s ease;
            border-left: 4px solid #667eea;
        }
        .rt-toast svg { width: 22px; height: 22px; color: #667eea; flex-shrink: 0; }
        .rt-toast strong { display: block; color: #111827; font-size: 0.875rem; }
        .rt-toast small { color: #6b7280; font-size: 0.8rem; }

        /* ── Table ── */
        .table-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .table-header h2 { font-size: 1.1rem; color: #1e293b; font-weight: 600; }
        .report-count {
            background: #ede9fe; color: #5b21b6;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        table { width: 100%; border-collapse: collapse; }
        th {
            padding: 0.875rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
            color: #374151;
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }

        .badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-disaster { background: #dbeafe; color: #1e40af; }
        .badge-pending   { background: #fef3c7; color: #92400e; }
        .badge-verified  { background: #d1fae5; color: #065f46; }
        .badge-unverified{ background: #fee2e2; color: #991b1b; }
        .badge-solved    { background: #d1fae5; color: #065f46; }
        .badge-progress  { background: #dbeafe; color: #1e40af; }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #94a3b8;
        }
        .empty-state svg { width: 56px; height: 56px; margin: 0 auto 1rem; opacity: 0.4; }
        .empty-state p   { font-size: 1rem; }

        /* ── Detail modal ── */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 580px;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: fadeIn 0.2s ease;
        }
        @keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h3 { font-size: 1.1rem; color: #1e293b; font-weight: 700; }
        .modal-close {
            background: none; border: none; font-size: 1.5rem;
            cursor: pointer; color: #94a3b8; line-height: 1;
        }
        .modal-close:hover { color: #374151; }
        .modal-body { padding: 1.5rem; }
        .detail-row { display: flex; gap: 1rem; margin-bottom: 1rem; }
        .detail-item { flex: 1; }
        .detail-label { font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; margin-bottom: 0.3rem; }
        .detail-value { font-size: 0.9rem; color: #1e293b; font-weight: 500; }
        .detail-full  { margin-bottom: 1rem; }
        .media-img    { width: 100%; border-radius: 8px; margin-top: 0.5rem; max-height: 260px; object-fit: cover; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>{{ $barangay->name }}</h2>
            <p>Barangay Portal</p>
        </div>
        <nav>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('barangay.dashboard') }}" class="nav-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('barangay.reports') }}" class="nav-link active">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Reports
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h1>Reports</h1>
            <div class="barangay-info">
                <span class="barangay-name">Welcome, {{ $barangay->name }}</span>
                <!-- Notification Bell -->
                <div class="notification-bell-wrap" id="notifWrap">
                    <button onclick="toggleNotifDropdown(event)" title="Notifications">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="notif-badge" id="notifBadge">0</span>
                    </button>
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">Notifications</div>
                        <div class="notif-body" id="notifList">
                            <div class="notif-empty">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <div>No new notifications</div>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{ route('barangay.logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-bar">
            <label>Filter by:</label>
            <select class="filter-select" id="typeFilter" onchange="filterReports()">
                <option value="">All Types</option>
                @foreach($reports->pluck('disaster_type')->unique()->sort() as $type)
                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            <select class="filter-select" id="statusFilter" onchange="filterReports()">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="verified">Verified</option>
                <option value="unverified">Unverified</option>
            </select>
            <select class="filter-select" id="actionFilter" onchange="filterReports()">
                <option value="">All Action Status</option>
                <option value="solved">Solved</option>
                <option value="in_progress">In Progress</option>
                <option value="none">No Action</option>
            </select>
            <select class="filter-select" id="barangayActionFilter" onchange="filterReports()">
                <option value="">All Barangay Action</option>
                <option value="approved">Approved</option>
                <option value="disapproved">Disapproved</option>
                <option value="none">No Action</option>
            </select>
        </div>

        <!-- Table -->
        <div class="table-card">
            <div class="table-header">
                <h2>All Reports</h2>
                <span class="report-count" id="reportCount">{{ $reports->count() }} reports</span>
            </div>

            @if($reports->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Reporter</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action Status</th>
                        <th>Barangay Action</th>
                    </tr>
                </thead>
                <tbody id="reportsTableBody">
                    @foreach($reports as $report)
                    @php
                        $actionStatus = $report->solved ? 'solved' : ($report->responses->where('action_type', 'in_progress')->count() ? 'in_progress' : 'none');
                        $reportLocation = $report->location ?: (number_format($report->latitude,6).', '.number_format($report->longitude,6));
                        $barangayAction = $report->barangay_action_status ?? 'none';
                    @endphp
                    <tr class="report-row"
                        data-type="{{ $report->disaster_type }}"
                        data-status="{{ $report->status }}"
                        data-action="{{ $actionStatus }}"
                        data-barangay-action="{{ $barangayAction }}"
                        data-report-id="{{ $report->id }}"
                        data-disaster="{{ e(ucfirst($report->disaster_type)) }}"
                        data-description="{{ e($report->description) }}"
                        data-reporter="{{ e($report->user->name) }}"
                        data-location="{{ e($reportLocation) }}"
                        data-date="{{ $report->created_at->format('M d, Y h:i A') }}"
                        data-image="{{ $report->image ? Storage::url($report->image) : '' }}"
                        data-video="{{ $report->video ? Storage::url($report->video) : '' }}"
                        style="cursor:pointer;"
                        onclick="openModal(this)">
                        <td><span class="badge badge-disaster">{{ ucfirst($report->disaster_type) }}</span></td>
                        <td style="max-width:260px;">{{ Str::limit($report->description, 70) }}</td>
                        <td>{{ $report->user->name }}</td>
                        <td style="max-width:180px; color:#64748b;">
                            {{ Str::limit($report->location ?: number_format($report->latitude,6).', '.number_format($report->longitude,6), 45) }}
                        </td>
                        <td style="white-space:nowrap; color:#64748b;">{{ $report->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($report->status === 'pending')
                                <span class="badge badge-pending">Pending</span>
                            @elseif($report->status === 'verified')
                                <span class="badge badge-verified">Verified</span>
                            @else
                                <span class="badge badge-unverified">Unverified</span>
                            @endif
                        </td>
                        <td>
                            @if($report->solved)
                                <span class="badge badge-solved">Solved</span>
                            @elseif($report->responses->where('action_type','in_progress')->count())
                                <span class="badge badge-progress">In Progress</span>
                            @else
                                <span style="color:#94a3b8; font-size:0.875rem;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($report->barangay_action_status === 'approved')
                                <span class="badge badge-solved">Approved</span>
                            @elseif($report->barangay_action_status === 'disapproved')
                                <span class="badge badge-unverified">Disapproved</span>
                            @else
                                <span style="color:#94a3b8; font-size:0.875rem;">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p>No reports assigned to {{ $barangay->name }} yet.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal-overlay" id="detailModal" onclick="closeModal(event)">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3>Report Details</h3>
                <button class="modal-close" onclick="document.getElementById('detailModal').classList.remove('open')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">Type of Disaster</div>
                        <div class="detail-value" id="d-type"></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Date &amp; Time</div>
                        <div class="detail-value" id="d-date"></div>
                    </div>
                </div>
                <div class="detail-full">
                    <div class="detail-label">Description</div>
                    <div class="detail-value" id="d-desc" style="line-height:1.6; margin-top:0.3rem;"></div>
                </div>
                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">Reporter</div>
                        <div class="detail-value" id="d-reporter"></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Location</div>
                        <div class="detail-value" id="d-location"></div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div id="d-status"></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Action Status</div>
                        <div id="d-action"></div>
                    </div>
                </div>
                <div id="d-image-wrap" style="display:none;">
                    <div class="detail-label">Image</div>
                    <img id="d-image" src="" alt="Report image" class="media-img"/>
                </div>
                <div id="d-video-wrap" style="display:none; margin-top:1rem;">
                    <div class="detail-label">Video</div>
                    <video id="d-video" controls class="media-img" style="max-height:260px; width:100%; border-radius:8px;"></video>
                </div>
                <!-- Barangay Action Status Update -->
                <div style="margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid #e2e8f0;">
                    <div class="detail-label" style="margin-bottom:0.5rem;">Update Barangay Action Status</div>
                    <form id="barangayActionForm" method="POST" action="" style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">
                        @csrf
                        <select name="barangay_action_status" id="barangayActionSelect" class="filter-select" style="flex:1; min-width:160px;">
                            <option value="none">— No Action —</option>
                            <option value="approved">Approved</option>
                            <option value="disapproved">Disapproved</option>
                        </select>
                        <button type="submit" style="background:linear-gradient(135deg,#667eea,#764ba2); color:white; border:none; padding:0.5rem 1.25rem; border-radius:8px; font-size:0.875rem; font-weight:600; cursor:pointer;">
                            Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterReports() {
            const type          = document.getElementById('typeFilter').value;
            const status        = document.getElementById('statusFilter').value;
            const action        = document.getElementById('actionFilter').value;
            const barangayAction = document.getElementById('barangayActionFilter').value;
            const rows          = document.querySelectorAll('.report-row');
            let visible         = 0;

            rows.forEach(row => {
                const matchType          = !type          || row.dataset.type          === type;
                const matchStatus        = !status        || row.dataset.status        === status;
                const matchAction        = !action        || row.dataset.action        === action;
                const matchBarangayAction = !barangayAction || row.dataset.barangayAction === barangayAction;
                if (matchType && matchStatus && matchAction && matchBarangayAction) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });
            document.getElementById('reportCount').textContent = visible + ' report' + (visible !== 1 ? 's' : '');
        }

        const statusBadge = {
            pending:    '<span class="badge badge-pending">Pending</span>',
            verified:   '<span class="badge badge-verified">Verified</span>',
            unverified: '<span class="badge badge-unverified">Unverified</span>',
        };
        const actionBadge = {
            solved:      '<span class="badge badge-solved">Solved</span>',
            in_progress: '<span class="badge badge-progress">In Progress</span>',
            none:        '<span style="color:#94a3b8;">—</span>',
        };

        const barangayActionBadge = {
            approved:    '<span class="badge badge-solved">Approved</span>',
            disapproved: '<span class="badge badge-unverified">Disapproved</span>',
            none:        '<span style="color:#94a3b8;">—</span>',
        };

        function openModal(row) {
            const type          = row.dataset.disaster;
            const desc          = row.dataset.description;
            const reporter      = row.dataset.reporter;
            const location      = row.dataset.location;
            const date          = row.dataset.date;
            const status        = row.dataset.status;
            const action        = row.dataset.action;
            const barangayAction = row.dataset.barangayAction || 'none';
            const reportId      = row.dataset.reportId;
            const image         = row.dataset.image;
            const video         = row.dataset.video;

            document.getElementById('d-type').textContent     = type;
            document.getElementById('d-date').textContent     = date;
            document.getElementById('d-desc').textContent     = desc;
            document.getElementById('d-reporter').textContent = reporter;
            document.getElementById('d-location').textContent = location;
            document.getElementById('d-status').innerHTML     = statusBadge[status] || status;
            document.getElementById('d-action').innerHTML     = actionBadge[action] || '—';

            // Set barangay action form
            document.getElementById('barangayActionForm').action = '/barangay/reports/' + reportId + '/action';
            document.getElementById('barangayActionSelect').value = barangayAction;

            const imgWrap = document.getElementById('d-image-wrap');
            const img     = document.getElementById('d-image');
            if (image) {
                img.src = image;
                imgWrap.style.display = 'block';
            } else {
                imgWrap.style.display = 'none';
            }

            const videoWrap = document.getElementById('d-video-wrap');
            const videoEl   = document.getElementById('d-video');
            if (video) {
                videoEl.src = video;
                videoWrap.style.display = 'block';
            } else {
                videoEl.src = '';
                videoWrap.style.display = 'none';
            }
            document.getElementById('detailModal').classList.add('open');
        }

        function closeModal(e) {
            if (e.target === document.getElementById('detailModal')) {
                document.getElementById('detailModal').classList.remove('open');
            }
        }

        // ── Notification Bell ────────────────────────────────────────────────
        let notifList = [];
        let unreadCount = 0;

        function toggleNotifDropdown(e) {
            e.stopPropagation();
            const dd = document.getElementById('notifDropdown');
            dd.classList.toggle('show');
            if (dd.classList.contains('show')) {
                renderNotifList();
                // Mark all as read
                unreadCount = 0;
                notifList.forEach(n => n.read = true);
                updateBadge();
                localStorage.setItem('barangay_notif_reset', Date.now());
            }
        }

        document.addEventListener('click', (e) => {
            const wrap = document.getElementById('notifWrap');
            if (wrap && !wrap.contains(e.target)) {
                document.getElementById('notifDropdown').classList.remove('show');
            }
        });

        function updateBadge() {
            const badge = document.getElementById('notifBadge');
            badge.textContent = unreadCount;
            unreadCount > 0 ? badge.classList.add('show') : badge.classList.remove('show');
        }

        function renderNotifList() {
            const container = document.getElementById('notifList');
            if (!notifList.length) {
                container.innerHTML = `
                    <div class="notif-empty">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <div>No new notifications</div>
                    </div>`;
                return;
            }
            container.innerHTML = notifList.map(n => `
                <div class="notif-item ${n.read ? '' : 'unread'}">
                    <div class="notif-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title">${n.label || 'Notification'}</div>
                        <div class="notif-text">${n.disaster_type} — ${n.user_name}</div>
                        <div class="notif-time">${n.time_ago}</div>
                    </div>
                </div>
            `).join('');
        }

        function showToast(title, message) {
            const toast = document.createElement('div');
            toast.className = 'rt-toast';
            toast.innerHTML = `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <div>
                    <strong>${title}</strong>
                    <small>${message}</small>
                </div>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease forwards';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        function addRowToTable(report) {
            const tbody = document.getElementById('reportsTableBody');
            if (!tbody) return;

            const actionStatus = 'none';
            const barangayAction = 'none';
            const location = report.location || (report.latitude + ', ' + report.longitude);

            const tr = document.createElement('tr');
            tr.className = 'report-row';
            tr.dataset.type          = report.disaster_type;
            tr.dataset.status        = report.status;
            tr.dataset.action        = actionStatus;
            tr.dataset.barangayAction = barangayAction;
            tr.dataset.reportId      = report.id;
            tr.dataset.disaster      = report.disaster_type_name;
            tr.dataset.description   = report.description;
            tr.dataset.reporter      = report.user_name;
            tr.dataset.location      = location;
            tr.dataset.date          = report.formatted_date + ' ' + report.formatted_time;
            tr.dataset.image         = report.image || '';
            tr.dataset.video         = report.video || '';
            tr.style.cursor          = 'pointer';
            tr.setAttribute('onclick', 'openModal(this)');

            tr.innerHTML = `
                <td><span class="badge badge-disaster">${report.disaster_type_name}</span></td>
                <td style="max-width:260px;">${report.description.substring(0, 70)}${report.description.length > 70 ? '…' : ''}</td>
                <td>${report.user_name}</td>
                <td style="max-width:180px; color:#64748b;">${location.substring(0, 45)}${location.length > 45 ? '…' : ''}</td>
                <td style="white-space:nowrap; color:#64748b;">${report.formatted_date}</td>
                <td><span class="badge badge-pending">Pending</span></td>
                <td><span style="color:#94a3b8; font-size:0.875rem;">—</span></td>
                <td><span style="color:#94a3b8; font-size:0.875rem;">—</span></td>`;

            tr.style.background = '#f0fdf4';
            tbody.insertBefore(tr, tbody.firstChild);
            setTimeout(() => { tr.style.transition = 'background 1s'; tr.style.background = ''; }, 3000);

            // Update count
            const countEl = document.getElementById('reportCount');
            if (countEl) {
                const cur = parseInt(countEl.textContent) || 0;
                countEl.textContent = (cur + 1) + ' report' + (cur + 1 !== 1 ? 's' : '');
            }
        }

        // ── Polling-based real-time (works without Pusher) ───────────────────
        // lastPollTime is set from the SERVER clock so client/server skew never causes missed reports
        let lastPollTime = '{{ now()->toISOString() }}';
        const POLL_INTERVAL = 5000; // ms

        // IDs already rendered server-side — we never notify for these
        const knownReportIds = new Set([
            @foreach($reports as $r) {{ $r->id }}, @endforeach
        ]);

        function pollForUpdates() {
            fetch('{{ route('barangay.reports.poll') }}?since=' + encodeURIComponent(lastPollTime), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                lastPollTime = data.server_time;
                data.reports.forEach(report => {
                    if (report.is_new) {
                        handleNewReport(report);
                    } else {
                        handleUpdatedReport(report);
                    }
                });
            })
            .catch(() => {}); // silently ignore network errors
        }

        // Called when the poll endpoint returns a report with is_new === true
        function handleNewReport(report) {
            // Skip reports already on the page (either server-rendered or already processed)
            if (knownReportIds.has(report.id)) return;
            knownReportIds.add(report.id);

            notifList.unshift({
                label: '🆕 New Report',
                disaster_type: report.disaster_type_name,
                user_name: report.user_name,
                time_ago: 'Just now',
                read: false
            });
            if (notifList.length > 50) notifList.pop();
            unreadCount++;
            updateBadge();
            showToast('🆕 New Report', report.disaster_type_name + ' by ' + report.user_name);
            addRowToTable(report);
            playBeep();
        }

        // Called when the poll endpoint returns a report with is_new === false (status update)
        function handleUpdatedReport(report) {
            const row = document.querySelector(`[data-report-id="${report.id}"]`);
            if (!row) return;

            const prevStatus = row.dataset.status;
            const prevAction = row.dataset.action;

            // Only react if something actually changed
            if (prevStatus === report.status && prevAction === report.action_status) return;

            row.dataset.status = report.status;
            row.dataset.action = report.action_status;

            const tds = row.querySelectorAll('td');
            const statusMap = {
                pending:    '<span class="badge badge-pending">Pending</span>',
                verified:   '<span class="badge badge-verified">Verified</span>',
                unverified: '<span class="badge badge-unverified">Unverified</span>',
            };
            const actionMap = {
                solved:      '<span class="badge badge-solved">Solved</span>',
                in_progress: '<span class="badge badge-progress">In Progress</span>',
                none:        '<span style="color:#94a3b8;font-size:.875rem;">—</span>',
            };
            if (tds[5]) tds[5].innerHTML = statusMap[report.status] || report.status;
            if (tds[6]) tds[6].innerHTML = actionMap[report.action_status] || '—';

            notifList.unshift({
                label: '📋 Admin Responded',
                disaster_type: report.disaster_type_name,
                user_name: 'Admin',
                time_ago: 'Just now',
                read: false
            });
            if (notifList.length > 50) notifList.pop();
            unreadCount++;
            updateBadge();
            showToast('📋 Admin Responded', report.disaster_type_name + ' — Status: ' + report.status);

            // Yellow flash highlight
            row.style.transition = 'none';
            row.style.background = '#fef9c3';
            setTimeout(() => { row.style.transition = 'background 1.2s'; row.style.background = ''; }, 2500);
            playBeep();
        }

        // Start polling immediately, then every 5 seconds
        pollForUpdates();
        setInterval(pollForUpdates, POLL_INTERVAL);

        // ── Also try Pusher if credentials are real (bonus) ──────────────────
        const pusherKey = '{{ config("broadcasting.connections.pusher.key") }}';
        if (pusherKey && pusherKey !== 'your_app_key') {
            try {
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: pusherKey,
                    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                    forceTLS: true,
                    encrypted: true,
                });
                const ch = window.Echo.channel('barangay.' + {{ $barangay->id }});
                ch.listen('.report.submitted', e => handleNewReport({
                    id: e.id, disaster_type: e.disaster_type, disaster_type_name: e.disaster_type_name,
                    description: e.description, location: e.location, user_name: e.user_name,
                    status: e.status, action_status: 'none', barangay_action: 'none',
                    image: e.image || '', video: e.video || '',
                    formatted_date: e.formatted_date, formatted_time: e.formatted_time, is_new: true
                }));
                ch.listen('.admin.responded', e => handleUpdatedReport({
                    id: e.report_id, disaster_type_name: e.disaster_type_name, status: e.status,
                    action_status: e.action_type === 'solved' ? 'solved'
                                 : e.action_type === 'in_progress' ? 'in_progress' : 'none'
                }));
                console.log('✓ Pusher real-time active on barangay.' + {{ $barangay->id }});
            } catch(e) { console.warn('Pusher init failed, polling only:', e); }
        }

        function updateRowStatus(event) {
            handleUpdatedReport({
                id: event.report_id,
                disaster_type_name: event.disaster_type_name,
                status: event.status,
                action_status: event.action_type === 'solved' ? 'solved'
                             : event.action_type === 'in_progress' ? 'in_progress' : 'none'
            });
        }

        function playBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain); gain.connect(ctx.destination);
                osc.frequency.value = 800; osc.type = 'sine';
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.5);
            } catch(e) {}
        }
    </script>
</body>
</html>
