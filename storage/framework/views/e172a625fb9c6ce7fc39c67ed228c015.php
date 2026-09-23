<!-- SUPER ADMIN SIDEBAR NAVIGATION -->
<?php
  $navigationGroups = [
      [
          'label' => 'Orient',
          'caption' => 'Start from visibility and platform posture.',
          'badge' => 'Now',
          'items' => [
              [
                  'label' => 'Dashboard',
                  'description' => 'Platform-wide overview and priority queues.',
                  'route' => route('super-admin.dashboard'),
                  'match' => 'super-admin.dashboard',
                  'icon' => 'fa-gauge-high',
                  'tone' => 'violet',
              ],
          ],
      ],
      [
          'label' => 'Operate',
          'caption' => 'Run tenant lifecycle and identity work.',
          'badge' => 'Core',
          'items' => [
              [
                  'label' => 'Universities',
                  'description' => 'Provision, inspect, suspend, and restore tenants.',
                  'route' => route('super-admin.universities'),
                  'match' => 'super-admin.universities*',
                  'icon' => 'fa-building-columns',
                  'tone' => 'blue',
              ],
              [
                  'label' => 'All Users',
                  'description' => 'Manage admins, supervisors, and students across tenants.',
                  'route' => route('super-admin.users'),
                  'match' => 'super-admin.users*',
                  'icon' => 'fa-users-gear',
                  'tone' => 'emerald',
              ],
          ],
      ],
      [
          'label' => 'Govern',
          'caption' => 'Security, traceability, and policy review.',
          'badge' => 'Risk',
          'items' => [
              [
                  'label' => 'Audit Logs',
                  'description' => 'Review interventions, changes, and operator history.',
                  'route' => route('super-admin.audit-logs'),
                  'match' => 'super-admin.audit-logs',
                  'icon' => 'fa-clipboard-list',
                  'tone' => 'rose',
              ],
              [
                  'label' => 'System Status',
                  'description' => 'Track runtime health, queues, cache, and delivery state.',
                  'route' => route('super-admin.system-status'),
                  'match' => 'super-admin.system-status',
                  'icon' => 'fa-server',
                  'tone' => 'slate',
              ],
          ],
      ],
      [
          'label' => 'Configure',
          'caption' => 'Platform defaults and shared service settings.',
          'badge' => 'Setup',
          'items' => [
              [
                  'label' => 'System Config',
                  'description' => 'Update AI, email, and global platform settings.',
                  'route' => route('super-admin.config'),
                  'match' => 'super-admin.config',
                  'icon' => 'fa-gears',
                  'tone' => 'amber',
              ],
              [
                  'label' => 'Resources',
                  'description' => 'Control platform-managed resources and shared data.',
                  'route' => route('super-admin.resources'),
                  'match' => 'super-admin.resources',
                  'icon' => 'fa-database',
                  'tone' => 'blue',
              ],
          ],
      ],
  ];

  $quickCreate = [
      ['label' => 'Add University', 'route' => route('super-admin.universities.create'), 'icon' => 'fa-building-circle-plus'],
      ['label' => 'Create User', 'route' => route('super-admin.users.create'), 'icon' => 'fa-user-plus'],
  ];

  $toneClasses = [
      'violet' => 'text-violet-500 bg-violet-50 dark:bg-violet-900/30 dark:text-violet-300',
      'blue' => 'text-blue-500 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300',
      'emerald' => 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-300',
      'rose' => 'text-rose-500 bg-rose-50 dark:bg-rose-900/30 dark:text-rose-300',
      'amber' => 'text-amber-500 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-300',
      'slate' => 'text-slate-500 bg-slate-100 dark:bg-slate-800 dark:text-slate-300',
  ];
?>

