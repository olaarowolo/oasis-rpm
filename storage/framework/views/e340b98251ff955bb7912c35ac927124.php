<?php $__env->startSection('title', 'System Status'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <?php if (isset($component)) { $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.breadcrumbs','data' => ['breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'System Status', 'url' => route('super-admin.system-status'), 'icon' => 'fa-server'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('super-admin.breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'System Status', 'url' => route('super-admin.system-status'), 'icon' => 'fa-server'],
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
<?php
    $cardTones = [
        'healthy' => 'border-emerald-200 bg-emerald-50/70 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-300',
        'warning' => 'border-amber-200 bg-amber-50/70 text-amber-700 dark:border-amber-900/50 dark:bg-amber-900/20 dark:text-amber-300',
        'degraded' => 'border-rose-200 bg-rose-50/70 text-rose-700 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-300',
        'error' => 'border-rose-200 bg-rose-50/70 text-rose-700 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-300',
        'running' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-300',
    ];
?>

<div class="space-y-6">
    <div class="rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-violet-900 p-6 text-white shadow-2xl lg:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-200">
                    <i class="fa-solid fa-wave-square text-emerald-300"></i>
                    Runtime Diagnostics
                </span>
                <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">Inspect Platform Services, Capacity, and Operational Warnings</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">Use this surface to verify core service health, confirm infrastructure configuration, and spot tenant-wide risk signals before they escalate into support incidents.</p>
            </div>

            <div class="grid w-full max-w-xl grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Universities</p>
                    <p class="mt-2 text-3xl font-black"><?php echo e($summary['universities']); ?></p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Users</p>
                    <p class="mt-2 text-3xl font-black"><?php echo e($summary['users']); ?></p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm col-span-2 sm:col-span-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Config Entries</p>
                    <p class="mt-2 text-3xl font-black"><?php echo e($summary['platform_settings']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        <?php $__currentLoopData = $healthCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-slate-800 <?php echo e($cardTones[$card['state']] ?? 'border-slate-200 text-slate-700 dark:border-slate-700 dark:text-slate-300'); ?>">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] opacity-80"><?php echo e($card['label']); ?></p>
                        <p class="mt-3 text-lg font-bold text-slate-900 dark:text-white"><?php echo e(ucfirst($card['state'])); ?></p>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300"><?php echo e($card['detail']); ?></p>
                    </div>
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white/70 text-base shadow-sm dark:bg-slate-900/40">
                        <i class="fa-solid <?php echo e($card['state'] === 'healthy' ? 'fa-circle-check' : ($card['state'] === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-exclamation')); ?>"></i>
                    </span>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <?php $__currentLoopData = $queues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $queue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($queue['route']); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700/40">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400"><?php echo e($queue['label']); ?></p>
                <p class="mt-2 text-3xl font-black <?php echo e($queue['tone'] === 'amber' ? 'text-amber-700 dark:text-amber-300' : ($queue['tone'] === 'violet' ? 'text-violet-700 dark:text-violet-300' : 'text-blue-700 dark:text-blue-300')); ?>"><?php echo e($queue['count']); ?></p>
                <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400"><?php echo e($queue['detail']); ?></p>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Universities</p>
            <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white"><?php echo e($summary['universities']); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Active Universities</p>
            <p class="mt-3 text-3xl font-black text-emerald-700 dark:text-emerald-300"><?php echo e($summary['active_universities']); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Users</p>
            <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white"><?php echo e($summary['users']); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Inactive Users</p>
            <p class="mt-3 text-3xl font-black text-amber-700 dark:text-amber-300"><?php echo e($summary['inactive_users']); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Platform Settings</p>
            <p class="mt-3 text-3xl font-black text-blue-700 dark:text-blue-300"><?php echo e($summary['platform_settings']); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Resources</p>
            <p class="mt-3 text-3xl font-black text-violet-700 dark:text-violet-300"><?php echo e($summary['resources']); ?></p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Runtime Components</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Version, service state, and dependency signals for the shared platform runtime.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Component</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    <?php $__currentLoopData = $components; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $statusClass = $component['status'] === 'running'
                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                                : ($component['status'] === 'warning'
                                    ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                                    : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300');
                        ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white"><?php echo e($component['name']); ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($statusClass); ?>">
                                    <i class="fa-solid <?php echo e($component['status'] === 'running' ? 'fa-circle-check' : ($component['status'] === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-exclamation')); ?> text-[10px]"></i>
                                    <?php echo e(ucfirst($component['status'])); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400"><?php echo e($component['value']); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/super-admin/system-status.blade.php ENDPATH**/ ?>