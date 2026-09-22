<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Supervisor Hub | TheOAsis Research Supervision System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
          colors: {
            academic: {
              50: '#f0f4f8',
              100: '#d9e2ec',
              500: '#102a43',
              600: '#0b69a3',
              700: '#035388',
              800: '#003e6b',
              900: '#002744',
            },
            lasu: {
              gold: '#f59e0b',
              blue: '#002744',
            }
          }
        }
      }
    }
  </script>
  <style>
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.03); }
    ::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.4); border-radius: 4px; }
  </style>
</head>
<body class="h-full flex flex-col font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100">
  
  <!-- ================= HEADER ================= -->
  <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      
      <!-- Brand & Title -->
      <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-academic-900 via-academic-800 to-amber-600 flex items-center justify-center text-white shadow-md">
          <i class="fa-solid fa-newspaper text-lg"></i>
        </div>
        <div class="min-w-0">
          <h1 class="font-bold text-lg text-slate-900 dark:text-white leading-tight flex items-center gap-2">
            <span class="truncate">Research Supervision Portal</span>
            <span class="hidden xs:inline text-[10px] font-mono px-2 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 font-bold">LASU</span>
          </h1>
          <p class="text-[11px] text-slate-500 dark:text-slate-400">Supervisor Research Management</p>
        </div>
      </div>

      <!-- User Profile -->
      <div class="flex items-center gap-3">
        <!-- Role Badge -->
        <div class="px-3 py-1.5 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold flex items-center gap-2">
          <i class="fa-solid fa-user-shield"></i>
          <span>Supervisor Hub</span>
        </div>

        <!-- User Avatar -->
        <div class="flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-700">
          <div id="user-avatar" class="w-9 h-9 rounded-full bg-academic-800 text-amber-400 border border-amber-500/30 flex items-center justify-center text-sm font-bold shadow-sm">
            OA
          </div>
          <div class="text-left">
            <p class="text-xs font-semibold text-slate-800 dark:text-slate-100 leading-tight">Dr. O. Arowolo</p>
            <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">Authorized Supervisor</p>
          </div>
        </div>

        <!-- Logout Button -->
        <button onclick="logout()" class="p-2 rounded-lg text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition" title="Log out">
          <i class="fa-solid fa-right-from-bracket text-base"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- ================= MAIN CONTENT ================= -->
  <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-academic-900 to-academic-800 rounded-2xl p-6 text-white shadow-lg">
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold">Welcome, Supervisor</h2>
          <p class="text-slate-300 mt-1">Manage your supervised students, review proposals, log meetings, and track research progress.</p>
        </div>
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 rounded-full bg-emerald-500 text-white text-xs font-bold">Supervisor Portal</span>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-circle-check"></i> Online
          </span>
        </div>
      </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Supervised Cohort</p>
            <p class="mt-2 text-3xl font-bold text-academic-900 dark:text-white" data-metric="total_students">5</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-academic-100 dark:bg-academic-900/30 text-academic-600 dark:text-academic-400 flex items-center justify-center">
            <i class="fa-solid fa-users text-xl"></i>
          </div>
        </div>
        <p class="mt-2 text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
          <i class="fa-solid fa-circle-check"></i> LASU Undergrads
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
    </div>

    <!-- Students by Stage -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
          <i class="fa-solid fa-layer-group text-academic-600 dark:text-academic-400"></i>
          Students by Stage
        </h3>
        <span class="text-xs text-slate-500 dark:text-slate-400">Across all supervised students</span>
      </div>
      <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3">
        <div class="p-3 rounded-xl bg-academic-50 dark:bg-academic-900/20 border border-academic-200 dark:border-academic-800/30 stage-card" data-stage="1">
          <p class="text-xs text-academic-700 dark:text-academic-300 font-semibold">Topic Ideation</p>
          <p class="mt-1 text-2xl font-bold text-academic-900 dark:text-academic-100 stage-count">2</p>
        </div>
        <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 stage-card" data-stage="2">
          <p class="text-xs text-blue-700 dark:text-blue-300 font-semibold">Chapter 1</p>
          <p class="mt-1 text-2xl font-bold text-blue-900 dark:text-blue-100 stage-count">1</p>
        </div>
        <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/30 stage-card" data-stage="3">
          <p class="text-xs text-emerald-700 dark:text-emerald-300 font-semibold">Chapter 2</p>
          <p class="mt-1 text-2xl font-bold text-emerald-900 dark:text-emerald-100 stage-count">1</p>
        </div>
        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 stage-card" data-stage="4">
          <p class="text-xs text-amber-700 dark:text-amber-300 font-semibold">Chapter 3</p>
          <p class="mt-1 text-2xl font-bold text-amber-900 dark:text-amber-100 stage-count">0</p>
        </div>
        <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800/30 stage-card" data-stage="5">
          <p class="text-xs text-indigo-700 dark:text-indigo-300 font-semibold">Chapter 4</p>
          <p class="mt-1 text-2xl font-bold text-indigo-900 dark:text-indigo-100 stage-count">0</p>
        </div>
        <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/30 stage-card" data-stage="6">
          <p class="text-xs text-emerald-700 dark:text-emerald-300 font-semibold">Chapter 5</p>
          <p class="mt-1 text-2xl font-bold text-emerald-900 dark:text-emerald-100 stage-count">1</p>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-r from-amber-600 to-amber-500 rounded-2xl p-6 text-white shadow-lg">
      <h3 class="font-bold text-lg mb-4">Quick Actions</h3>
      <div class="flex flex-wrap gap-3">
        <button onclick="window.location.href='/supervisor/proposals'" class="px-4 py-2.5 rounded-xl bg-white text-amber-600 hover:bg-amber-50 text-sm font-bold transition flex items-center gap-2">
          <i class="fa-solid fa-file-circle-check"></i> Review Proposals (2 pending)
        </button>
        <button onclick="window.location.href='/supervisor/meetings/new'" class="px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 border border-white/20 text-white text-sm font-bold transition flex items-center gap-2">
          <i class="fa-solid fa-calendar-plus"></i> Log New Session
        </button>
        <button onclick="window.location.href='/supervisor/students'" class="px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 border border-white/20 text-white text-sm font-bold transition flex items-center gap-2">
          <i class="fa-solid fa-users"></i> View Student Roster
        </button>
      </div>
    </div>

    <!-- Student Roster -->
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

  </main>

  <script>
    // Logout function
    function logout() {
      if (confirm('Are you sure you want to log out?')) {
        console.log('Logout functionality would be implemented here');
      }
    }
  </script>
</body>
</html>