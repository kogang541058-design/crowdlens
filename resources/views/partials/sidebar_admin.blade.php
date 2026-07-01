<div class="w-full md:w-[260px] bg-gradient-to-b from-slate-800 to-slate-900 text-white md:fixed md:h-screen md:overflow-y-auto z-40 border-b md:border-b-0 border-slate-700 shadow-lg">
    
    <div class="p-4 md:p-6 border-b border-white/10 flex items-center justify-between md:block">
        <div>
            <h2 class="text-lg md:text-xl font-bold tracking-tight text-white">Admin Portal</h2>
            <p class="text-xs md:text-sm text-slate-400 mt-0.5">Davao City Reports</p>
        </div>
        
        <button onclick="toggleMobileMenu()" class="md:hidden p-2 rounded-lg text-slate-400 hover:bg-slate-700 hover:text-white focus:outline-none transition-colors" title="Toggle Menu">
            <svg id="menuIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <nav id="sidebarMenu" class="hidden md:block transition-all duration-300">
        <ul class="p-3 space-y-1">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="group flex items-center py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.dashboard') ? 'pl-3.5 pr-4 font-semibold bg-blue-600/20 text-white border-l-4 border-blue-500' : 'px-4 font-medium text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-blue-400' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="{{ route('admin.map') }}" class="group flex items-center py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.map') ? 'pl-3.5 pr-4 font-semibold bg-blue-600/20 text-white border-l-4 border-blue-500' : 'px-4 font-medium text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.map') ? 'text-blue-400' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Map
                </a>
            </li>

            <li>
                <a href="{{ route('admin.reports') }}" class="group flex items-center py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.reports') ? 'pl-3.5 pr-4 font-semibold bg-blue-600/20 text-white border-l-4 border-blue-500' : 'px-4 font-medium text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.reports') ? 'text-blue-400' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reports
                </a>
            </li>

            <li>
                <a href="{{ route('admin.users') }}" class="group flex items-center py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.users') ? 'pl-3.5 pr-4 font-semibold bg-blue-600/20 text-white border-l-4 border-blue-500' : 'px-4 font-medium text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.users') ? 'text-blue-400' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Users
                </a>
            </li>

            <li>
                <a href="{{ route('admin.barangay') }}" class="group flex items-center py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.barangay') ? 'pl-3.5 pr-4 font-semibold bg-blue-600/20 text-white border-l-4 border-blue-500' : 'px-4 font-medium text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.barangay') ? 'text-blue-400' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Barangay
                </a>
            </li>

            <li>
                <a href="{{ route('admin.solved') }}" class="group flex items-center py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.solved') ? 'pl-3.5 pr-4 font-semibold bg-blue-600/20 text-white border-l-4 border-blue-500' : 'px-4 font-medium text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.solved') ? 'text-blue-400' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Solved
                </a>
            </li>

            <li>
                <a href="#" class="group flex items-center py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.settings') ? 'pl-3.5 pr-4 font-semibold bg-blue-600/20 text-white border-l-4 border-blue-500' : 'px-4 font-medium text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.settings') ? 'text-blue-400' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>
            </li>
        </ul>
    </nav>
</div>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('sidebarMenu');
        const icon = document.getElementById('menuIcon');
        
        menu.classList.toggle('hidden');
        
        if (menu.classList.contains('hidden')) {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
        } else {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
        }
    }
</script>