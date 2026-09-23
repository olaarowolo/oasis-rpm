<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'title' => 'Super Admin Portal',
    'breadcrumbs' => [],
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'title' => 'Super Admin Portal',
    'breadcrumbs' => [],
]); ?>
<?php foreach (array_filter(([
    'title' => 'Super Admin Portal',
    'breadcrumbs' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo e($title); ?></title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?php echo e(asset('vendor/fontawesome/css/all.min.css')); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          screens: {
            xs: '400px',
          },
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
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.03); }
    ::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.4); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(156, 163, 175, 0.6); }

    .mobile-nav-toggle {
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 4px;
    }
    .mobile-nav-line {
      display: block;
      width: 20px;
      height: 2px;
      border-radius: 2px;
      background: currentColor;
      transition: transform 0.24s ease, opacity 0.24s ease;
    }
    .mobile-nav-toggle[aria-expanded="true"] .mobile-nav-line:nth-child(1) {
      transform: translateY(6px) rotate(45deg);
    }
    .mobile-nav-toggle[aria-expanded="true"] .mobile-nav-line:nth-child(2) {
      opacity: 0;
    }
    .mobile-nav-toggle[aria-expanded="true"] .mobile-nav-line:nth-child(3) {
      transform: translateY(-6px) rotate(-45deg);
    }
    .mobile-nav-toggle:focus-visible,
    [data-mobile-nav-close]:focus-visible,
    .nav-btn:focus-visible {
      outline: 2px solid #0b69a3;
      outline-offset: 2px;
    }

    @media (max-width: 1023.98px) {
      #super-admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 50;
        width: min(86vw, 20rem);
        max-width: 20rem;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 1rem;
        transform: translateX(-105%);
        visibility: hidden;
        transition: transform 0.28s ease, visibility 0.28s ease;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
      }
      #super-admin-sidebar.drawer-open {
        transform: translateX(0);
        visibility: visible;
      }
      body.nav-drawer-locked {
        overflow: hidden;
      }
    }
    @media (min-width: 1024px) {
      #super-admin-sidebar {
        transform: none !important;
        visibility: visible !important;
      }
    }

    #nav-backdrop {
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.25s ease;
    }
    #nav-backdrop.nav-backdrop-visible {
      pointer-events: auto;
      opacity: 1;
    }

    /* Main content area spacing */
    #super-admin-main {
      @apply min-h-[calc(100vh-160px)];
    }
  </style>

  <?php echo e($head ?? ''); ?>

</head>
<body class="h-full font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100">
  <!-- Navigation Backdrop -->
  <div id="nav-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 opacity-0 hidden" aria-hidden="true"></div>

  <!-- Main Wrapper -->
  <div class="min-h-screen lg:flex lg:flex-row lg:items-start">
    <!-- Super Admin Sidebar -->
    <?php if (isset($component)) { $__componentOriginal2d8397fd11279dcfdf38082d773a517e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2d8397fd11279dcfdf38082d773a517e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.sidebar','data' => ['currentUser' => $currentUser ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('super-admin.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['currentUser' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentUser ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2d8397fd11279dcfdf38082d773a517e)): ?>
