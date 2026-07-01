
<nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight leading-none">CROWDLENS</h1>
                    <p class="text-xs font-semibold text-blue-600 tracking-wide uppercase">Citizen Portal</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">

                <div class="relative notification-bell">
                    <button id="notificationBellBtn" title="Notifications" class="relative p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors focus:outline-none">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="userNotificationBadge" class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white hidden [&.show]:block">0</span>
                    </button>
                    
                    <div class="notification-dropdown absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl border border-slate-100 z-50 overflow-hidden hidden" id="userNotificationsDropdown">
                        
                        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-slate-800">Notifications</h3>
                            <button onclick="markAllAsRead()" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Mark all read</button>
                        </div>
                        
                        <div class="max-h-80 overflow-y-auto" id="notificationList">
                            <div id="emptyState" class="p-6 text-center flex flex-col items-center justify-center text-slate-400 h-full min-h-[200px]">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-8 h-8 mb-2 opacity-50">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <div class="text-sm">No new notifications</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 border-l border-slate-200 pl-4">
                    <span class="hidden sm:flex text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                    <a href="{{ route('settings') }}" class="text-slate-500 hover:text-slate-700 transition-colors" title="Settings">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </a>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="ml-2">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
