@php
    $navLinkClasses = 'nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition';
    $activeClasses = 'bg-academic-50 text-academic-700 dark:bg-academic-900/40 dark:text-academic-100 font-semibold';
    $idleClasses = 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50';
@endphp

<aside id="app-sidebar" aria-label="Admin navigation" class="w-full md:w-72 flex-shrink-0 bg-slate-50 dark:bg-slate-900 md:bg-transparent md:dark:bg-transparent">
    <div class="md:hidden flex items-center justify-between mb-3">
        <span class="font-bold text-slate-900 dark:text-white text-sm">Menu</span>
        <button type="button" data-mobile-nav-close aria-label="Close menu" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700/50">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <nav class="bg-white dark:bg-slate-800 rounded-2xl p-2 shadow-sm border border-slate-200 dark:border-slate-700 space-y-1">
        <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Admin Workspace</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ $navLinkClasses }} {{ request()->routeIs('admin.dashboard') ? $activeClasses : $idleClasses }}">
            <i class="fa-solid fa-chart-line w-5 text-center text-academic-600 dark:text-academic-400"></i>
            Dashboard
        </a>
        <a href="{{ route('admin.users') }}" class="{{ $navLinkClasses }} {{ request()->routeIs('admin.users') ? $activeClasses : $idleClasses }}">
            <i class="fa-solid fa-users w-5 text-center"></i>
            Users
        </a>
        <a href="{{ route('admin.config') }}" class="{{ $navLinkClasses }} {{ request()->routeIs('admin.config') ? $activeClasses : $idleClasses }}">
            <i class="fa-solid fa-sliders w-5 text-center"></i>
            Configuration
        </a>
        <a href="{{ route('admin.resources') }}" class="{{ $navLinkClasses }} {{ request()->routeIs('admin.resources', 'admin.resources.*') ? $activeClasses : $idleClasses }}">
            <i class="fa-solid fa-book-open w-5 text-center"></i>
            Resources
        </a>
        <a href="{{ route('admin.audit-logs') }}" class="{{ $navLinkClasses }} {{ request()->routeIs('admin.audit-logs', 'admin.audit-logs.*') ? $activeClasses : $idleClasses }}">
            <i class="fa-solid fa-shield-halved w-5 text-center"></i>
            Audit Logs
        </a>
    </nav>

    <div class="mt-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-4 text-xs space-y-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-academic-800 text-white flex items-center justify-center text-sm font-bold shadow-md">{{ $adminShellProfile['initials'] }}</div>
            <div>
                <h4 class="font-bold text-slate-900 dark:text-white text-xs">{{ $adminShellProfile['name'] }}</h4>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $adminShellProfile['roleLabel'] }}</p>
            </div>
        </div>
        <div class="pt-2 border-t border-slate-100 dark:border-slate-700 space-y-1.5 text-[11px]">
            <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-building-columns text-academic-600 w-3.5 text-center"></i>
                <span class="truncate">{{ $adminShellProfile['universityName'] }}</span>
            </div>
            <div class="flex items-center gap-1.5 font-mono text-[10px] text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-900 p-1.5 rounded-lg border border-slate-100 dark:border-slate-800">
                <i class="fa-solid fa-envelope text-amber-500"></i>
                <span class="truncate">{{ $adminShellProfile['email'] }}</span>
            </div>
        </div>
    </div>
</aside>