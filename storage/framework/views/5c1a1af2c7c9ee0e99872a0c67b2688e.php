<?php $__env->startSection('title', 'Topic Approvals | Research Supervision Portal | LASU'); ?>

<?php $__env->startSection('content'); ?>
<!-- MAIN LAYOUT CONTAINER -->
<div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col md:flex-row gap-4 sm:gap-6">

  <!-- SIDE NAVIGATION BAR -->
  <?php echo $__env->make('components.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <!-- MAIN CONTENT VIEWPORTS CONTAINER -->
  <main class="flex-1 min-w-0 space-y-6">

    <!-- ================= TAB 3: TOPIC PROPOSALS REVIEW ================= -->
    <section id="tab-supervisor-proposals" class="tab-content hidden fade-in space-y-4">
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white text-base">Pending Research Proposals</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Review student topic submissions, approve titles, or request revisions.</p>
          </div>
          <span class="text-xs font-mono px-2.5 py-1 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg border border-amber-200 dark:border-amber-800">
            Supervisor Action Required
          </span>
        </div>

        <!-- Sub-tabs: Pending Approval vs Conditionally Approved -->
        <div class="mt-4 flex p-1 gap-1 bg-slate-100 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-700 w-full sm:w-auto">
          <button onclick="switchProposalSubTab('pending')" id="proposal-subtab-pending" class="proposal-subtab flex-1 sm:flex-none px-3 py-1.5 text-xs font-semibold rounded-lg transition flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-hourglass-half"></i> Pending Approval
            <span id="badge-pending-proposals-sub" class="ml-1 px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200">0</span>
          </button>
          <button onclick="switchProposalSubTab('conditional')" id="proposal-subtab-conditional" class="proposal-subtab flex-1 sm:flex-none px-3 py-1.5 text-xs font-semibold rounded-lg transition flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-clipboard-check"></i> Conditionally Approved
            <span id="badge-conditional-proposals-sub" class="ml-1 px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200">0</span>
          </button>
        </div>
      </div>

      <!-- Pending Approval list -->
      <div id="proposals-pending-container" class="space-y-4 proposal-sub-panel">
        <!-- Dynamically rendered pending proposal cards -->
        <?php for($i=1; $i<=2; $i++): ?>
          <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <span class="text-xs font-mono px-2 py-0.5 bg-academic-50 dark:bg-academic-900/30 text-academic-700 dark:text-academic-300 rounded">
                    Student: Demo Student <?php echo e($i); ?>

                  </span>
                  <span class="text-xs font-mono px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded">
                    Matric: DEMO-00<?php echo e($i); ?>

                  </span>
                  <span class="text-[10px] font-bold px-2 py-0.5 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 rounded-full">
                    PENDING
                  </span>
                </div>
                <h4 class="font-bold text-slate-900 dark:text-white text-lg mb-2">Sample Research Topic Title <?php echo e($i); ?></h4>
                <div class="space-y-2">
                  <div>
                    <span class="text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">Location Focus:</span>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">Lagos State Main Campus</p>
                  </div>
                  <div>
                    <span class="text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">Abstract:</span>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 line-clamp-2">
                      This research examines the relationship between media consumption and public opinion formation in Lagos State, with particular focus on broadcast journalism and its impact on civic engagement among undergraduates.
                    </p>
                  </div>
                  <div class="pt-2">
                    <span class="text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">Submitted:</span>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">2 days ago</p>
                  </div>
                </div>
              </div>

              <div class="flex flex-col gap-2 sm:items-end">
                <button onclick="openConditionalApproval(<?php echo e($i); ?>)" class="px-3 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-xs font-bold transition flex items-center gap-2">
                  <i class="fa-solid fa-clipboard-check"></i> Approve with Conditions
                </button>
                <div class="flex gap-2">
                  <button onclick="approveProposal(<?php echo e($i); ?>)" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold transition">
                    <i class="fa-solid fa-check"></i> Approve
                  </button>
                  <button onclick="rejectProposal(<?php echo e($i); ?>)" class="px-3 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold transition">
                    <i class="fa-solid fa-xmark"></i> Reject
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php endfor; ?>
      </div>

      <!-- Conditionally Approved list -->
      <div id="proposals-conditional-container" class="space-y-4 proposal-sub-panel hidden">
        <!-- Dynamically rendered conditionally-approved proposal cards -->
      </div>
    </section>

  </main>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/supervisor/proposals.blade.php ENDPATH**/ ?>