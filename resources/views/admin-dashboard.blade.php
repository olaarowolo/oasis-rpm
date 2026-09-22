<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Super Admin Portal | TheOAsis Research Supervision System</title>
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
          <p class="text-[11px] text-slate-500 dark:text-slate-400">System Administration Portal</p>
        </div>
      </div>

      <!-- User Profile -->
      <div class="flex items-center gap-3">
        <!-- Role Badge -->
        <div class="px-3 py-1.5 rounded-lg bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300 text-xs font-bold flex items-center gap-2">
          <i class="fa-solid fa-user-shield"></i>
          <span>Super Admin</span>
        </div>

        <!-- User Avatar -->
        <div class="flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-700">
          <div id="user-avatar" class="w-9 h-9 rounded-full bg-academic-800 text-amber-400 border border-amber-500/30 flex items-center justify-center text-sm font-bold shadow-sm">
            OA
          </div>
          <div class="text-left">
            <p class="text-xs font-semibold text-slate-800 dark:text-slate-100 leading-tight">Dr. O. Arowolo</p>
            <p class="text-[10px] text-violet-600 dark:text-violet-400 font-medium">Super Admin</p>
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
          <h2 class="text-2xl font-bold">Welcome, Super Admin</h2>
          <p class="text-slate-300 mt-1">Manage all universities, users, system configuration, and monitor system health.</p>
        </div>
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 rounded-full bg-violet-500 text-white text-xs font-bold">Super Admin Portal</span>
          <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-circle-check"></i> Online
          </span>
        </div>
      </div>
    </div>

    <!-- System Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Total Universities</p>
            <p class="mt-2 text-3xl font-bold text-academic-900 dark:text-white">0</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-academic-100 dark:bg-academic-900/30 text-academic-600 dark:text-academic-400 flex items-center justify-center">
            <i class="fa-solid fa-building-columns text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Active Users</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900 dark:text-emerald-400">0</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
            <i class="fa-solid fa-users text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Supervisors</p>
            <p class="mt-2 text-3xl font-bold text-amber-900 dark:text-amber-400">0</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
            <i class="fa-solid fa-user-shield text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Students</p>
            <p class="mt-2 text-3xl font-bold text-blue-900 dark:text-blue-400">0</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
            <i class="fa-solid fa-user-graduate text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- System Controls Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Universities & Tenants -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="bg-slate-50 dark:bg-slate-700/50 p-4 border-b border-slate-200 dark:border-slate-700">
          <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
            <i class="fa-solid fa-building-columns text-academic-600 dark:text-academic-400"></i>
            Universities &amp; Tenants
          </h3>
        </div>
        <div class="p-4 space-y-3">
          <button onclick="window.location.href='/admin/universities'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left">
            <i class="fa-solid fa-building w-8 h-8 rounded-lg bg-academic-100 dark:bg-academic-900/30 text-academic-600 dark:text-academic-400 flex items-center justify-center text-sm"></i>
            <div class="flex-1">
              <p class="text-sm font-semibold text-slate-900 dark:text-white">Manage Universities</p>
              <p class="text-xs text-slate-500 dark:text-slate-400">Add, edit, or configure university tenants</p>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-400"></i>
          </button>
          <button onclick="window.location.href='/admin/config'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left">
            <i class="fa-solid fa-sliders w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 flex items-center justify-center text-sm"></i>
            <div class="flex-1">
              <p class="text-sm font-semibold text-slate-900 dark:text-white">System Configuration</p>
              <p class="text-xs text-slate-500 dark:text-slate-400">Configure global system settings</p>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-400"></i>
          </button>
        </div>
      </div>

      <!-- User Access & Roles -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="bg-slate-50 dark:bg-slate-700/50 p-4 border-b border-slate-200 dark:border-slate-700">
          <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
            <i class="fa-solid fa-users-gear text-emerald-600 dark:text-emerald-400"></i>
            User Access &amp; Roles
          </h3>
        </div>
        <div class="p-4 space-y-3">
          <button onclick="window.location.href='/admin/users'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left">
            <i class="fa-solid fa-user-gear w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm"></i>
            <div class="flex-1">
              <p class="text-sm font-semibold text-slate-900 dark:text-white">Manage Users</p>
              <p class="text-xs text-slate-500 dark:text-slate-400">Create users and assign roles</p>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-400"></i>
          </button>
          <button onclick="window.location.href='/admin/roles'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left">
            <i class="fa-solid fa-shield-halved w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm"></i>
            <div class="flex-1">
              <p class="text-sm font-semibold text-slate-900 dark:text-white">Roles & Permissions</p>
              <p class="text-xs text-slate-500 dark:text-slate-400">Configure role-based access control</p>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-400"></i>
          </button>
        </div>
      </div>

      <!-- Security & Audit -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="bg-slate-50 dark:bg-slate-700/50 p-4 border-b border-slate-200 dark:border-slate-700">
          <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-rose-600 dark:text-rose-400"></i>
            Security &amp; Audit
          </h3>
        </div>
        <div class="p-4 space-y-3">
          <button onclick="window.location.href='/admin/audit-logs'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left">
            <i class="fa-solid fa-book-open w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center text-sm"></i>
            <div class="flex-1">
              <p class="text-sm font-semibold text-slate-900 dark:text-white">Audit Logs</p>
              <p class="text-xs text-slate-500 dark:text-slate-400">View system activity and user actions</p>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-400"></i>
          </button>
          <button onclick="window.location.href='/admin/resources'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left">
            <i class="fa-solid fa-database w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm"></i>
            <div class="flex-1">
              <p class="text-sm font-semibold text-slate-900 dark:text-white">Resource Management</p>
              <p class="text-xs text-slate-500 dark:text-slate-400">Manage system resources and archives</p>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-400"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- System Status -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
            <i class="fa-solid fa-server text-academic-600 dark:text-academic-400"></i>
            System Status
          </h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Real-time system health monitoring</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-bold flex items-center gap-2">
          <i class="fa-solid fa-circle text-[8px] animate-pulse"></i>
          System Operational
        </span>
      </div>
      
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-slate-200 dark:border-slate-700">
          <p class="text-xs text-slate-500 dark:text-slate-400">Database</p>
          <p class="mt-1 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
            <span class="text-sm font-semibold text-slate-900 dark:text-white">Connected</span>
          </p>
        </div>
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-slate-200 dark:border-slate-700">
          <p class="text-xs text-slate-500 dark:text-slate-400">Mail Server</p>
          <p class="mt-1 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
            <span class="text-sm font-semibold text-slate-900 dark:text-white">Active</span>
          </p>
        </div>
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-slate-200 dark:border-slate-700">
          <p class="text-xs text-slate-500 dark:text-slate-400">Session Storage</p>
          <p class="mt-1 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
            <span class="text-sm font-semibold text-slate-900 dark:text-white">Ready</span>
          </p>
        </div>
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-slate-200 dark:border-slate-700">
          <p class="text-xs text-slate-500 dark:text-slate-400">API Status</p>
          <p class="mt-1 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
            <span class="text-sm font-semibold text-slate-900 dark:text-white">Online</span>
          </p>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-r from-violet-900 to-academic-900 rounded-2xl p-6 text-white shadow-lg">
      <h3 class="font-bold text-lg mb-4">Quick Actions</h3>
      <div class="flex flex-wrap gap-3">
        <button onclick="window.location.href='/admin/users/new'" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-semibold transition flex items-center gap-2">
          <i class="fa-solid fa-plus"></i> Create User
        </button>
        <button onclick="window.location.href='/admin/config/import'" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-semibold transition flex items-center gap-2">
          <i class="fa-solid fa-file-import"></i> Import Data
        </button>
        <button onclick="window.location.href='/admin/audit-logs/export'" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-semibold transition flex items-center gap-2">
          <i class="fa-solid fa-file-export"></i> Export Logs
        </button>
        <button onclick="window.location.href='/admin/system/backup'" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-900 text-sm font-semibold transition flex items-center gap-2">
          <i class="fa-solid fa-database"></i> Create Backup
        </button>
      </div>
    </div>

  </main>

  <script>
    // Logout function
    function logout() {
      if (confirm('Are you sure you want to log out?')) {
        // Add your logout logic here
        // This would typically call the backend logout endpoint
        // and redirect to the login page
        console.log('Logout functionality would be implemented here');
      }
    }

    // Load user data on page load
    document.addEventListener('DOMContentLoaded', function() {
      // You can fetch user data from the backend here
      // if needed for dynamic content
      console.log('Super Admin Dashboard loaded successfully');
    });
  </script>
</body>
</html>