<?php $attributes = $__attributesOriginal2d8397fd11279dcfdf38082d773a517e; ?>
<?php unset($__attributesOriginal2d8397fd11279dcfdf38082d773a517e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2d8397fd11279dcfdf38082d773a517e)): ?>
<?php $component = $__componentOriginal2d8397fd11279dcfdf38082d773a517e; ?>
<?php unset($__componentOriginal2d8397fd11279dcfdf38082d773a517e); ?>
<?php endif; ?>

    <!-- Header + Content + Footer Column -->
    <div class="flex min-h-screen min-w-0 flex-1 flex-col">
      <!-- Header -->
      <?php if (isset($component)) { $__componentOriginalf99ccb69736aed055ce39f0d27dd5e50 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf99ccb69736aed055ce39f0d27dd5e50 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.header','data' => ['currentUser' => $currentUser ?? null,'breadcrumbs' => $breadcrumbs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('super-admin.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['currentUser' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentUser ?? null),'breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($breadcrumbs)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf99ccb69736aed055ce39f0d27dd5e50)): ?>
<?php $attributes = $__attributesOriginalf99ccb69736aed055ce39f0d27dd5e50; ?>
<?php unset($__attributesOriginalf99ccb69736aed055ce39f0d27dd5e50); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf99ccb69736aed055ce39f0d27dd5e50)): ?>
<?php $component = $__componentOriginalf99ccb69736aed055ce39f0d27dd5e50; ?>
<?php unset($__componentOriginalf99ccb69736aed055ce39f0d27dd5e50); ?>
<?php endif; ?>

      <!-- Main Content -->
      <main id="super-admin-main" class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 sm:py-8 sm:pb-32">
        <?php echo e($slot); ?>

      </main>

      <!-- Footer -->
      <?php if (isset($component)) { $__componentOriginal06c0218c7d6d5c899160b5753eee362a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal06c0218c7d6d5c899160b5753eee362a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('super-admin.footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal06c0218c7d6d5c899160b5753eee362a)): ?>
<?php $attributes = $__attributesOriginal06c0218c7d6d5c899160b5753eee362a; ?>
<?php unset($__attributesOriginal06c0218c7d6d5c899160b5753eee362a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal06c0218c7d6d5c899160b5753eee362a)): ?>
<?php $component = $__componentOriginal06c0218c7d6d5c899160b5753eee362a; ?>
<?php unset($__componentOriginal06c0218c7d6d5c899160b5753eee362a); ?>
<?php endif; ?>
    </div>
  </div>

  <script>
    (function () {
      var sidebar = document.getElementById('super-admin-sidebar');
      var backdrop = document.getElementById('nav-backdrop');
      var toggles = Array.prototype.slice.call(document.querySelectorAll('[data-mobile-nav-toggle]'));
      var backdropTimer = null;
      var lastTrigger = null;

      function setNavOpen(open, options) {
        if (!sidebar || !backdrop) return;
        var mobile = window.matchMedia('(max-width: 1023.98px)').matches;
        if (!mobile && open) return;

        sidebar.classList.toggle('drawer-open', open);
        backdrop.classList.toggle('nav-backdrop-visible', open);
        backdrop.classList.toggle('hidden', !open);
        document.body.classList.toggle('nav-drawer-locked', open && mobile);
        sidebar.setAttribute('aria-hidden', String(!open));
        toggles.forEach(function (toggle) {
          toggle.setAttribute('aria-expanded', String(open));
          toggle.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
        });

        if (open) {
          backdrop.removeAttribute('aria-hidden');
          var focusable = sidebar.querySelector('a[href], button:not([disabled])');
          window.setTimeout(function () {
            if (focusable) focusable.focus();
          }, 90);
        } else {
          backdrop.setAttribute('aria-hidden', 'true');
          if (backdropTimer) window.clearTimeout(backdropTimer);
          backdropTimer = window.setTimeout(function () {
            if (!sidebar.classList.contains('drawer-open')) backdrop.classList.add('hidden');
          }, 260);
          if ((!options || options.restoreFocus !== false) && lastTrigger && document.contains(lastTrigger)) {
            lastTrigger.focus();
          }
        }
      }

      window.openMobileNav = function () {
        lastTrigger = document.activeElement && document.activeElement.hasAttribute('data-mobile-nav-toggle')
          ? document.activeElement
          : toggles[0] || null;
        setNavOpen(true);
      };
      window.closeMobileNav = function (options) {
        setNavOpen(false, options);
      };
      window.toggleMobileNav = function () {
        setNavOpen(!sidebar.classList.contains('drawer-open'));
      };

      document.addEventListener('click', function (event) {
        var toggle = event.target.closest ? event.target.closest('[data-mobile-nav-toggle]') : null;
        if (toggle) {
          event.preventDefault();
          window.toggleMobileNav();
          return;
        }

        var close = event.target.closest ? event.target.closest('[data-mobile-nav-close]') : null;
        if (close) {
          event.preventDefault();
          window.closeMobileNav();
          return;
        }

        var navLink = event.target.closest ? event.target.closest('.nav-link[href]') : null;
        if (navLink && window.matchMedia('(max-width: 1023.98px)').matches) {
          window.closeMobileNav({ restoreFocus: false });
        }
      });

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && sidebar && sidebar.classList.contains('drawer-open')) {
          window.closeMobileNav();
        }
      });

      backdrop.addEventListener('click', function () {
        window.closeMobileNav();
      });

      window.addEventListener('resize', function () {
        if (window.matchMedia('(min-width: 1024px)').matches && sidebar) {
          sidebar.classList.remove('drawer-open');
          backdrop.classList.remove('nav-backdrop-visible', 'hidden');
          document.body.classList.remove('nav-drawer-locked');
          sidebar.setAttribute('aria-hidden', 'false');
          toggles.forEach(function (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Open navigation menu');
          });
        }
      });

      function markCurrentNavigation() {
        var currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
        document.querySelectorAll('.nav-link[href]').forEach(function (link) {
          var href = link.getAttribute('href');
          if (!href || href === '#') return;
          try {
            var linkPath = new URL(href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
            if (linkPath === currentPath) {
              link.setAttribute('aria-current', 'page');
            }
          } catch (error) {}
        });
      }
      markCurrentNavigation();
    }());
  </script>
</body>
</html><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/components/layouts/super-admin.blade.php ENDPATH**/ ?>