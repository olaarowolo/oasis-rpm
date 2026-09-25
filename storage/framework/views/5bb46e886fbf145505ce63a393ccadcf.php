<?php $__env->startSection('title', 'Admin Dashboard | TheOAsis Research Supervision System'); ?>

<?php $__env->startSection('content'); ?>
  <?php
    $adminName = $currentUser->name ?? 'Admin';
    $adminEmail = $currentUser->email ?? 'admin@example.com';
  ?>

    <?php if(session('success')): ?>
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-300">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
      <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/40 dark:bg-rose-900/20 dark:text-rose-300">
        <?php echo e(session('error')); ?>

      </div>
    <?php endif; ?>

    <section class="rounded-3xl p-6 sm:p-8 text-white shadow-xl bg-gradient-to-br from-slate-950 via-academic-900 to-academic-800 overflow-hidden relative">
      <div class="absolute inset-0 opacity-25 bg-[radial-gradient(circle_at_top_right,_rgba(245,158,11,0.55),_transparent_30%),radial-gradient(circle_at_bottom_left,_rgba(56,189,248,0.35),_transparent_28%)]"></div>
      <div class="relative flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="max-w-2xl space-y-4">
          <p class="text-xs uppercase tracking-[0.3em] text-amber-300 font-semibold">Administrative overview</p>
          <h2 class="text-3xl sm:text-4xl font-black leading-tight">Welcome, <?php echo e($adminName); ?></h2>
          <p class="text-slate-300 text-sm sm:text-base">Manage users, configure system settings, and keep the supervision platform aligned across universities and departments.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold"><?php echo e($stats['total_universities'] ?? 0); ?> universities</span>
          <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold"><?php echo e($stats['active_users'] ?? 0); ?> active users</span>
          <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold"><?php echo e($stats['students'] ?? 0); ?> students</span>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Universities</p>
        <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white"><?php echo e($stats['total_universities'] ?? 0); ?></p>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Configured institutions in the system</p>
      </article>
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Active Users</p>
        <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-400"><?php echo e($stats['active_users'] ?? 0); ?></p>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Users currently enabled for access</p>
      </article>
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Supervisors</p>
        <p class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-400"><?php echo e($stats['supervisors'] ?? 0); ?></p>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Authorized supervision staff</p>
      </article>
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Pending Approvals</p>
        <p class="mt-2 text-3xl font-black text-rose-700 dark:text-rose-400"><?php echo e($stats['pending_approvals'] ?? 0); ?></p>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Students or supervisors needing review</p>
      </article>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-[1.25fr_0.75fr] gap-6">
      <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <a href="<?php echo e(route('admin.users')); ?>" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm hover:shadow-md transition group">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">User Management</p>
                <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">Manage Users</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Add, edit, and remove admins, supervisors, and students.</p>
              </div>
              <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 flex items-center justify-center group-hover:scale-105 transition">
                <i class="fa-solid fa-user-gear"></i>
              </div>
            </div>
          </a>

          <a href="<?php echo e(route('admin.config')); ?>" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm hover:shadow-md transition group">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">System Configuration</p>
                <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">Configure Platform</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Adjust Gemini, email, and platform settings.</p>
              </div>
              <div class="w-11 h-11 rounded-xl bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 flex items-center justify-center group-hover:scale-105 transition">
                <i class="fa-solid fa-sliders"></i>
              </div>
            </div>
          </a>

          <a href="<?php echo e(route('admin.dashboard')); ?>#relationship-operations" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm hover:shadow-md transition group">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Workflow</p>
                <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">Link Supervision</h3>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Assign or rebalance students directly from the admin dashboard.</p>
              </div>
              <div class="w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 flex items-center justify-center group-hover:scale-105 transition">
                <i class="fa-solid fa-link"></i>
              </div>
            </div>
          </a>
        </div>

        <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
          <a href="<?php echo e(route('admin.users', ['queue' => 'unassigned_students'])); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Queue</p>
            <p class="mt-2 text-3xl font-black text-blue-700 dark:text-blue-400"><?php echo e($stats['unassigned_students'] ?? 0); ?></p>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Students still waiting for a supervisor link.</p>
          </a>
          <a href="<?php echo e(route('admin.users', ['queue' => 'inactive_supervisors', 'role' => 'supervisor'])); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Queue</p>
            <p class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-400"><?php echo e($stats['inactive_supervisors'] ?? 0); ?></p>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Supervisors needing reactivation or replacement review.</p>
          </a>
          <a href="<?php echo e(route('admin.users', ['queue' => 'pending_invites'])); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Queue</p>
            <p class="mt-2 text-3xl font-black text-violet-700 dark:text-violet-400"><?php echo e($stats['pending_invites'] ?? 0); ?></p>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Invitations that still need recipient completion.</p>
          </a>
          <a href="<?php echo e(route('admin.users', ['queue' => 'suspended_students', 'role' => 'student'])); ?>" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Queue</p>
            <p class="mt-2 text-3xl font-black text-rose-700 dark:text-rose-400"><?php echo e($stats['suspended_students'] ?? 0); ?></p>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Students whose academic status requires follow-up.</p>
          </a>
        </section>

        <section id="relationship-operations" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
            <div>
              <h3 class="font-bold text-lg text-slate-900 dark:text-white">Supervision Link Desk</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">Assign or rebalance supervisors inside the active university scope and notify both parties automatically.</p>
            </div>
            <a href="<?php echo e(route('admin.users', ['queue' => 'unassigned_students'])); ?>" class="text-sm font-semibold text-academic-700 dark:text-academic-300 hover:underline">Open intervention queue</a>
          </div>
          <div class="grid grid-cols-1 xl:grid-cols-[0.95fr_1.05fr] gap-0">
            <div class="p-5 border-b xl:border-b-0 xl:border-r border-slate-200 dark:border-slate-700">
              <form method="POST" action="<?php echo e(route('admin.relationships.assign')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="scope_university_id" value="<?php echo e($selectedUniversityId); ?>">
                <div>
                  <label for="dashboard-student-id" class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Student</label>
                  <select id="dashboard-student-id" name="student_id" required class="block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-2.5 text-sm text-slate-900 dark:text-white">
                    <option value="">Select student</option>
                    <?php $__currentLoopData = $relationshipStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($student->id); ?>" <?php if((int) optional($recommendationStudent)->id === (int) $student->id): echo 'selected'; endif; ?>><?php echo e($student->full_name); ?><?php echo e($student->supervisor?->user?->name ? ' - Current: ' . $student->supervisor->user->name : ' - Unassigned'); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
                <div>
                  <label for="dashboard-supervisor-id" class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Supervisor</label>
                  <select id="dashboard-supervisor-id" name="supervisor_id" required class="block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-2.5 text-sm text-slate-900 dark:text-white">
                    <option value="">Select supervisor</option>
                    <?php $__currentLoopData = $assignmentSupervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($supervisor->id); ?>"><?php echo e($supervisor->user->name ?? 'Supervisor'); ?> - <?php echo e($supervisor->department); ?> (<?php echo e($supervisor->students_count); ?> students · <?php echo e($supervisor->load_label); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
                <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs text-blue-700 dark:border-blue-900/30 dark:bg-blue-900/20 dark:text-blue-300">
                  Recommended load is up to <?php echo e($loadPolicy['recommended_max']); ?> students. Supervisors above <?php echo e($loadPolicy['watch_max']); ?> students are flagged as high load.
                </div>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-academic-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-academic-800 transition">
                  <i class="fa-solid fa-link"></i>
                  Link and notify
                </button>
              </form>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
              <div class="p-5 border-b lg:border-b-0 lg:border-r border-slate-200 dark:border-slate-700">
                <h4 class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Unassigned Students</h4>
                <div class="mt-4 space-y-3">
                  <?php $__empty_1 = true; $__currentLoopData = $unassignedStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3">
                      <p class="font-semibold text-slate-900 dark:text-white"><?php echo e($student->full_name); ?></p>
                      <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"><?php echo e($student->matric_number); ?> · <?php echo e($student->degree_level); ?></p>
                      <a href="<?php echo e(route('admin.dashboard', ['student_id' => $student->id])); ?>#relationship-operations" class="mt-3 inline-flex text-xs font-semibold text-academic-700 hover:underline dark:text-academic-300">Focus recommendations</a>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-slate-500 dark:text-slate-400">No unassigned students in the current scope.</p>
                  <?php endif; ?>
                </div>
              </div>
              <div class="p-5">
                <h4 class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Recommended Supervisors</h4>
                <?php if($recommendationStudent): ?>
                  <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Recommendation focus: <?php echo e($recommendationStudent->full_name); ?> <?php if($recommendationStudent->research_topic): ?>&middot; <?php echo e($recommendationStudent->research_topic); ?> <?php else: ?> &middot; <?php echo e($recommendationStudent->degree_level); ?> stage <?php echo e($recommendationStudent->current_stage); ?> <?php endif; ?></p>
                <?php endif; ?>
                <div class="mt-4 space-y-3">
                  <?php $__empty_1 = true; $__currentLoopData = $recommendedSupervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3">
                      <div class="flex items-center justify-between gap-3">
                        <div>
                          <p class="font-semibold text-slate-900 dark:text-white"><?php echo e($supervisor->user->name ?? 'Supervisor'); ?></p>
                          <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"><?php echo e($supervisor->department); ?></p>
                          <p class="mt-1 text-xs <?php echo e($supervisor->load_band === 'recommended' ? 'text-emerald-600 dark:text-emerald-400' : ($supervisor->load_band === 'watch' ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400')); ?>"><?php echo e($supervisor->recommendation_reason); ?></p>
                        </div>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($supervisor->load_band === 'recommended' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($supervisor->load_band === 'watch' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300')); ?>"><?php echo e($supervisor->students_count); ?> students</span>
                      </div>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-slate-500 dark:text-slate-400">No active supervisors found in the current scope.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Recent Relationship Changes</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Recent supervisor links and reassignments, including notification delivery outcomes.</p>
          </div>
          <div class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $relationshipHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="px-5 py-4">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <p class="font-semibold text-slate-900 dark:text-white"><?php echo e(data_get($history->new_values, 'student_name', 'Student')); ?> · <?php echo e(data_get($history->new_values, 'transition') === 'supervisor_reassigned' ? 'Reassigned' : 'Linked'); ?></p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Supervisor: <?php echo e(data_get($history->new_values, 'supervisor_name', 'Unknown')); ?> · Actor: <?php echo e($history->user?->name ?? 'System'); ?></p>
                  </div>
                  <span class="text-xs text-slate-400"><?php echo e(optional($history->created_at)->diffForHumans()); ?></span>
                </div>
                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                  <span class="rounded-full px-2.5 py-1 <?php echo e(data_get($history->new_values, 'notifications.student.status') === 'sent' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'); ?>">Student email: <?php echo e(data_get($history->new_values, 'notifications.student.status', 'unknown')); ?></span>
                  <span class="rounded-full px-2.5 py-1 <?php echo e(data_get($history->new_values, 'notifications.supervisor.status') === 'sent' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'); ?>">Supervisor email: <?php echo e(data_get($history->new_values, 'notifications.supervisor.status', 'unknown')); ?></span>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <div class="px-5 py-8 text-sm text-slate-500 dark:text-slate-400">No relationship changes recorded yet in this scope.</div>
            <?php endif; ?>
          </div>
        </section>

        <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
            <div>
              <h3 class="font-bold text-lg text-slate-900 dark:text-white">Recent Users</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">Latest accounts visible in the current admin scope</p>
            </div>
            <a href="<?php echo e(route('admin.users')); ?>" class="text-sm font-semibold text-academic-700 dark:text-academic-300 hover:underline">Open user manager</a>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
              <thead class="bg-slate-50 dark:bg-slate-900/40 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                  <th class="px-5 py-3">Name</th>
                  <th class="px-5 py-3">Role</th>
                  <th class="px-5 py-3">University</th>
                  <th class="px-5 py-3">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30 transition">
                    <td class="px-5 py-4">
                      <div class="font-semibold text-slate-900 dark:text-white"><?php echo e($user->name); ?></div>
                      <div class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($user->email); ?></div>
                    </td>
                    <td class="px-5 py-4 text-sm">
                      <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($user->isSuperAdmin() ? 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300' : ($user->isAdmin() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($user->isSupervisor() ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200'))); ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $user->role))); ?>

                      </span>
                    </td>
                    <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300"><?php echo e($user->university->name ?? 'N/A'); ?></td>
                    <td class="px-5 py-4 text-sm">
                      <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($user->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'); ?>">
                        <?php echo e($user->is_active ? 'Active' : 'Inactive'); ?>

                      </span>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500 dark:text-slate-400">No users found in the current scope.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <aside class="space-y-6">
        <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h3 class="font-bold text-lg text-slate-900 dark:text-white">Gemini Assistant</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">System tools and AI configuration access</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 flex items-center justify-center">
              <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
          </div>
          <div class="mt-4 rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-900/40 space-y-3">
            <div class="flex items-center justify-between text-sm">
              <span class="text-slate-500 dark:text-slate-400">Selected scope</span>
              <span class="font-semibold text-slate-900 dark:text-white"><?php echo e($selectedUniversityId ? 'University #' . $selectedUniversityId : 'All universities'); ?></span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-slate-500 dark:text-slate-400">Status</span>
              <span class="font-semibold text-emerald-600 dark:text-emerald-400">Ready</span>
            </div>
            <a href="<?php echo e(route('admin.config')); ?>#ai-settings" class="inline-flex items-center gap-2 text-sm font-semibold text-academic-700 dark:text-academic-300 hover:underline">
              Open AI settings
              <i class="fa-solid fa-arrow-right"></i>
            </a>
          </div>
        </section>

        <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5">
          <h3 class="font-bold text-lg text-slate-900 dark:text-white">System Snapshot</h3>
          <div class="mt-4 space-y-3">
            <div class="flex items-center justify-between rounded-xl bg-slate-50 dark:bg-slate-900/40 px-4 py-3">
              <span class="text-sm text-slate-500 dark:text-slate-400">Database</span>
              <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Connected</span>
            </div>
            <div class="flex items-center justify-between rounded-xl bg-slate-50 dark:bg-slate-900/40 px-4 py-3">
              <span class="text-sm text-slate-500 dark:text-slate-400">API</span>
              <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Operational</span>
            </div>
            <div class="flex items-center justify-between rounded-xl bg-slate-50 dark:bg-slate-900/40 px-4 py-3">
              <span class="text-sm text-slate-500 dark:text-slate-400">Admin email</span>
              <span class="text-sm font-semibold text-slate-900 dark:text-white truncate max-w-[12rem]"><?php echo e($adminEmail); ?></span>
            </div>
          </div>
        </section>
      </aside>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/admin-dashboard.blade.php ENDPATH**/ ?>