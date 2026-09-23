<?php $__env->startSection('title', 'Student Portal | Research Supervision Portal | LASU'); ?>

<?php $__env->startSection('content'); ?>
<!-- AUTH RESTRICTION BANNER -->
<div id="auth-warning-banner" class="hidden bg-amber-500/10 border-b border-amber-500/20 px-4 py-2 text-xs text-amber-800 dark:text-amber-200">
  <div class="flex items-center gap-2 max-w-7xl mx-auto w-full justify-between">
    <div class="flex items-center gap-2">
      <i class="fa-solid fa-lock text-amber-600"></i>
      <span>Logged in as <strong id="banner-user-email" class="font-mono">student@lasu.edu.ng</strong> (Student View)</span>
    </div>
  </div>
</div>

<!-- MAIN LAYOUT CONTAINER -->
<div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col md:flex-row gap-4 sm:gap-6">

  <!-- SIDE NAVIGATION BAR -->
  <?php echo $__env->make('components.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <!-- MAIN CONTENT VIEWPORTS CONTAINER -->
  <main class="flex-1 min-w-0 space-y-6">

    <!-- ================= TAB 4: STUDENT PORTAL VIEWPORT ================= -->
    <section id="tab-student-dashboard" class="tab-content fade-in space-y-6">
      
      <!-- Student Info Header Card -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-4 gap-3">
          <div class="flex items-center gap-3">
            <div id="student-portal-avatar" class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg shadow-md">
              ST
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h2 id="student-portal-name" class="text-lg font-bold text-slate-900 dark:text-white">Student Name</h2>
                <span id="student-portal-matric" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">MATRIC: DEMO-001</span>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400">Supervisor: <strong class="text-slate-800 dark:text-slate-200">Dr. Olasunkanmi Arowolo (LASU)</strong></p>
            </div>
          </div>

          <!-- Student Action Buttons -->
          <div class="flex items-center gap-2">
            <button id="btn-submit-topic" onclick="openSubmitTopicModal()" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
              <i class="fa-solid fa-plus text-academic-600"></i>
              Submit Topic Proposal
            </button>
            <button onclick="openStudentMeetingModal()" class="px-3.5 py-2 bg-academic-700 hover:bg-academic-800 text-white rounded-xl text-xs font-semibold shadow-md transition flex items-center gap-1.5">
              <i class="fa-solid fa-clipboard-list"></i>
              Log Progress Session
            </button>
            <button onclick="refreshStudentData(false)" title="Refresh" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
              <i class="fa-solid fa-rotate"></i>
              Refresh
            </button>
          </div>
        </div>

        <!-- Active Research Status Box -->
        <div class="bg-gradient-to-r from-slate-900 to-academic-900 text-white rounded-xl p-5 space-y-3">
          <div class="flex items-center justify-between text-xs text-academic-200">
            <span class="uppercase tracking-wider font-semibold"><i class="fa-solid fa-bookmark text-amber-400 mr-1"></i> Research Topic</span>
            <span id="student-topic-status" class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-semibold text-[10px]">ACTIVE</span>
          </div>
          
          <h3 id="student-active-topic" class="text-base sm:text-lg font-bold leading-snug">
            "Topic pending approval"
          </h3>

          <p id="student-topic-approved" class="hidden text-[11px] text-emerald-300"><i class="fa-solid fa-circle-check mr-1"></i>Approved on <span id="student-topic-approved-date"></span></p>
          <p id="student-topic-conditions" class="hidden text-[11px] text-amber-300 mt-1"><i class="fa-solid fa-clipboard-check mr-1"></i>Conditionally approved — see supervisor conditions below</p>

          <div id="student-conditions-box" class="hidden mt-3 bg-amber-500/10 border border-amber-500/30 rounded-xl p-3">
            <h5 class="text-[10px] font-bold text-amber-400 uppercase tracking-wider mb-1">Supervisor Conditions</h5>
            <p id="student-conditions-text" class="text-xs text-amber-100 whitespace-pre-wrap"></p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs border-t border-slate-700/80">
            <div>
              <span class="text-slate-400 block text-[10px]">Current Milestone:</span>
              <span id="student-milestone" class="font-semibold text-amber-400">Chapter 1 (Introduction)</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[10px]">Supervisor Email:</span>
              <span class="font-mono text-[11px] text-amber-300">olasunkanmi.arowolo@lasu.edu.ng</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[10px]">Overall Progress:</span>
              <div class="flex items-center gap-2 mt-0.5">
                <div class="w-full bg-slate-700 h-2 rounded-full overflow-hidden">
                  <div id="student-progress-bar" class="bg-amber-400 h-full rounded-full" style="width: 0%"></div>
                </div>
                <span id="student-progress-pct" class="font-bold text-amber-400 text-xs">0%</span>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Supervision Chapter Roadmap -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider flex items-center gap-2">
          <i class="fa-solid fa-list-check text-academic-600"></i> Research Lifecycle Roadmap
        </h3>

        <div id="student-roadmap-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 text-xs">
          <!-- Dynamically rendered 11-stage roadmap -->
          <?php for($i=1; $i<=11; $i++): ?>
            <?php
              $currentStage = 3; // Demo: student at Chapter 1 (Stage 3)
              if ($i < $currentStage) {
                $stateClass = "bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-300";
                $icon = '<i class="fa-solid fa-circle-check text-emerald-600"></i>';
                $label = "Completed";
              } elseif ($i === $currentStage) {
                $stateClass = "bg-amber-50 dark:bg-amber-900/30 border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-300 ring-2 ring-amber-400/50";
                $icon = '<i class="fa-solid fa-spinner animate-spin text-amber-600"></i>';
                $label = "In Progress";
              } else {
                $stateClass = "bg-slate-50 dark:bg-slate-700/40 border-slate-200 dark:border-slate-700 text-slate-400";
                $icon = '<i class="fa-solid fa-lock text-slate-400"></i>';
                $label = "Locked";
              }
            ?>
            <div class="p-3 rounded-xl border flex flex-col <?php echo e($stateClass); ?>">
              <div class="flex justify-between items-center font-bold mb-1">
                <span>Stage <?php echo e($i); ?></span>
                <?php echo $icon; ?>

              </div>
              <p class="text-[11px] font-medium flex-1">Stage <?php echo e($i); ?> Content</p>
              <span class="text-[10px] font-medium block mt-2"><?php echo e($label); ?></span>
            </div>
          <?php endfor; ?>
        </div>
      </div>

    </section>

  </main>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/student/dashboard.blade.php ENDPATH**/ ?>