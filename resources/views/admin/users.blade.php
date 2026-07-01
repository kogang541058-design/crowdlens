@extends('layouts.admin')

@section('title', 'Users - Admin Dashboard')


@push('styles')
<style>
    .notification-dropdown { display: none; }
    .notification-dropdown.show { display: block; animation: slideDown 0.2s ease-out; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 w-full max-w-7xl mx-auto">
    
    @include('partials.notif_logout', ['page_name' => 'Users Management'])

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 md:p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="text-lg font-bold text-slate-800">All Users</h2>
            <button class="add-btn flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg font-semibold text-sm transition-colors shadow-sm shadow-indigo-500/30 w-full sm:w-auto justify-center">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add User
            </button>
        </div>

        @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">NAME</th>
                        <th class="px-6 py-4 flex items-center">
                            EMAIL
                            <button onclick="toggleEmailMasking()" id="emailToggleBtn" title="Show/Hide emails" class="ml-2 text-indigo-500 hover:text-indigo-700 transition-colors focus:outline-none">
                                <svg id="eyeIcon" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg id="eyeOffIcon" class="w-4 h-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </th>
                        <th class="px-6 py-4">REGISTERED</th>
                        <th class="px-6 py-4">STATUS</th>
                        <th class="px-6 py-4 text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            <span class="email-masked font-mono">
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
                            <span class="email-full hidden">{{ $user->email }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            @if($user->isBlocked())
                                <span class="bg-red-50 text-red-700 px-2.5 py-1 rounded-lg text-xs font-bold border border-red-100 cursor-help" title="Reason: {{ $user->block_reason }}">Blocked</span>
                            @else
                                <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg text-xs font-bold border border-emerald-100">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="relative inline-block text-left">
                                <button onclick="toggleManageMenu({{ $user->id }})" class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-sm font-bold transition-colors border border-blue-200">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                    Manage
                                </button>
                                
                                <div id="manageMenu{{ $user->id }}" class="manage-menu absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-100 z-[100] overflow-hidden" style="display: none;">
                                    <button onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}'); toggleManageMenu({{ $user->id }})" class="w-full px-4 py-2.5 text-left text-sm font-medium text-slate-700 hover:bg-slate-50 flex items-center gap-2 border-b border-slate-50 transition-colors">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </button>
                                    
                                    @if($user->isBlocked())
                                    <form method="POST" action="{{ route('admin.users.block', $user->id) }}" onsubmit="return confirm('Are you sure you want to unblock this user?');" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="unblock">
                                        <button type="submit" class="w-full px-4 py-2.5 text-left text-sm font-medium text-emerald-600 hover:bg-emerald-50 flex items-center gap-2 transition-colors">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Unblock
                                        </button>
                                    </form>
                                    @else
                                    <button onclick="openBlockModal({{ $user->id }}, '{{ $user->name }}'); toggleManageMenu({{ $user->id }})" class="w-full px-4 py-2.5 text-left text-sm font-medium text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
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
        </div>
        @else
        <div class="p-12 text-center flex flex-col items-center justify-center text-slate-500 bg-slate-50/50">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-16 h-16 text-slate-300 mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <p class="text-lg font-medium text-slate-600">No users registered yet</p>
            <p class="text-sm mt-1">Add a new user to get started.</p>
        </div>
        @endif
    </div>
</div>

<div id="addUserModal" style="display: none;" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[10000] items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 md:p-8 max-w-md w-full relative shadow-2xl">
        <button onclick="closeAddUserModal()" class="absolute top-4 right-4 bg-slate-100 hover:bg-slate-200 text-slate-500 w-8 h-8 rounded-full flex items-center justify-center transition-colors focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        
        <h2 class="text-2xl font-bold text-slate-800 mb-6">Add New User</h2>
        
        <div id="addUserErrors" style="display: none;" class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 text-sm">
            <ul id="addUserErrorList" class="list-disc pl-5 m-0 space-y-1"></ul>
        </div>
        
        <form id="addUserForm" method="POST" action="{{ route('admin.users.store') }}" class="flex flex-col gap-4">
            @csrf
            
            <div>
                <label class="block mb-1.5 text-sm font-bold text-slate-700">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm outline-none">
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-bold text-slate-700">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm outline-none">
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-bold text-slate-700">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm outline-none">
                <p class="text-slate-500 text-xs mt-1.5">Minimum 8 characters</p>
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-bold text-slate-700">Confirm Password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm outline-none">
            </div>

            <div class="flex gap-3 mt-4">
                <button type="button" onclick="closeAddUserModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 py-2.5 rounded-lg font-bold transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white py-2.5 rounded-lg font-bold transition-colors shadow-sm shadow-emerald-500/30">
                    Add User
                </button>
            </div>
        </form>
    </div>
</div>

<div id="editUserModal" style="display: none;" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[10000] items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 md:p-8 max-w-md w-full relative shadow-2xl">
        <button onclick="closeEditUserModal()" class="absolute top-4 right-4 bg-slate-100 hover:bg-slate-200 text-slate-500 w-8 h-8 rounded-full flex items-center justify-center transition-colors focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        
        <h2 class="text-2xl font-bold text-slate-800 mb-6">Edit User</h2>
        
        <div id="editUserErrors" style="display: none;" class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 text-sm">
            <ul id="editUserErrorList" class="list-disc pl-5 m-0 space-y-1"></ul>
        </div>
        
        @if($errors->any() && old('_form') === 'edit_user')
        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 text-sm">
            <ul class="list-disc pl-5 m-0 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form id="editUserForm" method="POST" class="flex flex-col gap-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="editUserId" name="user_id">
            <input type="hidden" name="_form" value="edit_user">
            
            <div>
                <label class="block mb-1.5 text-sm font-bold text-slate-700">Name <span class="text-red-500">*</span></label>
                <input type="text" id="editUserName" name="name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm outline-none">
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-bold text-slate-700">Email <span class="text-red-500">*</span></label>
                <input type="email" id="editUserEmail" name="email" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm outline-none">
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-bold text-slate-700">New Password</label>
                <input type="password" name="password" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm outline-none">
                <p class="text-slate-500 text-xs mt-1.5">Leave blank to keep current password</p>
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-bold text-slate-700">Confirm New Password</label>
                <input type="password" name="password_confirmation" minlength="8" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm outline-none">
            </div>

            <div class="flex gap-3 mt-4">
                <button type="button" onclick="closeEditUserModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 py-2.5 rounded-lg font-bold transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2.5 rounded-lg font-bold transition-colors shadow-sm shadow-blue-500/30">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>

<div id="blockUserModal" style="display: none;" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[10000] items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden">
        <div class="bg-red-50 border-b border-red-100 p-5 flex items-center justify-between">
            <h3 class="text-xl font-bold text-red-700 flex items-center gap-2">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Block User
            </h3>
        </div>
        
        <form method="POST" id="blockUserForm" class="p-6">
            @csrf
            @method('PATCH')
            <input type="hidden" name="action" value="block">
            
            <div class="mb-6">
                <p class="text-slate-600 mb-5 bg-slate-50 p-3 rounded-lg border border-slate-100">
                    You are about to block: <strong id="blockUserName" class="text-slate-900 ml-1"></strong>
                </p>
                
                <div class="mb-4">
                    <label class="block mb-1.5 text-sm font-bold text-slate-700">Select Reason for Blocking</label>
                    <select name="block_reason" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm outline-none appearance-none">
                        <option value="">Choose a reason...</option>
                        <option value="Spam reports">Spam reports</option>
                        <option value="Always invalid reports">Always invalid reports</option>
                        <option value="Abusive behavior">Abusive behavior</option>
                        <option value="Fake information">Fake information</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1.5 text-sm font-bold text-slate-700">Block Duration</label>
                    <select name="block_duration" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm outline-none appearance-none">
                        <option value="">Select duration...</option>
                        <option value="1">1 Day</option>
                        <option value="3">3 Days</option>
                        <option value="7">1 Week</option>
                        <option value="14">2 Weeks</option>
                        <option value="30">1 Month</option>
                        <option value="permanent">Permanent</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeBlockModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 py-2.5 rounded-lg font-bold transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-lg font-bold transition-colors shadow-sm shadow-red-500/30">
                    Block User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection




@push('scripts')
<style>
    /* Add a quick animation for the real-time toast pop-up */
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .toast-enter { animation: slideInRight 0.3s ease-out forwards; }
    .toast-exit { animation: slideOutRight 0.3s ease-in forwards; }
</style>

<script>
    // ==========================================
    // NOTIFICATION SYSTEM
    // ==========================================
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
                
                if (notificationsList.length > 0) {
                    lastCheckedReportId = Math.max(...notificationsList.map(n => n.id));
                }
                
                updateNotificationBadge();
            })
            .catch(error => console.error('Error loading notifications:', error));
    }

    loadNotifications();

    function hasShownNotification(reportId) {
        const shown = localStorage.getItem('shown_notifications') || '[]';
        const shownIds = JSON.parse(shown);
        return shownIds.includes(reportId);
    }

    function markNotificationAsShown(reportId) {
        const shown = localStorage.getItem('shown_notifications') || '[]';
        let shownIds = JSON.parse(shown);
        shownIds.push(reportId);
        if (shownIds.length > 100) {
            shownIds = shownIds.slice(-100);
        }
        localStorage.setItem('shown_notifications', JSON.stringify(shownIds));
    }

    function showRealtimeNotification(report) {
        if (hasShownNotification(report.id)) return;

        const notification = document.createElement('div');
        // Styled with Tailwind
        notification.className = 'fixed bottom-6 right-6 bg-slate-900 text-white px-5 py-4 rounded-xl shadow-2xl z-[9999] flex items-center gap-4 toast-enter min-w-[300px] border border-slate-700';
        notification.innerHTML = `
            <div class="bg-red-500/20 text-red-400 p-2 rounded-full flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div>
                <div class="font-bold text-sm text-slate-100">🚨 New Report Submitted!</div>
                <div class="text-xs text-slate-400 mt-0.5">${report.disaster_type || 'Report'} - ${report.user_name}</div>
            </div>
        `;
        
        document.body.appendChild(notification);
        playNotificationSound();
        markNotificationAsShown(report.id);
        
        setTimeout(() => {
            notification.classList.remove('toast-enter');
            notification.classList.add('toast-exit');
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
            markAllAsRead();
        }
    }

    function markAllAsRead() {
        notificationsList.forEach(notif => {
            if (!notif.read) notif.read = true;
        });
        
        updateNotificationBadge();
        localStorage.setItem('notificationsBadgeReset', 'true');
        
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
                <div class="p-6 text-center flex flex-col items-center justify-center text-slate-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-8 h-8 mb-2 opacity-50">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <div class="text-sm">No new notifications</div>
                </div>
            `;
            return;
        }

        listContainer.innerHTML = notificationsList.map(notif => `
            <div class="px-4 py-3 border-b border-slate-50 cursor-pointer transition-colors hover:bg-slate-50 ${notif.read ? 'opacity-75' : 'bg-indigo-50/30'}" onclick="viewReport(${notif.id})">
                <div class="flex items-start gap-3">
                    <div class="bg-indigo-100 text-indigo-600 p-2 rounded-lg flex-shrink-0">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-800">New Report Submitted</div>
                        <div class="text-xs text-slate-600 mt-0.5">${notif.disaster_type} - ${notif.user_name}</div>
                        <div class="text-[10px] text-slate-400 mt-1 font-medium">${notif.time_ago}</div>
                    </div>
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).catch(error => console.error('Error marking notification as read:', error));
            }
        }
        
        document.getElementById('notificationDropdown').classList.remove('show');
        sessionStorage.setItem('openReportModal', reportId);
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

    window.addEventListener('storage', function(e) {
        if (e.key === 'notificationsBadgeReset') {
            notificationsList.forEach(notif => notif.read = true);
            updateNotificationBadge();
        }
    });

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

    checkNewReports();
    setInterval(checkNewReports, 5000);


    // ==========================================
    // USER MANAGEMENT
    // ==========================================

    function openAddUserModal() {
        document.getElementById('addUserModal').style.display = 'flex';
        document.getElementById('addUserErrors').style.display = 'none';
        document.getElementById('addUserErrorList').innerHTML = '';
    }

    function closeAddUserModal() {
        document.getElementById('addUserModal').style.display = 'none';
        document.getElementById('addUserForm').reset();
        document.getElementById('addUserErrors').style.display = 'none';
        document.getElementById('addUserErrorList').innerHTML = '';
    }

    function toggleManageMenu(userId) {
        const menu = document.getElementById('manageMenu' + userId);
        const allMenus = document.querySelectorAll('.manage-menu');
        
        allMenus.forEach(m => {
            if (m !== menu) {
                m.style.display = 'none';
            }
        });
        
        if (menu.style.display === 'none' || menu.style.display === '') {
            menu.style.display = 'block';
        } else {
            menu.style.display = 'none';
        }
    }

    function toggleEmailMasking() {
        const maskedEmails = document.querySelectorAll('.email-masked');
        const fullEmails = document.querySelectorAll('.email-full');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');
        
        maskedEmails.forEach(masked => masked.classList.toggle('hidden'));
        fullEmails.forEach(full => full.classList.toggle('hidden'));
        
        eyeIcon.classList.toggle('hidden');
        eyeOffIcon.classList.toggle('hidden');
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.manage-menu') && !event.target.closest('button')) {
            document.querySelectorAll('.manage-menu').forEach(menu => {
                menu.style.display = 'none';
            });
        }
    });

    function editUser(userId, userName, userEmail) {
        document.getElementById('editUserModal').style.display = 'flex';
        document.getElementById('editUserId').value = userId;
        document.getElementById('editUserName').value = userName;
        document.getElementById('editUserEmail').value = userEmail;
        document.getElementById('editUserForm').action = `/admin/users/${userId}`;
        
        document.getElementById('editUserErrors').style.display = 'none';
        document.getElementById('editUserErrorList').innerHTML = '';
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').style.display = 'none';
        document.getElementById('editUserForm').reset();
        document.getElementById('editUserErrors').style.display = 'none';
        document.getElementById('editUserErrorList').innerHTML = '';
    }

    function openBlockModal(userId, userName) {
        document.getElementById('blockUserModal').style.display = 'flex';
        document.getElementById('blockUserName').textContent = userName;
        document.getElementById('blockUserForm').action = `/admin/users/${userId}/block`;
    }

    function closeBlockModal() {
        document.getElementById('blockUserModal').style.display = 'none';
        document.getElementById('blockUserForm').reset();
    }

    // AJAX for Edit User
    document.addEventListener('DOMContentLoaded', function() {
        const editForm = document.getElementById('editUserForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                document.getElementById('editUserErrors').style.display = 'none';
                document.getElementById('editUserErrorList').innerHTML = '';
                
                const password = this.querySelector('input[name="password"]').value;
                const passwordConfirm = this.querySelector('input[name="password_confirmation"]').value;
                const errors = [];
                
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
                        const errorList = document.getElementById('editUserErrorList');
                        const errorMessages = Object.values(data.errors).flat();
                        errorList.innerHTML = errorMessages.map(error => `<li>${error}</li>`).join('');
                        document.getElementById('editUserErrors').style.display = 'block';
                    } else if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    const errorList = document.getElementById('editUserErrorList');
                    errorList.innerHTML = '<li>An error occurred. Please try again.</li>';
                    document.getElementById('editUserErrors').style.display = 'block';
                });
                
                return false;
            });
        }
    });
    
    // Auto-reopen Edit User modal if validation fails (server-side fallback)
    @if($errors->any() && old('_form') === 'edit_user')
        document.getElementById('editUserModal').style.display = 'flex';
        document.getElementById('editUserId').value = '{{ old('user_id') }}';
        document.getElementById('editUserName').value = '{{ old('name') }}';
        document.getElementById('editUserEmail').value = '{{ old('email') }}';
        document.getElementById('editUserForm').action = `/admin/users/{{ old('user_id') }}`;
    @endif
    
    // AJAX for Add User
    document.addEventListener('DOMContentLoaded', function() {
        const addForm = document.getElementById('addUserForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                document.getElementById('addUserErrors').style.display = 'none';
                document.getElementById('addUserErrorList').innerHTML = '';
                
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
                        const errorList = document.getElementById('addUserErrorList');
                        const errorMessages = Object.values(data.errors).flat();
                        errorList.innerHTML = errorMessages.map(error => `<li>${error}</li>`).join('');
                        document.getElementById('addUserErrors').style.display = 'block';
                    } else if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    const errorList = document.getElementById('addUserErrorList');
                    errorList.innerHTML = '<li>An error occurred. Please try again.</li>';
                    document.getElementById('addUserErrors').style.display = 'block';
                });
                
                return false;
            });
        }
        
        // Attach event listener for Add User button manually in case class is clicked
        const addBtn = document.querySelector('.add-btn');
        if(addBtn) {
            addBtn.addEventListener('click', openAddUserModal);
        }
    });
</script>
@endpush
