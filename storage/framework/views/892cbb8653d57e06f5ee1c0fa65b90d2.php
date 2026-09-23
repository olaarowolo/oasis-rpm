<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'label',
    'value',
    'note' => null,
    'tone' => 'default',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'label',
    'value',
    'note' => null,
    'tone' => 'default',
]); ?>
<?php foreach (array_filter(([
    'label',
    'value',
    'note' => null,
    'tone' => 'default',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
  $toneClass = match ($tone) {
      'success' => 'text-emerald-700 dark:text-emerald-400',
      'warning' => 'text-amber-700 dark:text-amber-400',
      'danger' => 'text-rose-700 dark:text-rose-400',
      default => 'text-slate-900 dark:text-white',
  };
?>

<article <?php echo e($attributes->merge(['class' => 'rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm'])); ?>>
  <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400"><?php echo e($label); ?></p>
  <p class="mt-2 text-3xl font-black <?php echo e($toneClass); ?>"><?php echo e($value); ?></p>
  <?php if($note): ?>
    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"><?php echo e($note); ?></p>
  <?php endif; ?>
</article>
<?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/components/portal/stat-card.blade.php ENDPATH**/ ?>