<!-- SUPER ADMIN BREADCRUMB COMPONENT -->
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'breadcrumbs' => [],
    'separator' => '<i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'breadcrumbs' => [],
    'separator' => '<i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>',
]); ?>
<?php foreach (array_filter(([
    'breadcrumbs' => [],
    'separator' => '<i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
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
?>

<nav aria-label="Breadcrumb" class="flex items-center gap-1.5 overflow-x-auto pb-1" role="navigation">
  <ol class="flex items-center gap-1.5 text-sm whitespace-nowrap min-w-max">
    <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        $isLast = $index === array_key_last($breadcrumbs);
        $hasUrl = !empty($crumb['url']) && !$isLast;
      ?>
      
      <li class="flex items-center gap-1.5 <?php echo e($isLast ? 'text-slate-900 dark:text-white font-medium' : 'text-slate-500 dark:text-slate-400'); ?>">
        <?php if($hasUrl): ?>
          <a href="<?php echo e($crumb['url']); ?>" 
             class="flex items-center gap-1.5 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
             <?php echo e($index === 0 ? 'aria-label="Go to Dashboard"' : ''); ?>>
            <?php if($crumb['icon']): ?>
              <i class="fa-solid <?php echo e($crumb['icon']); ?> text-base"></i>
            <?php endif; ?>
            <span class="hidden sm:inline"><?php echo e($crumb['label']); ?></span>
          </a>
        <?php else: ?>
          <span class="flex items-center gap-1.5 <?php echo e($isLast ? 'font-medium text-slate-900 dark:text-white' : ''); ?>" aria-current="<?php echo e($isLast ? 'page' : 'false'); ?>">
            <?php if($crumb['icon'] && !$isLast): ?>
              <i class="fa-solid <?php echo e($crumb['icon']); ?> text-base text-slate-400 dark:text-slate-500"></i>
            <?php endif; ?>
            <?php echo e($crumb['label']); ?>

          </span>
        <?php endif; ?>
        
        <?php if(!$isLast): ?>
          <span class="flex-shrink-0" aria-hidden="true"><?php echo $separator; ?></span>
        <?php endif; ?>
      </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
</style><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/components/super-admin/breadcrumbs.blade.php ENDPATH**/ ?>