@extends('layouts.app')

@section('title', 'Supervisor Dashboard | Research Supervision Portal | LASU')

@section('content')
<!-- AUTH RESTRICTION BANNER (Triggered if user is NOT authenticated as supervisor) -->
<div id="auth-warning-banner" class="hidden bg-amber-500/10 border-b border-amber-500/20 px-4 py-2 text-xs text-amber-800 dark:text-amber-200">
  <div class="flex items-center gap-2 max-w-7xl mx-auto w-full justify-between">
    <div class="flex items-center gap-2">
      <i class="fa-solid fa-lock text-amber-600"></i>
      <span>Logged in as <strong id="banner-user-email" class="font-mono">student@lasu.edu.ng</strong> (Student View). Supervisor controls are restricted to <strong class="font-mono">olasunkanmi.arowolo@lasu.edu.ng</strong>.</span>
    </div>
  </div>
</div>

<!-- Backdrop behind the mobile nav drawer (tap to close). Hidden on md+. -->
<div id="nav-backdrop" onclick="closeMobileNav()" class="hidden md:hidden fixed inset-0 z-40 bg-slate-900/50 opacity-0"></div>

<!-- MAIN LAYOUT CONTAINER -->
<div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col md:flex-row gap-4 sm:gap-6">

  <!-- SIDE NAVIGATION BAR -->
  @include('components.sidebar')

  <!-- MAIN CONTENT VIEWPORTS CONTAINER -->
  <main class="flex-1 min-w-0 space-y-6">

    <!-- ================= TAB 1: SUPERVISOR DASHBOARD ================= -->
    <section id="tab-supervisor-dashboard" class="tab-content fade-in space-y-6">
      
      <!-- Key Performance Metrics Grid -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        <div class="bg-white dark:bg-slate-800 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
          <div class="flex items-center justify-between gap-1">
            <span class="text-[11px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 leading-tight">Supervised Cohort</span>
            <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs sm:text-sm">
              <i class="fa-solid fa-users"></i>
            </div>
          </div>
          <p id="stat-total-students" class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1.5 sm:mt-2">5</p>
          <p class="text-[10px] sm:text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-medium flex items-center gap-1">
            <i class="fa-solid fa-circle-check"></i> LASU Undergrads
          </p>
        </div>

        <div class="bg-white dark:bg-slate-800 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
          <div class="flex items-center justify-between gap-1">
            <span class="text-[11px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 leading-tight">Pending Topics</span>
            <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs sm:text-sm">
              <i class="fa-solid fa-file-pen"></i>
            </div>
          </div>
          <p id="stat-pending-proposals" class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1.5 sm:mt-2">2</p>
          <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1 font-medium">Requires approval</p>
        </div>

        <div class="bg-white dark:bg-slate-800 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
          <div class="flex items-center justify-between gap-1">
            <span class="text-[11px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 leading-tight">Approved Topics</span>
            <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs sm:text-sm">
              <i class="fa-solid fa-file-circle-check"></i>
            </div>
          </div>
          <p id="stat-approved-topics" class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1.5 sm:mt-2">0</p>
          <p class="text-[10px] sm:text-xs text-indigo-600 dark:text-indigo-400 mt-1 font-medium">Topics approved</p>
        </div>

        <div class="bg-white dark:bg-slate-800 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
          <div class="flex items-center justify-between gap-1">
            <span class="text-[11px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 leading-tight">Supervision Logs</span>
            <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs sm:text-sm">
              <i class="fa-solid fa-comments"></i>
            </div>
          </div>
          <p id="stat-meetings-count" class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1.5 sm:mt-2">6</p>
          <p class="text-[10px] sm:text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-medium"><span id="stat-meetings-this-month">0</span> this month</p>
        </div>
      </div>

      <!-- Students by Stage breakdown -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
            <i class="fa-solid fa-layer-group text-academic-600 dark:text-academic-400"></i>
            Students by Stage
          </h3>
          <span class="text-[11px] text-slate-500 dark:text-slate-400">Across all supervised students</span>
        </div>
        <div id="stage-breakdown" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
          <!-- Populated by renderStageBreakdown() -->
          @for($i=1; $i<=8; $i++)
            <div class="bg-slate-50 dark:bg-slate-700/30 p-3 rounded-xl border border-slate-200 dark:border-slate-700/50">
              <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Stage {{ $i }}</span>
                <span class="text-xs font-bold text-academic-600 dark:text-academic-400">{{ rand(1, 3) }}</span>
              </div>
              <div class="w-full bg-slate-200 dark:bg-slate-600 h-1.5 rounded-full overflow-hidden">
                <div class="bg-academic-600 dark:bg-academic-400 h-full rounded-full" style="width: {{ rand(20, 80) }}%"></div>
              </div>
            </div>
          @endfor
        </div>
      </div>

      <!-- Supervisor Banner -->
      <div class="bg-gradient-to-r from-slate-900 via-academic-900 to-academic-800 rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-1 text-center md:text-left z-10">
          <div class="flex items-center justify-center md:justify-start gap-2">
            <span class="px-2.5 py-0.5 text-[10px] font-bold bg-amber-500 text-slate-950 rounded-full uppercase tracking-wider">SUPERVISOR HUB</span>
            <span class="text-xs font-mono text-slate-300">olasunkanmi.arowolo@lasu.edu.ng</span>
          </div>
          <h2 class="text-xl font-bold">Welcome, Dr. Olasunkanmi Arowolo</h2>
          <p class="text-slate-300 text-xs max-w-xl">
            Department of Journalism and Media Studies, Lagos State University (LASU). Track research milestones, review proposal titles, log official feedback, and manage student projects.
          </p>
        </div>
        <div class="flex flex-wrap justify-center gap-3 z-10">
          <button onclick="openLogMeetingModal()" class="supervisor-btn px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-xs font-bold shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-calendar-plus"></i> Log Supervision Session
          </button>
          <button onclick="switchTab('supervisor-proposals')" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl text-xs font-bold transition flex items-center gap-2">
            <i class="fa-solid fa-check-double text-amber-400"></i> Pending Proposals (<span id="banner-pending-count">2</span>)
          </button>
        </div>
      </div>

      <!-- Student Research Tracking Roster -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-700 space-y-3">
          <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
            <div>
              <h3 class="font-bold text-slate-900 dark:text-white text-base">Supervised Student Roster (LASU)</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">Synced in real-time with Google Sheets</p>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
              <div class="relative w-full sm:w-60">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input id="supervisor-search-input" onkeyup="filterSupervisorRoster()" type="text" placeholder="Search student or matric no..." class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-academic-600 dark:text-white">
              </div>
              <button onclick="refreshData()" class="p-2 text-xs bg-slate-100 dark:bg-slate-700 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition" title="Sync with Google Sheets">
                <i id="refresh-icon" class="fa-solid fa-rotate"></i>
              </button>
            </div>
          </div>

          <!-- Filter & sort controls -->
          <div class="flex flex-wrap items-center gap-2 text-[11px]">
            <span class="text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mr-1"><i class="fa-solid fa-filter"></i> Filter</span>

            <label class="flex items-center gap-1.5">
              <span class="text-slate-500 dark:text-slate-400">Phase</span>
              <select id="roster-filter-phase" onchange="applyRosterControls()" class="p-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg dark:text-white">
                <option value="">All stages</option>
              </select>
            </label>

            <label class="flex items-center gap-1.5">
              <span class="text-slate-500 dark:text-slate-400">Progress</span>
              <select id="roster-filter-progress" onchange="applyRosterControls()" class="p-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg dark:text-white">
                <option value="">Any</option>
                <option value="0">0% (not started)</option>
                <option value="1-33">1–33%</option>
                <option value="34-66">34–66%</option>
                <option value="67-99">67–99%</option>
                <option value="100">100% (complete)</option>
              </select>
            </label>

            <label class="flex items-center gap-1.5">
              <span class="text-slate-500 dark:text-slate-400">Last session</span>
              <select id="roster-filter-lastsession" onchange="applyRosterControls()" class="p-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg dark:text-white">
                <option value="">Any</option>
                <option value="logged">Has a session</option>
                <option value="none">No session yet</option>
              </select>
            </label>

            <span class="mx-1 h-4 w-px bg-slate-200 dark:border-slate-700"></span>
            <span class="text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mr-1"><i class="fa-solid fa-arrow-down-wide-short"></i> Sort</span>

            <label class="flex items-center gap-1.5">
              <select id="roster-sort-by" onchange="applyRosterControls()" class="p-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg dark:text-white">
                <option value="name">Name</option>
                <option value="progress">Progress</option>
                <option value="phase">Phase / Stage</option>
                <option value="lastSession">Last session</option>
              </select>
            </label>
            <select id="roster-sort-dir" onchange="applyRosterControls()" class="p-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg dark:text-white">
              <option value="asc">Asc</option>
              <option value="desc">Desc</option>
            </select>

            <button onclick="resetRosterControls()" class="ml-auto px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 font-semibold transition">
              <i class="fa-solid fa-rotate-left"></i> Reset
            </button>
          </div>
        </div>

        <div class="overflow-hidden">
          <table class="w-full table-fixed text-left text-xs text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-700/50 uppercase text-[10px] font-bold text-slate-500 dark:text-slate-400 tracking-wider">
              <tr>
                <th class="px-3 py-3 w-1/2 sm:w-2/5 md:w-1/3 lg:w-1/4">Student / Matric</th>
                <th class="px-3 py-3 hidden lg:table-cell lg:w-1/4">Approved Topic</th>
                <th class="px-3 py-3 hidden sm:table-cell sm:w-1/4 lg:w-1/6">Phase</th>
                <th class="px-3 py-3 hidden md:table-cell md:w-1/6">Progress</th>
                <th class="px-3 py-3 hidden xl:table-cell xl:w-[12%]">Last Session</th>
                <th class="px-3 py-3 text-right w-1/2 sm:w-1/3 md:w-1/4 lg:w-[18%]">Action</th>
              </tr>
            </thead>
            <tbody id="supervisor-student-tbody" class="divide-y divide-slate-100 dark:divide-slate-700">
              <!-- Dynamically loaded rows -->
              @for($i=1; $i<=5; $i++)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                  <td class="px-3 py-3">
                    <div class="font-semibold text-slate-900 dark:text-white text-sm">Student {{ $i }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-mono">DEMO-00{{ $i }}</div>
                  </td>
                  <td class="px-3 py-3 hidden lg:table-cell">
                    <span class="text-xs text-slate-600 dark:text-slate-300 truncate block">Sample research topic for student {{ $i }}</span>
                  </td>
                  <td class="px-3 py-3 hidden sm:table-cell">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                      Chapter 1
                    </span>
                  </td>
                  <td class="px-3 py-3 hidden md:table-cell">
                    <div class="flex items-center gap-2">
                      <div class="w-full bg-slate-200 dark:bg-slate-600 h-1.5 rounded-full overflow-hidden flex-1">
                        <div class="bg-academic-600 dark:bg-academic-400 h-full rounded-full" style="width: {{ $i * 15 }}%"></div>
                      </div>
                      <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $i * 15 }}%</span>
                    </div>
                  </td>
                  <td class="px-3 py-3 hidden xl:table-cell">
                    <span class="text-xs text-slate-500 dark:text-slate-400">2 days ago</span>
                  </td>
                  <td class="px-3 py-3 text-right">
                    <button class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-lg bg-academic-50 dark:bg-academic-900/30 text-academic-700 dark:text-academic-300 hover:bg-academic-100 dark:hover:bg-academic-900/50 transition">
                      <i class="fa-solid fa-eye"></i> View
                    </button>
                  </td>
                </tr>
              @endfor
            </tbody>
          </table>
        </div>

        <!-- Pagination footer -->
        <div id="roster-pager" class="hidden px-4 py-3 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3 text-xs">
          <span id="roster-pager-info" class="text-slate-500 dark:text-slate-400"></span>
          <div class="flex items-center gap-1.5">
            <button id="roster-prev" onclick="rosterPrevPage()" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed">
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            <span id="roster-page-nums" class="flex items-center gap-1"></span>
            <button id="roster-next" onclick="rosterNextPage()" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>

    </section>

  </main>
</div>

@endsection
