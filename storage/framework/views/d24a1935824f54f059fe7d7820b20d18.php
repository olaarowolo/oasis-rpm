<?php
  $eyebrow = $eyebrow ?? null;
  $intro = $intro ?? null;
  $sections = $sections ?? [];
  $status = $status ?? null;
?>

<?php if (isset($component)) { $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.public','data' => ['title' => $pageTitle,'description' => $pageDescription]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('layouts.public'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitle),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageDescription)]); ?>
  <section class="bg-slate-50 py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl">
        <?php if($eyebrow): ?>
          <p class="text-xs font-bold tracking-wider text-academic-700 uppercase"><?php echo e($eyebrow); ?></p>
        <?php endif; ?>
        <h1 class="mt-2 font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-academic-900"><?php echo e($heading); ?></h1>
        <?php if($intro): ?>
          <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-2xl leading-relaxed"><?php echo e($intro); ?></p>
        <?php endif; ?>
      </div>

      <?php if($sections && count($sections) > 0): ?>
        <div class="mt-10 grid md:grid-cols-2 xl:grid-cols-3 gap-4">
          <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="elevate rounded-2xl p-5 border border-slate-200 bg-slate-50">
              <?php if(isset($section['icon'])): ?>
                <p class="w-10 h-10 rounded-xl <?php echo e($section['icon_bg'] ?? 'bg-blue-100'); ?> <?php echo e($section['icon_color'] ?? 'text-blue-700'); ?> grid place-content-center">
                  <i class="fa-solid <?php echo e($section['icon']); ?>"></i>
                </p>
              <?php endif; ?>
              <h3 class="mt-4 font-semibold text-lg text-academic-900"><?php echo e($section['title']); ?></h3>
              <?php if(isset($section['description'])): ?>
                <p class="mt-2 text-sm text-slate-600"><?php echo e($section['description']); ?></p>
              <?php endif; ?>
            </article>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>

      <div class="mt-10 flex flex-col sm:flex-row gap-3">
        <a href="<?php echo e(route('login')); ?>#request-demo" class="btn-animate inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-semibold transition">
          <i class="fa-solid fa-calendar-check"></i>
          Request Demo
        </a>
        <a href="<?php echo e(route('login')); ?>" class="btn-animate inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 hover:border-slate-300 font-semibold transition">
          <i class="fa-solid fa-sign-in-alt"></i>
          Go to Login
        </a>
      </div>

      <?php if($status): ?>
        <div class="mt-10 p-4 rounded-xl bg-slate-100 border border-slate-200 text-center">
          <p class="text-sm text-slate-600"><?php echo e($status); ?></p>
        </div>
      <?php endif; ?>
    </div>
  </section>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd)): ?>
<?php $attributes = $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd; ?>
<?php unset($__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd)): ?>
<?php $component = $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd; ?>
<?php unset($__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd); ?>
<?php endif; ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/public/page.blade.php ENDPATH**/ ?>