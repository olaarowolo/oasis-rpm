<!-- SUPER ADMIN HEADER -->
<header id="super-admin-header" class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200 dark:border-slate-700">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16 lg:h-14 gap-4">
      <?php
        $hasBreadcrumbItems = isset($breadcrumbs) && is_countable($breadcrumbs) && count($breadcrumbs) > 0;
        $hasBreadcrumbMarkup = isset($breadcrumbs) && is_string($breadcrumbs) && trim($breadcrumbs) !== '';
      ?>

      <!-- Mobile Menu Toggle -->
      <button type="button" 
              data-mobile-nav-toggle 
              aria-label="Open navigation menu" 
              aria-expanded="false" 
              aria-controls="super-admin-sidebar"
              class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
        <span class="mobile-nav-line"></span>
        <span class="mobile-nav-line"></span>
        <span class="mobile-nav-line"></span>
      </button>

      <!-- Breadcrumb Section -->
      <div class="flex-1 min-w-0 lg:max-w-2xl" role="navigation" aria-label="Breadcrumb">
        <?php if($hasBreadcrumbItems): ?>
          <?php if (isset($component)) { $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.breadcrumbs','data' => ['breadcrumbs' => $breadcrumbs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('super-admin.breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($breadcrumbs)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6)): ?>
