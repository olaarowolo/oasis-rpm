// Public Header JavaScript - Dropdowns, Mobile Sheet, Scroll Effects

(function () {
  'use strict';

  // ==========================================
  // Utility Functions
  // ==========================================
  function $(selector, context = document) {
    return context.querySelector(selector);
  }

  function $$(selector, context = document) {
    return Array.from(context.querySelectorAll(selector));
  }

  function on(event, selector, handler) {
    document.addEventListener(event, function (e) {
      const target = e.target.closest(selector);
      if (target) handler.call(target, e);
    });
  }

  function trapFocus(element) {
    const focusableElements = element.querySelectorAll(
      'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])'
    );
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];

    function handleTab(e) {
      if (e.key !== 'Tab') return;

      if (e.shiftKey) {
        if (document.activeElement === firstElement) {
          e.preventDefault();
          lastElement.focus();
        }
      } else {
        if (document.activeElement === lastElement) {
          e.preventDefault();
          firstElement.focus();
        }
      }
    }

    element.addEventListener('keydown', handleTab);
    return function () {
      element.removeEventListener('keydown', handleTab);
    };
  }

  // ==========================================
  // Scroll Header Effect
  // ==========================================
  const header = $('#public-header');
  if (header) {
    let lastScrollY = window.scrollY;
    let ticking = false;

    function onScroll() {
      lastScrollY = window.scrollY;
      if (!ticking) {
        window.requestAnimationFrame(function () {
          if (lastScrollY > 10) {
            header.classList.add('scrolled');
          } else {
            header.classList.remove('scrolled');
          }
          ticking = false;
        });
        ticking = true;
      }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
  }

  // ==========================================
  // Desktop Dropdown Menus
  // ==========================================
  const dropdowns = $$('.public-header__nav-item');
  let activeDropdown = null;
  let hoverTimeout = null;

  function openDropdown(item) {
    if (activeDropdown && activeDropdown !== item) {
      closeDropdown(activeDropdown);
    }
    activeDropdown = item;
    const dropdown = item.querySelector('.public-header__dropdown');
    const trigger = item.querySelector('.public-header__nav-trigger');
    if (dropdown && trigger) {
      dropdown.classList.add('public-header__dropdown--open');
      trigger.setAttribute('aria-expanded', 'true');
    }
  }

  function closeDropdown(item) {
    const dropdown = item.querySelector('.public-header__dropdown');
    const trigger = item.querySelector('.public-header__nav-trigger');
    if (dropdown && trigger) {
      dropdown.classList.remove('public-header__dropdown--open');
      trigger.setAttribute('aria-expanded', 'false');
    }
    if (activeDropdown === item) activeDropdown = null;
  }

  function closeAllDropdowns() {
    dropdowns.forEach(closeDropdown);
  }

  dropdowns.forEach(function (item) {
    const trigger = item.querySelector('.public-header__nav-trigger');
    const dropdown = item.querySelector('.public-header__dropdown');

    if (!trigger || !dropdown) return;

    // Mouse enter with hover intent
    item.addEventListener('mouseenter', function () {
      if (hoverTimeout) clearTimeout(hoverTimeout);
      hoverTimeout = setTimeout(function () {
        openDropdown(item);
      }, 150);
    });

    item.addEventListener('mouseleave', function () {
      if (hoverTimeout) clearTimeout(hoverTimeout);
      hoverTimeout = setTimeout(function () {
        closeDropdown(item);
      }, 150);
    });

    // Keyboard support
    trigger.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      if (dropdown.classList.contains('public-header__dropdown--open')) {
        closeDropdown(item);
      } else {
        openDropdown(item);
      }
    });

    trigger.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        closeDropdown(item);
        trigger.focus();
      } else if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        if (dropdown.classList.contains('public-header__dropdown--open')) {
          closeDropdown(item);
        } else {
          openDropdown(item);
        }
      } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        openDropdown(item);
        const firstItem = dropdown.querySelector('.public-header__dropdown-item, .public-header__dropdown-label');
        if (firstItem) firstItem.focus();
      }
    });

    // Dropdown internal keyboard navigation
    dropdown.addEventListener('keydown', function (e) {
      const items = Array.from(dropdown.querySelectorAll('.public-header__dropdown-item, .public-header__dropdown-label'));
      const currentIndex = items.indexOf(document.activeElement);

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        const nextIndex = (currentIndex + 1) % items.length;
        items[nextIndex].focus();
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prevIndex = (currentIndex - 1 + items.length) % items.length;
        items[prevIndex].focus();
      } else if (e.key === 'Escape') {
        closeDropdown(item);
        trigger.focus();
      } else if (e.key === 'Tab' && e.shiftKey && document.activeElement === items[0]) {
        e.preventDefault();
        trigger.focus();
      }
    });

    // Collapsible sections within dropdown
    const sectionTriggers = dropdown.querySelectorAll('.public-header__dropdown-label');
    sectionTriggers.forEach(function (sectionTrigger) {
      const list = sectionTrigger.nextElementSibling;
      if (!list || !list.classList.contains('public-header__dropdown-list')) return;

      sectionTrigger.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const isOpen = list.classList.contains('public-header__dropdown-list--open');
        list.classList.toggle('public-header__dropdown-list--open', !isOpen);
        sectionTrigger.setAttribute('aria-expanded', String(!isOpen));
      });

      sectionTrigger.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          const isOpen = list.classList.contains('public-header__dropdown-list--open');
          list.classList.toggle('public-header__dropdown-list--open', !isOpen);
          sectionTrigger.setAttribute('aria-expanded', String(!isOpen));
        }
      });
    });
  });

  // Close dropdowns on click outside
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.public-header__nav-item')) {
      closeAllDropdowns();
    }
  });

  // Close dropdowns on Escape
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && activeDropdown) {
      closeAllDropdowns();
    }
  });

  // ==========================================
  // Mobile Bottom Sheet
  // ==========================================
  const mobileToggle = $('#public-header__mobile-toggle');
  const sheet = $('#public-header__sheet');
  const sheetContent = $('.public-header__sheet-content');
  const backdrop = $('#public-header__sheet-backdrop');
  const sheetTriggers = $$('.public-header__sheet-trigger');
  let lastFocusedElement = null;
  let touchStartY = 0;
  let touchMoveY = 0;

  function openSheet() {
    if (!sheet || !backdrop) return;
    lastFocusedElement = document.activeElement;
    sheet.classList.add('public-header__sheet--open');
    sheet.setAttribute('aria-hidden', 'false');
    backdrop.classList.add('public-header__sheet-backdrop--visible');
    backdrop.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    mobileToggle.setAttribute('aria-expanded', 'true');
    mobileToggle.setAttribute('aria-label', 'Close navigation menu');
    const icon = mobileToggle.querySelector('i');
    if (icon) {
      icon.classList.remove('fa-bars');
      icon.classList.add('fa-xmark');
    }

    // Focus first focusable element in sheet
    setTimeout(function () {
      const firstFocusable = sheet.querySelector('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])');
      if (firstFocusable) firstFocusable.focus();
    }, 100);

    // Trap focus
    sheet._focusTrap = trapFocus(sheet);
  }

  function closeSheet() {
    if (!sheet || !backdrop) return;
    sheet.classList.remove('public-header__sheet--open');
    sheet.setAttribute('aria-hidden', 'true');
    sheet.style.transform = '';
    sheet.style.transition = '';
    backdrop.classList.remove('public-header__sheet-backdrop--visible');
    backdrop.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    mobileToggle.setAttribute('aria-expanded', 'false');
    mobileToggle.setAttribute('aria-label', 'Open navigation menu');
    const icon = mobileToggle.querySelector('i');
    if (icon) {
      icon.classList.remove('fa-xmark');
      icon.classList.add('fa-bars');
    }

    // Restore focus
    if (lastFocusedElement && document.contains(lastFocusedElement)) {
      lastFocusedElement.focus();
    } else if (mobileToggle) {
      mobileToggle.focus();
    }

    // Clean up focus trap
    if (sheet._focusTrap) {
      sheet._focusTrap();
      sheet._focusTrap = null;
    }

    // Close all accordion sections
    $$('.public-header__sheet-list--open').forEach(function (list) {
      list.classList.remove('public-header__sheet-list--open');
      const trigger = list.previousElementSibling;
      if (trigger) trigger.setAttribute('aria-expanded', 'false');
    });
  }

  if (mobileToggle && sheet && backdrop) {
    mobileToggle.addEventListener('click', function () {
      const isOpen = sheet.classList.contains('public-header__sheet--open');
      isOpen ? closeSheet() : openSheet();
    });

    backdrop.addEventListener('click', closeSheet);

    // Touch swipe to close
    sheetContent.addEventListener('touchstart', function (e) {
      touchStartY = e.touches[0].clientY;
    }, { passive: true });

    sheetContent.addEventListener('touchmove', function (e) {
      touchMoveY = e.touches[0].clientY;
      const deltaY = touchMoveY - touchStartY;

      // Only allow downward swipe when at top of scroll
      if (deltaY > 0 && sheetContent.scrollTop === 0) {
        sheet.style.transform = "translateY(" + Math.min(deltaY, 100) + "px)";
        sheet.style.transition = 'none';
      }
    }, { passive: true });

    sheetContent.addEventListener('touchend', function () {
      const deltaY = touchMoveY - touchStartY;
      sheet.style.transition = 'transform 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
      if (deltaY > 60) {
        closeSheet();
      } else {
        sheet.style.transform = 'translateY(0)';
      }
      touchStartY = 0;
      touchMoveY = 0;
    });
  }

  // Sheet accordion triggers
  sheetTriggers.forEach(function (trigger) {
    const list = trigger.nextElementSibling;
    if (!list || !list.classList.contains('public-header__sheet-list')) return;

    trigger.addEventListener('click', function () {
      const isOpen = list.classList.contains('public-header__sheet-list--open');
      // Close other open lists
      $$('.public-header__sheet-list--open').forEach(function (openList) {
        if (openList !== list) {
          openList.classList.remove('public-header__sheet-list--open');
          const otherTrigger = openList.previousElementSibling;
          if (otherTrigger) otherTrigger.setAttribute('aria-expanded', 'false');
        }
      });
      list.classList.toggle('public-header__sheet-list--open', !isOpen);
      trigger.setAttribute('aria-expanded', String(!isOpen));
    });

    trigger.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        const isOpen = list.classList.contains('public-header__sheet-list--open');
        list.classList.toggle('public-header__sheet-list--open', !isOpen);
        trigger.setAttribute('aria-expanded', String(!isOpen));
      } else if (e.key === 'Escape') {
        closeSheet();
      }
    });
  });

  // Close sheet on Escape
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sheet && sheet.classList.contains('public-header__sheet--open')) {
      closeSheet();
    }
  });

  // Close sheet on resize to desktop
  window.addEventListener('resize', function () {
    if (window.innerWidth >= 768 && sheet && sheet.classList.contains('public-header__sheet--open')) {
      closeSheet();
    }
  });

  // ==========================================
  // Mobile Sticky CTA Bar
  // ==========================================
  const mobileCtaBar = $('#public-header__mobile-cta-bar');
  const heroSection = $('#hero-section');

  if (mobileCtaBar && heroSection) {
    let ctaBarVisible = false;
    let ctaBarTicking = false;

    function checkCtaBarVisibility() {
      const heroRect = heroSection.getBoundingClientRect();
      const isHeroPast = heroRect.bottom <= 0;

      if (isHeroPast && !ctaBarVisible) {
        mobileCtaBar.classList.add('public-header__mobile-cta-bar--visible');
        mobileCtaBar.setAttribute('aria-hidden', 'false');
        ctaBarVisible = true;
      } else if (!isHeroPast && ctaBarVisible) {
        mobileCtaBar.classList.remove('public-header__mobile-cta-bar--visible');
        mobileCtaBar.setAttribute('aria-hidden', 'true');
        ctaBarVisible = false;
      }
      ctaBarTicking = false;
    }

    function onScrollCta() {
      if (!ctaBarTicking) {
        window.requestAnimationFrame(checkCtaBarVisibility);
        ctaBarTicking = true;
      }
    }

    window.addEventListener('scroll', onScrollCta, { passive: true });
    // Initial check
    checkCtaBarVisibility();
  }

  // ==========================================
  // Smooth Scroll for Anchor Links
  // ==========================================
  $$('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href === '#') return;

      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        const headerOffset = header ? header.offsetHeight : 0;
        const targetPosition = target.getBoundingClientRect().top + window.scrollY - headerOffset;

        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });

        // Close mobile sheet if open
        if (sheet && sheet.classList.contains('public-header__sheet--open')) {
          closeSheet();
        }
      }
    });
  });

  // ==========================================
  // Active Section Highlight (IntersectionObserver)
  // ==========================================
  const sections = $$('section[id]');
  const navLinks = $$('.public-header__nav-trigger, .public-header__sheet-item');

  if (sections.length && navLinks.length && 'IntersectionObserver' in window) {
    const observerOptions = {
      rootMargin: '-20% 0px -70% 0px',
      threshold: 0
    };

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          const id = entry.target.id;
          navLinks.forEach(function (link) {
            const href = link.getAttribute('href');
            if (href === '#' + id) {
              link.classList.add('active');
              // For dropdown triggers, also add visual indicator
              if (link.classList.contains('public-header__nav-trigger')) {
                link.style.color = '#0b69a3';
              }
            } else {
              link.classList.remove('active');
              if (link.classList.contains('public-header__nav-trigger')) {
                link.style.color = '';
              }
            }
          });
        }
      });
    }, observerOptions);

    sections.forEach(function (section) {
      observer.observe(section);
    });
  }

})();