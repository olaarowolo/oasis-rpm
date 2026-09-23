<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
  <div class="p-5 border-b border-slate-200 dark:border-slate-700">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-bold text-base text-slate-900 dark:text-white">Supervised Student Roster</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Synced in real-time with Google Sheets</p>
      </div>
      <div class="flex items-center gap-2">
        <input type="text" id="student-search" placeholder="Search students..." class="px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:ring-2 focus:ring-academic-600">
        <button onclick="loadStudentRoster()" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition" title="Refresh student list">
          <i class="fa-solid fa-rotate"></i>
        </button>
      </div>
    </div>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
      <thead class="bg-slate-50 dark:bg-slate-700/50 uppercase text-[10px] font-bold text-slate-500 dark:text-slate-400">
        <tr>
          <th class="px-4 py-3">Student</th>
          <th class="px-4 py-3">Matric</th>
          <th class="px-4 py-3">Current Stage</th>
          <th class="px-4 py-3">Progress</th>
          <th class="px-4 py-3">Last Session</th>
          <th class="px-4 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody id="student-roster-table" class="divide-y divide-slate-100 dark:divide-slate-700">
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
          <td class="px-4 py-3">
            <div class="font-medium text-slate-900 dark:text-white">John Doe</div>
            <div class="text-xs text-slate-500">Undergraduate</div>
          </td>
          <td class="px-4 py-3 font-mono text-slate-700 dark:text-slate-300">2024/123456</td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 rounded bg-academic-100 dark:bg-academic-900/30 text-academic-700 dark:text-academic-300 text-[10px] font-semibold">Topic Ideation</span>
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-2">
              <div class="w-16 h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                <div class="h-full rounded-full bg-academic-600 w-25%"></div>
              </div>
              <span class="text-xs">25%</span>
            </div>
          </td>
          <td class="px-4 py-3 text-slate-500">2024-09-15</td>
          <td class="px-4 py-3 text-right">
            <button class="text-academic-600 hover:text-academic-700 mr-2">
              <i class="fa-solid fa-eye"></i>
            </button>
            <button class="text-emerald-600 hover:text-emerald-700">
              <i class="fa-solid fa-comments"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
