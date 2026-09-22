<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Portal | TheOAsis Research Supervision System</title>
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
          <p class="text-[11px] text-slate-500 dark:text-slate-400">Student Research Portal</p>
        </div>
      </div>

      <!-- User Profile -->
      <div class="flex items-center gap-3">
        <!-- Role Badge -->
        <div class="px-3 py-1.5 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-xs font-bold flex items-center gap-2">
          <i class="fa-solid fa-user-graduate"></i>
          <span>Student View</span>
        </div>

        <!-- User Avatar -->
        <div class="flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-700">
          <div id="user-avatar" class="w-9 h-9 rounded-full bg-academic-800 text-amber-400 border border-amber-500/30 flex items-center justify-center text-sm font-bold shadow-sm">
            JD
          </div>
          <div class="text-left">
            <p class="text-xs font-semibold text-slate-800 dark:text-slate-100 leading-tight">John Doe</p>
            <p class="text-[10px] text-blue-600 dark:text-blue-400 font-medium">Undergraduate Student</p>
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
          <h2 class="text-2xl font-bold">Welcome, Research Student</h2>
          <p class="text-slate-300 mt-1">Track your research progress, access resources, log meetings, and stay on track to completion.</p>
        </div>
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 rounded-full bg-blue-500 text-white text-xs font-bold">Student Portal</span>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-circle-check"></i> Active
          </span>
        </div>
      </div>
    </div>

    <!-- Progress Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Current Stage</p>
            <p class="mt-2 text-3xl font-bold text-academic-900 dark:text-white">Topic Ideation</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-academic-100 dark:bg-academic-900/30 text-academic-600 dark:text-academic-400 flex items-center justify-center">
            <i class="fa-solid fa-lightbulb text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Overall Progress</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900 dark:text-emerald-400">25%</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
            <i class="fa-solid fa-chart-line text-xl"></i>
          </div>
        </div>
        <div class="mt-2 w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
          <div class="h-full bg-emerald-500 rounded-full" style="width: 25%"></div>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Meetings</p>
            <p class="mt-2 text-3xl font-bold text-blue-900 dark:text-blue-400">6</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
            <i class="fa-solid fa-comments text-xl"></i>
          </div>
        </div>
        <p class="mt-2 text-xs text-blue-600 dark:text-blue-400 font-medium"><span class="text-blue-700 dark:text-blue-300">4</span> logged this month</p>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Next Deadline</p>
            <p class="mt-2 text-xl font-bold text-amber-900 dark:text-amber-400">Proposal Review</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
            <i class="fa-solid fa-calendar-check text-xl"></i>
          </div>
        </div>
        <p class="mt-2 text-xs text-amber-600 dark:text-amber-400 font-medium">Due: Oct 15, 2024</p>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-r from-academic-700 to-academic-600 rounded-2xl p-6 text-white shadow-lg">
      <h3 class="font-bold text-lg mb-4">Your Research Actions</h3>
      <div class="flex flex-wrap gap-3">
        <button onclick="window.location.href='/student/meetings'" class="px-4 py-2.5 rounded-xl bg-white text-academic-700 hover:bg-academic-50 text-sm font-bold transition flex items-center gap-2">
          <i class="fa-solid fa-calendar-plus"></i> Log Meeting Session
        </button>
        <button onclick="window.location.href='/student/resources'" class="px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 border border-white/20 text-white text-sm font-bold transition flex items-center gap-2">
          <i class="fa-solid fa-book-open"></i> Access Learning Resources
        </button>
        <button onclick="window.location.href='/student/proposals'" class="px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 border border-white/20 text-white text-sm font-bold transition flex items-center gap-2">
          <i class="fa-solid fa-file-circle-plus"></i> Submit Proposal
        </button>
        <button onclick="window.location.href='/student/defense-readiness'" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-900 text-sm font-bold transition flex items-center gap-2">
          <i class="fa-solid fa-shield-halved"></i> Defense Readiness
        </button>
      </div>
    </div>

    <!-- My Research Progress -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
      <div class="p-5 border-b border-slate-200 dark:border-slate-700">
        <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
          <i class="fa-solid fa-road text-academic-600 dark:text-academic-400"></i>
          My Research Journey (12 Stages)
        </h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Track your progress from ideation to graduation</p>
      </div>
      <div class="p-4 space-y-2">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold">✓</div>
          <div class="flex-1">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Stage 1: Topic Ideation</p>
            <p class="text-xs text-slate-500">Topic approved on Sep 1, 2024</p>
          </div>
          <span class="px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-semibold">Complete</span>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold">✓</div>
          <div class="flex-1">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Stage 2: Research Gap Identification</p>
            <p class="text-xs text-slate-500">Gap documented</p>
          </div>
          <span class="px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-semibold">Complete</span>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold">✓</div>
          <div class="flex-1">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Stage 3: Chapter 1 - Introduction</p>
            <p class="text-xs text-slate-500">Chapter 1 submitted</p>
          </div>
          <span class="px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-semibold">Complete</span>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-academic-600 text-white flex items-center justify-center text-xs font-bold">3</div>
          <div class="flex-1">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Stage 4: Chapter 2 - Literature Review</p>
            <p class="text-xs text-slate-500">In progress</p>
          </div>
          <span class="px-2 py-0.5 rounded bg-academic-100 dark:bg-academic-900/30 text-academic-700 dark:text-academic-300 text-xs font-semibold">In Progress</span>
        </div>
        <div class="flex items-center gap-3 opacity-50">
          <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-400 flex items-center justify-center text-xs font-bold">4</div>
          <div class="flex-1">
            <p class="text-sm text-slate-500">Stage 5: Chapter 3 - Methodology</p>
            <p class="text-xs text-slate-400">Pending</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Upcoming Meetings -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
      <div class="p-5 border-b border-slate-200 dark:border-slate-700">
        <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
          <i class="fa-solid fa-clock text-academic-600 dark:text-academic-400"></i>
          Recent Meeting Logs
        </h3>
      </div>
      <div class="divide-y divide-slate-100 dark:divide-slate-700">
        <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
          <div class="flex items-start justify-between">
            <div class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-lg bg-academic-100 dark:bg-academic-900/30 text-academic-600 dark:text-academic-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-comments text-sm"></i>
              </div>
              <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Supervision Session #1</p>
                <p class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                  <i class="fa-solid fa-calendar text-[10px]"></i> Sep 15, 2024
                  <i class="fa-solid fa-clock text-[10px]"></i> 2 hours
                  <i class="fa-solid fa-user text-[10px]"></i> Dr. O. Arowolo
                </p>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-2">Discussed research gap identification and literature search strategy. Key actions: complete database search, identify 5-7 key papers.</p>
              </div>
            </div>
            <span class="px-2 py-1 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-bold">Completed</span>
          </div>
        </div>
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

    // Load data on page load
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Student Dashboard loaded successfully');
    });
  </script>
</body>
</html>