<?php $__env->startSection('title', $university->name); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <?php if (isset($component)) { $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.breadcrumbs','data' => ['breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Universities', 'url' => route('super-admin.universities'), 'icon' => 'fa-building-columns'],
        ['label' => $university->name, 'url' => route('super-admin.universities.show', $university), 'icon' => 'fa-building-shield'],
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
        ['label' => $university->name, 'url' => route('super-admin.universities.show', $university), 'icon' => 'fa-building-shield'],
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
    $statusLabel = $university->archived_at ? 'Archived' : ($university->is_active ? 'Active' : 'Suspended');
    $statusClass = $university->archived_at
        ? 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'
        : ($university->is_active
            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
            : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300');
?>

<div class="space-y-6">
    <div class="rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900 p-6 text-white shadow-2xl lg:p-8">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-3xl">
                <a href="<?php echo e(route('super-admin.universities')); ?>" class="inline-flex items-center gap-2 text-sm font-medium text-blue-200 hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Universities
                </a>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl"><?php echo e($university->name); ?></h1>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold <?php echo e($statusClass); ?>">
                        <i class="fa-solid <?php echo e($university->archived_at ? 'fa-box-archive' : ($university->is_active ? 'fa-circle-check' : 'fa-pause')); ?> text-[10px]"></i>
                        <?php echo e($statusLabel); ?>

                    </span>
                </div>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base"><?php echo e($university->code); ?> is configured as a tenant on the platform. Review profile settings, recent identities, academic assets, and the latest audit activity from one operator surface.</p>
                <div class="mt-5 flex flex-wrap gap-3 text-sm text-slate-200">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5">
                        <i class="fa-solid fa-envelope text-blue-200"></i>
                        <?php echo e($university->email); ?>

                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5">
                        <i class="fa-solid fa-building-user text-amber-200"></i>
                        <?php echo e($university->department); ?>

                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="<?php echo e(route('super-admin.universities.edit', $university)); ?>" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-pen"></i>
                    Edit University
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Users</p>
            <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white"><?php echo e($university->users_count); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Students</p>
            <p class="mt-3 text-3xl font-black text-blue-700 dark:text-blue-300"><?php echo e($university->students_count); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Supervisors</p>
            <p class="mt-3 text-3xl font-black text-amber-700 dark:text-amber-300"><?php echo e($university->supervisors_count); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Resources</p>
            <p class="mt-3 text-3xl font-black text-violet-700 dark:text-violet-300"><?php echo e($university->resources_count); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Proposals</p>
            <p class="mt-3 text-3xl font-black text-emerald-700 dark:text-emerald-300"><?php echo e($university->proposals_count); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Meetings</p>
            <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white"><?php echo e($university->meeting_logs_count); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[0.85fr_1.15fr]">
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Tenant Profile</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Core contact and lifecycle metadata.</p>
                    </div>
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900/40">
                        <span class="h-6 w-6 rounded-full border border-white/60" style="background-color: <?php echo e($university->branding_color); ?>"></span>
                    </span>
                </div>

                <dl class="mt-6 space-y-4 text-sm">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-900/40">
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Department</dt>
                        <dd class="mt-1 font-medium text-slate-900 dark:text-white"><?php echo e($university->department); ?></dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-900/40">
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Phone</dt>
                        <dd class="mt-1 font-medium text-slate-900 dark:text-white"><?php echo e($university->phone ?: 'Not set'); ?></dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-900/40">
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Branding Color</dt>
                        <dd class="mt-1 flex items-center gap-2 font-medium text-slate-900 dark:text-white">
                            <span class="inline-block h-4 w-4 rounded-full border border-slate-200 dark:border-slate-600" style="background-color: <?php echo e($university->branding_color); ?>"></span>
                            <?php echo e($university->branding_color); ?>

                        </dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-900/40">
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Created</dt>
                        <dd class="mt-1 font-medium text-slate-900 dark:text-white"><?php echo e($university->created_at?->format('Y-m-d H:i')); ?></dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-900/40">
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Archived At</dt>
                        <dd class="mt-1 font-medium text-slate-900 dark:text-white"><?php echo e($university->archived_at?->format('Y-m-d H:i') ?: 'Not archived'); ?></dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent Audit Activity</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Latest operator actions affecting this tenant.</p>
                <ul class="mt-5 space-y-3 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $recentAuditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-700">
                            <p class="font-semibold text-slate-900 dark:text-white"><?php echo e(ucfirst($log->action)); ?> <?php echo e($log->model_type); ?></p>
                            <p class="mt-1 text-slate-500 dark:text-slate-400"><?php echo e($log->user->name ?? 'System'); ?> · <?php echo e($log->created_at?->format('Y-m-d H:i')); ?></p>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="rounded-2xl border border-dashed border-slate-200 px-4 py-5 text-slate-500 dark:border-slate-700 dark:text-slate-400">No audit activity recorded yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent User Accounts</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Newest tenant identities and their current role state.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold uppercase tracking-[0.2em] text-xs text-slate-500 dark:text-slate-400">Name</th>
                                <th class="px-5 py-3 text-left font-semibold uppercase tracking-[0.2em] text-xs text-slate-500 dark:text-slate-400">Email</th>
                                <th class="px-5 py-3 text-left font-semibold uppercase tracking-[0.2em] text-xs text-slate-500 dark:text-slate-400">Role</th>
                                <th class="px-5 py-3 text-left font-semibold uppercase tracking-[0.2em] text-xs text-slate-500 dark:text-slate-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="px-5 py-4 font-medium text-slate-900 dark:text-white"><?php echo e($user->name); ?></td>
                                    <td class="px-5 py-4 text-slate-500 dark:text-slate-400"><?php echo e($user->email); ?></td>
                                    <td class="px-5 py-4 text-slate-500 dark:text-slate-400"><?php echo e(ucfirst(str_replace('_', ' ', $user->role))); ?></td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($user->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'); ?>">
                                            <?php echo e($user->is_active ? 'Active' : 'Suspended'); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-slate-500 dark:text-slate-400">No users yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent Resources</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Most recently updated academic assets for this tenant.</p>
                    </div>
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">
                        <i class="fa-solid fa-book-open"></i>
                    </span>
                </div>

                <ul class="mt-5 space-y-3 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $recentResources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="flex items-start justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-700">
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white"><?php echo e($resource->title); ?></p>
                                <p class="mt-1 text-slate-500 dark:text-slate-400">Stage <?php echo e($resource->stage); ?> · <?php echo e(ucfirst($resource->type)); ?></p>
                            </div>
                            <span class="text-xs text-slate-400 dark:text-slate-500"><?php echo e($resource->updated_at?->diffForHumans()); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="rounded-2xl border border-dashed border-slate-200 px-4 py-5 text-slate-500 dark:border-slate-700 dark:text-slate-400">No resources found for this university.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/super-admin/university-show.blade.php ENDPATH**/ ?>