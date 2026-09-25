@props([
    'variant' => 'minimal',
    'role' => null,
    'user' => null,
    'scope' => null,
    'showBrand' => null,
    'showVersion' => null,
    'showLegalLinks' => null,
    'showExternalLinks' => null,
    'showSystemStatus' => null,
    'showQuickLinks' => null,
    'quickLinks' => [],
    'columns' => [],
    'ctaLinks' => [],
    'legalLinks' => [],
    'externalLinks' => [],
    'systemStatus' => 'All systems operational',
    'systemStatusColor' => 'emerald',
])

@php
    $variant = $variant ?: 'minimal';
    $isMegaPublic = in_array($variant, ['public', 'mega-public'], true);
    $isAuthenticated = in_array($variant, ['authenticated', 'app', 'auth', 'super-admin'], true);
    $isMinimal = $variant === 'minimal';

    if (!$role) {
        $role = match ($variant) {
            'super-admin' => 'super_admin',
            'auth' => 'auth',
            default => session('role'),
        };
    }

    $brandConfig = config('footer.brand', []);
    $publicConfig = config('footer.public', []);
    $roleConfig = $role === 'auth'
        ? config('footer.auth', [])
        : config('footer.roles.' . $role, config('footer.roles.student', []));

    $resolvedColumns = $columns ?: ($isMegaPublic ? ($publicConfig['columns'] ?? []) : []);
    $resolvedCtaLinks = $ctaLinks ?: ($isMegaPublic ? ($publicConfig['cta'] ?? []) : []);
    $resolvedLegalLinks = $legalLinks ?: ($isMegaPublic
        ? ($publicConfig['legal'] ?? [])
        : ($roleConfig['support'] ?? []));
    $resolvedExternalLinks = $externalLinks ?: ($isMegaPublic ? ($publicConfig['external'] ?? []) : []);
    $resolvedQuickLinks = $quickLinks ?: ($roleConfig['links'] ?? []);

    $showBrand = $showBrand ?? ! $isMinimal;
    $showVersion = $showVersion ?? ($isAuthenticated && $role === 'super_admin');
    $showLegalLinks = $showLegalLinks ?? ($isMegaPublic || $isAuthenticated || $isMinimal);
    $showExternalLinks = $showExternalLinks ?? ($isMegaPublic || $role === 'super_admin');
    $showSystemStatus = $showSystemStatus ?? $role === 'super_admin';
    $showQuickLinks = $showQuickLinks ?? ($isAuthenticated && $variant !== 'auth');

    $statusColors = [
        'emerald' => 'bg-emerald-50 dark:bg-emerald-900/30 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300',
        'amber' => 'bg-amber-50 dark:bg-amber-900/30 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300',
        'rose' => 'bg-rose-50 dark:bg-rose-900/30 border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300',
    ];
    $statusColorClasses = $statusColors[$systemStatusColor] ?? $statusColors['emerald'];
    $statusDotColor = str_replace(
        ['bg-', 'dark:bg-', 'border-', 'dark:border-', 'text-', 'dark:text-'],
        '',
        $statusColorClasses
    );
    $statusDotColor = 'bg-' . explode(' ', $statusDotColor)[0];

    $resolveLinks = function (array $links): array {
        return array_values(array_filter(
            array_map(function (array $link): array {
                if (isset($link['route']) && app('router')->has($link['route'])) {
                    $href = route($link['route'], $link['parameters'] ?? []);
                    if (! empty($link['fragment'])) {
                        $href .= '#' . ltrim($link['fragment'], '#');
                    }
                    $link['href'] = $href;
                } elseif (isset($link['url'])) {
                    $link['href'] = $link['url'];
                } else {
                    $link['href'] = '#';
                }

                return $link;
            }, $links),
            fn (array $link): bool => ($link['label'] ?? '') !== '' && ($link['href'] ?? '') !== ''
        ));
    };

    $resolvedColumns = array_map(
        fn (array $column): array => array_merge($column, ['links' => $resolveLinks($column['links'] ?? [])]),
        $resolvedColumns
    );
    $resolvedCtaLinks = $resolveLinks($resolvedCtaLinks);
    $resolvedLegalLinks = $resolveLinks($resolvedLegalLinks);
    $resolvedExternalLinks = $resolveLinks($resolvedExternalLinks);
    $resolvedQuickLinks = $resolveLinks($resolvedQuickLinks);

    $scopeLabel = $scope;
    if (! $scopeLabel) {
        $scopeLabel = $role === 'super_admin'
            ? 'Platform-wide'
            : (session('university_id') ? 'University scope' : 'Authenticated workspace');
    }
@endphp

