<!-- MAIN LAYOUT CONTAINER -->
<div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col md:flex-row gap-4 sm:gap-6">

  <!-- SIDE NAVIGATION BAR (off-canvas drawer on mobile, inline on md+) -->
  <aside id="app-sidebar" aria-label="Primary navigation" class="w-full md:w-64 flex-shrink-0 bg-slate-50 dark:bg-slate-900 md:bg-transparent md:dark:bg-transparent">
    <!-- Drawer header (mobile only) -->
    <div class="md:hidden flex items-center justify-between mb-3">
      <span class="font-bold text-slate-900 dark:text-white text-sm">Menu</span>
      <button type="button" data-mobile-nav-close aria-label="Close menu" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700/50">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>
    <nav class="bg-white dark:bg-slate-800 rounded-2xl p-2 shadow-sm border border-slate-200 dark:border-slate-700 space-y-1">
      
      <!-- Supervisor Navigation Items -->
      <div id="nav-group-supervisor" class="space-y-1">
        <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
          <span>Supervisor Hub</span>
          <span id="nav-auth-status-tag" class="text-[9px] bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 font-mono px-1.5 py-0.5 rounded">AUTHENTICATED</span>
        </div>
        <button onclick="switchTab('supervisor-dashboard')" id="nav-supervisor-dashboard" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-xl transition bg-academic-50 text-academic-700 dark:bg-academic-900/40 dark:text-academic-100">
          <i class="fa-solid fa-chart-line w-5 text-center text-academic-600 dark:text-academic-400"></i>
          Supervisor Matrix
        </button>
        <button onclick="switchTab('supervisor-students')" id="nav-supervisor-students" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
          <i class="fa-solid fa-users w-5 text-center"></i>
          Assigned Cohort (<span id="nav-students-count">5</span>)
        </button>
        <button onclick="switchTab('supervisor-proposals')" id="nav-supervisor-proposals" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
          <i class="fa-solid fa-file-signature w-5 text-center"></i>
          Topic Approvals
          <span id="badge-pending-proposals" class="ml-auto px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">2</span>
        </button>
        <button onclick="switchTab('supervisor-resource-approvals')" id="nav-supervisor-resource-approvals" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
          <i class="fa-solid fa-check-to-mark w-5 text-center text-academic-600 dark:text-academic-400"></i>
          Resource Approvals
          <span id="badge-pending-resources" class="ml-auto px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 hidden">0</span>
        </button>
        <a href="https://tech.olaarowolo.com/apps/broadcast-graphics-studio.html" target="_blank" rel="noopener" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
          <i class="fa-solid fa-tv w-5 text-center text-amber-500"></i>
          Broadcast Graphics Studio
        </a>
        <button onclick="switchTab('supervisor-analytics')" id="nav-supervisor-analytics" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
          <i class="fa-solid fa-chart-pie w-5 text-center text-purple-500"></i>
          Analytics &amp; Insights
        </button>
        <a href="https://docs.google.com/spreadsheets/d/1SjroodZJ-LeYOYqjH0OEFKvMjByq95JU5ELFmNw_Juk/edit?gid=1509989229#gid=1509989229" target="_blank" rel="noopener" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
          <i class="fa-solid fa-database w-5 text-center text-emerald-600"></i>
          Portal Database (Sheet)
        </a>
      </div>

      <!-- Student Navigation Items -->
      <div id="nav-group-student" class="space-y-1">
        <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Student Portal</div>
        <button onclick="switchTab('student-dashboard')" id="nav-student-dashboard" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
          <i class="fa-solid fa-book-open-reader w-5 text-center text-academic-600 dark:text-academic-400"></i>
          My Research Portal
        </button>
        <button onclick="switchTab('student-meetings')" id="nav-student-meetings" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
          <i class="fa-solid fa-comments w-5 text-center"></i>
          Meeting Logs
        </button>
        <button onclick="switchTab('student-resources')" id="nav-student-resources" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
          <i class="fa-solid fa-graduation-cap w-5 text-center text-academic-600 dark:text-academic-400"></i>
          Learning Resources
        </button>
        <a href="https://olaarowolo.com/apps/ug-pg-dr.html" target="_blank" rel="noopener" id="nav-defense-readiness" class="nav-btn w-full items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition hidden">
          <i class="fa-solid fa-shield-halved w-5 text-center text-emerald-600 dark:text-emerald-400"></i>
          UG/PG Defense Readiness Tracker
        </a>
      </div>

      <!-- Drive Folders (shown for logged-in students) -->
      <div id="nav-group-drive" class="hidden pt-2 border-t border-slate-100 dark:border-slate-700/60 space-y-1">
        <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Drive Folders</div>
        <a id="link-my-drive" href="#" target="_blank" rel="noopener" class="hidden w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
          <i class="fa-brands fa-google-drive w-5 text-center text-emerald-600"></i>
          My Drive Folder
        </a>
        <a id="link-cohort-drive" href="#" target="_blank" rel="noopener" class="hidden w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
          <i class="fa-solid fa-users-rectangle w-5 text-center text-academic-600"></i>
          Cohort Drive
        </a>
      </div>

      <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60 space-y-1">
        <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Tools & System</div>
        <button onclick="switchTab('ai-assistant')" id="nav-ai-assistant" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
          <i class="fa-solid fa-wand-magic-sparkles w-5 text-center text-amber-500"></i>
          Gemini Assistant
        </button>
      </div>
    </nav>

    <!-- SUPERVISOR DETAILED PROFILE CARD -->
    <div class="mt-4 p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm text-xs space-y-3">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-academic-800 text-amber-400 font-extrabold flex items-center justify-center text-sm shadow-md">
          <?php echo e($sidebarSupervisorProfile['supervisorInitials']); ?>

        </div>
        <div>
          <h4 class="font-bold text-slate-900 dark:text-white text-xs"><?php echo e($sidebarSupervisorProfile['supervisorName']); ?></h4>
          <p class="text-[10px] text-slate-500 dark:text-slate-400"><?php echo e($sidebarSupervisorProfile['supervisorTitle']); ?></p>
        </div>
      </div>

      <?php if(!empty($sidebarSupervisorProfile['researchAreas'])): ?>
        <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-snug">
          <?php echo e($sidebarSupervisorProfile['researchAreas']); ?>

        </p>
      <?php endif; ?>

      <div class="pt-2 border-t border-slate-100 dark:border-slate-700 space-y-1.5 text-[11px]">
        <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
          <i class="fa-solid fa-building-columns text-academic-600 w-3.5 text-center"></i>
          <span class="truncate"><?php echo e($sidebarSupervisorProfile['supervisorDepartment']); ?></span>
        </div>
        <?php if(!empty($sidebarSupervisorProfile['universityName'])): ?>
          <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
            <i class="fa-solid fa-university text-academic-600 w-3.5 text-center"></i>
            <span><?php echo e($sidebarSupervisorProfile['universityName']); ?></span>
          </div>
        <?php endif; ?>
        <div class="flex items-center gap-1.5 font-mono text-[10px] text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-900 p-1.5 rounded-lg border border-slate-100 dark:border-slate-800">
          <i class="fa-solid fa-envelope text-amber-500"></i>
          <span class="truncate"><?php echo e($sidebarSupervisorProfile['supervisorEmail']); ?></span>
        </div>
      </div>
    </div>

    <!-- BOOK A SESSION CARD -->
    <div class="mt-4 p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm">
      <h4 class="font-bold text-slate-900 dark:text-white text-xs mb-3 flex items-center gap-2">
        <i class="fa-solid fa-calendar-alt text-academic-600 dark:text-academic-400"></i>
        Need to chat? Book a time:
      </h4>
      <?php if(!empty($sidebarSupervisorProfile['bookingUrl'])): ?>
        <a href="<?php echo e($sidebarSupervisorProfile['bookingUrl']); ?>" target="_blank" rel="noopener"
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
      <?php else: ?>
        <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/40 px-3 py-3 text-xs font-medium text-slate-600 dark:text-slate-300">
          <i class="fa-solid fa-user-clock mr-1.5 text-academic-600 dark:text-academic-400"></i>
          To be set by supervisor.
        </div>
      <?php endif; ?>
    </div>
  </aside>
<?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/components/sidebar.blade.php ENDPATH**/ ?>