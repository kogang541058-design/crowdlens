@extends('layouts.admin')

@section('title', 'Solved - Admin Dashboard')

@section('content')
<!-- <div class="flex flex-col w-full min-h-screen bg-slate-50"> -->
    
<div class="p-4 md:p-6 lg:p-8 w-full max-w-7xl mx-auto">
    
    @include('partials.notif_logout', ['page_name' => 'Solved Reports'])

    <div class="p-4 sm:p-6 lg:p-8 flex-1">
        
        @if(session('success'))
        <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-50 text-emerald-800 rounded-lg border border-emerald-200 shadow-sm">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
        @endif


        

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="p-6 border-b border-slate-100 bg-slate-50/50 space-y-4">
            <!-- <div class="px-6 py-5 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"> -->
                <!-- <h2 class="text-lg font-bold text-slate-800">Recent Reports</h2> -->

                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-bold text-slate-800">Solved</h2>
                    <button onclick="exportTableToExcel('Reports_Export')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Export to Excel
                    </button>

                </div>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                            <th class="px-6 py-4 font-semibold w-[150px] align-top">
                                <span class="block mb-2">Disaster</span>
                                <select id="adminDisasterFilter" onchange="filterAdminReports()" class="w-full px-2 py-1.5 text-sm bg-white border border-slate-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All</option>
                                    @foreach($disasterTypes as $type)
                                        <option value="{{ $type->name }}">{{ $type->icon }} {{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th class="px-6 py-4 font-semibold w-[250px] align-top">Description</th>
                            <th class="px-6 py-4 font-semibold w-[140px] align-top">
                                <span class="block mb-2">Date</span>
                                <select id="dateFilter" onchange="filterByDate()" class="w-full px-2 py-1.5 text-sm bg-white border border-slate-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All</option>
                                    <option value="january">January</option>
                                    <option value="february">February</option>
                                    <option value="march">March</option>
                                    <option value="april">April</option>
                                    <option value="may">May</option>
                                    <option value="june">June</option>
                                    <option value="july">July</option>
                                    <option value="august">August</option>
                                    <option value="september">September</option>
                                    <option value="october">October</option>
                                    <option value="november">November</option>
                                    <option value="december">December</option>
                                </select>
                            </th>
                            <th class="px-6 py-4 font-semibold w-[150px] align-top">User</th>
                            <th class="px-6 py-4 font-semibold w-[200px] align-top">Location</th>
                            <th class="px-6 py-4 font-semibold w-[120px] align-top">Solved By</th>
                            <th class="px-6 py-4 font-semibold w-[150px] align-top">Solved At</th>
                            <th class="px-6 py-4 font-semibold w-[120px] align-top text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                        @if($solvedReports->count() > 0)
                            @foreach($solvedReports as $solved)
                            <tr class="hover:bg-slate-50 transition-colors" 
                                data-disaster-type="{{ $solved->report->disaster_type }}"
                                data-report-date="{{ $solved->report->created_at->toISOString() }}">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ ucfirst($solved->report->disaster_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 max-w-[300px] truncate" title="{{ $solved->report->description }}">
                                    {{ Str::limit($solved->report->description, 100) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                    {{ $solved->report->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    {{ $solved->report->user->name }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($solved->report->location)
                                        <span class="truncate block max-w-[180px]" title="{{ $solved->report->location }}">
                                            {{ Str::limit($solved->report->location, 50) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-mono text-xs">
                                            {{ number_format($solved->report->latitude, 6) }}, {{ number_format($solved->report->longitude, 6) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $solved->admin->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs">
                                    {{ $solved->solved_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 w-full">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Solved
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400 gap-3">
                                        <div class="p-3 bg-slate-50 rounded-full">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-8 h-8">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium">No solved reports yet</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="mediaModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/80 backdrop-blur-sm transition-opacity">
    <div class="relative w-full max-w-4xl mx-4">
        <button class="absolute -top-12 right-0 text-white hover:text-rose-400 p-2 transition-colors focus:outline-none" onclick="closeMediaModal()">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div id="mediaContent" class="bg-black rounded-xl overflow-hidden shadow-2xl flex items-center justify-center min-h-[300px]">
            </div>
    </div>
</div>
@endsection

@push('scripts')


<!-- // ── Export Scripts -->
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
    function exportTableToExcel(filename = 'Export') {
        // 1. Get the table element
        let table = document.querySelector("table");
        
        // 2. Create a clone of the table so we don't modify the actual UI
        let cloneTable = table.cloneNode(true);
        
        // 3. Remove rows that are hidden by your JS filters
        let rows = cloneTable.querySelectorAll('tr');
        rows.forEach(row => {
            if (row.style.display === 'none' || row.classList.contains('hidden')) {
                row.remove();
            }
        });

        // 4. Remove hidden columns/cells (This removes your hidden image, video, and action cells)
        let cells = cloneTable.querySelectorAll('th, td');
        cells.forEach(cell => {
            if (cell.style.display === 'none' || cell.classList.contains('hidden')) {
                cell.remove();
            }
        });

        // 5. Convert table to Excel workbook
        // Adding { raw: true } forces Excel to treat everything as plain text, fixing the =#NUM! error
        let wb = XLSX.utils.table_to_book(cloneTable, { 
            sheet: "Reports",
            raw: true 
        });
        
        // 6. Download the file
        XLSX.writeFile(wb, filename + ".xlsx");
    }
</script>

<script>
    // Global State
    let notificationCount = 0;
    let notificationsList = [];
    let lastCheckedReportId = 0;

    // ==========================================
    // 1. MEDIA MODAL MANAGEMENT
    // ==========================================
    function showMedia(url, type) {
        console.log('Opening media:', type, url);
        const modal = document.getElementById('mediaModal');
        const content = document.getElementById('mediaContent');
        
        if (type === 'image') {
            content.innerHTML = `<img src="${url}" alt="Report Image" class="max-w-[90vw] max-h-[90vh] w-auto h-auto rounded-lg shadow-md" onerror="console.error('Failed to load image:', this.src)">`;
        } else if (type === 'video') {
            content.innerHTML = `<video src="${url}" controls autoplay class="max-w-[90vw] max-h-[90vh] w-auto h-auto rounded-lg shadow-md" onerror="console.error('Failed to load video:', this.src)">Your browser does not support the video tag.</video>`;
        }
        
        // Tailwind Toggle: Switch from hidden to a flex container layout
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeMediaModal() {
        const modal = document.getElementById('mediaModal');
        const content = document.getElementById('mediaContent');
        
        // Tailwind Toggle: Hide container layout
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        
        // Stop video playback if it exists
        const video = content.querySelector('video');
        if (video) {
            video.pause();
            video.currentTime = 0;
        }
        
        content.innerHTML = '';
    }

    // Close modal on background clicks or Escape press
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('mediaModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeMediaModal();
            });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeMediaModal();
    });


    // ==========================================
    // 2. TABLE DATA FILTERING
    // ==========================================
    function filterAdminReports() {
        const filterValue = document.getElementById('adminDisasterFilter').value;
        const dateFilter = document.getElementById('dateFilter').value;
        // Targets all table rows containing data-disaster-type
        const rows = document.querySelectorAll('tbody tr[data-disaster-type]');
        
        rows.forEach(row => {
            const disasterType = row.getAttribute('data-disaster-type');
            const reportDate = row.getAttribute('data-report-date');
            let showRow = true;
            
            // Filter by disaster type
            if (filterValue !== '' && disasterType !== filterValue) {
                showRow = false;
            }
            
            // Filter by month
            if (dateFilter !== '' && reportDate) {
                const rowDate = new Date(reportDate);
                const monthNames = ['january', 'february', 'march', 'april', 'may', 'june', 
                                    'july', 'august', 'september', 'october', 'november', 'december'];
                const reportMonth = monthNames[rowDate.getMonth()];
                
                if (reportMonth !== dateFilter) {
                    showRow = false;
                }
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    function filterByDate() {
        filterAdminReports();
    }


    // ==========================================
    // 3. NOTIFICATION DROPDOWN & READ STATE
    // ==========================================
    function toggleNotificationDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('notificationDropdown');
        
        // Tailwind Toggle
        dropdown.classList.toggle('hidden');
        
        if (!dropdown.classList.contains('hidden')) {
            populateNotifications();
            markAllAsRead();
        }
    }

    function markAllAsRead() {
        notificationsList.forEach(notif => notif.read = true);
        updateNotificationBadge();
        
        localStorage.setItem('notificationsBadgeReset', 'true');
        
        fetch('/admin/notifications/read-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        }).catch(error => console.error('Error marking all as read:', error));
    }

    function populateNotifications() {
        const listContainer = document.getElementById('notificationList');
        
        if (notificationsList.length === 0) {
            listContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center py-8 text-slate-400 gap-2">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-8 h-8 opacity-50">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="text-sm">No new notifications</span>
                </div>`;
            return;
        }

        listContainer.innerHTML = notificationsList.map(notif => `
            <div class="flex items-start gap-3 p-4 border-b border-slate-100 hover:bg-slate-50 cursor-pointer transition-colors ${notif.read ? '' : 'bg-indigo-50/50'}" onclick="viewReport(${notif.id})">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-slate-800 truncate">New Report Submitted</div>
                    <div class="text-xs text-slate-600 truncate">${notif.disaster_type} - ${notif.user_name}</div>
                    <div class="text-[10px] text-slate-400 mt-1">${notif.time_ago}</div>
                </div>
            </div>
        `).join('');
    }

    function viewReport(reportId) {
        const notif = notificationsList.find(n => n.id === reportId);
        if (notif && !notif.read) {
            notif.read = true;
            if (notif.notification_id) {
                fetch(`/admin/notifications/${notif.notification_id}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                }).catch(error => console.error('Error marking notification as read:', error));
            }
        }
        
        document.getElementById('notificationDropdown').classList.add('hidden');
        sessionStorage.setItem('openReportModal', reportId);
        window.location.href = '{{ route('admin.reports') }}';
    }

    function updateNotificationBadge() {
        const unreadCount = notificationsList.filter(n => !n.read).length;
        notificationCount = unreadCount;
        const badge = document.getElementById('notificationBadge');
        
        if (!badge) return;
        
        badge.textContent = unreadCount;
        if (unreadCount > 0) {
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    // Document click listener to dismiss active dropdown
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });


    // ==========================================
    // 4. POLLING ENGINE & REALTIME ALERTS
    // ==========================================
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
                
                if (notificationsList.length > 0) {
                    lastCheckedReportId = Math.max(...notificationsList.map(n => n.id));
                }
                updateNotificationBadge();
            })
            .catch(error => console.error('Error loading notifications:', error));
    }

    function hasShownNotification(reportId) {
        const shown = localStorage.getItem('shown_notifications') || '[]';
        return JSON.parse(shown).includes(reportId);
    }

    function markNotificationAsShown(reportId) {
        let shownIds = JSON.parse(localStorage.getItem('shown_notifications') || '[]');
        shownIds.push(reportId);
        if (shownIds.length > 100) shownIds = shownIds.slice(-100);
        localStorage.setItem('shown_notifications', JSON.stringify(shownIds));
    }

    function showRealtimeNotification(report) {
        if (hasShownNotification(report.id)) return;

        // Tailwind Realtime Alert Element Creation
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-4 right-4 z-[999] flex items-center gap-3 bg-slate-900 text-white px-4 py-3 rounded-xl shadow-xl border border-slate-800 transition-all duration-300 max-w-sm transform translate-y-10 opacity-0';
        notification.innerHTML = `
            <div class="p-2 bg-red-500/10 text-red-500 rounded-lg shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-bold">🚨 New Report Submitted!</div>
                <div class="text-xs text-slate-300 truncate">${report.disaster_type || 'Report'} - ${report.user_name}</div>
            </div>`;
        
        document.body.appendChild(notification);
        
        // Trigger presentation animation frame
        requestAnimationFrame(() => {
            notification.classList.remove('translate-y-10', 'opacity-0');
        });
        
        playNotificationSound();
        markNotificationAsShown(report.id);
        
        setTimeout(() => {
            notification.classList.add('translate-y-10', 'opacity-0');
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

    function checkNewReports() {
        fetch(`{{ route('admin.reports.check-new') }}?since=${lastCheckedReportId}`)
            .then(response => response.json())
            .then(data => {
                if (data.new_reports && data.new_reports.length > 0) {
                    localStorage.removeItem('notificationsBadgeReset');
                    
                    data.new_reports.forEach(report => {
                        if (!hasShownNotification(report.id)) {
                            showRealtimeNotification(report);
                            
                            const existingNotif = notificationsList.find(n => n.id === report.id);
                            if (!existingNotif) {
                                notificationsList.unshift({
                                    id: report.id,
                                    disaster_type: report.disaster_type_name,
                                    user_name: report.user_name,
                                    time_ago: 'Just now',
                                    read: false
                                });
                                
                                if (notificationsList.length > 50) {
                                    notificationsList = notificationsList.slice(0, 50);
                                }
                            }
                            if (report.id > lastCheckedReportId) lastCheckedReportId = report.id;
                        }
                    });
                    updateNotificationBadge();
                }
            })
            .catch(error => console.error('Error checking reports:', error));
    }

    // Multi-tab synchronization event listener
    window.addEventListener('storage', function(e) {
        if (e.key === 'notificationsBadgeReset') {
            notificationsList.forEach(notif => notif.read = true);
            updateNotificationBadge();
        }
    });

    // Run Application Poll Cycle
    loadNotifications();
    setInterval(checkNewReports, 5000);
</script>
@endpush