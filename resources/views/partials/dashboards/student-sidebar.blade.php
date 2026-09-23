<!-- ================= STUDENT SIDEBAR ================= -->
<aside id="app-sidebar" aria-label="Primary navigation" class="w-full md:w-64 flex-shrink-0 bg-slate-50 dark:bg-slate-900 md:bg-transparent md:dark:bg-transparent">
  <!-- Drawer header (mobile only) -->
  <div class="md:hidden flex items-center justify-between mb-3">
    <span class="font-bold text-slate-900 dark:text-white text-sm">Menu</span>
    <button type="button" data-mobile-nav-close aria-label="Close menu" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700/50">
      <i class="fa-solid fa-xmark text-lg"></i>
    </button>
  </div>

  <nav class="bg-white dark:bg-slate-800 rounded-2xl p-2 shadow-sm border border-slate-200 dark:border-slate-700 space-y-1">
    <!-- Student Navigation Items -->
    <div class="space-y-1">
      <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
        <span>Student Portal</span>
        <span class="text-[9px] bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 font-mono px-1.5 py-0.5 rounded">ACTIVE</span>
      </div>

      <a href="{{ route('student.dashboard', ['tab' => 'student-dashboard']) }}" id="nav-student-dashboard" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-xl transition bg-academic-50 text-academic-700 dark:bg-academic-900/40 dark:text-academic-100">
        <i class="fa-solid fa-book-open-reader w-5 text-center text-academic-600 dark:text-academic-400"></i>
        My Research Portal
      </a>

      <a href="{{ route('student.dashboard', ['tab' => 'student-roadmap']) }}" id="nav-student-roadmap" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-list-check w-5 text-center"></i>
        Research Roadmap
      </a>

      <a href="{{ route('student.dashboard', ['tab' => 'student-proposals']) }}" id="nav-student-proposals" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-file-signature w-5 text-center"></i>
        Topic Proposals
        <span id="nav-proposals-count" class="ml-auto px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">0</span>
      </a>

      <a href="{{ url('/student/meetings') }}" id="nav-student-meetings" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-comments w-5 text-center"></i>
        Meeting Logs
      </a>

      <a href="{{ url('/student/resources') }}" id="nav-student-resources" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-graduation-cap w-5 text-center text-academic-600 dark:text-academic-400"></i>
        Learning Resources
      </a>

      <a href="{{ url('/student/defense-readiness') }}" id="nav-defense-readiness" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-shield-halved w-5 text-center text-emerald-600 dark:text-emerald-400"></i>
        Defense Readiness
      </a>
    </div>

  </nav>

  <!-- SUPERVISOR PROFILE CARD -->
  <div class="mt-4 p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm text-xs space-y-3">
    <div class="flex items-center gap-3">
      <div id="sidebar-supervisor-avatar" class="w-10 h-10 rounded-xl bg-academic-800 text-amber-400 font-extrabold flex items-center justify-center text-sm shadow-md">
        {{ $studentSidebarSupervisorInitials }}
      </div>
      <div class="min-w-0">
        <h4 id="sidebar-supervisor-name" class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $studentSidebarSupervisorName }}</h4>
        <p id="sidebar-supervisor-title" class="text-[10px] text-slate-500 dark:text-slate-400">{{ $studentSidebarSupervisorTitle }}</p>
      </div>
    </div>

    <div class="pt-2 border-t border-slate-100 dark:border-slate-700 space-y-1.5 text-[11px]">
      <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
        <i class="fa-solid fa-building-columns text-academic-600 w-3.5 text-center"></i>
        <span id="sidebar-supervisor-dept" class="truncate">{{ $studentSidebarSupervisorDepartment }}</span>
      </div>
      <div class="flex items-center gap-1.5 font-mono text-[10px] text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-900 p-1.5 rounded-lg border border-slate-100 dark:border-slate-800">
        <i class="fa-solid fa-envelope text-amber-500"></i>
        <span id="sidebar-supervisor-email" class="truncate">{{ $studentSidebarSupervisorEmail }}</span>
      </div>
    </div>
  </div>

  <!-- BOOK A SESSION CARD -->
  <div class="mt-4 p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm">
    <h4 class="font-bold text-slate-900 dark:text-white text-xs mb-3 flex items-center gap-2">
      <i class="fa-solid fa-calendar-alt text-academic-600 dark:text-academic-400"></i>
      Need to chat? Book a time:
    </h4>
    @if ($studentSidebarBookingUrl)
      <a href="{{ $studentSidebarBookingUrl }}" target="_blank" rel="noopener"
         class="flex items-center justify-between gap-2 p-3 rounded-xl bg-academic-600 hover:bg-academic-700 text-white font-bold shadow-md ring-2 ring-academic-300 dark:ring-academic-500/40 transition">
        <span class="flex items-center gap-2 leading-tight">
          <i class="fa-solid fa-calendar-check"></i>
          <span>
            Book With Supervisor
            <span class="block text-[10px] font-medium text-academic-100">Availability is set by your supervisor</span>
          </span>
        </span>
        <i class="fa-solid fa-up-right-from-square text-xs"></i>
      </a>
    @else
      <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/40 px-3 py-3 text-xs font-medium text-slate-600 dark:text-slate-300">
        <i class="fa-solid fa-user-clock mr-1.5 text-academic-600 dark:text-academic-400"></i>
        To be set by supervisor.
      </div>
    @endif
  </div>

  <div class="mt-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-2">
    <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Account</div>
    <a href="{{ route('student.profile') }}" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm rounded-xl transition {{ request()->routeIs('student.profile') ? 'bg-academic-50 text-academic-700 dark:bg-academic-900/40 dark:text-academic-100 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium' }}">
      <i class="fa-solid fa-user-gear w-5 text-center"></i>
      Profile Settings
    </a>
    <button onclick="logout()" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-900/30 hover:text-rose-600 dark:hover:text-rose-400 transition">
      <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
      Log Out
    </button>
  </div>
</aside>
