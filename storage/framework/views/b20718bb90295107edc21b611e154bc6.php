
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['type' => 'student']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['type' => 'student']); ?>
<?php foreach (array_filter((['type' => 'student']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<?php ($type = $type ?? 'student'); ?>

<div class="relative">
  <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5 text-xs flex items-center gap-1.5">
    <i class="fa-solid fa-building-columns text-academic-500"></i> University
  </label>
  <div class="relative">
    <input
      id="login-university-code-<?php echo e($type); ?>"
      type="text"
      onclick="toggleUniversityDropdown('<?php echo e($type); ?>')"
      readonly
      placeholder="Select your university"
      class="w-full pl-3 pr-9 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 focus:border-academic-600 outline-none transition dark:text-white text-xs font-semibold cursor-pointer uppercase">
    <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 pointer-events-none text-[10px]"></i>
  </div>

  <!-- Dropdown -->
  <div id="university-dropdown-<?php echo e($type); ?>" class="hidden absolute z-20 mt-2 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">
    <div class="p-2 border-b border-slate-100 dark:border-slate-700">
      <div class="relative">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs"></i>
        <input
          id="university-search-<?php echo e($type); ?>"
          type="text"
          oninput="filterUniversities('<?php echo e($type); ?>')"
          placeholder="Search by code or name"
          class="w-full pl-8 pr-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-academic-600 outline-none transition dark:text-white text-xs">
      </div>
    </div>
    <div id="university-list-<?php echo e($type); ?>" class="max-h-52 overflow-y-auto p-1.5 space-y-0.5">
      <div class="p-3 text-xs text-slate-500 dark:text-slate-400 text-center">Loading universities...</div>
    </div>
  </div>
</div>
<?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/partials/auth/university-selector.blade.php ENDPATH**/ ?>