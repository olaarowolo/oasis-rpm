<x-layouts.app title="Supervisor Hub | TheOAsis Research Supervision System">

  @include('partials.dashboards.supervisor-header')

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