@if($isMegaPublic)
  <footer id="app-footer" class="border-t border-white/10 bg-academic-900 text-slate-300 dark:bg-slate-950">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="grid gap-10 py-12 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,2fr)] lg:py-16">
        <section class="space-y-6" aria-labelledby="footer-brand-title">
          <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-400/15 ring-1 ring-amber-300/30">
              <img src="{{ asset('img/afriscribe-logo-white.png') }}" alt="AfriScribe" class="h-6 w-auto object-contain" loading="lazy" decoding="async">
            </div>
            <div>
              <p id="footer-brand-title" class="text-base font-bold text-white sm:text-lg">{{ $brandConfig['name'] ?? config('app.name', 'TheOAsis Research Supervision Portal') }}</p>
              <p class="text-xs text-amber-200/90">{{ $brandConfig['tagline'] ?? 'From topic to completion, with clarity.' }}</p>
            </div>
          </div>

          <p class="max-w-md text-sm leading-6 text-slate-300">{{ $brandConfig['description'] ?? 'A multi-tenant research supervision platform for universities, students, supervisors and research leaders.' }}</p>

          @if(count($resolvedCtaLinks) > 0)
            <div class="flex flex-wrap gap-3">
              @foreach($resolvedCtaLinks as $link)
                @php
                  $ctaClasses = ($link['style'] ?? 'secondary') === 'primary'
                      ? 'bg-amber-400 text-slate-950 hover:bg-amber-300'
                      : 'bg-white/10 text-white ring-1 ring-white/20 hover:bg-white/15';
                @endphp
                <a href="{{ $link['href'] }}" class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition {{ $ctaClasses }}">
                  <i class="fa-solid {{ $link['style'] === 'primary' ? 'fa-calendar-check' : 'fa-right-to-bracket' }}"></i>
                  <span>{{ $link['label'] }}</span>
                </a>
              @endforeach
            </div>
          @endif

          <div class="flex items-center gap-2 text-xs text-emerald-200">
            <span class="h-2 w-2 rounded-full bg-emerald-400" aria-hidden="true"></span>
            <span>Available for university pilots and supervised research workflows</span>
          </div>
        </section>

        <div class="lg:hidden space-y-3" aria-label="Public footer navigation on mobile">
          @foreach($resolvedColumns as $column)
            <details class="rounded-xl border border-white/10 bg-white/5 px-4 py-3">
              <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-white">
                <span>{{ $column['heading'] ?? '' }}</span>
                <i class="fa-solid fa-chevron-down text-xs text-slate-400" aria-hidden="true"></i>
              </summary>
              <ul class="mt-3 space-y-3 pb-1">
                @foreach($column['links'] as $link)
                  <li>
                    <a href="{{ $link['href'] }}" class="inline-flex items-center gap-2 text-sm text-slate-300 transition hover:text-white">
                      <span>{{ $link['label'] }}</span>
                    </a>
                  </li>
                @endforeach
              </ul>
            </details>
          @endforeach
        </div>

        <nav class="hidden grid-cols-2 gap-8 lg:grid sm:grid-cols-4" aria-label="Public footer navigation">
          @foreach($resolvedColumns as $column)
            <section>
              <h2 class="text-xs font-bold uppercase tracking-wider text-white/70">{{ $column['heading'] ?? '' }}</h2>
              <ul class="mt-4 space-y-3">
                @foreach($column['links'] as $link)
                  <li>
                    <a href="{{ $link['href'] }}" class="inline-flex items-center gap-2 text-sm text-slate-300 transition hover:text-white">
                      <span>{{ $link['label'] }}</span>
                      <i class="fa-solid fa-arrow-up-right-from-square ml-auto text-[10px] text-slate-500 transition hover:text-white" aria-hidden="true"></i>
                    </a>
                  </li>
                @endforeach
              </ul>
            </section>
          @endforeach
        </nav>
      </div>

      <div class="flex flex-col gap-4 border-t border-white/10 py-6 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-xs text-slate-400">
          @if(isset($slot->bottom) && $slot->bottom)
            {{ $slot->bottom }}
          @else
            <p>&copy; {{ date('Y') }} {{ $brandConfig['name'] ?? config('app.name', 'TheOAsis Research Supervision Portal') }}. All rights reserved.</p>
          @endif
        </div>

        <div class="flex flex-wrap items-center gap-x-5 gap-y-3 text-xs">
          @if($showLegalLinks)
            <nav class="flex flex-wrap items-center gap-x-5 gap-y-3" aria-label="Legal navigation">
              @foreach($resolvedLegalLinks as $link)
                <a href="{{ $link['href'] }}" class="text-slate-400 transition hover:text-white">{{ $link['label'] }}</a>
              @endforeach
            </nav>
          @endif

          @if($showExternalLinks && count($resolvedExternalLinks) > 0)
            <div class="flex items-center gap-2">
              @foreach($resolvedExternalLinks as $link)
                <a href="{{ $link['href'] }}" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-white/10 hover:text-white" aria-label="{{ $link['label'] }}">
                  <i class="{{ $link['icon'] ?? 'fa-solid fa-link' }} text-sm"></i>
                </a>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </footer>
