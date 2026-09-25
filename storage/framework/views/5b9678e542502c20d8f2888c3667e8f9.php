<header data-app-header-root class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/85">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-3 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-2 sm:gap-3 lg:flex-[0_1_28rem]">
            <button type="button" data-mobile-nav-toggle aria-controls="<?php echo e($sidebarId); ?>" aria-expanded="false" aria-label="Open navigation menu" class="mobile-nav-toggle <?php echo e($mobileToggleHiddenClass); ?> -ml-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700/50">
                <span class="mobile-nav-line" aria-hidden="true"></span>
                <span class="mobile-nav-line" aria-hidden="true"></span>
                <span class="mobile-nav-line" aria-hidden="true"></span>
            </button>

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-academic-900 via-academic-800 to-amber-600 shadow-md ring-1 ring-white/10">
                <img src="<?php echo e(asset('img/afriscribe-logo-white.png')); ?>" alt="AfriScribe" class="h-5 w-auto object-contain" loading="lazy" />
            </div>

            <div class="min-w-0">
                <p class="flex min-w-0 items-center gap-2 text-sm font-bold leading-tight text-slate-900 dark:text-white sm:text-lg">
                    <span class="truncate"><?php echo e($title); ?></span>
                    <span class="hidden shrink-0 rounded bg-amber-100 px-2 py-0.5 font-mono text-[10px] font-bold text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 xs:inline">LASU</span>
                </p>
                <p class="truncate text-[11px] text-slate-500 dark:text-slate-400"><?php echo e($subtitle); ?></p>
            </div>
        </div>

        <?php if (isset($component)) { $__componentOriginal132a5c0232f4cd575f2a7ca5ab773aa2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal132a5c0232f4cd575f2a7ca5ab773aa2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-header-navigation','data' => ['items' => $navigation]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-header-navigation'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navigation)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal132a5c0232f4cd575f2a7ca5ab773aa2)): ?>
