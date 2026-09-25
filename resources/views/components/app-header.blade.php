<header data-app-header-root class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/85">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-3 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-2 sm:gap-3 lg:flex-[0_1_28rem]">
            <button type="button" data-mobile-nav-toggle aria-controls="{{ $sidebarId }}" aria-expanded="false" aria-label="Open navigation menu" class="mobile-nav-toggle {{ $mobileToggleHiddenClass }} -ml-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700/50">
                <span class="mobile-nav-line" aria-hidden="true"></span>
                <span class="mobile-nav-line" aria-hidden="true"></span>
                <span class="mobile-nav-line" aria-hidden="true"></span>
            </button>

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-academic-900 via-academic-800 to-amber-600 shadow-md ring-1 ring-white/10">
                <img src="{{ asset('img/afriscribe-logo-white.png') }}" alt="AfriScribe" class="h-5 w-auto object-contain" loading="lazy" />
            </div>

            <div class="min-w-0">
                <p class="flex min-w-0 items-center gap-2 text-sm font-bold leading-tight text-slate-900 dark:text-white sm:text-lg">
                    <span class="truncate">{{ $title }}</span>
                    <span class="hidden shrink-0 rounded bg-amber-100 px-2 py-0.5 font-mono text-[10px] font-bold text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 xs:inline">LASU</span>
                </p>
                <p class="truncate text-[11px] text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
            </div>
        </div>

        <x-app-header-navigation :items="$navigation" />

        <div class="flex items-center gap-2 sm:gap-3 lg:flex-[0_0_auto]">
            <div class="hidden items-center gap-2 rounded-xl px-3 py-1.5 text-xs font-bold sm:flex {{ $config['accent'] }}">
                <i class="fa-solid {{ $config['icon'] }}"></i>
                <span>{{ $config['badge'] }}</span>
            </div>

            @if (!empty($resolvedPrimaryAction) || isset($primaryAction))
                <div class="hidden lg:block">
                    @isset($primaryAction)
                        {{ $primaryAction }}
                    @else
                        <a href="{{ $resolvedPrimaryAction['href'] ?? '#' }}" class="inline-flex items-center gap-2 rounded-xl bg-academic-700 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-academic-800 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-academic-600">
                            <i class="fa-solid {{ $resolvedPrimaryAction['icon'] ?? 'fa-bolt' }} text-xs"></i>
                            <span>{{ $resolvedPrimaryAction['label'] ?? 'Open' }}</span>
                        </a>
                    @endisset
                </div>
            @endif

            <x-app-header-notifications :view-all-href="$notificationsViewAllHref" />

            <button type="button" data-theme-toggle class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white/90 text-slate-500 shadow-sm transition hover:border-slate-300 hover:text-slate-700 dark:border-slate-700 dark:bg-slate-800/90 dark:text-slate-300 dark:hover:border-slate-600 dark:hover:text-white" aria-label="Toggle dark mode">
                <i class="fa-solid fa-moon text-sm dark:hidden"></i>
                <i class="fa-solid fa-sun hidden text-sm dark:inline"></i>
            </button>

            <x-app-header-user-menu
                :user="$currentUser"
                :display-name="$displayName"
                :role-label="$config['roleLabel']"
                :initials="$initials"
                :menu-links="$menuLinks"
            />
        </div>
    </div>

    <div class="border-t border-slate-200/70 px-3 py-2 lg:hidden dark:border-slate-700/80">
        <div class="flex gap-2 overflow-x-auto pb-1">
            @if (!empty($resolvedPrimaryAction))
                <a href="{{ $resolvedPrimaryAction['href'] ?? '#' }}" class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl bg-academic-700 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-academic-800">
                    <i class="fa-solid {{ $resolvedPrimaryAction['icon'] ?? 'fa-bolt' }} text-[11px]"></i>
                    <span>{{ $resolvedPrimaryAction['label'] ?? 'Open' }}</span>
                </a>
            @endif

            @foreach ($navigation ?? [] as $item)
                <a href="{{ $item['href'] }}" class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl px-3 py-2 text-xs font-semibold transition {{ $item['active'] ? 'bg-academic-50 text-academic-700 dark:bg-academic-900/40 dark:text-academic-100' : 'bg-white text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                    <i class="fa-solid {{ $item['icon'] }} text-[11px]"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    @if ($breadcrumbs)
        <div class="border-t border-slate-200/70 px-3 py-2 dark:border-slate-700/80 sm:px-6 lg:px-8">
            @if (is_countable($breadcrumbs) && count($breadcrumbs) > 0)
                <x-super-admin.breadcrumbs :breadcrumbs="$breadcrumbs" />
            @elseif (is_string($breadcrumbs) && trim($breadcrumbs) !== '')
                {!! $breadcrumbs !!}
            @endif
        </div>
    @endif
</header>