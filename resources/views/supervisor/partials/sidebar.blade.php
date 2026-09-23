@php
    $navLinkClasses = 'nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition';
    $activeClasses = 'bg-academic-50 text-academic-700 dark:bg-academic-900/40 dark:text-academic-100 font-semibold';
    $idleClasses = 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50';
@endphp

<aside id="app-sidebar" aria-label="Supervisor navigation" class="w-full md:w-64 flex-shrink-0 bg-slate-50 dark:bg-slate-900 md:bg-transparent md:dark:bg-transparent">
    <div class="md:hidden flex items-center justify-between mb-3">
        <span class="font-bold text-slate-900 dark:text-white text-sm">Menu</span>
        <button type="button" data-mobile-nav-close aria-label="Close menu" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700/50">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <nav class="bg-white dark:bg-slate-800 rounded-2xl p-2 shadow-sm border border-slate-200 dark:border-slate-700 space-y-1">
        <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Supervisor Hub</div>

        <a href="{{ route('supervisor.dashboard') }}" class="{{ $navLinkClasses }} {{ request()->routeIs('supervisor.dashboard') ? $activeClasses : $idleClasses }}">
            <i class="fa-solid fa-chart-line w-5 text-center text-academic-600 dark:text-academic-400"></i>
            Dashboard
        </a>
        <a href="{{ route('supervisor.students') }}" class="{{ $navLinkClasses }} {{ request()->routeIs('supervisor.students') ? $activeClasses : $idleClasses }}">
            <i class="fa-solid fa-users w-5 text-center"></i>
            Student Roster
        </a>
        <a href="{{ route('supervisor.proposals') }}" class="{{ $navLinkClasses }} {{ request()->routeIs('supervisor.proposals') ? $activeClasses : $idleClasses }}">
            <i class="fa-solid fa-file-signature w-5 text-center"></i>
            Topic Approvals
        </a>
        <a href="{{ route('supervisor.meetings') }}" class="{{ $navLinkClasses }} {{ request()->routeIs('supervisor.meetings', 'supervisor.meetings.*') ? $activeClasses : $idleClasses }}">
            <i class="fa-solid fa-comments w-5 text-center"></i>
            Meeting Logs
        </a>
        <a href="{{ route('supervisor.analytics') }}" class="{{ $navLinkClasses }} {{ request()->routeIs('supervisor.analytics') ? $activeClasses : $idleClasses }}">
            <i class="fa-solid fa-chart-pie w-5 text-center text-purple-500"></i>
            Analytics
        </a>
        <a href="{{ route('supervisor.resources.pending') }}" class="{{ $navLinkClasses }} {{ request()->routeIs('supervisor.resources.pending') ? $activeClasses : $idleClasses }}">
            <i class="fa-solid fa-check-to-mark w-5 text-center text-emerald-500"></i>
            Resource Approvals
        </a>
    </nav>

    <div class="mt-4 p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm text-xs space-y-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-academic-800 text-amber-400 font-extrabold flex items-center justify-center text-sm shadow-md">
                {{ $sidebarSupervisorProfile['supervisorInitials'] }}
            </div>
            <div>
                <h4 class="font-bold text-slate-900 dark:text-white text-xs">{{ $sidebarSupervisorProfile['supervisorName'] }}</h4>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $sidebarSupervisorProfile['supervisorTitle'] }}</p>
            </div>
        </div>

        @if (!empty($sidebarSupervisorProfile['researchAreas']))
            <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-snug">
                {{ $sidebarSupervisorProfile['researchAreas'] }}
            </p>
        @endif

        <div class="pt-2 border-t border-slate-100 dark:border-slate-700 space-y-1.5 text-[11px]">
            <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-building-columns text-academic-600 w-3.5 text-center"></i>
                <span class="truncate">{{ $sidebarSupervisorProfile['supervisorDepartment'] }}</span>
            </div>
            @if (!empty($sidebarSupervisorProfile['universityName']))
                <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-university text-academic-600 w-3.5 text-center"></i>
                    <span>{{ $sidebarSupervisorProfile['universityName'] }}</span>
                </div>
            @endif
            <div class="flex items-center gap-1.5 font-mono text-[10px] text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-900 p-1.5 rounded-lg border border-slate-100 dark:border-slate-800">
                <i class="fa-solid fa-envelope text-amber-500"></i>
                <span class="truncate">{{ $sidebarSupervisorProfile['supervisorEmail'] }}</span>
            </div>
        </div>
    </div>

    <div class="mt-4 p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm">
        <h4 class="font-bold text-slate-900 dark:text-white text-xs mb-3 flex items-center gap-2">
            <i class="fa-solid fa-calendar-alt text-academic-600 dark:text-academic-400"></i>
            Booking link
        </h4>
        @if (!empty($sidebarSupervisorProfile['bookingUrl']))
            <a href="{{ $sidebarSupervisorProfile['bookingUrl'] }}" target="_blank" rel="noopener" class="flex items-center justify-between gap-2 p-3 rounded-xl bg-academic-600 hover:bg-academic-700 text-white font-bold shadow-md transition">
                <span class="flex items-center gap-2 leading-tight">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Open booking page</span>
                </span>
                <i class="fa-solid fa-up-right-from-square text-xs"></i>
            </a>
        @else
            <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/40 px-3 py-3 text-xs font-medium text-slate-600 dark:text-slate-300">
                Booking link has not been configured yet.
            </div>
        @endif
    </div>
</aside>