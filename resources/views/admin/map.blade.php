@extends('layouts.admin')

@section('title', 'Map - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="p-4 md:p-8 space-y-6">
    
    
    @include('partials.notif_logout', ['page_name' => 'Reports Map'])

    <div class="relative w-full bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden h-[60vh] min-h-[500px]">
        
        <div class="absolute bottom-4 left-4 right-4 sm:right-auto sm:w-64 z-[400] bg-white/95 backdrop-blur-sm p-4 rounded-xl shadow-md border border-slate-100 transition-all">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Verified Reports</h3>
            
            <div class="flex items-center gap-3 p-2 rounded-lg bg-slate-50 border border-slate-100">
                <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200"></span>
                <span class="text-sm font-medium text-slate-700">Verified ({{ $verifiedReports->count() }})</span>
            </div>
        </div>

        <div id="map" class="absolute inset-0 z-10 w-full h-full bg-slate-100"></div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
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

        // Force Leaflet to recalculate the map container size after a brief delay
        setTimeout(function () {
            map.invalidateSize();
        }, 100);

        // Get disaster types and verified reports from database
        const disasterTypes = @json($disasterTypes);
        const verifiedReports = @json($verifiedReports);

        // Add markers for all verified reports, using disaster type icons/colors
        if (verifiedReports.length > 0) {
            const bounds = [];

            verifiedReports.forEach(report => {
                const lat = parseFloat(report.latitude);
                const lng = parseFloat(report.longitude);
                
                bounds.push([lat, lng]);

                // Find the disaster type for this report
                const dt = disasterTypes ? disasterTypes.find(d => d.name === report.disaster_type) : null;
                const color = (dt && dt.color) ? dt.color : '#10b981'; // fallback green
                const emoji = (dt && dt.icon) ? dt.icon : '✓';

                // Create disaster type-specific icon
                const typeIcon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: ${color}; width: 30px; height: 30px; border-radius: 50%; border: 4px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16px;">${emoji}</div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15],
                    popupAnchor: [0, -15]
                });

                const marker = L.marker([lat, lng], { icon: typeIcon }).addTo(map);
                
                marker.bindPopup(`
                    <div style="padding: 0.75rem; min-width: 200px;">
                        <h4 style="margin: 0 0 0.5rem 0; color: #1e293b; font-size: 1rem; font-weight: 600;">
                            ${report.disaster_type.charAt(0).toUpperCase() + report.disaster_type.slice(1)}
                        </h4>
                        <p style="margin: 0 0 0.5rem 0; color: #64748b; font-size: 0.875rem;">
                            ${report.description.length > 100 ? report.description.substring(0, 100) + '...' : report.description}
                        </p>
                        <div style="margin-bottom: 0.5rem;">
                            <small style="color: #94a3b8; font-size: 0.75rem;">
                                📍 ${report.location || `${lat.toFixed(6)}, ${lng.toFixed(6)}`}
                            </small>
                        </div>
                        <div style="margin-bottom: 0.5rem;">
                            <small style="color: #94a3b8; font-size: 0.75rem;">
                                👤 Reported by: ${report.user.name}
                            </small>
                        </div>
                        <div style="margin-bottom: 0.5rem;">
                            <small style="color: #94a3b8; font-size: 0.75rem;">
                                📅 ${new Date(report.created_at).toLocaleDateString()}
                            </small>
                        </div>
                        ${report.image ? `<a href="/storage/${report.image}" target="_blank" style="display: inline-block; margin-right: 0.5rem; padding: 0.25rem 0.5rem; background: #3b82f6; color: white; text-decoration: none; border-radius: 4px; font-size: 0.75rem;">📷 View Image</a>` : ''}
                        ${report.video ? `<a href="/storage/${report.video}" target="_blank" style="display: inline-block; padding: 0.25rem 0.5rem; background: #8b5cf6; color: white; text-decoration: none; border-radius: 4px; font-size: 0.75rem;">🎥 View Video</a>` : ''}
                        <div style="margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px solid #e2e8f0;">
                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #d1fae5; color: #065f46; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                Verified
                            </span>
                        </div>
                    </div>
                `);
            });

            // Fit map to show all markers
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }

        // Add click handler to map
        map.on('click', function(e) {
            console.log('Clicked at: ', e.latlng);
        });

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
            // Tailwind classes for a floating toast notification
            notification.className = 'fixed bottom-4 right-4 bg-white border-l-4 border-blue-500 rounded-lg shadow-xl p-4 flex items-start gap-3 z-[500] transition-opacity duration-300';
            notification.innerHTML = `
                <div class="text-blue-500 mt-0.5">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-slate-800 text-sm">🚨 New Report Submitted!</div>
                    <div class="text-xs text-slate-500 mt-1">${report.disaster_type || 'Report'} - ${report.user_name}</div>
                </div>
            `;
            
            document.body.appendChild(notification);
            playNotificationSound();
            markNotificationAsShown(report.id);
            
            setTimeout(() => {
                notification.style.opacity = '0';
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
            dropdown.classList.toggle('hidden');
            
            if (!dropdown.classList.contains('hidden')) {
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
                    <div class="p-6 flex flex-col items-center justify-center gap-2 text-slate-400">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-8 h-8 text-slate-300">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="text-sm font-medium">No new notifications</span>
                    </div>
                `;
                return;
            }

            listContainer.innerHTML = notificationsList.map(notif => `
                <button class="w-full text-left p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors flex items-start gap-3 ${notif.read ? 'opacity-70' : 'bg-blue-50/50'}" onclick="viewReport(${notif.id})">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-full flex-shrink-0 mt-0.5">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-slate-800 ${notif.read ? 'font-medium' : 'font-bold'}">New Report Submitted</div>
                        <div class="text-xs text-slate-500 mt-0.5 truncate">${notif.disaster_type} - ${notif.user_name}</div>
                        <div class="text-[10px] text-slate-400 mt-1">${notif.time_ago}</div>
                    </div>
                </button>
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
                notificationBadge.classList.remove('hidden');
            } else {
                notificationBadge.classList.add('hidden');
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
            // Check if clicking outside the dropdown and the button that triggers it
            if (dropdown && !event.target.closest('#notificationDropdown') && !event.target.closest('button[onclick^="toggleNotificationDropdown"]')) {
                dropdown.classList.add('hidden');
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
@endpush