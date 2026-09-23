<?php $__env->startSection('title', $mode === 'edit' ? 'Edit University' : 'Add University'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <?php if (isset($component)) { $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.breadcrumbs','data' => ['breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Universities', 'url' => route('super-admin.universities'), 'icon' => 'fa-building-columns'],
        ['label' => $mode === 'edit' ? 'Edit University' : 'Add University', 'url' => $mode === 'edit' ? route('super-admin.universities.edit', $university) : route('super-admin.universities.create'), 'icon' => 'fa-pen-ruler'],
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
        ['label' => $mode === 'edit' ? 'Edit University' : 'Add University', 'url' => $mode === 'edit' ? route('super-admin.universities.edit', $university) : route('super-admin.universities.create'), 'icon' => 'fa-pen-ruler'],
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
<div class="space-y-6 max-w-5xl">
    <div class="rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-violet-900 p-6 text-white shadow-2xl lg:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <a href="<?php echo e(route('super-admin.universities')); ?>" class="inline-flex items-center gap-2 text-sm font-medium text-violet-200 hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Universities
                </a>
                <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl"><?php echo e($mode === 'edit' ? 'Edit University' : 'Add University'); ?></h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base"><?php echo e($mode === 'edit' ? 'Update the tenant identity, contact routing, and branding metadata used across the platform.' : 'Create a new tenant with the identifiers and contact information needed for provisioning.'); ?></p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Brand Preview</p>
                <div class="mt-3 flex items-center gap-3">
                    <span class="inline-flex h-12 w-12 rounded-2xl border border-white/20 shadow-inner" style="background-color: <?php echo e(old('branding_color', $university->branding_color ?: '#3B82F6')); ?>"></span>
                    <div>
                        <p class="text-sm font-semibold text-white"><?php echo e(old('name', $university->name ?: 'New University')); ?></p>
                        <p class="text-xs text-slate-300"><?php echo e(old('branding_color', $university->branding_color ?: '#3B82F6')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(isset($errors) && $errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-300">
            <ul class="list-inside list-disc space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e($mode === 'edit' ? route('super-admin.universities.update', $university) : route('super-admin.universities.store')); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php if($mode === 'edit'): ?>
            <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Tenant Identity</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">The name and code used to provision and reference this tenant.</p>
            </div>
            <div class="grid grid-cols-1 gap-6 px-6 py-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">University Name</label>
                    <input type="text" name="name" value="<?php echo e(old('name', $university->name)); ?>" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">University Code</label>
                    <input type="text" name="code" value="<?php echo e(old('code', $university->code)); ?>" required maxlength="20" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm uppercase text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Contact Routing</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Platform operators use this information for account management and support escalations.</p>
            </div>
            <div class="grid grid-cols-1 gap-6 px-6 py-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $university->email)); ?>" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone', $university->phone)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Department</label>
                    <input type="text" name="department" value="<?php echo e(old('department', $university->department)); ?>" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Branding</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Set the primary color used for tenant-level branding accents.</p>
            </div>
            <div class="grid grid-cols-1 gap-6 px-6 py-6 lg:grid-cols-[1fr_240px] lg:items-center">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Branding Color</label>
                    <input type="text" name="branding_color" value="<?php echo e(old('branding_color', $university->branding_color ?: '#3B82F6')); ?>" required placeholder="#3B82F6" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Preview</p>
                    <div class="mt-4 rounded-2xl p-5 text-white shadow-sm" style="background: linear-gradient(135deg, <?php echo e(old('branding_color', $university->branding_color ?: '#3B82F6')); ?> 0%, #0f172a 100%);">
                        <p class="text-sm font-semibold"><?php echo e(old('name', $university->name ?: 'University Name')); ?></p>
                        <p class="mt-1 text-xs text-white/80">Tenant branding accent</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 font-semibold text-white hover:bg-violet-700 transition-colors">
                <i class="fa-solid fa-save"></i>
                <?php echo e($mode === 'edit' ? 'Save Changes' : 'Create University'); ?>

            </button>
            <a href="<?php echo e(route('super-admin.universities')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 font-semibold text-slate-700 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700/50">
                <i class="fa-solid fa-xmark"></i>
                Cancel
            </a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/super-admin/university-form.blade.php ENDPATH**/ ?>