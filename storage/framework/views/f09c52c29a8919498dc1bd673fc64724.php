<!-- ================= HEADER ================= -->
<header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-30 shadow-sm">
  <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-2">
    
    <!-- Mobile menu toggle + Brand & Title -->
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
      <button id="mobile-nav-toggle" type="button" data-mobile-nav-toggle aria-controls="app-sidebar" aria-expanded="false" aria-label="Open navigation menu" class="mobile-nav-toggle md:hidden -ml-1 w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition shrink-0">
        <span class="mobile-nav-line" aria-hidden="true"></span>
        <span class="mobile-nav-line" aria-hidden="true"></span>
        <span class="mobile-nav-line" aria-hidden="true"></span>
      </button>
      <div class="h-9 sm:h-10 px-2.5 sm:px-3 rounded-xl bg-gradient-to-tr from-academic-900 via-academic-800 to-amber-600 flex items-center justify-center shadow-md ring-1 ring-white/10 shrink-0">
        <img src="<?php echo e(asset('img/afriscribe-logo-white.png')); ?>" alt="AfriScribe" class="h-4 sm:h-5 w-auto object-contain" loading="lazy" />
      </div>
      <div class="min-w-0">
        <h1 class="font-bold text-sm sm:text-lg text-slate-900 dark:text-white leading-tight flex items-center gap-2 min-w-0">
          <span class="truncate">Research Supervision Portal</span>
          <span class="hidden xs:inline text-[10px] font-mono px-2 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 font-bold shrink-0">LASU</span>
        </h1>
        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Supervisor Research Management</p>
      </div>
    </div>

    <!-- User Profile -->
    <div class="flex items-center gap-3">
      <!-- Role Badge -->
      <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold">
        <i class="fa-solid fa-user-shield"></i>
        <span>Supervisor Hub</span>
      </div>

      <!-- User Avatar -->
      <div class="hidden md:flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-700">
        <div id="user-avatar" class="w-9 h-9 rounded-full bg-academic-800 text-amber-400 border border-amber-500/30 flex items-center justify-center text-sm font-bold shadow-sm">
          OA
        </div>
        <div class="text-left">
          <p class="text-xs font-semibold text-slate-800 dark:text-slate-100 leading-tight">Dr. O. Arowolo</p>
          <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">Authorized Supervisor</p>
        </div>
      </div>

      <!-- Logout Button -->
      <button onclick="logout()" class="p-2 rounded-lg text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition" title="Log out">
        <i class="fa-solid fa-right-from-bracket text-base"></i>
      </button>
    </div>
  </div>
</header><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/partials/dashboards/supervisor-header.blade.php ENDPATH**/ ?>