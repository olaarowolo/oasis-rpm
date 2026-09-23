<?php $__env->startSection('title', 'Platform Resources'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <?php if (isset($component)) { $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.breadcrumbs','data' => ['breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Resources', 'url' => route('super-admin.resources'), 'icon' => 'fa-database'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('super-admin.breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Resources', 'url' => route('super-admin.resources'), 'icon' => 'fa-database'],
    ])]); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Platform Resources</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">View all learning resources across the platform.</p>
        </div>
    </div>

    <!-- Resources Table -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Title</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Type</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Stage</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">University</th>
                        <th class="px-5 py-3 text-right font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    <?php $__empty_1 = true; $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-900 dark:text-white"><?php echo e($resource->title); ?></p>
                                <?php if($resource->description): ?>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 truncate max-w-xs"><?php echo e($resource->description); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    <?php echo e(ucfirst($resource->type)); ?>

                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                    Stage <?php echo e($resource->stage); ?>

                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400"><?php echo e($resource->university->name ?? 'Platform'); ?></td>
                            <td class="px-5 py-4 text-right">
                                <a href="#" class="text-sm font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">View</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-database text-3xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
                                No resources found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($resources->hasPages()): ?>
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
                <?php echo e($resources->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/super-admin/resources.blade.php ENDPATH**/ ?>