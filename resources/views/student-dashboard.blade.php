<x-layouts.app title="Student Portal | TheOAsis Research Supervision System">

  <x-slot:head>
    @include('partials.dashboards.student-styles')
  </x-slot:head>

  @include('partials.dashboards.student-header')

  <!-- Backdrop behind the mobile nav drawer -->
  <div id="nav-backdrop" onclick="closeMobileNav()" class="hidden md:hidden fixed inset-0 z-40 bg-slate-900/50 opacity-0"></div>

  <!-- ================= MAIN LAYOUT (sidebar + content) ================= -->
  <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col md:flex-row gap-4 sm:gap-6">

    @include('partials.dashboards.student-sidebar')

    <main class="flex-1 min-w-0">
      @include('partials.dashboards.student-content')
    </main>
  </div>

  <!-- ================= LOADING OVERLAY ================= -->
  <div id="loading-overlay" class="fixed inset-0 z-[70] bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-sm flex flex-col items-center justify-center gap-4">
    <div class="loading-ring"></div>
    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Loading your research portal...</p>
  </div>

  @include('partials.dashboards.student-modals')
  @include('partials.dashboards.student-script')
</x-layouts.app>
