<x-layouts.app title="Admin Dashboard | TheOAsis Research Supervision System">
  @php
    $adminName = $currentUser->name ?? 'Admin';
    $adminEmail = $currentUser->email ?? 'admin@example.com';
    $adminInitials = collect(explode(' ', $adminName))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('');
    $adminInitials = $adminInitials ?: 'AD';
    $roleLabel = $currentUser?->isSuperAdmin() ? 'Super Admin' : 'Admin';
  @endphp

  <header class="sticky top-0 z-30 border-b border-slate-200/80 dark:border-slate-700 bg-white/90 dark:bg-slate-800/90 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
      <div class="flex items-center gap-2 sm:gap-3 min-w-0">
        <button id="mobile-nav-toggle" type="button" data-mobile-nav-toggle aria-controls="app-sidebar" aria-expanded="false" aria-label="Open navigation menu" class="mobile-nav-toggle md:hidden -ml-1 w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition shrink-0">
          <span class="mobile-nav-line" aria-hidden="true"></span>
          <span class="mobile-nav-line" aria-hidden="true"></span>
          <span class="mobile-nav-line" aria-hidden="true"></span>
        </button>
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-academic-900 via-academic-800 to-amber-500 flex items-center justify-center text-white shadow-md">
          <i class="fa-solid fa-layer-group text-lg"></i>
        </div>
        <div class="min-w-0">
          <h1 class="font-bold text-sm sm:text-lg text-slate-900 dark:text-white leading-tight truncate">Admin Dashboard</h1>
          <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Dynamic operations view for user access, settings, and platform oversight</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-semibold">
          <i class="fa-solid fa-circle-check"></i>
          Live data
        </div>
        <div class="hidden md:flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-700">
          <div class="w-9 h-9 rounded-full bg-academic-800 text-white border border-white/10 flex items-center justify-center text-sm font-bold shadow-sm">
            {{ $adminInitials }}
          </div>
          <div class="text-left leading-tight">
            <p class="text-xs font-semibold text-slate-800 dark:text-slate-100">{{ $adminName }}</p>
            <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $roleLabel }}</p>
          </div>
        </div>
        <button onclick="logout()" class="p-2 rounded-lg text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition" title="Log out">
          <i class="fa-solid fa-right-from-bracket text-base"></i>
        </button>
      </div>
    </div>
  </header>

  <aside id="app-sidebar" aria-label="Admin navigation" class="md:hidden w-full flex-shrink-0 bg-slate-50 dark:bg-slate-900">
    <div class="flex items-center justify-between mb-4">
      <span class="font-bold text-slate-900 dark:text-white text-sm">Menu</span>
      <button type="button" data-mobile-nav-close aria-label="Close menu" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700/50">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>

    <nav class="bg-white dark:bg-slate-800 rounded-2xl p-2 shadow-sm border border-slate-200 dark:border-slate-700 space-y-1" aria-label="Admin navigation">
      <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Admin Workspace</div>
      <a href="{{ route('admin.dashboard') }}" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-xl transition bg-academic-50 text-academic-700 dark:bg-academic-900/40 dark:text-academic-100">
        <i class="fa-solid fa-chart-line w-5 text-center text-academic-600 dark:text-academic-400"></i>
        Dashboard
      </a>
      <a href="{{ route('admin.users') }}" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-users w-5 text-center"></i>
        Users
      </a>
      <a href="{{ route('admin.config') }}" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-sliders w-5 text-center"></i>
        Configuration
      </a>
      <a href="{{ route('admin.resources') }}" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-book-open w-5 text-center"></i>
        Resources
      </a>
      <a href="{{ route('admin.audit-logs') }}" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-shield-halved w-5 text-center"></i>
        Audit Logs
      </a>
    </nav>
  </aside>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <section class="rounded-3xl p-6 sm:p-8 text-white shadow-xl bg-gradient-to-br from-slate-950 via-academic-900 to-academic-800 overflow-hidden relative">
      <div class="absolute inset-0 opacity-25 bg-[radial-gradient(circle_at_top_right,_rgba(245,158,11,0.55),_transparent_30%),radial-gradient(circle_at_bottom_left,_rgba(56,189,248,0.35),_transparent_28%)]"></div>
      <div class="relative flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="max-w-2xl space-y-4">
          <p class="text-xs uppercase tracking-[0.3em] text-amber-300 font-semibold">Administrative overview</p>
          <h2 class="text-3xl sm:text-4xl font-black leading-tight">Welcome, {{ $adminName }}</h2>
          <p class="text-slate-300 text-sm sm:text-base">Manage users, configure system settings, and keep the supervision platform aligned across universities and departments.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold">{{ $stats['total_universities'] ?? 0 }} universities</span>
          <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold">{{ $stats['active_users'] ?? 0 }} active users</span>
          <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold">{{ $stats['students'] ?? 0 }} students</span>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Universities</p>
        <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ $stats['total_universities'] ?? 0 }}</p>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Configured institutions in the system</p>
      </article>
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Active Users</p>
        <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-400">{{ $stats['active_users'] ?? 0 }}</p>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Users currently enabled for access</p>
      </article>
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Supervisors</p>
        <p class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-400">{{ $stats['supervisors'] ?? 0 }}</p>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Authorized supervision staff</p>
      </article>
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Pending Approvals</p>
        <p class="mt-2 text-3xl font-black text-rose-700 dark:text-rose-400">{{ $stats['pending_approvals'] ?? 0 }}</p>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Students or supervisors needing review</p>
      </article>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-[1.25fr_0.75fr] gap-6">
      <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <a href="{{ route('admin.users') }}" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm hover:shadow-md transition group">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">User Management</p>
                <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">Manage Users</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Add, edit, and remove admins, supervisors, and students.</p>
              </div>
              <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 flex items-center justify-center group-hover:scale-105 transition">
                <i class="fa-solid fa-user-gear"></i>
              </div>
            </div>
          </a>

          <a href="{{ route('admin.config') }}" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm hover:shadow-md transition group">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">System Configuration</p>
                <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">Configure Platform</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Adjust Gemini, email, and platform settings.</p>
              </div>
              <div class="w-11 h-11 rounded-xl bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 flex items-center justify-center group-hover:scale-105 transition">
                <i class="fa-solid fa-sliders"></i>
              </div>
            </div>
          </a>

          <a href="{{ route('admin.users', ['role' => 'supervisor']) }}" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm hover:shadow-md transition group">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Quick Filter</p>
                <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">Supervisors</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Jump straight to the supervisory roster.</p>
              </div>
              <div class="w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 flex items-center justify-center group-hover:scale-105 transition">
                <i class="fa-solid fa-chalkboard-user"></i>
              </div>
            </div>
          </a>
        </div>

        <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
            <div>
              <h3 class="font-bold text-lg text-slate-900 dark:text-white">Recent Users</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">Latest accounts visible in the current admin scope</p>
            </div>
            <a href="{{ route('admin.users') }}" class="text-sm font-semibold text-academic-700 dark:text-academic-300 hover:underline">Open user manager</a>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
              <thead class="bg-slate-50 dark:bg-slate-900/40 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                  <th class="px-5 py-3">Name</th>
                  <th class="px-5 py-3">Role</th>
                  <th class="px-5 py-3">University</th>
                  <th class="px-5 py-3">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                @forelse($recentUsers as $user)
                  <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30 transition">
                    <td class="px-5 py-4">
                      <div class="font-semibold text-slate-900 dark:text-white">{{ $user->name }}</div>
                      <div class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</div>
                    </td>
                    <td class="px-5 py-4 text-sm">
                      <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->isSuperAdmin() ? 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300' : ($user->isAdmin() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($user->isSupervisor() ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200')) }}">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                      </span>
                    </td>
                    <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $user->university->name ?? 'N/A' }}</td>
                    <td class="px-5 py-4 text-sm">
                      <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500 dark:text-slate-400">No users found in the current scope.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <aside class="space-y-6">
        <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h3 class="font-bold text-lg text-slate-900 dark:text-white">Gemini Assistant</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">System tools and AI configuration access</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 flex items-center justify-center">
              <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
          </div>
          <div class="mt-4 rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-900/40 space-y-3">
            <div class="flex items-center justify-between text-sm">
              <span class="text-slate-500 dark:text-slate-400">Selected scope</span>
              <span class="font-semibold text-slate-900 dark:text-white">{{ $selectedUniversityId ? 'University #' . $selectedUniversityId : 'All universities' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-slate-500 dark:text-slate-400">Status</span>
              <span class="font-semibold text-emerald-600 dark:text-emerald-400">Ready</span>
            </div>
            <a href="{{ route('admin.config') }}#ai-settings" class="inline-flex items-center gap-2 text-sm font-semibold text-academic-700 dark:text-academic-300 hover:underline">
              Open AI settings
              <i class="fa-solid fa-arrow-right"></i>
            </a>
          </div>
        </section>

        <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5">
          <h3 class="font-bold text-lg text-slate-900 dark:text-white">System Snapshot</h3>
          <div class="mt-4 space-y-3">
            <div class="flex items-center justify-between rounded-xl bg-slate-50 dark:bg-slate-900/40 px-4 py-3">
              <span class="text-sm text-slate-500 dark:text-slate-400">Database</span>
              <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Connected</span>
            </div>
            <div class="flex items-center justify-between rounded-xl bg-slate-50 dark:bg-slate-900/40 px-4 py-3">
              <span class="text-sm text-slate-500 dark:text-slate-400">API</span>
              <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Operational</span>
            </div>
            <div class="flex items-center justify-between rounded-xl bg-slate-50 dark:bg-slate-900/40 px-4 py-3">
              <span class="text-sm text-slate-500 dark:text-slate-400">Admin email</span>
              <span class="text-sm font-semibold text-slate-900 dark:text-white truncate max-w-[12rem]">{{ $adminEmail }}</span>
            </div>
          </div>
        </section>
      </aside>
    </section>
  </main>

  <form id="logout-form" method="POST" action="/logout" class="hidden">
    @csrf
  </form>

  <script>
    function logout() {
      const form = document.getElementById('logout-form');
      if (form) form.submit();
    }
  </script>
</x-layouts.app>