<?php $attributes = $__attributesOriginal132a5c0232f4cd575f2a7ca5ab773aa2; ?>
<?php unset($__attributesOriginal132a5c0232f4cd575f2a7ca5ab773aa2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal132a5c0232f4cd575f2a7ca5ab773aa2)): ?>
<?php $component = $__componentOriginal132a5c0232f4cd575f2a7ca5ab773aa2; ?>
<?php unset($__componentOriginal132a5c0232f4cd575f2a7ca5ab773aa2); ?>
<?php endif; ?>

        <div class="flex items-center gap-2 sm:gap-3 lg:flex-[0_0_auto]">
            <div class="hidden items-center gap-2 rounded-xl px-3 py-1.5 text-xs font-bold sm:flex <?php echo e($config['accent']); ?>">
                <i class="fa-solid <?php echo e($config['icon']); ?>"></i>
                <span><?php echo e($config['badge']); ?></span>
            </div>

            <?php if(!empty($resolvedPrimaryAction) || isset($primaryAction)): ?>
                <div class="hidden lg:block">
                    <?php if(isset($primaryAction)): ?>
                        <?php echo e($primaryAction); ?>

                    <?php else: ?>
                        <a href="<?php echo e($resolvedPrimaryAction['href'] ?? '#'); ?>" class="inline-flex items-center gap-2 rounded-xl bg-academic-700 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-academic-800 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-academic-600">
                            <i class="fa-solid <?php echo e($resolvedPrimaryAction['icon'] ?? 'fa-bolt'); ?> text-xs"></i>
                            <span><?php echo e($resolvedPrimaryAction['label'] ?? 'Open'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal9f248e11e54ab059af20b5cf61961315 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f248e11e54ab059af20b5cf61961315 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-header-notifications','data' => ['viewAllHref' => $notificationsViewAllHref]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-header-notifications'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['view-all-href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationsViewAllHref)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f248e11e54ab059af20b5cf61961315)): ?>
<?php $attributes = $__attributesOriginal9f248e11e54ab059af20b5cf61961315; ?>
<?php unset($__attributesOriginal9f248e11e54ab059af20b5cf61961315); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f248e11e54ab059af20b5cf61961315)): ?>
<?php $component = $__componentOriginal9f248e11e54ab059af20b5cf61961315; ?>
<?php unset($__componentOriginal9f248e11e54ab059af20b5cf61961315); ?>
<?php endif; ?>

            <button type="button" data-theme-toggle class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white/90 text-slate-500 shadow-sm transition hover:border-slate-300 hover:text-slate-700 dark:border-slate-700 dark:bg-slate-800/90 dark:text-slate-300 dark:hover:border-slate-600 dark:hover:text-white" aria-label="Toggle dark mode">
                <i class="fa-solid fa-moon text-sm dark:hidden"></i>
                <i class="fa-solid fa-sun hidden text-sm dark:inline"></i>
            </button>

            <?php if (isset($component)) { $__componentOriginal5b014308a193f2cc26b991ca7c425054 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5b014308a193f2cc26b991ca7c425054 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-header-user-menu','data' => ['user' => $currentUser,'displayName' => $displayName,'roleLabel' => $config['roleLabel'],'initials' => $initials,'menuLinks' => $menuLinks]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-header-user-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentUser),'display-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($displayName),'role-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($config['roleLabel']),'initials' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($initials),'menu-links' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($menuLinks)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5b014308a193f2cc26b991ca7c425054)): ?>
<?php $attributes = $__attributesOriginal5b014308a193f2cc26b991ca7c425054; ?>
<?php unset($__attributesOriginal5b014308a193f2cc26b991ca7c425054); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5b014308a193f2cc26b991ca7c425054)): ?>
<?php $component = $__componentOriginal5b014308a193f2cc26b991ca7c425054; ?>
<?php unset($__componentOriginal5b014308a193f2cc26b991ca7c425054); ?>
<?php endif; ?>
        </div>
    </div>

    <div class="border-t border-slate-200/70 px-3 py-2 lg:hidden dark:border-slate-700/80">
        <div class="flex gap-2 overflow-x-auto pb-1">
            <?php if(!empty($resolvedPrimaryAction)): ?>
                <a href="<?php echo e($resolvedPrimaryAction['href'] ?? '#'); ?>" class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl bg-academic-700 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-academic-800">
                    <i class="fa-solid <?php echo e($resolvedPrimaryAction['icon'] ?? 'fa-bolt'); ?> text-[11px]"></i>
                    <span><?php echo e($resolvedPrimaryAction['label'] ?? 'Open'); ?></span>
                </a>
            <?php endif; ?>

            <?php $__currentLoopData = $navigation ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($item['href']); ?>" class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl px-3 py-2 text-xs font-semibold transition <?php echo e($item['active'] ? 'bg-academic-50 text-academic-700 dark:bg-academic-900/40 dark:text-academic-100' : 'bg-white text-slate-600 dark:bg-slate-800 dark:text-slate-300'); ?>">
                    <i class="fa-solid <?php echo e($item['icon']); ?> text-[11px]"></i>
                    <span><?php echo e($item['label']); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <?php if($breadcrumbs): ?>
        <div class="border-t border-slate-200/70 px-3 py-2 dark:border-slate-700/80 sm:px-6 lg:px-8">
            <?php if(is_countable($breadcrumbs) && count($breadcrumbs) > 0): ?>
                <?php if (isset($component)) { $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.breadcrumbs','data' => ['breadcrumbs' => $breadcrumbs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('super-admin.breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($breadcrumbs)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6)): ?>
<?php $attributes = $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6; ?>
<?php unset($__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6)): ?>
<?php $component = $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6; ?>
<?php unset($__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6); ?>
<?php endif; ?>
            <?php elseif(is_string($breadcrumbs) && trim($breadcrumbs) !== ''): ?>
                <?php echo $breadcrumbs; ?>

            <?php endif; ?>
        </div>
    <?php endif; ?>
</header><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/components/app-header.blade.php ENDPATH**/ ?>