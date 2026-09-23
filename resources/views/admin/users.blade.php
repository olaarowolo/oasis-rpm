<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users | TheOAsis Research Supervision System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'sans-serif'] },
          colors: {
            academic: {
              50: '#f0f4f8',
              100: '#d9e2ec',
              500: '#102a43',
              600: '#0b69a3',
              700: '#035388',
              800: '#003e6b',
              900: '#002744',
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
<body class="min-h-full font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100">
  @php
    $activeUniversity = collect($universities)->firstWhere('id', (int) $selectedUniversityId);
  @endphp

  <header class="sticky top-0 z-30 border-b border-slate-200/80 dark:border-slate-700 bg-white/90 dark:bg-slate-800/90 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
      <div class="min-w-0">
        <p class="text-xs uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400 font-semibold">User management</p>
        <h1 class="font-bold text-lg text-slate-900 dark:text-white truncate">Manage platform users</h1>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">Dashboard</a>
        <button onclick="openCreateModal()" class="px-4 py-2 rounded-xl bg-academic-700 hover:bg-academic-800 text-white text-sm font-semibold shadow-sm transition">
          <i class="fa-solid fa-user-plus mr-2"></i>Add user
        </button>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <section class="rounded-3xl p-6 sm:p-8 text-white shadow-xl bg-gradient-to-br from-slate-950 via-academic-900 to-academic-800 relative overflow-hidden">
      <div class="absolute inset-0 opacity-25 bg-[radial-gradient(circle_at_top_right,_rgba(245,158,11,0.55),_transparent_30%),radial-gradient(circle_at_bottom_left,_rgba(56,189,248,0.35),_transparent_28%)]"></div>
      <div class="relative flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="max-w-2xl space-y-3">
          <p class="text-xs uppercase tracking-[0.3em] text-amber-300 font-semibold">Access control</p>
          <h2 class="text-3xl sm:text-4xl font-black leading-tight">Create, update, and revoke user access from one place.</h2>
          <p class="text-slate-300 text-sm sm:text-base">This page is backed by the live admin API. Changes apply immediately to the selected university scope.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold">{{ $stats['total'] ?? 0 }} total</span>
          <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold">{{ $stats['active'] ?? 0 }} active</span>
          <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold">{{ $stats['students'] ?? 0 }} students</span>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Total users</p>
        <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ $stats['total'] ?? 0 }}</p>
      </article>
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Active users</p>
        <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-400">{{ $stats['active'] ?? 0 }}</p>
      </article>
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Admins</p>
        <p class="mt-2 text-3xl font-black text-violet-700 dark:text-violet-400">{{ $stats['admins'] ?? 0 }}</p>
      </article>
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Supervisors</p>
        <p class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-400">{{ $stats['supervisors'] ?? 0 }}</p>
      </article>
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Students</p>
        <p class="mt-2 text-3xl font-black text-blue-700 dark:text-blue-400">{{ $stats['students'] ?? 0 }}</p>
      </article>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-[0.8fr_1.2fr] gap-6">
      <div class="space-y-6">
        <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-4">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h3 class="font-bold text-lg text-slate-900 dark:text-white">Scope and filters</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">Switch university or narrow by role.</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label class="space-y-1 text-sm">
              <span class="font-semibold text-slate-700 dark:text-slate-300">University</span>
              <select id="university-filter" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
                <option value="">All universities</option>
                @foreach($universities as $university)
                  <option value="{{ $university->id }}" @selected((string) $selectedUniversityId === (string) $university->id)>{{ $university->name }} ({{ $university->code }})</option>
                @endforeach
              </select>
            </label>
            <label class="space-y-1 text-sm">
              <span class="font-semibold text-slate-700 dark:text-slate-300">Role</span>
              <select id="role-filter" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
                <option value="">All roles</option>
                @foreach($roles as $roleKey => $roleLabel)
                  <option value="{{ $roleKey }}" @selected($selectedRole === $roleKey)>{{ $roleLabel }}</option>
                @endforeach
              </select>
            </label>
          </div>

          <div class="flex gap-2">
            <button type="button" onclick="applyFilters()" class="px-4 py-2.5 rounded-xl bg-academic-700 hover:bg-academic-800 text-white text-sm font-semibold">Apply</button>
            <a href="{{ route('admin.users') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-700/50">Reset</a>
          </div>
        </section>

        <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-4">
          <h3 class="font-bold text-lg text-slate-900 dark:text-white">Quick create</h3>
          <p class="text-sm text-slate-600 dark:text-slate-300">Create a user invite with the minimum identity details. The recipient completes setup from the email link.</p>
          <form id="create-user-form" class="grid grid-cols-1 sm:grid-cols-2 gap-3" onsubmit="submitCreateUser(event)">
            <input name="name" required placeholder="Full name" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
            <input name="email" type="email" required placeholder="Email" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
            <select id="create-role" name="role" required class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
              @foreach($roles as $roleKey => $roleLabel)
                <option value="{{ $roleKey }}">{{ $roleLabel }}</option>
              @endforeach
            </select>
            <select id="create-university-id" name="university_id" required class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
              <option value="">Select university</option>
              @foreach($universities as $university)
                <option value="{{ $university->id }}" @selected((string) $selectedUniversityId === (string) $university->id)>{{ $university->name }}</option>
              @endforeach
            </select>
            <div id="create-student-supervisor-box" class="hidden sm:col-span-2 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 p-4 space-y-3">
              <p class="text-sm text-slate-600 dark:text-slate-300">Student invites auto-assign the least-loaded active supervisor in the selected university unless you require a manual choice.</p>
              <label class="inline-flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                <input type="hidden" name="require_supervisor_selection" value="0">
                <input id="create-require-supervisor-selection" type="checkbox" name="require_supervisor_selection" value="1" class="rounded border-slate-300 text-academic-700 focus:ring-academic-600">
                Require supervisor selection before sending this student invite
              </label>
              <select id="create-supervisor-id" name="supervisor_id" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
                <option value="">Auto-assign least-loaded supervisor</option>
                @foreach($supervisors as $supervisor)
                  <option value="{{ $supervisor->id }}" data-university-id="{{ $supervisor->university_id }}">{{ $supervisor->user->name ?? 'Supervisor' }} - {{ $supervisor->department }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="sm:col-span-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold shadow-sm">Send invite</button>
          </form>
          <p id="user-form-feedback" class="text-sm hidden"></p>
        </section>
      </div>

      <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
          <div>
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Users</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $users->count() }} records in the current scope</p>
          </div>
          <div class="text-xs text-slate-500 dark:text-slate-400">
            {{ $activeUniversity?->name ?? 'All universities' }}
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-900/40 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
              <tr>
                <th class="px-5 py-3">User</th>
                <th class="px-5 py-3">Role</th>
                <th class="px-5 py-3">University</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
              @forelse($users as $user)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30 transition">
                  <td class="px-5 py-4">
                    <div class="font-semibold text-slate-900 dark:text-white">{{ $user->name }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $user->department ?: 'No department set' }}</div>
                  </td>
                  <td class="px-5 py-4 text-sm">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->isSuperAdmin() ? 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300' : ($user->isAdmin() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($user->isSupervisor() ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200')) }}">
                      {{ $roles[$user->role] ?? ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                  </td>
                  <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $user->university->name ?? 'N/A' }}</td>
                  <td class="px-5 py-4 text-sm">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ !$user->is_active && is_null($user->email_verified_at) ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : ($user->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300') }}">
                      {{ !$user->is_active && is_null($user->email_verified_at) ? 'Pending invite' : ($user->is_active ? 'Active' : 'Inactive') }}
                    </span>
                  </td>
                  <td class="px-5 py-4 text-right text-sm space-x-2 whitespace-nowrap">
                    <button type="button" onclick='openEditModal(@json($user))' class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50">Edit</button>
                    <button type="button" onclick="deleteUser({{ $user->id }})" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white">Delete</button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500 dark:text-slate-400">No users found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
    </section>
  </main>

  <div id="user-modal" class="hidden fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm items-center justify-center p-4">
    <div class="w-full max-w-2xl rounded-3xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
        <div>
          <h3 id="modal-title" class="font-bold text-xl text-slate-900 dark:text-white">Add user</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400">Changes are sent directly to the admin API.</p>
        </div>
        <button type="button" onclick="closeUserModal()" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <form id="edit-user-form" class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-3" onsubmit="submitEditUser(event)">
        <input type="hidden" name="id" value="">
        <input name="name" required placeholder="Full name" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
        <input name="email" type="email" required placeholder="Email" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
        <input name="password" type="password" placeholder="New password (optional)" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
        <input name="phone" placeholder="Phone" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
        <input name="department" placeholder="Department" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm sm:col-span-2">
        <select name="role" required class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
          @foreach($roles as $roleKey => $roleLabel)
            <option value="{{ $roleKey }}">{{ $roleLabel }}</option>
          @endforeach
        </select>
        <select name="university_id" required class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
          @foreach($universities as $university)
            <option value="{{ $university->id }}">{{ $university->name }}</option>
          @endforeach
        </select>
        <div class="sm:col-span-2 flex items-center justify-end gap-3 pt-2">
          <button type="button" onclick="closeUserModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold">Cancel</button>
          <button type="submit" class="px-4 py-2.5 rounded-xl bg-academic-700 hover:bg-academic-800 text-white text-sm font-semibold">Save user</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const csrfToken = @json(csrf_token());
    const userModal = document.getElementById('user-modal');
    const editForm = document.getElementById('edit-user-form');
    const createForm = document.getElementById('create-user-form');
    const feedback = document.getElementById('user-form-feedback');
    const createRoleSelect = document.getElementById('create-role');
    const createUniversitySelect = document.getElementById('create-university-id');
    const createSupervisorSelect = document.getElementById('create-supervisor-id');
    const createSupervisorBox = document.getElementById('create-student-supervisor-box');
    const createRequireSupervisorCheckbox = document.getElementById('create-require-supervisor-selection');

    function buildUrl(path) {
      return path;
    }

    async function apiRequest(path, options = {}) {
      const response = await fetch(buildUrl(path), {
        method: options.method || 'GET',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          ...(options.headers || {}),
        },
        credentials: 'same-origin',
        body: options.body ? JSON.stringify(options.body) : undefined,
      });

      const payload = await response.json().catch(() => ({}));
      if (!response.ok || payload.success === false) {
        throw new Error(payload.message || 'Request failed');
      }
      return payload;
    }

    function openCreateModal() {
      document.getElementById('modal-title').innerText = 'Add user';
      editForm.reset();
      editForm.elements.id.value = '';
      userModal.classList.remove('hidden');
      userModal.classList.add('flex');
    }

    function closeUserModal() {
      userModal.classList.add('hidden');
      userModal.classList.remove('flex');
    }

    function openEditModal(user) {
      document.getElementById('modal-title').innerText = 'Edit user';
      editForm.elements.id.value = user.id || '';
      editForm.elements.name.value = user.name || '';
      editForm.elements.email.value = user.email || '';
      editForm.elements.password.value = '';
      editForm.elements.phone.value = user.phone || '';
      editForm.elements.department.value = user.department || '';
      editForm.elements.role.value = user.role || 'student';
      editForm.elements.university_id.value = user.university_id || '';
      userModal.classList.remove('hidden');
      userModal.classList.add('flex');
    }

    async function submitCreateUser(event) {
      event.preventDefault();
      const formData = new FormData(createForm);
      const payload = Object.fromEntries(formData.entries());
      try {
        await apiRequest('/api/admin/users', { method: 'POST', body: payload });
        showFeedback('Invite sent successfully.', 'success');
        window.location.reload();
      } catch (error) {
        showFeedback(error.message || 'Could not create user.', 'error');
      }
    }

    async function submitEditUser(event) {
      event.preventDefault();
      const formData = new FormData(editForm);
      const payload = Object.fromEntries(formData.entries());
      const userId = payload.id;
      delete payload.id;
      if (!payload.password) {
        delete payload.password;
      }

      if (!userId) {
        closeUserModal();
        return;
      }

      try {
        await apiRequest('/api/admin/users/' + userId, { method: 'PUT', body: payload });
        closeUserModal();
        window.location.reload();
      } catch (error) {
        showFeedback(error.message || 'Could not update user.', 'error');
      }
    }

    async function deleteUser(userId) {
      if (!confirm('Delete this user? This cannot be undone.')) {
        return;
      }

      try {
        await apiRequest('/api/admin/users/' + userId, { method: 'DELETE' });
        window.location.reload();
      } catch (error) {
        alert(error.message || 'Could not delete user.');
      }
    }

    function applyFilters() {
      const universityId = document.getElementById('university-filter').value;
      const role = document.getElementById('role-filter').value;
      const params = new URLSearchParams();
      if (universityId) params.set('university_id', universityId);
      if (role) params.set('role', role);
      const query = params.toString();
      window.location.href = query ? ('{{ route('admin.users') }}?' + query) : '{{ route('admin.users') }}';
    }

    function showFeedback(message, type) {
      feedback.textContent = message;
      feedback.className = 'text-sm ' + (type === 'success' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400');
      feedback.classList.remove('hidden');
      setTimeout(() => feedback.classList.add('hidden'), 4000);
    }

    function syncCreateSupervisorVisibility() {
      if (!createRoleSelect || !createSupervisorBox) {
        return;
      }

      const isStudent = createRoleSelect.value === 'student';
      createSupervisorBox.classList.toggle('hidden', !isStudent);
      syncCreateSupervisorOptions();
      syncCreateSupervisorRequirement();
    }

    function syncCreateSupervisorOptions() {
      if (!createUniversitySelect || !createSupervisorSelect) {
        return;
      }

      const universityId = createUniversitySelect.value;
      Array.prototype.forEach.call(createSupervisorSelect.options, function (option) {
        if (!option.value) {
          option.hidden = false;
          return;
        }

        const matches = !universityId || option.dataset.universityId === universityId;
        option.hidden = !matches;

        if (!matches && option.selected) {
          createSupervisorSelect.value = '';
        }
      });
    }

    function syncCreateSupervisorRequirement() {
      if (!createRoleSelect || !createSupervisorSelect || !createRequireSupervisorCheckbox) {
        return;
      }

      const requireSelection = createRoleSelect.value === 'student' && createRequireSupervisorCheckbox.checked;
      createSupervisorSelect.required = requireSelection;

      if (!requireSelection) {
        createSupervisorSelect.value = '';
      }
    }

    if (createRoleSelect) {
      createRoleSelect.addEventListener('change', syncCreateSupervisorVisibility);
      syncCreateSupervisorVisibility();
    }

    if (createUniversitySelect) {
      createUniversitySelect.addEventListener('change', syncCreateSupervisorOptions);
      syncCreateSupervisorOptions();
    }

    if (createRequireSupervisorCheckbox) {
      createRequireSupervisorCheckbox.addEventListener('change', syncCreateSupervisorRequirement);
      syncCreateSupervisorRequirement();
    }

    userModal.addEventListener('click', function (event) {
      if (event.target === userModal) {
        closeUserModal();
      }
    });

    if (createForm) {
      createForm.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          closeUserModal();
        }
      });
    }
  </script>
</body>
</html>