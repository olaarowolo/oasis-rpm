<!-- Key Metrics -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
  <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Supervised Cohort</p>
        <p class="mt-2 text-3xl font-bold text-academic-900 dark:text-white" data-metric="total_students"><?php echo e($totalStudents ?? 0); ?></p>
      </div>
      <div class="w-12 h-12 rounded-xl bg-academic-100 dark:bg-academic-900/30 text-academic-600 dark:text-academic-400 flex items-center justify-center">
        <i class="fa-solid fa-users text-xl"></i>
      </div>
    </div>
    <p class="mt-2 text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
      <i class="fa-solid fa-circle-check"></i> Assigned students
    </p>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Pending Topics</p>
        <p class="mt-2 text-3xl font-bold text-amber-900 dark:text-amber-400" data-metric="pending_proposals">2</p>
      </div>
      <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
        <i class="fa-solid fa-file-pen text-xl"></i>
      </div>
    </div>
    <p class="mt-2 text-xs text-amber-600 dark:text-amber-400 font-medium">Requires your approval</p>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Approved Topics</p>
        <p class="mt-2 text-3xl font-bold text-indigo-900 dark:text-indigo-400" data-metric="completed_proposals">0</p>
      </div>
      <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
        <i class="fa-solid fa-file-circle-check text-xl"></i>
      </div>
    </div>
    <p class="mt-2 text-xs text-indigo-600 dark:text-indigo-400 font-medium">Topics approved</p>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Supervision Logs</p>
        <p class="mt-2 text-3xl font-bold text-emerald-900 dark:text-emerald-400" data-metric="completed_meetings">6</p>
      </div>
      <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
        <i class="fa-solid fa-comments text-xl"></i>
      </div>
    </div>
    <p class="mt-2 text-xs text-emerald-600 dark:text-emerald-400 font-medium"><span class="text-emerald-700 dark:text-emerald-300" data-metric="monthly_meetings">6</span> this month</p>
  </div>
</div><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/partials/dashboards/supervisor-metrics.blade.php ENDPATH**/ ?>