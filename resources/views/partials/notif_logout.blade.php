@php
    // 1. Setup default variables safely
    $notificationRoute = '';
    $channelName = '';
    $userName = $display_name ?? 'User';

    // 2. Check if it's an Admin
    if (Auth::guard('admin')->check()) {
        $notificationRoute = route('admin.notifications.get');
        $channelName = 'admin-notifications';
        $userName = $display_name ?? Auth::guard('admin')->user()->name;
    } 
    // 3. Check if it's a Barangay
    elseif (Auth::guard('barangay')->check()) {
        $notificationRoute = route('barangay.notifications.get');
        $barangayId = Auth::guard('barangay')->id();
        $channelName = "barangay-notifications.{$barangayId}";
        $userName = $display_name ?? Auth::guard('barangay')->user()->name; // Adjust if your barangay model uses a different column for name
    }
@endphp

<div class="flex flex-row justify-between items-start md:items-center mb-8 gap-4">
    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">{{ $page_name }}</h1>
    
    <div class="flex items-center gap-4 bg-white px-4 py-2.5 rounded-xl shadow-sm border 
            border-slate-200 w-0 w-auto justify-between md:justify-start">
        <div class="relative notification-bell">
            <button onclick="toggleNotificationDropdown(event)" title="Notifications" class="relative p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors focus:outline-none">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span id="notificationBadge" class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white hidden [&.show]:block">0</span>
            </button>
            
            <div class="notification-dropdown absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl border border-slate-100 z-50 overflow-hidden hidden" id="notificationDropdown">
                <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 font-bold text-slate-700 text-sm">
                    Notifications
                </div>
                <div class="max-h-80 overflow-y-auto" id="notificationList">
                    <div class="p-6 text-center flex flex-col items-center justify-center text-slate-400">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-8 h-8 mb-2 opacity-50">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <div class="text-sm">No new notifications</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="h-6 w-px bg-slate-200 block"></div>
        <span class="font-bold text-slate-700 text-sm">{{ $display_name ?? Auth::guard('admin')->user()->name }}</span>
        <div class="h-6 w-px bg-slate-200 block"></div>
        
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-sm font-bold text-red-500 hover:text-red-700 transition-colors">Logout</button>
        </form>
    </div>
</div>




