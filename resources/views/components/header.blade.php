<!-- MAIN TOP HEADER NAVIGATION -->
<header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-30 shadow-sm">
  <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-2">
    
    <!-- Brand & Title -->
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
      <!-- Mobile hamburger (opens the off-canvas nav drawer). Hidden on md+. -->
      <button id="mobile-nav-toggle" type="button" onclick="toggleMobileNav()" aria-label="Open navigation menu" class="md:hidden -ml-1 w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition shrink-0">
        <i class="fa-solid fa-bars text-lg"></i>
      </button>
      <div class="h-9 sm:h-10 px-2.5 sm:px-3 rounded-xl bg-gradient-to-tr from-academic-900 via-academic-800 to-amber-600 flex items-center justify-center shadow-md shadow-academic-800/20 shrink-0 ring-1 ring-white/10">
        <img src="{{ asset('img/afriscribe-logo-white.png') }}" alt="AfriScribe" class="h-4 sm:h-5 w-auto object-contain" loading="lazy" />
      </div>
      <div class="min-w-0">
        <h1 class="font-bold text-sm sm:text-lg text-slate-900 dark:text-white leading-tight flex items-center gap-2 min-w-0">
          <span class="truncate">Research Supervision Portal</span>
          <span class="hidden xs:inline text-[10px] font-mono px-2 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 font-bold shrink-0">LASU</span>
        </h1>
        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 min-w-0">
          <span id="gas-status-dot" class="w-2 h-2 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
          <span id="gas-status-text" class="truncate">Verifying LASU Session Auth...</span>
        </p>
      </div>
    </div>

    <!-- Auth Bar & Header Controls -->
    <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
      
      <!-- Active User Role Selector Badge -->
      <div class="flex items-center bg-slate-100 dark:bg-slate-700/80 p-1 rounded-xl border border-slate-200 dark:border-slate-600">
        <button id="role-btn-supervisor" onclick="switchUserRole('supervisor')" class="px-2 sm:px-2.5 py-1.5 sm:py-1 text-xs font-semibold rounded-lg transition-all bg-white dark:bg-slate-800 text-academic-700 dark:text-academic-100 shadow-sm flex items-center gap-1" title="Supervisor Hub">
          <i class="fa-solid fa-user-shield text-amber-500"></i>
          <span class="hidden xs:inline">Supervisor Hub</span>
        </button>
        <button id="role-btn-student" onclick="switchUserRole('student')" class="px-2 sm:px-2.5 py-1.5 sm:py-1 text-xs font-medium text-slate-600 dark:text-slate-300 rounded-lg hover:text-slate-900 dark:hover:text-white transition-all flex items-center gap-1" title="Student View">
          <i class="fa-solid fa-user-graduate"></i>
          <span class="hidden xs:inline">Student View</span>
        </button>
      </div>

      <!-- Dark Mode Switch -->
      <button id="theme-toggle" onclick="toggleDarkMode()" class="p-2 rounded-lg text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/50 transition" title="Toggle Theme">
        <i class="fa-solid fa-moon text-base dark:hidden"></i>
        <i class="fa-solid fa-sun text-base hidden dark:block"></i>
      </button>

      <!-- User Profile Pill -->
      <div class="hidden md:flex items-center gap-2 pl-3 border-l border-slate-200 dark:border-slate-700">
        <div id="user-avatar" class="w-8 h-8 rounded-full bg-academic-800 text-amber-400 border border-amber-500/30 flex items-center justify-center text-xs font-bold shadow-sm">
          OA
        </div>
        <div class="text-left">
          <p id="user-display-name" class="text-xs font-semibold text-slate-800 dark:text-slate-100 leading-tight">Dr. O. Arowolo</p>
          <p id="user-display-role" class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">Authorized Supervisor</p>
        </div>
      </div>

      <!-- Logout Button (shown only when a student is logged in) -->
      <button id="logout-btn" onclick="logout()" class="hidden p-2 rounded-lg text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition items-center gap-1.5 text-xs font-semibold" title="Log out">
        <i class="fa-solid fa-right-from-bracket text-base"></i>
        <span class="hidden sm:inline">Log out</span>
      </button>
    </div>
  </div>
</header>