@else
  <footer id="app-footer" class="mt-auto border-t border-slate-200 bg-white/95 backdrop-blur-xl dark:border-slate-700 dark:bg-slate-900/95">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-3">
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="min-w-0">
          @if($showBrand)
            <div class="flex items-center gap-2.5">
              <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-tr from-academic-900 via-academic-800 to-amber-600 text-white">
                <i class="fa-solid {{ $role === 'super_admin' ? 'fa-crown' : 'fa-flask' }} text-xs"></i>
              </div>
              <div class="min-w-0">
                <p class="truncate text-xs font-bold text-slate-900 dark:text-white">{{ $brandConfig['name'] ?? config('app.name', 'TheOAsis Research Supervision Portal') }}</p>
                <p class="truncate text-[11px] text-slate-500 dark:text-slate-400">{{ $roleConfig['label'] ?? 'Research Supervision Portal' }} &bull; {{ $scopeLabel }}</p>
                @if($showVersion)
                  <p class="truncate text-[10px] text-slate-400 dark:text-slate-500">v{{ config('app.version', '1.0.0') }}</p>
                @endif
              </div>
            </div>
          @endif
        </div>

        @if($showQuickLinks && count($resolvedQuickLinks) > 0)
          <div class="lg:hidden">
            <details class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800/60">
              <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold text-slate-800 dark:text-slate-100">
                <span>{{ $roleConfig['label'] ?? 'Authenticated' }} quick actions</span>
                <i class="fa-solid fa-chevron-down text-[11px] text-slate-500" aria-hidden="true"></i>
              </summary>
              <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-2 text-xs">
                @foreach($resolvedQuickLinks as $link)
                  <a href="{{ $link['href'] }}" class="inline-flex items-center gap-1.5 text-slate-600 transition hover:text-academic-700 dark:text-slate-300 dark:hover:text-academic-300">
                    @if(isset($link['icon']))
                      <i class="fa-solid {{ $link['icon'] }} text-[11px]" aria-hidden="true"></i>
                    @endif
                    <span>{{ $link['label'] }}</span>
                  </a>
                @endforeach
              </div>
            </details>
          </div>

          <nav class="hidden lg:flex lg:flex-wrap lg:items-center lg:gap-x-4 lg:gap-y-2 text-xs" aria-label="{{ $roleConfig['label'] ?? 'Authenticated' }} footer navigation">
            @foreach($resolvedQuickLinks as $link)
              <a href="{{ $link['href'] }}" class="inline-flex items-center gap-1.5 text-slate-600 transition hover:text-academic-700 dark:text-slate-300 dark:hover:text-academic-300">
                @if(isset($link['icon']))
                  <i class="fa-solid {{ $link['icon'] }} text-[11px]" aria-hidden="true"></i>
                @endif
                <span>{{ $link['label'] }}</span>
              </a>
            @endforeach
          </nav>
        @endif

        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs">
          @if($showSystemStatus)
            <div class="flex items-center gap-2 rounded-full border px-2.5 py-1 {{ $statusColorClasses }}">
              <span class="h-1.5 w-1.5 rounded-full {{ $statusDotColor }}" aria-hidden="true"></span>
              <span>{{ $systemStatus }}</span>
            </div>
          @endif

          @if($showExternalLinks && count($resolvedExternalLinks) > 0)
            <div class="flex items-center gap-1.5">
              @foreach($resolvedExternalLinks as $link)
                <a href="{{ $link['href'] }}" target="_blank" rel="noopener" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-700/50 dark:hover:text-slate-200" aria-label="{{ $link['label'] }}">
                  <i class="{{ $link['icon'] ?? 'fa-solid fa-link' }} text-xs"></i>
                </a>
              @endforeach
            </div>
          @endif
        </div>
      </div>

      @if($showLegalLinks && count($resolvedLegalLinks) > 0)
        <div class="mt-3 flex flex-col gap-2 border-t border-slate-200 pt-3 text-[11px] dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-slate-400 dark:text-slate-500">{{ $roleConfig['subtitle'] ?? 'Secure, role-scoped access to your research supervision workspace.' }}</p>
          <nav class="flex flex-wrap items-center gap-x-4 gap-y-2" aria-label="Footer support navigation">
            @foreach($resolvedLegalLinks as $link)
              <a href="{{ $link['href'] }}" class="text-slate-500 transition hover:text-academic-700 dark:text-slate-400 dark:hover:text-academic-300">{{ $link['label'] }}</a>
            @endforeach
          </nav>
        </div>
      @endif
    </div>
  </footer>
@endif
