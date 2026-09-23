<!-- SUPER ADMIN BREADCRUMB COMPONENT -->
@props([
    'breadcrumbs' => [],
    'separator' => '<i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>',
])

@php
    // Default breadcrumbs from route if not provided
    if (empty($breadcrumbs)) {
        $currentRoute = Route::currentRouteName();
        $breadcrumbs = [];
        
        // Always start with Dashboard
        $breadcrumbs[] = [
            'label' => 'Dashboard',
            'url' => route('super-admin.dashboard'),
            'icon' => 'fa-gauge-high',
        ];
        
        // Add route-specific breadcrumbs
        if ($currentRoute !== 'super-admin.dashboard') {
            $routeParts = explode('.', $currentRoute);
            if ($routeParts[0] === 'super-admin') {
                $resource = $routeParts[1] ?? '';
                $action = $routeParts[2] ?? '';
                
                $resourceLabels = [
                    'universities' => ['label' => 'Universities', 'icon' => 'fa-building-columns'],
                    'users' => ['label' => 'Users', 'icon' => 'fa-users-gear'],
                    'config' => ['label' => 'System Config', 'icon' => 'fa-gears'],
                    'resources' => ['label' => 'Resources', 'icon' => 'fa-database'],
                    'audit-logs' => ['label' => 'Audit Logs', 'icon' => 'fa-clipboard-list'],
                    'system-status' => ['label' => 'System Status', 'icon' => 'fa-server'],
                ];
                
                $actionLabels = [
                    'create' => 'Create',
                    'edit' => 'Edit',
                    'show' => 'View',
                    'index' => 'List',
                ];
                
                if (isset($resourceLabels[$resource])) {
                    $breadcrumbs[] = [
                        'label' => $resourceLabels[$resource]['label'],
                        'url' => route('super-admin.' . $resource),
                        'icon' => $resourceLabels[$resource]['icon'],
                    ];
                    
                    if (isset($actionLabels[$action])) {
                        $breadcrumbs[] = [
                            'label' => $actionLabels[$action],
                            'url' => null,
                            'icon' => null,
                        ];
                    }
                } else {
                    // Fallback for unknown routes
                    $breadcrumbs[] = [
                        'label' => ucfirst(str_replace(['super-admin.', '.'], ['', ' '], $currentRoute)),
                        'url' => null,
                        'icon' => null,
                    ];
                }
            }
        }
    }
@endphp

<nav aria-label="Breadcrumb" class="flex items-center gap-1.5 overflow-x-auto pb-1" role="navigation">
  <ol class="flex items-center gap-1.5 text-sm whitespace-nowrap min-w-max">
    @foreach($breadcrumbs as $index => $crumb)
      @php
        $isLast = $index === array_key_last($breadcrumbs);
        $hasUrl = !empty($crumb['url']) && !$isLast;
      @endphp
      
      <li class="flex items-center gap-1.5 {{ $isLast ? 'text-slate-900 dark:text-white font-medium' : 'text-slate-500 dark:text-slate-400' }}">
        @if($hasUrl)
          <a href="{{ $crumb['url'] }}" 
             class="flex items-center gap-1.5 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
             {{ $index === 0 ? 'aria-label="Go to Dashboard"' : '' }}>
            @if($crumb['icon'])
              <i class="fa-solid {{ $crumb['icon'] }} text-base"></i>
            @endif
            <span class="hidden sm:inline">{{ $crumb['label'] }}</span>
          </a>
        @else
          <span class="flex items-center gap-1.5 {{ $isLast ? 'font-medium text-slate-900 dark:text-white' : '' }}" aria-current="{{ $isLast ? 'page' : 'false' }}">
            @if($crumb['icon'] && !$isLast)
              <i class="fa-solid {{ $crumb['icon'] }} text-base text-slate-400 dark:text-slate-500"></i>
            @endif
            {{ $crumb['label'] }}
          </span>
        @endif
        
        @if(!$isLast)
          <span class="flex-shrink-0" aria-hidden="true">{!! $separator !!}</span>
        @endif
      </li>
    @endforeach
  </ol>
</nav>

<style>
  /* Breadcrumb responsive behavior */
  @media (max-width: 639px) {
    nav[aria-label="Breadcrumb"] ol {
      @apply gap-1;
    }
    nav[aria-label="Breadcrumb"] li:not(:first-child):not(:last-child) {
      @apply hidden;
    }
    nav[aria-label="Breadcrumb"] li:first-child a span {
      @apply hidden;
    }
  }
</style>