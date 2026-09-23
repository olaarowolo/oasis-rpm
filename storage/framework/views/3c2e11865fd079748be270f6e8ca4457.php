<?php $__env->startSection('title', 'Manage Universities'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <?php if (isset($component)) { $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.breadcrumbs','data' => ['breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Universities', 'url' => route('super-admin.universities'), 'icon' => 'fa-building-columns'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('super-admin.breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Universities', 'url' => route('super-admin.universities'), 'icon' => 'fa-building-columns'],
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
    <div class="rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900 p-6 text-white shadow-2xl lg:p-8">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-200">
                    <i class="fa-solid fa-building-columns text-blue-300"></i>
                    Tenant Lifecycle
                </span>
                <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">Run University Onboarding, Health Review, and Recovery From One Registry</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">This page is the operating surface for tenant lifecycle work. Review active footprint, isolate suspended or archived universities, and move directly into inspection or intervention.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="<?php echo e(route('super-admin.universities.create')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-plus"></i>
                    Add University
                </a>
                <a href="<?php echo e(route('super-admin.dashboard', ['tenant_status' => 'suspended'])); ?>" class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/15 transition-colors">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Review Tenant Risk
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 dark:bg-rose-900/20 px-4 py-3 text-sm text-rose-700 dark:text-rose-300">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Total Tenants</p>
            <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white"><?php echo e($summary['total']); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Active</p>
            <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300"><?php echo e($summary['active']); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Suspended</p>
            <p class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-300"><?php echo e($summary['suspended']); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Archived</p>
            <p class="mt-2 text-3xl font-black text-slate-700 dark:text-slate-300"><?php echo e($summary['archived']); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <?php $__currentLoopData = $queues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $queue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400"><?php echo e($queue['label']); ?></p>
                        <p class="mt-2 text-3xl font-black <?php echo e($queue['tone'] === 'amber' ? 'text-amber-700 dark:text-amber-300' : ($queue['tone'] === 'emerald' ? 'text-emerald-700 dark:text-emerald-300' : 'text-blue-700 dark:text-blue-300')); ?>"><?php echo e($queue['count']); ?></p>
                        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400"><?php echo e($queue['detail']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <form method="GET" action="<?php echo e(route('super-admin.universities')); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 space-y-4 md:space-y-0 md:grid md:grid-cols-[1.6fr_1fr_auto] md:items-end md:gap-4 md:p-6">
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Search</label>
            <input type="text" name="search" value="<?php echo e($filters['search']); ?>" placeholder="Search tenant name, code, or email" class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Status</label>
            <select name="status" class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                <?php $__currentLoopData = ['all' => 'All statuses', 'active' => 'Active', 'suspended' => 'Suspended', 'archived' => 'Archived']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php echo e($filters['status'] === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700 transition-colors">Apply</button>
            <a href="<?php echo e(route('super-admin.universities')); ?>" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700/50">Reset</a>
        </div>
    </form>

    <div class="flex flex-wrap gap-2">
        <?php $__currentLoopData = ['all' => 'All Tenants', 'active' => 'Active', 'suspended' => 'Suspended', 'archived' => 'Archived']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('super-admin.universities', array_merge(request()->query(), ['status' => $value]))); ?>" class="rounded-full px-3 py-1.5 text-xs font-semibold <?php echo e($filters['status'] === $value ? 'bg-violet-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700/50'); ?>"><?php echo e($label); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Universities Table -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Tenant Registry</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Review lifecycle state, footprint, and direct interventions for each university.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Name</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Code</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Email</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Status</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Users</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Students</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Supervisors</th>
                        <th class="px-5 py-3 text-right font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    <?php $__empty_1 = true; $__currentLoopData = $universities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $university): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $statusLabel = $university->archived_at ? 'Archived' : ($university->is_active ? 'Active' : 'Suspended');
                            $statusClass = $university->archived_at
                                ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                                : ($university->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300');
                        ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <a href="<?php echo e(route('super-admin.universities.show', $university)); ?>" class="font-medium text-slate-900 dark:text-white hover:text-violet-600 dark:hover:text-violet-400 transition-colors"><?php echo e($university->name); ?></a>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400 font-mono text-sm"><?php echo e($university->code); ?></td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400"><?php echo e($university->email); ?></td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($statusClass); ?>">
                                    <?php if($university->archived_at): ?>
                                        <i class="fa-solid fa-archive text-[10px]"></i>
                                    <?php elseif(!$university->is_active): ?>
                                        <i class="fa-solid fa-pause text-[10px]"></i>
                                    <?php else: ?>
                                        <i class="fa-solid fa-circle text-[6px]"></i>
                                    <?php endif; ?>
                                    <?php echo e($statusLabel); ?>

                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300"><?php echo e($university->users_count ?? 0); ?></td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300"><?php echo e($university->students_count ?? 0); ?></td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300"><?php echo e($university->supervisors_count ?? 0); ?></td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-2 flex-wrap">
                                    <a href="<?php echo e(route('super-admin.universities.show', $university)); ?>" class="text-sm font-medium text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">View</a>
                                    <a href="<?php echo e(route('super-admin.universities.edit', $university)); ?>" class="text-sm font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">Edit</a>

                                    <?php if($university->archived_at): ?>
                                        <form action="<?php echo e(route('super-admin.universities.activate', $university)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">Restore</button>
                                        </form>
                                    <?php elseif($university->is_active): ?>
                                        <form action="<?php echo e(route('super-admin.universities.suspend', $university)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-sm font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">Suspend</button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('super-admin.universities.activate', $university)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">Activate</button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if(! $university->archived_at): ?>
                                        <form action="<?php echo e(route('super-admin.universities.archive', $university)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-sm font-medium text-slate-600 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">Archive</button>
                                        </form>
                                    <?php endif; ?>

                                    <form action="<?php echo e(route('super-admin.universities.destroy', $university)); ?>" method="POST" onsubmit="return confirm('Delete this university permanently? This only works when it has no related records.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-building-columns text-3xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
                                No universities found. <a href="<?php echo e(route('super-admin.universities.create')); ?>" class="text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300 font-medium">Create the first one</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($universities->hasPages()): ?>
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
                <?php echo e($universities->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/super-admin/universities.blade.php ENDPATH**/ ?>