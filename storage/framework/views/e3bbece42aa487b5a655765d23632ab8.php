<?php $__env->startSection('title', 'Platform Configuration'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <?php if (isset($component)) { $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.breadcrumbs','data' => ['breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'System Config', 'url' => route('super-admin.config'), 'icon' => 'fa-gears'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('super-admin.breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'System Config', 'url' => route('super-admin.config'), 'icon' => 'fa-gears'],
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
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Platform Configuration</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage global platform settings and AI configuration.</p>
    </div>

    <!-- Flash Messages -->
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(isset($errors) && $errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 dark:bg-rose-900/20 px-4 py-3 text-sm text-rose-700 dark:text-rose-300">
            <ul class="list-inside list-disc space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('super-admin.config.update')); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- General Settings -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-gear text-violet-600 dark:text-violet-400"></i>
                    General Settings
                </h2>
            </div>
            <div class="px-6 py-6 space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">App Name</label>
                        <input type="text" name="app_name" value="<?php echo e(old('app_name', $settings['app_name'])); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Default University Code</label>
                        <input type="text" name="default_university_code" value="<?php echo e(old('default_university_code', $settings['default_university_code'])); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition-colors uppercase">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Support Email</label>
                        <input type="email" name="support_email" value="<?php echo e(old('support_email', $settings['support_email'])); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition-colors">
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Configuration -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-brain text-amber-500"></i>
                    AI Configuration
                </h2>
            </div>
            <div class="px-6 py-6 space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">AI Provider</label>
                        <input type="text" name="ai_provider" value="<?php echo e(old('ai_provider', $settings['ai_provider'])); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Default Model</label>
                        <input type="text" name="ai_default_model" value="<?php echo e(old('ai_default_model', $settings['ai_default_model'])); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Gemini API Key</label>
                        <input type="password" name="gemini_api_key" placeholder="<?php echo e($hasGeminiApiKey ? 'Stored. Leave blank to keep current key.' : 'Enter API key'); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition-colors">
                        <?php if($hasGeminiApiKey): ?>
                            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                <i class="fa-solid fa-shield-check text-emerald-500"></i>
                                A Gemini API key is already stored for the platform.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Actions -->
        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 font-semibold text-white hover:bg-violet-700 transition-colors">
                <i class="fa-solid fa-save"></i>
                Save Configuration
            </button>
            <a href="<?php echo e(route('super-admin.dashboard')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 px-5 py-2.5 font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                <i class="fa-solid fa-xmark"></i>
                Cancel
            </a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/super-admin/config.blade.php ENDPATH**/ ?>