<aside id="super-admin-sidebar" aria-label="Super Admin Navigation" class="flex h-full w-full flex-shrink-0 flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700 lg:sticky lg:top-0 lg:self-start lg:h-screen lg:w-80 lg:overflow-hidden">
  <div class="border-b border-slate-200 px-4 py-4 dark:border-slate-700">
    <a href="<?php echo e(route('super-admin.dashboard')); ?>" class="flex items-center gap-3" aria-label="Super Admin Dashboard">
      <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-600 to-purple-700 shadow-lg">
        <i class="fa-solid fa-crown text-lg text-white"></i>
      </div>
      <div class="min-w-0">
        <h2 class="truncate text-base font-bold text-slate-900 dark:text-white">Super Admin</h2>
        <p class="text-[10px] font-medium uppercase tracking-[0.24em] text-violet-600 dark:text-violet-400">Platform Command Center</p>
      </div>
    </a>

    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/60">
      <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-500 dark:text-slate-400">Navigation Principle</p>
      <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Group the workspace by workstream: orient first, then operate, govern, and configure.</p>
    </div>
  </div>

  <nav class="flex-1 space-y-4 overflow-y-auto px-3 py-4" aria-label="Main navigation">
    <?php $__currentLoopData = $navigationGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <section class="rounded-2xl border border-slate-200 bg-slate-50/70 p-3 dark:border-slate-700 dark:bg-slate-800/40">
        <div class="flex items-center justify-between gap-3 px-1 pb-2">
          <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-500 dark:text-slate-400"><?php echo e($group['label']); ?></p>
            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400"><?php echo e($group['caption']); ?></p>
          </div>
          <span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:bg-slate-900 dark:text-slate-300"><?php echo e($group['badge']); ?></span>
        </div>

        <div class="space-y-2">
          <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $isActive = request()->routeIs($item['match']);
            ?>
            <a href="<?php echo e($item['route']); ?>"
               class="nav-link flex items-start gap-3 rounded-2xl px-3 py-3 transition-colors <?php echo e($isActive ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-900 dark:text-white' : 'text-slate-600 hover:bg-white hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-900/70 dark:hover:text-white'); ?>"
               aria-current="<?php echo e($isActive ? 'page' : 'false'); ?>">
              <span class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl <?php echo e($toneClasses[$item['tone']] ?? $toneClasses['slate']); ?>">
                <i class="fa-solid <?php echo e($item['icon']); ?>"></i>
              </span>
              <span class="min-w-0 flex-1">
                <span class="block text-sm font-semibold"><?php echo e($item['label']); ?></span>
                <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400"><?php echo e($item['description']); ?></span>
              </span>
            </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </section>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <section class="rounded-2xl border border-dashed border-slate-300 p-3 dark:border-slate-600">
      <div class="px-1 pb-2">
        <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-500 dark:text-slate-400">Quick Create</p>
        <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">Fast access to the highest-frequency setup actions.</p>
      </div>
      <div class="space-y-2">
        <?php $__currentLoopData = $quickCreate; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e($item['route']); ?>" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-white">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900">
              <i class="fa-solid <?php echo e($item['icon']); ?>"></i>
            </span>
            <span><?php echo e($item['label']); ?></span>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </section>
  </nav>

  <div class="border-t border-slate-200 bg-white/60 p-4 backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/60">
    <div class="rounded-2xl bg-slate-50 p-3 dark:bg-slate-800/50">
      <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-600 to-purple-700 text-sm font-bold text-white">
          <?php echo e(Str::upper(($currentUser?->name ?? 'S')[0])); ?>

        </div>
        <div class="min-w-0 flex-1">
          <p class="truncate text-sm font-semibold text-slate-900 dark:text-white"><?php echo e($currentUser?->name ?? 'Super Admin'); ?></p>
          <p class="truncate text-[10px] text-slate-500 dark:text-slate-400"><?php echo e($currentUser?->email); ?></p>
        </div>
        <form action="<?php echo e(route('logout')); ?>" method="POST" class="ml-auto">
          <?php echo csrf_field(); ?>
          <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-500 dark:hover:bg-rose-900/20" aria-label="Sign out">
            <i class="fa-solid fa-right-from-bracket text-sm"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
</aside>

<style>
  /* Sidebar scrollbar styling */
  #super-admin-sidebar {
    @apply lg:max-h-screen;
  }
  #super-admin-sidebar::-webkit-scrollbar {
    width: 6px;
  }
  #super-admin-sidebar::-webkit-scrollbar-track {
    background: transparent;
  }
  #super-admin-sidebar::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.4);
    border-radius: 3px;
  }
  .nav-link {
    @apply border border-transparent;
  }
  .nav-link[aria-current="page"] {
    @apply border-violet-200 dark:border-violet-800;
  }
</style><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/components/super-admin/sidebar.blade.php ENDPATH**/ ?>