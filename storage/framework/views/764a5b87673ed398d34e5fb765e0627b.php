<?php $__env->startSection('title', 'Super Admin Dashboard'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <?php if (isset($component)) { $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.breadcrumbs','data' => ['breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('super-admin.breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
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
<div class="space-y-8">
    <!-- Hero Section -->
    <div class="rounded-3xl bg-gradient-to-br from-slate-950 via-academic-900 to-slate-800 p-6 text-white shadow-2xl lg:p-8">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-blue-100/80">
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1">Platform Command Center</span>
                    <span class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-emerald-200">Live Super Admin Surface</span>
                </div>
                <h1 class="mt-4 text-3xl font-black tracking-tight text-white sm:text-4xl">Operate Every Tenant, Identity, and Runtime From One Surface</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">This dashboard is the platform nerve center: provision tenants, intervene on identity risks, inspect runtime health, and move directly into tenant or user operations without leaving the super-admin workspace.</p>
                <div class="mt-5 flex flex-wrap items-center gap-3 text-sm text-slate-200">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5">
                        <i class="fa-solid fa-user-shield text-amber-300"></i>
                        <?php echo e($currentUser?->name ?? 'Platform Operator'); ?>

                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5">
                        <i class="fa-solid fa-building"></i>
                        <?php echo e($currentUser?->university?->name ?? 'Cross-tenant access'); ?>

                    </span>
                </div>
                <div class="mt-5 flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-300">
                    <?php $__currentLoopData = ['Orient', 'Operate', 'Govern', 'Configure']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lane): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5"><?php echo e($lane); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="w-full xl:max-w-md">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Operator Workbench</p>
                        <span class="rounded-full border border-white/10 bg-white/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-200">1-click actions</span>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <?php $__currentLoopData = $quickActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e($action['route']); ?>" class="group rounded-2xl border border-white/10 bg-slate-900/30 p-4 transition hover:border-white/20 hover:bg-white/10">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-xl <?php echo e($action['tone'] === 'academic' ? 'bg-blue-400/15 text-blue-200' : ($action['tone'] === 'purple' ? 'bg-purple-400/15 text-purple-200' : ($action['tone'] === 'emerald' ? 'bg-emerald-400/15 text-emerald-200' : 'bg-amber-400/15 text-amber-200'))); ?>">
                                        <i class="fa-solid <?php echo e($action['icon']); ?>"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-white group-hover:text-blue-100"><?php echo e($action['title']); ?></p>
                                        <p class="mt-1 text-xs leading-5 text-slate-300"><?php echo e($action['description']); ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="<?php echo e(route('super-admin.dashboard')); ?>" class="mt-6 rounded-2xl border border-white/10 bg-slate-900/30 p-4">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.4fr_0.8fr_0.8fr_0.8fr_auto]">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Search Tenants Or Users</label>
                    <input type="text" name="q" value="<?php echo e($dashboardFilters['q']); ?>" placeholder="University code, tenant name, user name, or email" class="block w-full rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400/40">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Tenant Filter</label>
                    <select name="tenant_status" class="block w-full rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400/40">
                        <?php $__currentLoopData = ['all' => 'All Tenants', 'active' => 'Active', 'suspended' => 'Suspended', 'archived' => 'Archived']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e($dashboardFilters['tenant_status'] === $value ? 'selected' : ''); ?> class="text-slate-900"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">User Focus</label>
                    <select name="user_focus" class="block w-full rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400/40">
                        <?php $__currentLoopData = ['all' => 'Watchlist', 'privileged' => 'Privileged', 'admins' => 'Admins', 'inactive' => 'Inactive', 'supervisors' => 'Supervisors', 'students' => 'Students']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e($dashboardFilters['user_focus'] === $value ? 'selected' : ''); ?> class="text-slate-900"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Trend Window</label>
                    <select name="trend_window" class="block w-full rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400/40">
                        <?php $__currentLoopData = [7 => '7 days', 14 => '14 days', 30 => '30 days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e((int) $dashboardFilters['trend_window'] === $value ? 'selected' : ''); ?> class="text-slate-900"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="rounded-xl bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-100">Apply</button>
                    <a href="<?php echo e(route('super-admin.dashboard')); ?>" class="rounded-xl border border-white/10 px-4 py-3 text-sm font-semibold text-white hover:bg-white/10">Reset</a>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <?php $__currentLoopData = ['all' => 'All Tenants', 'active' => 'Active Only', 'suspended' => 'Suspended', 'archived' => 'Archived']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('super-admin.dashboard', array_merge(request()->query(), ['tenant_status' => $value]))); ?>" class="rounded-full px-3 py-1.5 text-xs font-semibold <?php echo e($dashboardFilters['tenant_status'] === $value ? 'bg-blue-400 text-slate-950' : 'bg-white/10 text-slate-200 hover:bg-white/20'); ?>"><?php echo e($label); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = ['all' => 'Watchlist', 'privileged' => 'Privileged', 'admins' => 'Admins', 'inactive' => 'Inactive', 'supervisors' => 'Supervisors', 'students' => 'Students']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('super-admin.dashboard', array_merge(request()->query(), ['user_focus' => $value]))); ?>" class="rounded-full px-3 py-1.5 text-xs font-semibold <?php echo e($dashboardFilters['user_focus'] === $value ? 'bg-emerald-400 text-slate-950' : 'bg-white/10 text-slate-200 hover:bg-white/20'); ?>"><?php echo e($label); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500"><?php echo e($item['label']); ?></p>
                        <p class="mt-3 text-3xl font-black text-slate-900"><?php echo e($item['value']); ?></p>
                        <p class="mt-2 text-sm text-slate-500"><?php echo e($item['caption']); ?></p>
                    </div>
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl <?php echo e($item['tone'] === 'academic' ? 'bg-blue-100 text-blue-700' : ($item['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-700' : ($item['tone'] === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-violet-100 text-violet-700'))); ?>">
                        <i class="fa-solid <?php echo e($item['icon']); ?> text-lg"></i>
                    </span>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Command Model -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Command Model</h2>
                    <p class="mt-1 text-sm text-slate-500">One view for ownership, operating pillars, and the operator decision path.</p>
                </div>
                <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white">Architecture view</span>
            </div>

            <div class="space-y-6 p-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Ownership Lanes</p>
                    <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <?php $__currentLoopData = $workstreamOwnership; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stream): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e($stream['route']); ?>" class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:border-slate-300 hover:bg-white">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] <?php echo e($stream['tone'] === 'academic' ? 'text-blue-700' : ($stream['tone'] === 'emerald' ? 'text-emerald-700' : ($stream['tone'] === 'violet' ? 'text-violet-700' : 'text-amber-700'))); ?>"><?php echo e($stream['label']); ?></p>
                                        <p class="mt-2 text-3xl font-black text-slate-900"><?php echo e($stream['metric']); ?></p>
                                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400"><?php echo e($stream['unit']); ?></p>
                                    </div>
                                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl <?php echo e($stream['tone'] === 'academic' ? 'bg-blue-100 text-blue-700' : ($stream['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-700' : ($stream['tone'] === 'violet' ? 'bg-violet-100 text-violet-700' : 'bg-amber-100 text-amber-700'))); ?>">
                                        <i class="fa-solid <?php echo e($stream['icon']); ?>"></i>
                                    </span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-slate-500"><?php echo e($stream['description']); ?></p>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Operating Pillars</p>
                    <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <?php $__currentLoopData = $operatingModel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pillar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] <?php echo e($pillar['tone'] === 'academic' ? 'text-blue-700' : ($pillar['tone'] === 'violet' ? 'text-violet-700' : ($pillar['tone'] === 'emerald' ? 'text-emerald-700' : 'text-slate-600'))); ?>"><?php echo e($pillar['eyebrow']); ?></p>
                                        <h3 class="mt-2 text-lg font-bold text-slate-900"><?php echo e($pillar['title']); ?></h3>
                                    </div>
                                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl <?php echo e($pillar['tone'] === 'academic' ? 'bg-blue-100 text-blue-700' : ($pillar['tone'] === 'violet' ? 'bg-violet-100 text-violet-700' : ($pillar['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700'))); ?>">
                                        <i class="fa-solid <?php echo e($pillar['icon']); ?>"></i>
                                    </span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-slate-500"><?php echo e($pillar['description']); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Priority Queues</h2>
                    <p class="mt-1 text-sm text-slate-500">The top action lanes on the dashboard should always reflect tenant lifecycle, identity review, and runtime follow-up.</p>
                </div>
                <div class="space-y-3 p-5">
                    <?php $__currentLoopData = $priorityQueues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $queue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($queue['route']); ?>" class="block rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:border-slate-300 hover:bg-white">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl <?php echo e($queue['tone'] === 'academic' ? 'bg-blue-100 text-blue-700' : ($queue['tone'] === 'violet' ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700')); ?>">
                                        <i class="fa-solid <?php echo e($queue['icon']); ?>"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900"><?php echo e($queue['label']); ?></p>
                                        <p class="mt-1 text-sm leading-6 text-slate-500"><?php echo e($queue['detail']); ?></p>
                                    </div>
                                </div>
                                <span class="inline-flex min-w-[2.75rem] items-center justify-center rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white"><?php echo e($queue['count']); ?></span>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Operator Loop</h2>
                    <p class="mt-1 text-sm text-slate-500">A compact decision path from signal to verified recovery.</p>
                </div>
                <div class="space-y-3 p-5">
                    <?php $__currentLoopData = $operatorLoop; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <span class="inline-flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl <?php echo e($step['tone'] === 'academic' ? 'bg-blue-100 text-blue-700' : ($step['tone'] === 'violet' ? 'bg-violet-100 text-violet-700' : ($step['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-700' : ($step['tone'] === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700')))); ?> text-sm font-black">
                                <?php echo e($step['step']); ?>

                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900"><?php echo e($step['title']); ?></p>
                                <p class="mt-1 text-sm leading-6 text-slate-500"><?php echo e($step['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.25fr_0.75fr]">
        <!-- Tenant Health Board -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Tenant Health Board</h2>
                    <p class="mt-1 text-sm text-slate-500">The primary operating board for tenant footprint, configuration health, and intervention readiness.</p>
                </div>
                <a href="<?php echo e(route('super-admin.universities')); ?>" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Open all tenants</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-500">Tenant</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-500">Status</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-500">Users</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-500">Config</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-500">Last Activity</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $tenantHealth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900"><?php echo e($tenant['name']); ?></p>
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400"><?php echo e($tenant['code']); ?></p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($tenant['status'] === 'active' ? 'bg-emerald-100 text-emerald-700' : ($tenant['status'] === 'suspended' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700')); ?>"><?php echo e(ucfirst($tenant['status'])); ?></span>
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    <p><?php echo e($tenant['users_count']); ?> users</p>
                                    <p class="text-xs text-slate-400"><?php echo e($tenant['students_count']); ?> students · <?php echo e($tenant['supervisors_count']); ?> supervisors</p>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="w-24 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-2 rounded-full <?php echo e($tenant['config_completeness'] >= 80 ? 'bg-emerald-500' : ($tenant['config_completeness'] >= 60 ? 'bg-amber-500' : 'bg-rose-500')); ?>" style="width: <?php echo e($tenant['config_completeness']); ?>%"></div>
                                    </div>
                                    <p class="mt-2 text-xs text-slate-500"><?php echo e($tenant['config_completeness']); ?>% complete</p>
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    <?php echo e($tenant['last_audit_at'] ? \Illuminate\Support\Carbon::parse($tenant['last_audit_at'])->diffForHumans() : 'No audit trail yet'); ?>

                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-3 text-sm font-semibold">
                                        <a href="<?php echo e($tenant['route']); ?>" class="text-blue-600 hover:text-blue-800">Inspect</a>
                                        <?php if($tenant['status'] === 'active'): ?>
                                            <form action="<?php echo e($tenant['suspend_route']); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="text-amber-600 hover:text-amber-800">Suspend</button>
                                            </form>
                                        <?php elseif($tenant['status'] !== 'archived'): ?>
                                            <form action="<?php echo e($tenant['activate_route']); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="text-emerald-600 hover:text-emerald-800">Activate</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Side Cards -->
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Critical Alerts</h2>
                    <p class="mt-1 text-sm text-slate-500">Immediate issues surfaced before deeper inspection or configuration work.</p>
                </div>
                <div class="space-y-3 p-5">
                    <?php $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($alert['route']); ?>" class="flex items-start justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 transition hover:border-slate-300 hover:bg-white">
                            <div>
                                <p class="text-sm font-semibold text-slate-900"><?php echo e($alert['label']); ?></p>
                                <p class="mt-1 text-xs leading-5 text-slate-500"><?php echo e($alert['detail']); ?></p>
                            </div>
                            <span class="inline-flex min-w-[2.5rem] items-center justify-center rounded-full px-3 py-1 text-xs font-bold <?php echo e($alert['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-700' : ($alert['tone'] === 'amber' ? 'bg-amber-100 text-amber-700' : ($alert['tone'] === 'rose' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700'))); ?>"><?php echo e($alert['value']); ?></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Role Distribution</h2>
                    <p class="mt-1 text-sm text-slate-500">Privilege concentration and access mix across the platform.</p>
                </div>
                <div class="space-y-4 p-5">
                    <?php $__currentLoopData = $roleDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-slate-700"><?php echo e($role['label']); ?></span>
                                <span class="font-semibold text-slate-900"><?php echo e($role['value']); ?></span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-2 rounded-full <?php echo e($role['tone'] === 'purple' ? 'bg-violet-500' : ($role['tone'] === 'emerald' ? 'bg-emerald-500' : ($role['tone'] === 'amber' ? 'bg-amber-500' : 'bg-blue-500'))); ?>" style="width: <?php echo e(max(8, min(100, $summary[1]['value'] > 0 ? ($role['value'] / max(1, collect($roleDistribution)->sum('value'))) * 100 : 0))); ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Charts -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900"><?php echo e($auditTrend['label']); ?></h2>
                    <p class="mt-1 text-sm text-slate-500"><?php echo e($auditTrend['window']); ?>-day trend line for platform changes and interventions.</p>
                </div>
                <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700"><?php echo e($auditTrend['total']); ?> total</span>
            </div>
            <div class="p-5">
                <svg viewBox="0 0 240 56" class="h-20 w-full" preserveAspectRatio="none">
                    <polyline fill="none" stroke="#cbd5e1" stroke-width="1" points="0,50 240,50"></polyline>
                    <polyline fill="none" stroke="#7c3aed" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="<?php echo e($auditTrend['points']); ?>"></polyline>
                </svg>
                <div class="mt-4 grid gap-2 text-center text-[11px] text-slate-500" style="grid-template-columns: repeat(<?php echo e(count($auditTrend['series'])); ?>, minmax(0, 1fr));">
                    <?php $__currentLoopData = $auditTrend['series']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <div class="font-semibold text-slate-900"><?php echo e($point['value']); ?></div>
                            <div class="mt-1"><?php echo e($point['label']); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900"><?php echo e($provisioningTrend['label']); ?></h2>
                    <p class="mt-1 text-sm text-slate-500"><?php echo e($provisioningTrend['window']); ?>-day line for new account and identity creation.</p>
                </div>
                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700"><?php echo e($provisioningTrend['total']); ?> total</span>
            </div>
            <div class="p-5">
                <svg viewBox="0 0 240 56" class="h-20 w-full" preserveAspectRatio="none">
                    <polyline fill="none" stroke="#cbd5e1" stroke-width="1" points="0,50 240,50"></polyline>
                    <polyline fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="<?php echo e($provisioningTrend['points']); ?>"></polyline>
                </svg>
                <div class="mt-4 grid gap-2 text-center text-[11px] text-slate-500" style="grid-template-columns: repeat(<?php echo e(count($provisioningTrend['series'])); ?>, minmax(0, 1fr));">
                    <?php $__currentLoopData = $provisioningTrend['series']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <div class="font-semibold text-slate-900"><?php echo e($point['value']); ?></div>
                            <div class="mt-1"><?php echo e($point['label']); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Security & Audit -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[0.92fr_1.08fr]">
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Security Watchlist</h2>
                        <p class="mt-1 text-sm text-slate-500">Privileged identities and inactive accounts requiring review.</p>
                    </div>
                    <a href="<?php echo e(route('super-admin.users')); ?>" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Open user admin</a>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $securityWatch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-start justify-between gap-4 px-5 py-4">
                            <div>
                                <p class="font-semibold text-slate-900"><?php echo e($user->name); ?></p>
                                <p class="mt-1 text-sm text-slate-500"><?php echo e($user->email); ?></p>
                                <p class="mt-2 text-xs uppercase tracking-[0.2em] text-slate-400"><?php echo e($user->university->name ?? 'No university'); ?></p>
                                <?php if($user->role === 'student' && $user->student): ?>
                                    <p class="mt-2 text-xs text-slate-500">Supervisor: <?php echo e($user->student->supervisor?->user?->name ?? 'Unassigned'); ?></p>
                                <?php elseif($user->role === 'supervisor' && $user->supervisor): ?>
                                    <p class="mt-2 text-xs text-slate-500"><?php echo e($user->supervisor->students->count()); ?> mapped students</p>
                                <?php endif; ?>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'); ?>"><?php echo e($user->is_active ? 'Active' : 'Suspended'); ?></span>
                                <p class="mt-2 text-xs font-semibold uppercase tracking-[0.2em] <?php echo e($user->role === 'super_admin' ? 'text-violet-600' : ($user->role === 'admin' ? 'text-emerald-600' : 'text-slate-500')); ?>"><?php echo e(str_replace('_', ' ', $user->role)); ?></p>
                                <div class="mt-3 flex justify-end gap-3 text-sm font-semibold">
                                    <a href="<?php echo e(route('super-admin.users.edit', $user)); ?>" class="text-blue-600 hover:text-blue-800">Manage</a>
                                    <?php if(!$user->is_active && is_null($user->email_verified_at)): ?>
                                        <form action="<?php echo e(route('super-admin.users.resend-invite', $user)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-blue-600 hover:text-blue-800">Resend Invite</button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('super-admin.users.toggle-status', $user)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="<?php echo e($user->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800'); ?>"><?php echo e($user->is_active ? 'Suspend' : 'Activate'); ?></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-5 py-8 text-sm text-slate-500">No flagged accounts right now.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Runtime Snapshot</h2>
                    <p class="mt-1 text-sm text-slate-500">Critical platform services and execution defaults kept close to operator decisions.</p>
                </div>
                <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2">
                    <?php $__currentLoopData = $systemSnapshot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-slate-900"><?php echo e($item['label']); ?></p>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] <?php echo e($item['state'] === 'healthy' ? 'bg-emerald-100 text-emerald-700' : ($item['state'] === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700')); ?>"><?php echo e($item['state']); ?></span>
                            </div>
                            <p class="mt-3 text-sm text-slate-500"><?php echo e($item['value']); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900"><?php echo e($auditTrend['label']); ?></h2>
                            <p class="mt-1 text-sm text-slate-500"><?php echo e($auditTrend['window']); ?>-day trend line for platform changes and interventions.</p>
                        </div>
                        <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700"><?php echo e($auditTrend['total']); ?> total</span>
                    </div>
                    <div class="p-5">
                        <svg viewBox="0 0 240 56" class="h-20 w-full" preserveAspectRatio="none">
                            <polyline fill="none" stroke="#cbd5e1" stroke-width="1" points="0,50 240,50"></polyline>
                            <polyline fill="none" stroke="#7c3aed" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="<?php echo e($auditTrend['points']); ?>"></polyline>
                        </svg>
                        <div class="mt-4 grid gap-2 text-center text-[11px] text-slate-500" style="grid-template-columns: repeat(<?php echo e(count($auditTrend['series'])); ?>, minmax(0, 1fr));">
                            <?php $__currentLoopData = $auditTrend['series']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <div class="font-semibold text-slate-900"><?php echo e($point['value']); ?></div>
                                    <div class="mt-1"><?php echo e($point['label']); ?></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900"><?php echo e($provisioningTrend['label']); ?></h2>
                            <p class="mt-1 text-sm text-slate-500"><?php echo e($provisioningTrend['window']); ?>-day line for new account and identity creation.</p>
                        </div>
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700"><?php echo e($provisioningTrend['total']); ?> total</span>
                    </div>
                    <div class="p-5">
                        <svg viewBox="0 0 240 56" class="h-20 w-full" preserveAspectRatio="none">
                            <polyline fill="none" stroke="#cbd5e1" stroke-width="1" points="0,50 240,50"></polyline>
                            <polyline fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="<?php echo e($provisioningTrend['points']); ?>"></polyline>
                        </svg>
                        <div class="mt-4 grid gap-2 text-center text-[11px] text-slate-500" style="grid-template-columns: repeat(<?php echo e(count($provisioningTrend['series'])); ?>, minmax(0, 1fr));">
                            <?php $__currentLoopData = $provisioningTrend['series']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <div class="font-semibold text-slate-900"><?php echo e($point['value']); ?></div>
                                    <div class="mt-1"><?php echo e($point['label']); ?></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Recent Audit Activity</h2>
                        <p class="mt-1 text-sm text-slate-500">What changed most recently across tenants and platform settings.</p>
                    </div>
                    <a href="<?php echo e(route('super-admin.audit-logs')); ?>" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Audit explorer</a>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $recentAuditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-start gap-4 px-5 py-4">
                            <span class="mt-1 inline-flex h-10 w-10 items-center justify-center rounded-2xl <?php echo e($log->action === 'created' ? 'bg-emerald-100 text-emerald-700' : ($log->action === 'deleted' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700')); ?>">
                                <i class="fa-solid <?php echo e($log->action === 'created' ? 'fa-plus' : ($log->action === 'deleted' ? 'fa-trash' : 'fa-pen')); ?>"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-slate-900"><?php echo e(ucfirst($log->action)); ?> <?php echo e($log->model_type); ?></p>
                                <p class="mt-1 text-sm text-slate-500"><?php echo e($log->user->name ?? 'System'); ?> · <?php echo e($log->university->name ?? 'Platform context'); ?></p>
                                <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400"><?php echo e($log->created_at?->diffForHumans()); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-5 py-8 text-sm text-slate-500">No audit activity recorded yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Provisioning Feed</h2>
                        <p class="mt-1 text-sm text-slate-500">Recently created or updated operator-facing accounts.</p>
                    </div>
                    <a href="<?php echo e(route('super-admin.users.create')); ?>" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Create account</a>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $recentProvisioning; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-start justify-between gap-4 px-5 py-4">
                            <div>
                                <p class="font-semibold text-slate-900"><?php echo e($user->name); ?></p>
                                <p class="mt-1 text-sm text-slate-500"><?php echo e($user->email); ?></p>
                                <p class="mt-2 text-xs uppercase tracking-[0.2em] text-slate-400"><?php echo e($user->role); ?> · <?php echo e($user->university->name ?? 'No university'); ?></p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'); ?>"><?php echo e($user->is_active ? 'Active' : 'Inactive'); ?></span>
                                <p class="mt-2 text-xs text-slate-400"><?php echo e($user->created_at?->diffForHumans()); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-5 py-8 text-sm text-slate-500">No recent provisioning activity.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Tools -->
    <div class="mt-8 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-lg font-bold text-slate-900">Support Tools</h2>
            <p class="mt-1 text-sm text-slate-500">Secondary utilities for investigations, recovery, and direct jumps into specialist workflows.</p>
        </div>
        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 xl:grid-cols-4">
            <?php $__currentLoopData = $supportTools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($tool['route']); ?>" class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:border-slate-300 hover:bg-white">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white">
                        <i class="fa-solid <?php echo e($tool['icon']); ?>"></i>
                    </span>
                    <h3 class="mt-4 text-base font-bold text-slate-900 group-hover:text-blue-700"><?php echo e($tool['title']); ?></h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500"><?php echo e($tool['description']); ?></p>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/super-admin/dashboard.blade.php ENDPATH**/ ?>