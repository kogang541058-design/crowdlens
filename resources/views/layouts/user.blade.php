<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900 h-100">

    @include('partials.user-nav')
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 sm:space-y-8">
        @yield('content')
    </main>

    @stack('scripts')


    
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Elements
        const bellBtn = document.getElementById('notificationBellBtn');
        const badge = document.getElementById('userNotificationBadge');
        const dropdown = document.getElementById('userNotificationsDropdown');
        const notifList = document.getElementById('notificationList');
        const emptyState = document.getElementById('emptyNotifications');
        const markReadBtn = document.querySelector('button[onclick="markAllAsRead()"]');
        
        let unreadCount = 0;

        // 1. Toggle Dropdown
        bellBtn.addEventListener('click', function() {
            dropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInsideDropdown = dropdown.contains(event.target);
            const isClickOnBell = bellBtn.contains(event.target);
            
            if (!isClickOnBell && !isClickInsideDropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        });

        // 2. Update Badge UI
        function updateBadge(count) {
            unreadCount = count;
            badge.textContent = unreadCount;
            
            if (unreadCount > 0) {
                badge.classList.remove('hidden');
                if(emptyState) emptyState.style.display = 'none';
            } else {
                badge.classList.add('hidden');
                if(notifList.children.length <= 1) { // Only empty state exists
                    if(emptyState) emptyState.style.display = 'block';
                }
            }
        }

        // 3. Render a single notification item
        function renderNotification(message, isRead = false, timeString = 'Just now') {
            const bgClass = isRead ? 'bg-white opacity-60' : 'bg-indigo-50/30';
            
            const notifHtml = `
                <div class="p-3 border-b border-slate-100 hover:bg-slate-50 cursor-pointer transition-colors ${bgClass}">
                    <div class="text-xs font-bold text-slate-800 mb-0.5">Update</div>
                    <div class="text-xs text-slate-600 leading-snug">${message}</div>
                    <div class="text-[10px] text-slate-400 mt-1 font-medium">${timeString}</div>
                </div>
            `;
            
            if(emptyState) emptyState.style.display = 'none';
            notifList.insertAdjacentHTML('afterbegin', notifHtml);
        }

        // 4. Fetch initial notifications from database
        function fetchNotifications() {
            fetch('{{ route('notifications.fetch') }}')
                .then(response => response.json())
                .then(data => {
                    updateBadge(data.unread_count);
                    
                    if (data.notifications.length > 0) {
                        // Clear empty state text
                        notifList.innerHTML = ''; 
                        
                        // Reverse so the oldest of the 10 is inserted first, 
                        // and the newest ends up at the very top (afterbegin)
                        data.notifications.reverse().forEach(notif => {
                            const date = new Date(notif.created_at).toLocaleDateString();
                            renderNotification(notif.message, notif.is_read, date);
                        });
                    } else {
                        // Restore empty state
                        notifList.innerHTML = '<div id="emptyNotifications" class="p-4 text-center text-sm text-slate-500">No new notifications.</div>';
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }

        // 5. Mark all as read via AJAX
        if (markReadBtn) {
            markReadBtn.onclick = function() {
                // Add CSRF token for POST request
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch('{{ route('notifications.mark-read') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                }).then(response => {
                    if(response.ok) {
                        updateBadge(0);
                        // Visually dim all notifications
                        const items = notifList.querySelectorAll('.p-4');
                        items.forEach(item => {
                            if(item.id !== 'emptyNotifications') item.classList.add('opacity-60');
                        });
                    }
                });
            };
        }

        // Initialize: Fetch data on page load
        // fetchNotifications();

        // 6. Laravel Echo Real-time Listener
        const userId = {{ auth()->id() ?? 'null' }};

        if (userId !== null && window.Echo) {
            window.Echo.private(`user.${userId}`)
                .listen('.admin.responded', (event) => {
                    updateBadge(unreadCount + 1);
                    renderNotification(event.message, false, 'Just now');
                });
        }
    });
</script>
</body>
</html>