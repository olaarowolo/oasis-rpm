<div id="admin-mfa-modal" class="hidden fixed inset-0 z-[70] bg-slate-900/55 backdrop-blur-sm p-4 flex items-center justify-center">
  <div class="w-full max-w-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl p-6 space-y-4">
    <div class="flex items-start justify-between gap-3">
      <div>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Admin verification</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Enter the 6-digit code sent to your email to finish signing in.</p>
      </div>
      <button type="button" onclick="closeAdminMfaModal()" class="w-9 h-9 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition">
        <span class="sr-only">Close</span>
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="admin-mfa-form" onsubmit="handleAdminMfaSubmit(event)" class="space-y-3 text-xs">
      <div>
        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Verification Code</label>
        <input id="admin-mfa-code" type="text" maxlength="6" inputmode="numeric" placeholder="123456" autocomplete="one-time-code" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 dark:text-white tracking-widest text-center text-lg">
      </div>
      <div id="admin-mfa-error" class="hidden text-[11px] text-rose-600 dark:text-rose-400 font-medium items-center gap-1.5 flex">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span id="admin-mfa-error-text"></span>
      </div>
      <div class="flex gap-2">
        <button type="button" onclick="closeAdminMfaModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-sm font-semibold transition">Cancel</button>
        <button type="submit" class="flex-1 px-4 py-2.5 bg-academic-700 hover:bg-academic-800 text-white rounded-xl text-sm font-semibold shadow-md transition">Verify and continue</button>
      </div>
    </form>
  </div>
</div>