@props([
    'user' => null,
    'displayName' => 'Account',
    'roleLabel' => '',
    'initials' => 'AC',
    'menuLinks' => [],
])

<div class="relative" data-app-header-menu>
    <button
        type="button"
        class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/90 px-2.5 py-1.5 text-left shadow-sm transition hover:border-slate-300 hover:bg-white dark:border-slate-700 dark:bg-slate-800/90 dark:hover:border-slate-600"
        data-menu-toggle
        aria-expanded="false"
        aria-haspopup="true"
        aria-label="Open user menu"
    >
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-academic-800 text-sm font-bold text-amber-300 shadow-sm ring-1 ring-amber-500/20">
            {{ $initials }}
        </div>
        <div class="hidden min-w-0 sm:block">
            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $displayName }}</p>
            <p class="truncate text-[11px] text-slate-500 dark:text-slate-400">{{ $roleLabel }}</p>
        </div>
        <i class="fa-solid fa-chevron-down hidden text-xs text-slate-400 transition sm:block" data-menu-chevron></i>
    </button>

    <div
        class="invisible absolute right-0 z-50 mt-2 w-64 scale-95 rounded-2xl border border-slate-200 bg-white p-2 opacity-0 shadow-xl transition-all duration-150 dark:border-slate-700 dark:bg-slate-800"
        data-menu-panel
        role="menu"
        aria-orientation="vertical"
    >
        <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-slate-900/40">
            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $displayName }}</p>
            @if (!empty($user?->email))
                <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
            @endif
        </div>

        <div class="mt-2 space-y-1">
            @foreach ($menuLinks as $item)
                <a href="{{ $item['href'] }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700/60 dark:hover:text-white" role="menuitem">
                    <i class="fa-solid {{ $item['icon'] }} w-4 text-center text-slate-400 dark:text-slate-500"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="my-2 border-t border-slate-200 dark:border-slate-700"></div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-rose-600 transition hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-900/30" role="menuitem">
                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                <span>Sign out</span>
            </button>
        </form>
    </div>
</div>