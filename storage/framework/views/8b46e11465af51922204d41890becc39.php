<?php $__env->startSection('title', 'Analytics & Insights | Research Supervision Portal | LASU'); ?>

<?php $__env->startSection('content'); ?>
<!-- MAIN LAYOUT CONTAINER -->
<div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col md:flex-row gap-4 sm:gap-6">

  <!-- SIDE NAVIGATION BAR -->
  <?php echo $__env->make('components.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <!-- MAIN CONTENT VIEWPORTS CONTAINER -->
  <main class="flex-1 min-w-0 space-y-6">

    <!-- ================= TAB: ANALYTICS & INSIGHTS (supervisor-only) ================= -->
    <section id="tab-supervisor-analytics" class="tab-content hidden fade-in space-y-6">

      <!-- Header + refresh -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <div>
          <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
            <i class="fa-solid fa-chart-pie text-purple-500"></i> Analytics &amp; Insights
          </h3>
          <p class="text-xs text-slate-500 dark:text-slate-400">A full read of the supervision process: progress, activity, bottlenecks, and risk flags.</p>
        </div>
        <div class="flex items-center gap-3">
          <span id="analytics-generated-at" class="text-[11px] text-slate-400 dark:text-slate-500"></span>
          <button onclick="loadAnalytics(true)" class="p-2 text-xs bg-slate-100 dark:bg-slate-700 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition" title="Recompute analytics">
            <i id="analytics-refresh-icon" class="fa-solid fa-rotate"></i>
          </button>
        </div>
      </div>

      <!-- Features announcement (supervisor-only): copy text, open Chat room, or post via webhook -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <h4 class="font-semibold text-slate-900 dark:text-white text-sm flex items-center gap-2">
              <i class="fa-solid fa-bullhorn text-amber-500"></i> Features Announcement
            </h4>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">Copy the update note, open the Chat room, or post it automatically (webhook required).</p>
          </div>
          <div class="flex flex-wrap items-center gap-2">
            <button onclick="copyAnnouncementText()" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
              <i class="fa-solid fa-copy"></i> Copy Announcement
            </button>
            <a href="https://chat.google.com/room/AAQABfAyEgo?cls=7" target="_blank" rel="noopener" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
              <i class="fa-solid fa-up-right-from-square"></i> Open Chat Room
            </a>
            <button onclick="postAnnouncementToChat()" class="px-3 py-2 bg-academic-700 hover:bg-academic-800 text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
              <i id="announcement-post-icon" class="fa-solid fa-paper-plane"></i> Post to Chat
            </button>
          </div>
        </div>
        <!-- Hidden textarea holding the announcement text (source for copy + preview) -->
        <textarea id="announcement-text" class="sr-only" aria-hidden="true" readonly></textarea>
      </div>

      <!-- Loading / empty state -->
      <div id="analytics-loading" class="hidden bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm text-center text-slate-500 dark:text-slate-400 text-sm">
        <i class="fa-solid fa-spinner fa-spin mr-2"></i> Computing analytics...
      </div>
      <div id="analytics-error" class="hidden bg-rose-50 dark:bg-rose-900/20 rounded-2xl p-4 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-sm"></div>

      <!-- Content wrapper -->
      <div id="analytics-content" class="space-y-6">

        <!-- KPI grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
          <div class="bg-white dark:bg-slate-800 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between gap-1">
              <span class="text-[11px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 leading-tight">Total Students</span>
              <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs sm:text-sm"><i class="fa-solid fa-users"></i></div>
            </div>
            <p id="an-total-students" class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1.5 sm:mt-2">5</p>
            <p class="text-[10px] sm:text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-medium"><span id="an-active-students">4</span> active</p>
          </div>
          <div class="bg-white dark:bg-slate-800 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between gap-1">
              <span class="text-[11px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 leading-tight">Avg Progress</span>
              <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs sm:text-sm"><i class="fa-solid fa-gauge-high"></i></div>
            </div>
            <p id="an-avg-progress" class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1.5 sm:mt-2">45%</p>
            <p class="text-[10px] sm:text-xs text-purple-600 dark:text-purple-400 mt-1 font-medium">Cohort mean</p>
          </div>
          <div class="bg-white dark:bg-slate-800 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between gap-1">
              <span class="text-[11px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 leading-tight">Meeting Logs</span>
              <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs sm:text-sm"><i class="fa-solid fa-comments"></i></div>
            </div>
            <p id="an-total-logs" class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1.5 sm:mt-2">6</p>
            <p class="text-[10px] sm:text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-medium"><span id="an-logs-month">2</span> this month</p>
          </div>
           <div class="bg-white dark:bg-slate-800 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
             <div class="flex items-center justify-between gap-1">
               <span class="text-[11px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 leading-tight">Awaiting Action</span>
               <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs sm:text-sm"><i class="fa-solid fa-bell"></i></div>
             </div>
             <p id="an-awaiting" class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1.5 sm:mt-2">2</p>
             <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1 font-medium"><span id="an-pending-proposals">2</span> topics · <span id="an-pending-logs">0</span> logs</p>
           </div>
           <div class="bg-white dark:bg-slate-800 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
             <div class="flex items-center justify-between gap-1">
               <span class="text-[11px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 leading-tight">Resource Points</span>
               <div class="w-6 h-6 sm:w-8 sm:h-8 shrink-0 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs sm:text-sm"><i class="fa-solid fa-medal"></i></div>
             </div>
             <p id="an-resource-points" class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1.5 sm:mt-2">30</p>
             <p class="text-[10px] sm:text-xs text-amber-600 dark:text-amber-400 mt-1 font-medium"><span id="an-pending-resources">0</span> awaiting approval</p>
           </div>
         </div>

        <!-- Insights panel -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
          <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2 mb-3">
            <i class="fa-solid fa-lightbulb text-amber-500"></i> Insights
          </h3>
          <div id="an-insights" class="space-y-2">
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-3">
              <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-check text-emerald-600"></i> 3 students advanced this week
              </p>
            </div>
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3">
              <p class="text-xs font-semibold text-amber-700 dark:text-amber-300 flex items-center gap-1.5">
                <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> 2 proposals pending approval (1 week old)
              </p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Stage funnel -->
          <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2 mb-3">
              <i class="fa-solid fa-layer-group text-academic-600 dark:text-academic-400"></i> Stage Funnel
            </h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-3">Students currently at each of the 11 research stages.</p>
            <div id="an-stage-funnel" class="space-y-2">
              <?php for($i=1; $i<=8; $i++): ?>
                <div>
                  <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-600 dark:text-slate-400">Stage <?php echo e($i); ?></span>
                    <span class="font-bold text-slate-900 dark:text-white"><?php echo e(rand(1, 3)); ?> students</span>
                  </div>
                  <div class="w-full bg-slate-100 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                    <div class="bg-academic-600 dark:bg-academic-400 h-full rounded-full" style="width: <?php echo e(rand(10, 80)); ?>%"></div>
                  </div>
                </div>
              <?php endfor; ?>
            </div>
          </div>

          <!-- Meetings per month -->
          <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2 mb-3">
              <i class="fa-solid fa-chart-column text-emerald-500"></i> Meetings (last 6 months)
            </h3>
            <div id="an-meetings-month" class="flex items-end gap-2 h-40 mt-4">
              <?php
                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                $counts = [3, 5, 4, 6, 2, 4];
              ?>
              <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex-1 flex flex-col items-center gap-1 group cursor-pointer">
                  <div class="relative w-full flex items-end justify-center h-32 bg-slate-50 dark:bg-slate-700/30 rounded-lg">
                    <div class="w-6 bg-academic-600 dark:bg-academic-400 rounded-t-md transition-all duration-300 hover:bg-academic-700 dark:hover:bg-academic-500" style="height: <?php echo e($counts[$index] * 15); ?>%"></div>
                    <span class="absolute top-1 text-[10px] font-bold text-academic-700 dark:text-academic-300"><?php echo e($counts[$index]); ?></span>
                  </div>
                  <span class="text-[10px] font-medium text-slate-500 dark:text-slate-400"><?php echo e($month); ?></span>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
              <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-2">Logs by status</p>
              <div id="an-logs-status" class="flex flex-wrap gap-2">
                <div class="px-2 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 text-xs font-semibold">Approved: 4</div>
                <div class="px-2 py-1 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 text-xs font-semibold">Pending: 2</div>
                <div class="px-2 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 text-xs font-semibold">Under Review: 1</div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Bottlenecks -->
          <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2 mb-3">
              <i class="fa-solid fa-hourglass-half text-rose-500"></i> Stage Bottlenecks
            </h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-3">Average days students spend before advancing (from stage history).</p>
            <div id="an-bottlenecks" class="space-y-2">
              <?php for($i=1; $i<=5; $i++): ?>
                <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                  <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Stage <?php echo e($i); ?></span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400">Chapter <?php echo e($i); ?></span>
                  </div>
                  <div class="flex items-center gap-2">
                    <div class="w-24 bg-slate-200 dark:bg-slate-600 h-1.5 rounded-full overflow-hidden">
                      <div class="bg-rose-500 h-full rounded-full" style="width: <?php echo e($i * 15); ?>%"></div>
                    </div>
                    <span class="text-xs font-medium text-slate-700 dark:text-slate-300"><?php echo e($i * 3); ?> days avg</span>
                  </div>
                </div>
              <?php endfor; ?>
            </div>
          </div>

          <!-- Meeting modes -->
          <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2 mb-3">
              <i class="fa-solid fa-video text-indigo-500"></i> Meeting Modes
            </h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-3">How supervision sessions are conducted.</p>
            <div id="an-modes" class="space-y-2">
              <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                <div class="flex items-center gap-2">
                  <i class="fa-solid fa-users text-indigo-500"></i>
                  <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Physical Meeting</span>
                </div>
                <span class="text-xs font-bold text-slate-900 dark:text-white">3 (50%)</span>
              </div>
              <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                <div class="flex items-center gap-2">
                  <i class="fa-solid fa-video text-purple-500"></i>
                  <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Google Meet</span>
                </div>
                <span class="text-xs font-bold text-slate-900 dark:text-white">2 (33%)</span>
              </div>
              <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                <div class="flex items-center gap-2">
                  <i class="fa-solid fa-phone text-emerald-500"></i>
                  <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Phone/WhatsApp</span>
                </div>
                <span class="text-xs font-bold text-slate-900 dark:text-white">1 (17%)</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Risk flags -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
          <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2 mb-4">
            <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> Risk &amp; Attention
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-bell-slash text-amber-500"></i> Quiet students</p>
              <div id="an-risk-quiet" class="space-y-1.5 text-xs">
                <div class="flex items-center gap-2 p-2 rounded-lg bg-amber-50 dark:bg-amber-900/20">
                  <div class="w-6 h-6 rounded-full bg-academic-800 text-amber-400 text-xs font-bold flex items-center justify-center">ST</div>
                  <span class="text-slate-700 dark:text-slate-300">Student Name</span>
                  <span class="ml-auto text-[10px] text-amber-600 dark:text-amber-400">30 days since last log</span>
                </div>
              </div>
            </div>
            <div>
              <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-person-digging text-rose-500"></i> Stalled at stage</p>
              <div id="an-risk-stalled" class="space-y-1.5 text-xs">
                <div class="flex items-center gap-2 p-2 rounded-lg bg-rose-50 dark:bg-rose-900/20">
                  <div class="w-6 h-6 rounded-full bg-academic-800 text-amber-400 text-xs font-bold flex items-center justify-center">ST</div>
                  <span class="text-slate-700 dark:text-slate-300">Student Name</span>
                  <span class="ml-auto text-[10px] text-rose-600 dark:text-rose-400">Stage 3 (60 days)</span>
                </div>
              </div>
            </div>
            <div>
              <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-clock text-indigo-500"></i> Oldest pending proposals</p>
              <div id="an-risk-proposals" class="space-y-1.5 text-xs">
                <div class="flex items-center gap-2 p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/20">
                  <i class="fa-solid fa-file-alt text-indigo-500 text-xs"></i>
                  <span class="text-slate-700 dark:text-slate-300">Sample Topic</span>
                  <span class="ml-auto text-[10px] text-indigo-600 dark:text-indigo-400">7 days old</span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

  </main>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/supervisor/analytics.blade.php ENDPATH**/ ?>