@php
    $pageTitle = trim($__env->yieldContent('title', 'Admin Dashboard'));
@endphp

<x-layouts.app :title="$pageTitle">
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
                    <h1 class="font-bold text-sm sm:text-lg text-slate-900 dark:text-white leading-tight truncate">{{ $pageTitle }}</h1>
                    <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">University administration workspace</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-semibold">
                    <i class="fa-solid fa-circle-check"></i>
                    Live data
                </div>
                <div class="hidden md:flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-700">
                    <div class="w-9 h-9 rounded-full bg-academic-800 text-white border border-white/10 flex items-center justify-center text-sm font-bold shadow-sm">
                        {{ $adminShellProfile['initials'] }}
                    </div>
                    <div class="text-left leading-tight">
                        <p class="text-xs font-semibold text-slate-800 dark:text-slate-100">{{ $adminShellProfile['name'] }}</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $adminShellProfile['roleLabel'] }}</p>
                    </div>
                </div>
                <button onclick="logout()" class="p-2 rounded-lg text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition" title="Log out">
                    <i class="fa-solid fa-right-from-bracket text-base"></i>
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row gap-4 sm:gap-6">
        @include('admin.partials.sidebar')

        <main class="flex-1 min-w-0 space-y-6">
            @yield('content')
        </main>
    </div>

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