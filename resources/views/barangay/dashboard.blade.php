@extends('layouts.admin')

@section('title', "Barangay Dashboard - $barangay->name")

@section('content')
<div class="min-h-screen bg-slate-50 p-4 sm:p-6 lg:p-8 flex-1 w-full">
    
    @include('partials.notif_logout', ['page_name' => 'Dashboard', 'display_name' => $barangay->name])

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-800 mb-1">{{ $totalReports }}</div>
                <div class="text-sm font-medium text-slate-500">Total Reports</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-orange-50 text-orange-600 rounded-lg shrink-0">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-800 mb-1">{{ $pendingReports }}</div>
                <div class="text-sm font-medium text-slate-500">Pending Reports</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg shrink-0">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-3xl font-bold text-slate-800 mb-1">{{ $resolvedReports }}</div>
                <div class="text-sm font-medium text-slate-500">Resolved Reports</div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Notifications Configuration ───────────────────────────────────────
    let notifList = [];
    let unreadCount = 0;

    function toggleNotificationDropdown(e) {
        e.stopPropagation();
        const dd = document.getElementById('notificationDropdown');
        if (!dd) return;
        
        dd.classList.toggle('hidden');
        
        // If the dropdown was just opened
        if (!dd.classList.contains('hidden')) {
            renderNotificationList();
            unreadCount = 0;
            notifList.forEach(n => n.read = true);
            updateNotificationBadge();
            localStorage.setItem('barangay_notif_reset', Date.now());
        }
    }

    // Global click handler using closest() to find the wrapper class
    document.addEventListener('click', (e) => {
        const dd = document.getElementById('notificationDropdown');
        const isClickInsideBell = e.target.closest('.notification-bell');
        
        if (!isClickInsideBell && dd && !dd.classList.contains('hidden')) {
            dd.classList.add('hidden');
        }
    });

    function updateNotificationBadge() {
        const badge = document.getElementById('notificationBadge');
        if (!badge) return;

        badge.textContent = unreadCount;
        
        // Matches your Tailwind logic: hidden [&.show]:block
        if (unreadCount > 0) {
            badge.classList.remove('hidden');
            badge.classList.add('show');
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('show');
        }
    }

    function renderNotificationList() {
        const container = document.getElementById('notificationList');
        if (!container) return;

        // Empty state matching your exact HTML design fallback
        if (!notifList.length) {
            container.innerHTML = `
                <div class="p-6 text-center flex flex-col items-center justify-center text-slate-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-8 h-8 mb-2 opacity-50">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <div class="text-sm">No new notifications</div>
                </div>`;
            return;
        }

        // Dynamic list item generator
        container.innerHTML = notifList.map(n => `
            <div class="flex items-start gap-3 p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors ${n.read ? 'bg-white' : 'bg-blue-50/50'}">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-full shrink-0">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-semibold text-slate-800">${n.label || 'Notification'}</div>
                    <div class="text-xs text-slate-600 mt-0.5">${n.disaster_type} — ${n.user_name}</div>
                    <div class="text-[10px] font-medium text-slate-400 mt-1 uppercase tracking-wider">${n.time_ago}</div>
                </div>
            </div>
        `).join('');
    }
</script>
@endpush