<script>
    // Global Variables
    window.notificationCount = 0;
    window.notificationsList = [];

    window.hasShownNotification = function(reportId) {
        const shown = localStorage.getItem('shown_notifications') || '[]';
        const shownIds = JSON.parse(shown);
        return shownIds.includes(reportId);
    };

    window.markNotificationAsShown = function(reportId) {
        const shown = localStorage.getItem('shown_notifications') || '[]';
        let shownIds = JSON.parse(shown);
        shownIds.push(reportId);
        if (shownIds.length > 100) {
            shownIds = shownIds.slice(-100);
        }
        localStorage.setItem('shown_notifications', JSON.stringify(shownIds));
    };

    window.showRealtimeNotification = function(report) {
        if (window.hasShownNotification(report.id)) return;

        const notification = document.createElement('div');
        notification.className = 'fixed bottom-6 right-6 bg-white border border-slate-200 shadow-2xl rounded-xl p-4 flex items-center gap-4 z-50 transform transition-all duration-500 translate-y-0 opacity-100';
        
        notification.innerHTML = `
            <div class="bg-indigo-50 text-indigo-600 p-2 rounded-full flex-shrink-0">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div>
                <strong class="text-slate-800 text-sm font-bold block">New Report Submitted!</strong>
                <span class="text-slate-500 text-xs">${report.disaster_type_name || report.disaster_type} - ${report.location || 'Location unavailable'}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        window.markNotificationAsShown(report.id);
        
        setTimeout(() => {
            notification.classList.remove('translate-y-0', 'opacity-100');
            notification.classList.add('translate-y-4', 'opacity-0');
            setTimeout(() => notification.remove(), 500);
        }, 5000);
    };

    window.addReportToTable = function(report) {
        const tbody = document.getElementById('reportsTableBody');
        if (!tbody) return;
        
        const row = document.createElement('tr');
        row.className = 'report-row hover:bg-slate-50 transition-colors cursor-pointer group bg-yellow-50/50';
        
        row.setAttribute('data-type', report.disaster_type);
        row.setAttribute('data-status', report.status);
        row.setAttribute('data-action', report.action_status || 'none');
        row.setAttribute('data-barangay-action', report.barangay_action_status || 'none');
        row.setAttribute('data-report-id', report.id);
        row.setAttribute('data-disaster', report.disaster_type_name || report.disaster_type);
        row.setAttribute('data-description', report.description);
        row.setAttribute('data-reporter', report.user_name);
        row.setAttribute('data-location', report.location || 'N/A');
        row.setAttribute('data-date', report.formatted_date);
        row.setAttribute('data-image', report.image || '');
        row.setAttribute('data-video', report.video || '');
        row.onclick = function() { if(typeof openModalWrapper === 'function') openModalWrapper(this); };
        
        let statusBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Unverified</span>';
        if (report.status === 'pending') statusBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>';
        else if (report.status === 'verified') statusBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Verified</span>';
        
        let actionBadge = '<span class="text-slate-400 font-bold">—</span>';
        if (report.action_status === 'solved') actionBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">Solved</span>';
        else if (report.action_status === 'in_progress') actionBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">In Progress</span>';

        let barangayBadge = '<span class="text-slate-400 font-bold">—</span>';
        if (report.barangay_action_status === 'approved') barangayBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Approved</span>';
        else if (report.barangay_action_status === 'disapproved') barangayBadge = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Disapproved</span>';
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 capitalize">
                    ${report.disaster_type}
                </span>
            </td>
            <td class="px-6 py-4 max-w-[260px]">
                <div class="truncate group-hover:text-blue-600 transition-colors">${report.description}</div>
            </td>
            <td class="px-6 py-4 font-medium text-slate-900">${report.user_name}</td>
            <td class="px-6 py-4 max-w-[180px]">
                <div class="truncate text-slate-500">${report.location || 'N/A'}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-slate-500">${report.formatted_date}</td>
            <td class="px-6 py-4 text-center whitespace-nowrap">${statusBadge}</td>
            <td class="px-6 py-4 text-center whitespace-nowrap">${actionBadge}</td>
            <td class="px-6 py-4 text-center whitespace-nowrap">${barangayBadge}</td>
        `;
        
        tbody.insertBefore(row, tbody.firstChild);
        setTimeout(() => row.classList.remove('bg-yellow-50/50'), 3000);
    };

    window.playNotificationSound = function() {
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
    };

    window.updateNotificationBadge = function() {
        const notificationBadge = document.getElementById('notificationBadge');
        if (!notificationBadge) return;

        const unreadCount = window.notificationsList.filter(n => !n.read).length;
        window.notificationCount = unreadCount;
        notificationBadge.textContent = unreadCount;
        
        if (unreadCount > 0) {
            notificationBadge.classList.remove('hidden');
            notificationBadge.classList.add('block');
        } else {
            notificationBadge.classList.remove('block');
            notificationBadge.classList.add('hidden');
        }
    };

    window.populateNotifications = function() {
        const listContainer = document.getElementById('notificationList');
        if (!listContainer) return;
        
        if (window.notificationsList.length === 0) {
            listContainer.innerHTML = `
                <div class="p-8 text-center flex flex-col items-center justify-center text-slate-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-10 h-10 mb-3 opacity-50">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <div class="text-sm font-medium">No new notifications</div>
                </div>
            `;
            return;
        }

        listContainer.innerHTML = window.notificationsList.map(notif => `
            <div class="p-4 border-b border-slate-100 hover:bg-slate-50 cursor-pointer flex items-start gap-3 transition-colors ${notif.read ? 'bg-white' : 'bg-indigo-50/30'}" onclick="window.viewReport(${notif.id})">
                <div class="bg-indigo-100 text-indigo-600 p-2 rounded-full flex-shrink-0 mt-1">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-800">New Report Submitted</div>
                    <div class="text-xs text-slate-600 mt-0.5 capitalize">${notif.disaster_type} - ${notif.user_name}</div>
                    <div class="text-[10px] text-slate-400 mt-1 font-medium">${notif.time_ago}</div>
                </div>
            </div>
        `).join('');
    };

    window.markAllAsRead = function() {
        window.notificationsList.forEach(notif => notif.read = true);
        window.updateNotificationBadge();
        localStorage.setItem('notificationsBadgeReset', 'true');
        
        // Ensure CSRF exists before fetching
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            fetch('/admin/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content
                }
            }).catch(error => console.error('Error marking all as read:', error));
        }
    };

    window.toggleNotificationDropdown = function(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('notificationDropdown');
        if (!dropdown) return;

        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
            window.populateNotifications();
            window.markAllAsRead();
        }
    };

    window.viewReport = function(reportId) {
        const notif = window.notificationsList.find(n => n.id === reportId);
        if (notif && !notif.read) {
            notif.read = true;
            if (notif.notification_id) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    fetch(`/admin/notifications/${notif.notification_id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken.content
                        }
                    }).catch(error => console.error('Error marking as read:', error));
                }
            }
        }
        
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown) dropdown.classList.add('hidden');
        
        const rows = document.querySelectorAll('.report-row');
        rows.forEach(row => {
            const isMatch = row.getAttribute('data-report-id') == reportId || 
                            (row.getAttribute('onclick') && row.getAttribute('onclick').includes(reportId));
            
            if (isMatch) {
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                row.classList.add('bg-yellow-100');
                setTimeout(() => row.classList.remove('bg-yellow-100'), 2000);
                row.click();
            }
        });
        
        window.updateNotificationBadge();
    };

    // ========================================
    // DOM CONTENT LOADED (Bootstrapping)
    // ========================================

    document.addEventListener("DOMContentLoaded", function() {
        
        // Grab the dynamically generated route and channel from Blade
        const fetchUrl = '{{ $notificationRoute }}';
        const listenChannel = '{{ $channelName }}';

        // Only run the fetch if a valid route was generated
        if (fetchUrl) {
            fetch(fetchUrl)
                .then(response => response.json())
                .then(data => {
                    const lastResetTime = localStorage.getItem('notificationsBadgeReset');
                    window.notificationsList = data.notifications.map(notif => ({
                        id: notif.report_id,
                        disaster_type: notif.disaster_type,
                        user_name: notif.user_name,
                        time_ago: notif.time_ago,
                        read: lastResetTime ? true : notif.is_read,
                        notification_id: notif.id
                    }));
                    if (typeof window.updateNotificationBadge === 'function') {
                        window.updateNotificationBadge();
                    }
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        // Only connect to Echo if we have a valid channel
        if (typeof window.Echo !== 'undefined' && listenChannel) {
            
            const channel = window.Echo.channel(listenChannel);
            
            channel.listen('.report.submitted', (event) => {
                console.log(`🚨 Notification received on channel: ${listenChannel}`, event);
                
                localStorage.removeItem('notificationsBadgeReset');
                window.showRealtimeNotification(event);
                
                window.notificationsList.unshift({
                    id: event.id,
                    disaster_type: event.disaster_type_name || event.disaster_type,
                    user_name: event.user_name,
                    time_ago: 'Just now',
                    read: false
                });
                
                if (window.notificationsList.length > 50) {
                    window.notificationsList = window.notificationsList.slice(0, 50);
                }
                
                window.updateNotificationBadge();
                window.addReportToTable(event);
                window.playNotificationSound();
            });

            
            channel.listen('.report.submitted', (event) => {
                console.log('🚨 New report arrived for our Barangay!', event);
                
                // if (typeof window.addReportToTable === 'function') {
                //     window.addReportToTable(event);
                // }

                // 2. Optional: Update the notification badge for the barangay
                // You can replicate the badge logic you used on the admin side here!
            });
            
        } else {
            console.warn('⚠️ Laravel Echo not loaded - Real-time notifications disabled');
        }

        // 3. Dropdown click-outside listener
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationDropdown');
            const bell = document.querySelector('.notification-bell');
            if (dropdown && bell && !dropdown.classList.contains('hidden') && !bell.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // 4. Cross-tab synchronization
        window.addEventListener('storage', function(e) {
            if (e.key === 'notificationsBadgeReset') {
                window.notificationsList.forEach(notif => notif.read = true);
                window.updateNotificationBadge();
            }
        });
    });
</script>