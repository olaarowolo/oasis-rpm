(function () {
  'use strict';

  function trapFocus(element) {
    var focusableElements = element.querySelectorAll(
      'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])'
    );
    var firstElement = focusableElements[0];
    var lastElement = focusableElements[focusableElements.length - 1];

    function handleTab(event) {
      if (event.key !== 'Tab' || !firstElement || !lastElement) return;

      if (event.shiftKey) {
        if (document.activeElement === firstElement) {
          event.preventDefault();
          lastElement.focus();
        }
      } else if (document.activeElement === lastElement) {
        event.preventDefault();
        firstElement.focus();
      }
    }

    element.addEventListener('keydown', handleTab);

    return function () {
      element.removeEventListener('keydown', handleTab);
    };
  }

  function initTheme(dark) {
    document.documentElement.classList.toggle('dark', dark);
    localStorage.setItem('theme', dark ? 'dark' : 'light');
  }

  function initHeaderRoots() {
    var roots = document.querySelectorAll('[data-app-header-root]:not([data-app-header-ready])');
    if (!roots.length) return;

    var savedTheme = localStorage.getItem('theme');
    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    initTheme(savedTheme ? savedTheme === 'dark' : prefersDark);

    roots.forEach(function (root) {
      root.setAttribute('data-app-header-ready', 'true');

      var menus = Array.prototype.slice.call(root.querySelectorAll('[data-app-header-menu]'));
      var themeToggle = root.querySelector('[data-theme-toggle]');
      var notificationRoot = root.querySelector('[data-notifications-root]');

      function closeMenus(exceptMenu) {
        menus.forEach(function (menu) {
          if (exceptMenu && menu === exceptMenu) return;

          var button = menu.querySelector('[data-menu-toggle]');
          var panel = menu.querySelector('[data-menu-panel]');
          var chevron = menu.querySelector('[data-menu-chevron]');
          if (!button || !panel) return;

          button.setAttribute('aria-expanded', 'false');
          panel.classList.add('invisible', 'opacity-0', 'scale-95');
          panel.classList.remove('visible', 'opacity-100', 'scale-100');
          if (chevron) chevron.style.transform = 'rotate(0deg)';
        });
      }

      menus.forEach(function (menu) {
        var button = menu.querySelector('[data-menu-toggle]');
        var panel = menu.querySelector('[data-menu-panel]');
        var chevron = menu.querySelector('[data-menu-chevron]');
        if (!button || !panel) return;

        button.addEventListener('click', function (event) {
          event.preventDefault();
          event.stopPropagation();

          var isOpen = button.getAttribute('aria-expanded') === 'true';
          closeMenus(menu);

          if (!isOpen) {
            button.setAttribute('aria-expanded', 'true');
            panel.classList.remove('invisible', 'opacity-0', 'scale-95');
            panel.classList.add('visible', 'opacity-100', 'scale-100');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
          }
        });
      });

      if (themeToggle) {
        themeToggle.addEventListener('click', function () {
          initTheme(!document.documentElement.classList.contains('dark'));
        });
      }

      if (notificationRoot) {
        var list = notificationRoot.querySelector('[data-notifications-list]');
        var badge = notificationRoot.querySelector('[data-notifications-badge]');
        var summary = notificationRoot.querySelector('[data-notifications-summary]');
        var markAll = notificationRoot.querySelector('[data-mark-all-read]');
        var indexUrl = notificationRoot.getAttribute('data-index-url');
        var markAllUrl = notificationRoot.getAttribute('data-mark-all-url');

        function iconForType(type) {
          if (type === 'success' || /approved/.test(type)) return 'fa-circle-check text-emerald-500';
          if (type === 'warning' || /rejected/.test(type)) return 'fa-circle-exclamation text-amber-500';
          if (/meeting/.test(type)) return 'fa-calendar-check text-blue-500';
          return 'fa-bell text-slate-400';
        }

        function updateBadge(unreadCount) {
          if (!badge) return;
          if (unreadCount > 0) {
            badge.textContent = unreadCount > 9 ? '9+' : String(unreadCount);
            badge.classList.remove('hidden');
          } else {
            badge.textContent = '';
            badge.classList.add('hidden');
          }
        }

        function renderNotifications(payload) {
          var items = payload && payload.items ? payload.items : [];
          var unreadCount = payload && typeof payload.unread_count === 'number' ? payload.unread_count : 0;

          updateBadge(unreadCount);

          if (summary) {
            summary.textContent = unreadCount > 0
              ? unreadCount + ' unread update' + (unreadCount === 1 ? '' : 's')
              : 'You are all caught up';
          }

          if (!list) return;

          if (!items.length) {
            list.innerHTML = '<div class="rounded-xl border border-dashed border-slate-200 px-3 py-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">No notifications yet.</div>';
            return;
          }

          list.innerHTML = items.map(function (item) {
            var href = item.action_url || '#';
            var stateClasses = item.read
              ? 'border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800/70'
              : 'border-academic-100 bg-academic-50/80 dark:border-academic-800 dark:bg-academic-900/20';

            return '' +
              '<a href="' + href + '" class="block rounded-xl border px-3 py-3 transition hover:border-slate-300 hover:bg-slate-50 dark:hover:border-slate-600 dark:hover:bg-slate-700/40 ' + stateClasses + '" data-notification-item data-notification-id="' + item.id + '">' +
                '<div class="flex items-start gap-3">' +
                  '<div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-700/60">' +
                    '<i class="fa-solid ' + iconForType(item.type || '') + '"></i>' +
                  '</div>' +
                  '<div class="min-w-0 flex-1">' +
                    '<div class="flex items-start justify-between gap-2">' +
                      '<p class="text-sm font-semibold text-slate-900 dark:text-white">' + (item.title || 'Notification') + '</p>' +
                      '<span class="shrink-0 text-[11px] text-slate-400 dark:text-slate-500">' + (item.time || '') + '</span>' +
                    '</div>' +
                    '<p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">' + (item.message || '') + '</p>' +
                  '</div>' +
                '</div>' +
              '</a>';
          }).join('');
        }

        function fetchNotifications() {
          if (!indexUrl) return Promise.resolve();

          return fetch(indexUrl, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
            .then(function (response) { return response.json(); })
            .then(function (payload) {
              renderNotifications(payload.data || {});
            })
            .catch(function () {
              if (list) {
                list.innerHTML = '<div class="rounded-xl border border-dashed border-rose-200 px-3 py-4 text-sm text-rose-600 dark:border-rose-900/60 dark:text-rose-300">Could not load notifications right now.</div>';
              }
              if (summary) summary.textContent = 'Unable to refresh updates';
            });
        }

        function markNotificationRead(id) {
          if (!id) return Promise.resolve();

          return fetch('/api/notifications/' + id + '/read', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          }).catch(function () {});
        }

        fetchNotifications();
        window.setInterval(fetchNotifications, 60000);

        if (markAll) {
          markAll.addEventListener('click', function () {
            if (!markAllUrl) return;

            fetch(markAllUrl, {
              method: 'POST',
              credentials: 'same-origin',
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            })
              .then(function (response) { return response.json(); })
              .then(function () {
                return fetchNotifications();
              })
              .catch(function () {});
          });
        }

        if (list) {
          list.addEventListener('click', function (event) {
            var link = event.target.closest ? event.target.closest('[data-notification-item]') : null;
            if (!link) return;
            markNotificationRead(link.getAttribute('data-notification-id'));
          });
        }
      }

      document.addEventListener('click', function (event) {
        if (!root.contains(event.target)) {
          closeMenus();
        }
      });

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          closeMenus();
        }
      });
    });
  }

  function initSidebarDrawer() {
    var toggles = Array.prototype.slice.call(document.querySelectorAll('[data-mobile-nav-toggle]'));
    var backdrop = document.getElementById('nav-backdrop');
    if (!toggles.length || !backdrop) return;

    var sidebarId = toggles[0].getAttribute('aria-controls') || (document.getElementById('super-admin-sidebar') ? 'super-admin-sidebar' : 'app-sidebar');
    var sidebar = document.getElementById(sidebarId);
    if (!sidebar) return;

    var mobileQuery = sidebarId === 'super-admin-sidebar' ? '(max-width: 1023.98px)' : '(max-width: 767.98px)';
    var desktopQuery = sidebarId === 'super-admin-sidebar' ? '(min-width: 1024px)' : '(min-width: 768px)';
    var backdropTimer = null;
    var lastTrigger = null;
    var touchStartY = 0;
    var touchMoveY = 0;
    var cleanupFocusTrap = null;

    sidebar.setAttribute('aria-hidden', String(window.matchMedia(mobileQuery).matches));

    function setNavOpen(open, options) {
      if (!sidebar || !backdrop) return;
      var mobile = window.matchMedia(mobileQuery).matches;
      if (!mobile && open) return;

      sidebar.classList.toggle('drawer-open', open);
      backdrop.classList.toggle('nav-backdrop-visible', open);
      if (open) {
        backdrop.classList.remove('hidden');
      }
      if (!open) {
        backdrop.classList.toggle('hidden', false);
      }
      document.body.classList.toggle('nav-drawer-locked', open && mobile);
      sidebar.setAttribute('aria-hidden', String(!open));
      toggles.forEach(function (toggle) {
        if (toggle.getAttribute('aria-controls') !== sidebarId) return;
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
      });

      if (open) {
        if (backdropTimer) window.clearTimeout(backdropTimer);
        backdrop.removeAttribute('aria-hidden');
        cleanupFocusTrap = trapFocus(sidebar);
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
        sidebar.style.transform = '';
        sidebar.style.transition = '';
        if (cleanupFocusTrap) {
          cleanupFocusTrap();
          cleanupFocusTrap = null;
        }
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
      if (toggle && toggle.getAttribute('aria-controls') === sidebarId) {
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

      var navAction = event.target.closest ? event.target.closest('.nav-btn, .nav-link') : null;
      if (navAction && window.matchMedia(mobileQuery).matches) {
        window.closeMobileNav({ restoreFocus: false });
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && sidebar.classList.contains('drawer-open')) {
        window.closeMobileNav();
      }
    });

    backdrop.addEventListener('click', function () {
      window.closeMobileNav();
    });

    sidebar.addEventListener('touchstart', function (event) {
      touchStartY = event.touches[0].clientY;
    }, { passive: true });

    sidebar.addEventListener('touchmove', function (event) {
      touchMoveY = event.touches[0].clientY;
      var deltaY = touchMoveY - touchStartY;
      if (deltaY > 0 && sidebar.scrollTop === 0) {
        sidebar.style.transform = 'translateY(' + Math.min(deltaY, 90) + 'px)';
        sidebar.style.transition = 'none';
      }
    }, { passive: true });

    sidebar.addEventListener('touchend', function () {
      var deltaY = touchMoveY - touchStartY;
      sidebar.style.transition = 'transform 0.24s ease';
      if (deltaY > 70) {
        window.closeMobileNav();
      } else {
        sidebar.style.transform = '';
      }
      touchStartY = 0;
      touchMoveY = 0;
    });

    window.addEventListener('resize', function () {
      if (window.matchMedia(desktopQuery).matches) {
        sidebar.classList.remove('drawer-open');
        backdrop.classList.remove('nav-backdrop-visible', 'hidden');
        document.body.classList.remove('nav-drawer-locked');
        sidebar.setAttribute('aria-hidden', 'false');
        sidebar.style.transform = '';
        sidebar.style.transition = '';
        if (cleanupFocusTrap) {
          cleanupFocusTrap();
          cleanupFocusTrap = null;
        }
        toggles.forEach(function (toggle) {
          if (toggle.getAttribute('aria-controls') !== sidebarId) return;
          toggle.setAttribute('aria-expanded', 'false');
          toggle.setAttribute('aria-label', 'Open navigation menu');
        });
      }
    });

    var currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
    document.querySelectorAll('.nav-btn[href], .nav-link[href]').forEach(function (link) {
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

  initHeaderRoots();
  initSidebarDrawer();
}());