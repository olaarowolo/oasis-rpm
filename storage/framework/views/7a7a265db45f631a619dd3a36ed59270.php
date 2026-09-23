<!-- ================= MODAL: SUBMIT TOPIC PROPOSAL ================= -->
<div id="modal-submit-topic" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
  <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-4 fade-in">
    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
      <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
        <i class="fa-solid fa-file-export text-academic-600"></i> Submit Topic Proposal
      </h3>
      <button onclick="closeModal('modal-submit-topic')" class="text-slate-400 hover:text-slate-600 text-sm">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="form-submit-proposal" onsubmit="handleSubmitProposal(event)" class="space-y-3 text-xs">
      <div>
        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Student Name &amp; Matric</label>
        <input id="proposal-student-display" type="text" value="—" disabled class="w-full p-2.5 bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-500 font-semibold">
      </div>
      <div>
        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Proposed Research Topic Title</label>
        <input id="proposal-title" type="text" maxlength="255" placeholder="e.g. Broadcast News Credibility and Audience Perception in Lagos State" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
      </div>
      <div>
        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Study Context / Location Focus</label>
        <input id="proposal-location" type="text" maxlength="255" placeholder="e.g. Lagos State Main Campus / Ikeja Newsrooms" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
      </div>
      <div>
        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Brief Abstract / Problem Statement</label>
        <textarea id="proposal-abstract" rows="4" placeholder="Briefly state the research problem, objectives, and proposed methodology..." required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white"></textarea>
      </div>
      <div class="pt-2 flex justify-end gap-2">
        <button type="button" onclick="closeModal('modal-submit-topic')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold">Cancel</button>
        <button id="proposal-submit-btn" type="submit" class="px-4 py-2 bg-academic-700 hover:bg-academic-800 text-white rounded-xl font-semibold shadow-md flex items-center gap-2">
          <span>Submit to Supervisor</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ================= TOAST CONTAINER ================= -->
<div id="toast-container" class="fixed bottom-5 right-5 z-[60] space-y-2 pointer-events-none"></div>
<?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/partials/dashboards/student-modals.blade.php ENDPATH**/ ?>