<?php $attributes = $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6; ?>
<?php unset($__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6)): ?>
<?php $component = $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6; ?>
<?php unset($__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6); ?>
<?php endif; ?>
        <?php elseif($hasBreadcrumbMarkup): ?>
          <?php echo $breadcrumbs; ?>

        <?php else: ?>
          <nav class="hidden sm:flex items-center gap-1.5" aria-label="Breadcrumb">
            <ol class="flex items-center gap-1.5 text-sm">
              <li>
                <a href="<?php echo e(route('super-admin.dashboard')); ?>" class="flex items-center gap-1.5 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                  <i class="fa-solid fa-gauge-high text-base"></i>
                  <span class="hidden sm:inline">Dashboard</span>
                </a>
              </li>
              <?php if(Route::currentRouteName() !== 'super-admin.dashboard'): ?>
                <li class="flex items-center gap-1.5">
                  <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
                  <span class="font-medium text-slate-900 dark:text-white"><?php echo e(ucfirst(str_replace(['super-admin.', '.'], ['', ' '], Route::currentRouteName()))); ?></span>
                </li>
              <?php endif; ?>
            </ol>
          </nav>
        <?php endif; ?>
      </div>

      <!-- Right Side Actions -->
      <div class="flex items-center gap-2 lg:gap-3">
        <!-- Theme Toggle -->
        <button type="button" 
                id="theme-toggle" 
                aria-label="Toggle dark mode"
                class="w-9 h-9 lg:w-10 lg:h-10 flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
          <i class="fa-solid fa-sun text-lg dark:hidden"></i>
          <i class="fa-solid fa-moon text-lg hidden dark:inline"></i>
        </button>

        <!-- Notifications -->
        <div class="relative hidden lg:flex">
          <button type="button" 
                  id="notifications-btn" 
                  aria-label="Notifications"
                  aria-expanded="false"
                  aria-haspopup="true"
                  class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors relative">
            <i class="fa-solid fa-bell text-lg"></i>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full animate-pulse" aria-hidden="true"></span>
          </button>
          <!-- Notifications Dropdown -->
          <div id="notifications-dropdown" 
               class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 opacity-0 invisible scale-95 transition-all duration-150 z-50"
               role="menu" aria-orientation="vertical">
            <div class="p-3 border-b border-slate-200 dark:border-slate-700">
              <h3 class="font-semibold text-slate-900 dark:text-white">Notifications</h3>
            </div>
            <div class="max-h-64 overflow-y-auto p-2 space-y-1">
              <a href="<?php echo e(route('super-admin.audit-logs')); ?>" class="block p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" role="menuitem">
                <p class="text-sm font-medium text-slate-900 dark:text-white">New audit entries</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">5 new system events recorded</p>
              </a>
              <a href="<?php echo e(route('super-admin.system-status')); ?>" class="block p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" role="menuitem">
                <p class="text-sm font-medium text-slate-900 dark:text-white">System health check</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">All services operational</p>
              </a>
            </div>
            <div class="p-3 border-t border-slate-200 dark:border-slate-700">
              <a href="<?php echo e(route('super-admin.audit-logs')); ?>" class="text-sm font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300 block text-center">View all notifications</a>
            </div>
          </div>
        </div>

        <!-- User Menu -->
        <div class="relative">
          <button type="button" 
                  id="user-menu-btn" 
                  aria-label="User menu"
                  aria-expanded="false"
                  aria-haspopup="true"
                  class="flex items-center gap-2 w-10 h-10 lg:w-auto lg:h-10 lg:px-3 lg:rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
            <div class="w-8 h-8 lg:w-9 lg:h-9 rounded-xl bg-gradient-to-br from-violet-600 to-purple-700 flex items-center justify-center text-white font-bold text-sm lg:text-base">
              <?php echo e(Str::upper(($currentUser?->name ?? 'S')[0])); ?>

            </div>
            <span class="hidden lg:inline-block font-medium text-slate-900 dark:text-white truncate max-w-[140px]"><?php echo e($currentUser?->name ?? 'Super Admin'); ?></span>
            <i class="fa-solid fa-chevron-down text-xs lg:text-sm hidden lg:inline transition-transform" id="user-menu-chevron"></i>
          </button>
          <!-- User Dropdown -->
          <div id="user-dropdown" 
               class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 opacity-0 invisible scale-95 transition-all duration-150 z-50"
               role="menu" aria-orientation="vertical">
            <div class="p-3 border-b border-slate-200 dark:border-slate-700">
              <p class="font-semibold text-slate-900 dark:text-white truncate"><?php echo e($currentUser?->name ?? 'Super Admin'); ?></p>
              <p class="text-xs text-slate-500 dark:text-slate-400 truncate"><?php echo e($currentUser?->email); ?></p>
            </div>
            <a href="#" class="block px-3 py-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-sm text-slate-700 dark:text-slate-300" role="menuitem">
              <i class="fa-solid fa-user mr-2 w-4"></i> Profile
            </a>
            <a href="#" class="block px-3 py-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-sm text-slate-700 dark:text-slate-300" role="menuitem">
              <i class="fa-solid fa-gear mr-2 w-4"></i> Settings
            </a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-1"></div>
            <form action="<?php echo e(route('logout')); ?>" method="POST">
              <?php echo csrf_field(); ?>
              <button type="submit" class="block w-full text-left px-3 py-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-sm text-rose-600 dark:text-rose-400" role="menuitem">
                <i class="fa-solid fa-right-from-bracket mr-2 w-4"></i> Sign Out
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<script>
  (function() {
    'use strict';
    
    // Theme toggle
    const themeToggle = document.getElementById('theme-toggle');
    const html = document.documentElement;
    
    function applyTheme(dark) {
      if (dark) {
        html.classList.add('dark');
      } else {
        html.classList.remove('dark');
      }
      localStorage.setItem('theme', dark ? 'dark' : 'light');
    }
    
    // Initialize theme
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(savedTheme ? savedTheme === 'dark' : prefersDark);
    
    if (themeToggle) {
      themeToggle.addEventListener('click', function() {
        applyTheme(!html.classList.contains('dark'));
      });
    }
    
    // Dropdown handling
    function setupDropdown(btnId, dropdownId, chevronId) {
      const btn = document.getElementById(btnId);
      const dropdown = document.getElementById(dropdownId);
      const chevron = document.getElementById(chevronId);
      
      if (!btn || !dropdown) return;
      
      let isOpen = false;
      let closeTimer = null;
      
      function open() {
        isOpen = true;
        dropdown.classList.remove('opacity-0', 'invisible', 'scale-95');
        dropdown.classList.add('opacity-100', 'visible', 'scale-100');
        btn.setAttribute('aria-expanded', 'true');
        if (chevron) chevron.style.transform = 'rotate(180deg)';
      }
      
      function close() {
        isOpen = false;
        dropdown.classList.add('opacity-0', 'invisible', 'scale-95');
        dropdown.classList.remove('opacity-100', 'visible', 'scale-100');
        btn.setAttribute('aria-expanded', 'false');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
      }
      
      function toggle() {
        isOpen ? close() : open();
      }
      
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        toggle();
      });
      
      dropdown.addEventListener('mouseenter', function() {
        if (closeTimer) clearTimeout(closeTimer);
      });
      
      dropdown.addEventListener('mouseleave', function() {
        closeTimer = setTimeout(close, 150);
      });
      
      btn.addEventListener('mouseleave', function() {
        closeTimer = setTimeout(close, 150);
      });
      
      btn.addEventListener('mouseenter', function() {
        if (closeTimer) clearTimeout(closeTimer);
      });
      
      document.addEventListener('click', function(e) {
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
          close();
        }
      });
      
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
          close();
          btn.focus();
        }
      });
    }
    
    setupDropdown('notifications-btn', 'notifications-dropdown', null);
    setupDropdown('user-menu-btn', 'user-dropdown', 'user-menu-chevron');
    
    // Mobile nav integration
    const mobileToggle = document.querySelector('[data-mobile-nav-toggle]');
    if (mobileToggle && window.openMobileNav) {
      mobileToggle.addEventListener('click', function(e) {
        e.preventDefault();
        window.toggleMobileNav();
      });
    }
  })();
</script>

<style>
  /* Header scrollbar for breadcrumbs on mobile */
  header nav ol {
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  header nav ol::-webkit-scrollbar {
    display: none;
  }
</style><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/components/super-admin/header.blade.php ENDPATH**/ ?>