@props([
    'title' => 'Super Admin Portal',
    'breadcrumbs' => [],
])

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title }}</title>

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

  {{ $head ?? '' }}
</head>
<body class="h-full font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100">
  <!-- Navigation Backdrop -->
  <div id="nav-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 opacity-0 hidden" aria-hidden="true"></div>

  <!-- Main Wrapper -->
  <div class="min-h-screen lg:flex lg:flex-row lg:items-start">
    <!-- Super Admin Sidebar -->
    <x-super-admin.sidebar :currentUser="$currentUser ?? null" />

    <!-- Header + Content + Footer Column -->
    <div class="flex min-h-screen min-w-0 flex-1 flex-col">
      <!-- Header -->
      <x-super-admin.header :currentUser="$currentUser ?? null" :breadcrumbs="$breadcrumbs" />

      <!-- Main Content -->
      <main id="super-admin-main" class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 sm:py-8 sm:pb-32">
        {{ $slot }}
      </main>

      <!-- Footer -->
      <x-super-admin.footer />
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
</html>