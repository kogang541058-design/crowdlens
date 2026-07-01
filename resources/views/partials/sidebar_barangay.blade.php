
<div class="w-full md:w-[260px] bg-gradient-to-b from-slate-800 to-slate-900 text-white md:fixed md:h-screen md:overflow-y-auto z-40 border-b md:border-b-0 border-slate-700 shadow-lg">
    
    <div class="p-4 md:p-6 border-b border-white/10 flex items-center justify-between md:block">
        <div>
            <h2 class="text-lg md:text-xl font-bold tracking-tight text-white">{{ $barangay->name }}</h2>
            <p class="text-xs md:text-sm text-slate-400 mt-0.5">Barangay Portal</p>
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
                <a href="{{ route('barangay.dashboard') }}" class="group flex items-center py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('barangay.dashboard') ? 'pl-3.5 pr-4 font-semibold bg-blue-600/20 text-white border-l-4 border-blue-500' : 'px-4 font-medium text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('barangay.dashboard') ? 'text-blue-400' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="{{ route('barangay.reports') }}" class="group flex items-center py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('barangay.reports') ? 'pl-3.5 pr-4 font-semibold bg-blue-600/20 text-white border-l-4 border-blue-500' : 'px-4 font-medium text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('barangay.reports') ? 'text-blue-400' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reports
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