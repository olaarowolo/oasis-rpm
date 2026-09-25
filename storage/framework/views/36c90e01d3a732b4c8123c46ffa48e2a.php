<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'items' => [],
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'items' => [],
]); ?>
<?php foreach (array_filter(([
    'items' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<nav class="hidden lg:flex flex-1 min-w-0 justify-center" aria-label="Header navigation">
    <div class="flex items-center gap-2 overflow-x-auto rounded-2xl border border-slate-200/80 bg-white/85 px-2 py-2 shadow-sm dark:border-slate-700 dark:bg-slate-800/85">
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $itemClasses = $item['active']
                    ? 'bg-academic-50 text-academic-700 shadow-sm ring-1 ring-academic-100 dark:bg-academic-900/40 dark:text-academic-100 dark:ring-academic-700/40'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700/60 dark:hover:text-white';
            ?>

            <a href="<?php echo e($item['href']); ?>" class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl px-3 py-2 text-sm font-medium transition <?php echo e($itemClasses); ?>">
                <i class="fa-solid <?php echo e($item['icon']); ?> text-xs <?php echo e($item['active'] ? 'text-academic-600 dark:text-academic-300' : 'text-slate-400 dark:text-slate-500'); ?>"></i>
                <span><?php echo e($item['label']); ?></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</nav><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/components/app-header-navigation.blade.php ENDPATH**/ ?>