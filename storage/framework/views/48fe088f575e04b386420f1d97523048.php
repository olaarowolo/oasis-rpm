<?php $__env->startSection('title', 'Audit Logs'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <?php if (isset($component)) { $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.breadcrumbs','data' => ['breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Audit Logs', 'url' => route('super-admin.audit-logs'), 'icon' => 'fa-clipboard-list'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('super-admin.breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Audit Logs', 'url' => route('super-admin.audit-logs'), 'icon' => 'fa-clipboard-list'],
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
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Audit Logs</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Platform-wide audit trail for all tenant and system changes.</p>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Date</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">User</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">University</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Action</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Model</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    <?php $__empty_1 = true; $__currentLoopData = $auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400"><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></td>
                            <td class="px-5 py-4 whitespace-nowrap font-medium text-slate-900 dark:text-white"><?php echo e($log->user->name ?? 'System'); ?></td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400"><?php echo e($log->university->name ?? 'Platform'); ?></td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <?php
                                    $actionClasses = [
                                        'created' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                        'updated' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        'deleted' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
                                        'suspended' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                        'activated' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                        'archived' => 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300',
                                    ];
                                ?>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($actionClasses[$log->action] ?? 'bg-slate-100 text-slate-800'); ?>">
                                    <i class="fa-solid <?php echo e($log->action === 'created' ? 'fa-plus' : ($log->action === 'deleted' ? 'fa-trash' : ($log->action === 'updated' ? 'fa-pen' : 'fa-circle'))); ?> text-[10px]"></i>
                                    <?php echo e(ucfirst($log->action)); ?>

                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300 font-mono text-sm"><?php echo e($log->model_type); ?></td>
                            <td class="px-5 py-4 max-w-xs">
                                <?php if($log->description): ?>
                                    <p class="text-slate-600 dark:text-slate-400 truncate"><?php echo e($log->description); ?></p>
                                <?php elseif($log->changes): ?>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 font-mono bg-slate-50 dark:bg-slate-700/50 rounded p-2 max-h-12 overflow-auto"><?php echo e(json_encode($log->changes)); ?></div>
                                <?php else: ?>
                                    <span class="text-slate-400 dark:text-slate-500 italic">No details</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-clipboard-list text-3xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
                                No audit logs recorded yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($auditLogs->hasPages()): ?>
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
                <?php echo e($auditLogs->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/super-admin/audit-logs.blade.php ENDPATH**/ ?>