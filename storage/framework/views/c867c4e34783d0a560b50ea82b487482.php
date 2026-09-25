<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'apiIndexUrl' => '/api/notifications',
    'markAllUrl' => '/api/notifications/read-all',
    'viewAllHref' => '#',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'apiIndexUrl' => '/api/notifications',
    'markAllUrl' => '/api/notifications/read-all',
    'viewAllHref' => '#',
]); ?>
<?php foreach (array_filter(([
    'apiIndexUrl' => '/api/notifications',
    'markAllUrl' => '/api/notifications/read-all',
    'viewAllHref' => '#',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div
    class="relative"
    data-app-header-menu
    data-notifications-root
    data-index-url="<?php echo e($apiIndexUrl); ?>"
    data-mark-all-url="<?php echo e($markAllUrl); ?>"
>
    <button
        type="button"
        class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white/90 text-slate-500 shadow-sm transition hover:border-slate-300 hover:text-slate-700 dark:border-slate-700 dark:bg-slate-800/90 dark:text-slate-300 dark:hover:border-slate-600 dark:hover:text-white"
        data-menu-toggle
        data-notifications-toggle
        aria-expanded="false"
        aria-haspopup="true"
        aria-label="Open notifications"
    >
        <i class="fa-solid fa-bell text-sm"></i>
        <span class="absolute right-1.5 top-1.5 hidden min-w-[1rem] rounded-full bg-rose-500 px-1 text-center text-[10px] font-bold leading-4 text-white" data-notifications-badge></span>
    </button>

    <div
        class="invisible absolute right-0 z-50 mt-2 w-[22rem] max-w-[calc(100vw-1.5rem)] scale-95 rounded-2xl border border-slate-200 bg-white p-2 opacity-0 shadow-xl transition-all duration-150 dark:border-slate-700 dark:bg-slate-800"
        data-menu-panel
        role="menu"
        aria-orientation="vertical"
    >
        <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-slate-900/40">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Notifications</p>
                <p class="text-xs text-slate-500 dark:text-slate-400" data-notifications-summary>Checking for updates...</p>
            </div>
            <button type="button" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-academic-700 transition hover:bg-academic-50 dark:text-academic-300 dark:hover:bg-academic-900/30" data-mark-all-read>
                Mark all read
            </button>
        </div>

        <div class="mt-2 max-h-80 space-y-2 overflow-y-auto pr-1" data-notifications-list>
            <div class="rounded-xl border border-dashed border-slate-200 px-3 py-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                Loading notifications...
            </div>
        </div>

        <div class="mt-2 border-t border-slate-200 pt-2 dark:border-slate-700">
            <a href="<?php echo e($viewAllHref); ?>" class="block rounded-xl px-3 py-2 text-center text-sm font-medium text-academic-700 transition hover:bg-academic-50 dark:text-academic-300 dark:hover:bg-academic-900/30">
                View all context
            </a>
        </div>
    </div>
</div><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/components/app-header-notifications.blade.php ENDPATH**/ ?>