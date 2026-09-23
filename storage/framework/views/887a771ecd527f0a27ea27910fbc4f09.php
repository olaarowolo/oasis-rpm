<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Supervisor Hub | TheOAsis Research Supervision System']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Supervisor Hub | TheOAsis Research Supervision System']); ?>

  <?php echo $__env->make('partials.dashboards.supervisor-header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <aside id="app-sidebar" aria-label="Supervisor navigation" class="md:hidden w-full flex-shrink-0 bg-slate-50 dark:bg-slate-900">
    <div class="flex items-center justify-between mb-4">
      <span class="font-bold text-slate-900 dark:text-white text-sm">Menu</span>
      <button type="button" data-mobile-nav-close aria-label="Close menu" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700/50">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>

    <nav class="bg-white dark:bg-slate-800 rounded-2xl p-2 shadow-sm border border-slate-200 dark:border-slate-700 space-y-1" aria-label="Supervisor navigation">
      <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Supervisor Hub</div>
      <a href="<?php echo e(route('supervisor.dashboard')); ?>" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-xl transition bg-academic-50 text-academic-700 dark:bg-academic-900/40 dark:text-academic-100">
        <i class="fa-solid fa-chart-line w-5 text-center text-academic-600 dark:text-academic-400"></i>
        Dashboard
      </a>
      <a href="<?php echo e(route('supervisor.students')); ?>" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-users w-5 text-center"></i>
        Students
      </a>
      <a href="<?php echo e(route('supervisor.proposals')); ?>" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-file-signature w-5 text-center"></i>
        Topic Approvals
      </a>
      <a href="<?php echo e(route('supervisor.meetings')); ?>" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-comments w-5 text-center"></i>
        Meeting Logs
      </a>
      <a href="<?php echo e(route('supervisor.analytics')); ?>" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-chart-pie w-5 text-center text-purple-500"></i>
        Analytics &amp; Insights
      </a>
      <a href="<?php echo e(route('supervisor.resources.pending')); ?>" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <i class="fa-solid fa-check-to-mark w-5 text-center text-academic-600 dark:text-academic-400"></i>
        Resource Approvals
      </a>
    </nav>

    <div class="mt-4 p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm text-xs">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-academic-800 text-amber-400 font-extrabold flex items-center justify-center text-sm shadow-md">OA</div>
        <div class="min-w-0">
          <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate">Olasunkanmi Arowolo, PhD</h4>
          <p class="text-[10px] text-slate-500 dark:text-slate-400">Research Supervisor</p>
        </div>
      </div>
    </div>
  </aside>

  <!-- ================= MAIN CONTENT ================= -->
  <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    
    <?php echo $__env->make('partials.dashboards.supervisor-welcome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('partials.dashboards.supervisor-metrics', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('partials.dashboards.supervisor-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('partials.dashboards.supervisor-roster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('partials.dashboards.supervisor-quick-actions', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  </main>

  <?php echo $__env->make('partials.dashboards.supervisor-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/supervisor-dashboard.blade.php ENDPATH**/ ?>