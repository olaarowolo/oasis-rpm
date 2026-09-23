<x-layouts.app title="Supervisor Hub | TheOAsis Research Supervision System">

  @include('partials.dashboards.supervisor-header')

  <aside id="app-sidebar" aria-label="Supervisor navigation" class="md:hidden w-full flex-shrink-0 bg-slate-50 dark:bg-slate-900">
    <div class="flex items-center justify-between mb-4">
      <span class="font-bold text-slate-900 dark:text-white text-sm">Menu</span>
      <button type="button" data-mobile-nav-close aria-label="Close menu" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700/50">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>

    <nav class="bg-white dark:bg-slate-800 rounded-2xl p-2 shadow-sm border border-slate-200 dark:border-slate-700 space-y-1" aria-label="Supervisor navigation">
      <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Supervisor Hub</div>
      <a href="{{ route('supervisor.dashboard') }}" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-xl transition bg-academic-50 text-academic-700 dark:bg-academic-900/40 dark:text-academic-100">
        <i class="fa-solid fa-chart-line w-5 text-center text-academic-600 dark:text-academic-400"></i>
        Dashboard
      </a>
      <a href="{{ route('supervisor.students') }}" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-users w-5 text-center"></i>
        Students
      </a>
      <a href="{{ route('supervisor.proposals') }}" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-file-signature w-5 text-center"></i>
        Topic Approvals
      </a>
      <a href="{{ route('supervisor.meetings') }}" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-comments w-5 text-center"></i>
        Meeting Logs
      </a>
      <a href="{{ route('supervisor.analytics') }}" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-chart-pie w-5 text-center text-purple-500"></i>
        Analytics &amp; Insights
      </a>
      <a href="{{ route('supervisor.resources.pending') }}" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-check-to-mark w-5 text-center text-academic-600 dark:text-academic-400"></i>
        Resource Approvals
      </a>
    </nav>

    <div class="mt-4 p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm text-xs">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-academic-800 text-amber-400 font-extrabold flex items-center justify-center text-sm shadow-md">OA</div>
        <div class="min-w-0">
          <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate">Olasunkanmi Arowolo, PhD</h4>
          <p class="text-[10px] text-slate-500 dark:text-slate-400">Research Supervisor</p>
        </div>
      </div>
    </div>
  </aside>

  <!-- ================= MAIN CONTENT ================= -->
  <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    
    @include('partials.dashboards.supervisor-welcome')
    @include('partials.dashboards.supervisor-metrics')
    @include('partials.dashboards.supervisor-content')
    @include('partials.dashboards.supervisor-roster')
    @include('partials.dashboards.supervisor-quick-actions')
  </main>

  @include('partials.dashboards.supervisor-script')
</x-layouts.app>