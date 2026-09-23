<!-- ================= HEADER ================= -->
<header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-30 shadow-sm">
  <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
    
    <!-- Mobile menu toggle + Brand & Title -->
    <div class="flex items-center gap-3 min-w-0">
      <button onclick="openMobileNav()" aria-label="Open menu" class="md:hidden p-2 -ml-1 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700/50">
        <i class="fa-solid fa-bars text-lg"></i>
      </button>
      <div class="h-10 px-3 rounded-xl bg-gradient-to-tr from-academic-900 via-academic-800 to-amber-600 flex items-center justify-center shadow-md ring-1 ring-white/10 shrink-0">
        <img src="<?php echo e(asset('img/afriscribe-logo-white.png')); ?>" alt="AfriScribe" class="h-5 w-auto object-contain" loading="lazy" />
      </div>
      <div class="min-w-0">
        <h1 class="font-bold text-lg text-slate-900 dark:text-white leading-tight flex items-center gap-2">
          <span class="truncate">Research Supervision Portal</span>
          <span class="hidden xs:inline text-[10px] font-mono px-2 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 font-bold">LASU</span>
        </h1>
        <p class="text-[11px] text-slate-500 dark:text-slate-400">Student Research Portal</p>
      </div>
    </div>

    <!-- User Profile -->
    <div class="flex items-center gap-3">
      <!-- Role Badge -->
      <div class="px-3 py-1.5 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-xs font-bold flex items-center gap-2">
        <i class="fa-solid fa-user-graduate"></i>
        <span>Student View</span>
      </div>

      <!-- User Avatar -->
      <div class="hidden sm:flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-700">
        <div id="header-user-avatar" class="w-9 h-9 rounded-full bg-academic-800 text-amber-400 border border-amber-500/30 flex items-center justify-center text-sm font-bold shadow-sm">
          ST
        </div>
        <div class="text-left">
          <p id="header-user-name" class="text-xs font-semibold text-slate-800 dark:text-slate-100 leading-tight">Student</p>
          <p id="header-user-role" class="text-[10px] text-blue-600 dark:text-blue-400 font-medium">Research Student</p>
        </div>
      </div>

      <!-- Logout Button -->
      <button onclick="logout()" class="p-2 rounded-lg text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition" title="Log out">
        <i class="fa-solid fa-right-from-bracket text-base"></i>
      </button>
    </div>
  </div>
</header><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/partials/dashboards/student-header.blade.php ENDPATH**/ ?>