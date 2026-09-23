<!-- SUPER ADMIN FOOTER -->
<footer id="super-admin-footer" class="sticky bottom-0 z-30 mt-auto border-t border-slate-200 bg-white/92 backdrop-blur-xl dark:border-slate-700 dark:bg-slate-900/92">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
      <!-- Left: Brand & Version -->
      <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-600 to-purple-700 flex items-center justify-center">
          <i class="fa-solid fa-crown text-white text-sm"></i>
        </div>
        <div>
          <p class="font-semibold text-slate-900 dark:text-white">OAsis Research Supervision Portal</p>
          <p class="text-[11px]">Super Admin Command Center v{{ config('app.version', '1.0.0') }}</p>
        </div>
      </div>

      <!-- Center: Quick Links -->
      <div class="hidden md:flex items-center gap-6 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('super-admin.dashboard') }}" class="hover:text-violet-600 dark:hover:text-violet-400 transition-colors">Dashboard</a>
        <a href="{{ route('super-admin.universities') }}" class="hover:text-violet-600 dark:hover:text-violet-400 transition-colors">Universities</a>
        <a href="{{ route('super-admin.users') }}" class="hover:text-violet-600 dark:hover:text-violet-400 transition-colors">Users</a>
        <a href="{{ route('super-admin.audit-logs') }}" class="hover:text-violet-600 dark:hover:text-violet-400 transition-colors">Audit Logs</a>
        <a href="{{ route('super-admin.system-status') }}" class="hover:text-violet-600 dark:hover:text-violet-400 transition-colors">System Status</a>
      </div>

      <!-- Right: System Status & Links -->
      <div class="flex items-center gap-4 text-sm">
        <!-- System Health Indicator -->
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" aria-hidden="true"></span>
          <span class="text-emerald-700 dark:text-emerald-300 font-medium">All Systems Operational</span>
        </div>

        <!-- External Links -->
        <div class="flex items-center gap-2">
          <a href="https://github.com/OlasunkanmiArowolo/OAsis-RS" target="_blank" rel="noopener" 
             class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors"
             aria-label="View on GitHub">
            <i class="fa-brands fa-github text-base"></i>
          </a>
          <a href="https://olaarowolo.com" target="_blank" rel="noopener"
             class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors"
             aria-label="Visit Ola Arowolo">
            <i class="fa-solid fa-globe text-base"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-400 dark:text-slate-500">
        <p>&copy; {{ date('Y') }} OAsis Research Supervision Portal. All rights reserved.</p>
        <div class="flex items-center gap-4">
          <a href="#" class="hover:text-slate-600 dark:hover:text-slate-300 transition-colors">Privacy Policy</a>
          <a href="#" class="hover:text-slate-600 dark:hover:text-slate-300 transition-colors">Terms of Service</a>
          <a href="#" class="hover:text-slate-600 dark:hover:text-slate-300 transition-colors">Accessibility</a>
        </div>
      </div>
    </div>
  </div>
</footer>

<style>
  /* Footer sticky positioning */
  #super-admin-footer {
    @apply shadow-[0_-8px_24px_rgba(15,23,42,0.06)];
  }
</style>