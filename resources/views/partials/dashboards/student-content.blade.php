<!-- ================= LOADING SKELETON (shown until data arrives) ================= -->
<div id="student-loading" class="space-y-6">
  <div class="skeleton h-40 w-full"></div>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="skeleton h-24"></div>
    <div class="skeleton h-24"></div>
    <div class="skeleton h-24"></div>
    <div class="skeleton h-24"></div>
  </div>
  <div class="skeleton h-64 w-full"></div>
</div>

<!-- ================= LIVE CONTENT (revealed after fetch) ================= -->
<div id="student-live" class="hidden space-y-6">

  <!-- ============ TAB: MY RESEARCH PORTAL ============ -->
  <section id="tab-student-dashboard" class="tab-content fade-in space-y-6">

    <!-- Student Info Header Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-4 gap-3">
        <div class="flex items-center gap-3">
          <div id="student-portal-avatar" class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg shadow-md">ST</div>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h2 id="student-portal-name" class="text-lg font-bold text-slate-900 dark:text-white">Student</h2>
              <span id="student-portal-matric" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">MATRIC: —</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Supervisor: <strong id="student-supervisor-name" class="text-slate-800 dark:text-slate-200">—</strong></p>
            <div id="student-dashboard-drive" class="mt-2">
              <p id="student-dashboard-drive-text" class="text-[11px] text-slate-500 dark:text-slate-400">No student drive has been added yet.</p>
              <a id="student-dashboard-drive-link" href="#" target="_blank" rel="noopener" class="hidden mt-1 inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1.5 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/40">
                <i class="fa-brands fa-google-drive"></i>
                Open Student Drive
              </a>
            </div>
          </div>
        </div>

        <!-- Student Action Buttons -->
        <div class="flex items-center gap-2 flex-wrap">
          <button id="btn-submit-topic" onclick="openSubmitTopicModal()" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
            <i class="fa-solid fa-plus text-academic-600"></i> Submit Topic Proposal
          </button>
          <button onclick="window.location.href='{{ url('/student/meetings') }}'" class="px-3.5 py-2 bg-academic-700 hover:bg-academic-800 text-white rounded-xl text-xs font-semibold shadow-md transition flex items-center gap-1.5">
            <i class="fa-solid fa-clipboard-list"></i> Log Progress Session
          </button>
          <button onclick="loadStudentData(true)" title="Refresh" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
            <i id="refresh-icon" class="fa-solid fa-rotate"></i> Refresh
          </button>
        </div>
      </div>

      <!-- Active Research Status Box -->
      <div class="bg-gradient-to-r from-slate-900 to-academic-900 text-white rounded-xl p-5 space-y-3">
        <div class="flex items-center justify-between text-xs text-academic-200">
          <span class="uppercase tracking-wider font-semibold"><i class="fa-solid fa-bookmark text-amber-400 mr-1"></i> Research Topic</span>
          <span id="student-topic-status" class="px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-semibold text-[10px]">PENDING</span>
        </div>
        <h3 id="student-active-topic" class="text-base sm:text-lg font-bold leading-snug">"Topic pending approval"</h3>
        <p id="student-topic-approved" class="hidden text-[11px] text-emerald-300"><i class="fa-solid fa-circle-check mr-1"></i>Approved on <span id="student-topic-approved-date"></span></p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs border-t border-slate-700/80">
          <div>
            <span class="text-slate-400 block text-[10px]">Current Milestone:</span>
            <span id="student-milestone" class="font-semibold text-amber-400">—</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[10px]">Supervisor Email:</span>
            <span id="student-supervisor-email" class="font-mono text-[11px] text-amber-300">—</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[10px]">Overall Progress:</span>
            <div class="flex items-center gap-2 mt-0.5">
              <div class="w-full bg-slate-700 h-2 rounded-full overflow-hidden">
                <div id="student-progress-bar" class="bg-amber-400 h-full rounded-full transition-all duration-700" style="width: 0%"></div>
              </div>
              <span id="student-progress-pct" class="font-bold text-amber-400 text-xs">0%</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Current Stage</p>
            <p id="metric-stage" class="mt-2 text-lg font-bold text-academic-900 dark:text-white leading-tight">—</p>
          </div>
          <div class="w-11 h-11 rounded-xl bg-academic-100 dark:bg-academic-900/30 text-academic-600 dark:text-academic-400 flex items-center justify-center"><i class="fa-solid fa-lightbulb"></i></div>
        </div>
      </div>
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Progress</p>
            <p id="metric-progress" class="mt-2 text-2xl font-bold text-emerald-700 dark:text-emerald-400">0%</p>
          </div>
          <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center"><i class="fa-solid fa-chart-line"></i></div>
        </div>
      </div>
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Meetings</p>
            <p id="metric-meetings" class="mt-2 text-2xl font-bold text-blue-700 dark:text-blue-400">0</p>
          </div>
          <div class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center"><i class="fa-solid fa-comments"></i></div>
        </div>
      </div>
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Points</p>
            <p id="metric-points" class="mt-2 text-2xl font-bold text-amber-700 dark:text-amber-400">0</p>
          </div>
          <div class="w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center"><i class="fa-solid fa-star"></i></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ TAB: RESEARCH ROADMAP ============ -->
  <section id="tab-student-roadmap" class="tab-content hidden fade-in space-y-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
      <h3 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider flex items-center gap-2">
        <i class="fa-solid fa-list-check text-academic-600"></i> Research Lifecycle Roadmap
      </h3>
      <p class="text-xs text-slate-500 dark:text-slate-400">Your 12-stage journey from topic ideation to graduation.</p>
      <div id="student-roadmap-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 text-xs">
        <!-- Rendered by renderStudentRoadmap() -->
      </div>
    </div>
  </section>

  <!-- ============ TAB: TOPIC PROPOSALS ============ -->
  <section id="tab-student-proposals" class="tab-content hidden fade-in space-y-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
      <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
        <div>
          <h3 class="font-bold text-slate-900 dark:text-white text-base">My Topic Proposals</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400">Submissions sent to your supervisor for approval.</p>
        </div>
        <button id="btn-submit-topic-2" onclick="openSubmitTopicModal()" class="px-3 py-1.5 bg-academic-700 hover:bg-academic-800 text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
          <i class="fa-solid fa-plus"></i> New Proposal
        </button>
      </div>
      <div id="student-drive-card" class="mx-5 mt-5 rounded-xl border border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/40 px-4 py-4 text-xs text-slate-600 dark:text-slate-300">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
              <i class="fa-brands fa-google-drive text-emerald-600"></i>
              Student Drive Folder
            </p>
            <p id="student-drive-text" class="mt-1">No drive folder has been added yet.</p>
          </div>
          <a id="student-drive-link" href="#" target="_blank" rel="noopener" class="hidden shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 font-semibold text-white transition hover:bg-emerald-700">
            <i class="fa-solid fa-up-right-from-square"></i>
            Open Drive
          </a>
        </div>
      </div>
      <div id="student-proposals-list" class="p-5 space-y-3">
        <div class="text-center text-slate-400 text-xs py-6"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Loading proposals...</div>
      </div>
    </div>
  </section>

</div>
