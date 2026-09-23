<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'title',
    'subtitle' => null,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'title',
    'subtitle' => null,
]); ?>
<?php foreach (array_filter(([
    'title',
    'subtitle' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div <?php echo e($attributes->merge(['class' => 'space-y-1'])); ?>>
  <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white"><?php echo e($title); ?></h2>
  <?php if($subtitle): ?>
    <p class="text-sm text-slate-600 dark:text-slate-300"><?php echo e($subtitle); ?></p>
  <?php endif; ?>
</div>
<?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/components/portal/section-header.blade.php ENDPATH**/ ?>