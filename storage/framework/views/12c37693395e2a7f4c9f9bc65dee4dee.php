<div id="demo-request-modal" class="hidden fixed inset-0 z-[70] bg-slate-900/55 backdrop-blur-sm p-4 flex items-center justify-center" onclick="closeDemoRequestModalOnBackdrop(event)">
  <div class="w-full max-w-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl p-6 space-y-4">
    <div class="flex items-start justify-between gap-3">
      <div>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Request Demo Access</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Submit your details and we will share a guided portal demo setup.</p>
      </div>
      <button type="button" onclick="closeDemoRequestModal()" class="w-9 h-9 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition">
        <span class="sr-only">Close</span>
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="demo-request-form" onsubmit="submitDemoRequest(event)" class="space-y-3 text-xs">
      <div>
        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
        <input id="demo-name" type="text" placeholder="Enter your full name" autocomplete="name" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 dark:text-white">
      </div>
      <div>
        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Work Email</label>
        <input id="demo-email" type="email" placeholder="name@university.edu" autocomplete="email" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 dark:text-white">
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">University Code</label>
          <div class="relative">
            <input id="demo-tenant" type="text" placeholder="Search and select university..." autocomplete="off" readonly class="w-full p-2.5 pr-10 uppercase bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 dark:text-white cursor-pointer" onclick="toggleUniversityDropdown('demo')">
            <i class="fa-solid fa-magnifying-glass absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            <i class="fa-solid fa-chevron-down absolute right-10 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            <!-- University search dropdown -->
            <div id="university-dropdown-demo" class="hidden absolute !z-[9999] w-full max-h-80 overflow-y-auto bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl mt-1">
              <div class="p-2 border-b border-slate-100 dark:border-slate-700 sticky top-0 bg-white dark:bg-slate-800 z-10">
                <input type="text" id="university-search-demo" onkeyup="filterUniversities('demo')" placeholder="Search universities..." class="w-full p-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-academic-600 dark:text-white">
              </div>
              <div id="university-list-demo" class="p-1">
                <!-- University options will be populated here -->
              </div>
            </div>
          </div>
        </div>
        <div>
          <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Department</label>
          <input id="demo-department" type="text" placeholder="Journalism & Media Studies" autocomplete="organization-title" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 dark:text-white">
        </div>
      </div>
      <div>
        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">What Do You Want To Evaluate?</label>
        <textarea id="demo-notes" rows="3" placeholder="Tell us your cohort size, timeline, and supervision workflow goals." class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 dark:text-white"></textarea>
      </div>
      <p id="demo-request-error" class="hidden text-[11px] text-rose-600 dark:text-rose-400 font-medium"></p>
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-1">
        <button type="button" onclick="closeDemoRequestModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-sm font-semibold transition">Cancel</button>
        <button type="submit" class="px-4 py-2.5 bg-academic-700 hover:bg-academic-800 text-white rounded-xl text-sm font-semibold shadow-md transition flex items-center justify-center gap-2">
          <i class="fa-solid fa-paper-plane"></i> Send Request
        </button>
      </div>
    </form>
  </div>
</div><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/partials/auth/demo-request-modal.blade.php ENDPATH**/ ?>