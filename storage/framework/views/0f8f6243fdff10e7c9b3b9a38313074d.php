<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TheOAsis Research Supervision Portal | From Topic To Completion</title>
  <meta name="description" content="A multi-tenant research supervision portal for universities. Track 12 research stages, streamline supervisor feedback, and improve student outcomes.">

  <link rel="icon" type="image/svg+xml" href="/img/favicon.svg">
  <link rel="apple-touch-icon" href="/img/apple-icon.png">

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo e(asset('vendor/fontawesome/css/all.min.css')); ?>">

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            display: ['Space Grotesk', 'Inter', 'sans-serif']
          },
          colors: {
            academic: {
              50: '#f0f4f8',
              100: '#d9e2ec',
              500: '#102a43',
              600: '#0b69a3',
              700: '#035388',
              800: '#003e6b',
              900: '#002744'
            },
            lasu: {
              gold: '#f59e0b',
              blue: '#002744'
            }
          },
          boxShadow: {
            glow: '0 0 0 1px rgba(245,158,11,.15), 0 20px 50px rgba(2,39,68,.20)'
          }
        }
      }
    }
  </script>

  <style>
    .mesh-bg {
      background:
        radial-gradient(1200px 600px at 95% -10%, rgba(245, 158, 11, 0.35), rgba(245, 158, 11, 0) 60%),
        radial-gradient(900px 500px at -20% 10%, rgba(11, 105, 163, 0.25), rgba(11, 105, 163, 0) 60%),
        linear-gradient(170deg, #f8fafc 0%, #eff6ff 100%);
    }

    .nav-link {
      position: relative;
      transition: color .25s ease;
    }

    .nav-link::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: -6px;
      width: 100%;
      height: 2px;
      background: linear-gradient(90deg, #0b69a3, #f59e0b);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform .25s ease;
    }

    .nav-link:hover::after,
    .nav-link:focus-visible::after {
      transform: scaleX(1);
    }

    .btn-animate {
      transition: transform .25s ease, box-shadow .25s ease, filter .25s ease;
    }

    .btn-animate:hover {
      transform: translateY(-2px);
      filter: saturate(1.05);
    }

    .elevate {
      transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
      will-change: transform;
    }

    .elevate:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 38px rgba(2, 39, 68, 0.12);
      border-color: #cbd5e1;
    }

    .section-reveal {
      opacity: 0;
      transform: translateY(24px);
      transition: opacity .65s ease, transform .65s ease;
    }

    .section-reveal.in-view {
      opacity: 1;
      transform: translateY(0);
    }

    .float-orb {
      position: absolute;
      border-radius: 9999px;
      filter: blur(1px);
      animation: drift 10s ease-in-out infinite;
      pointer-events: none;
    }

    .float-orb.delay {
      animation-delay: 1.8s;
    }

    .reveal {
      opacity: 0;
      transform: translateY(12px);
      animation: rise .5s ease forwards;
    }

    .reveal.delay-1 { animation-delay: .1s; }
    .reveal.delay-2 { animation-delay: .2s; }
    .reveal.delay-3 { animation-delay: .3s; }

    @keyframes rise {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes drift {
      0%, 100% {
        transform: translate(0, 0) scale(1);
      }
      50% {
        transform: translate(10px, -14px) scale(1.05);
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .reveal,
      .section-reveal,
      .section-reveal.in-view,
      .float-orb,
      .elevate,
      .btn-animate,
      .nav-link::after {
        animation: none !important;
        transition: none !important;
        transform: none !important;
        opacity: 1 !important;
      }
    }
  </style>
</head>
<body class="font-sans text-slate-800 bg-slate-50">
  <header class="sticky top-0 z-40 backdrop-blur bg-white/85 border-b border-slate-200/70">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <a href="/" class="flex items-center gap-3 min-w-0">
        <span class="w-11 h-11 flex items-center justify-center shrink-0">
          <img src="https://afriscribe.org/afriscribe/img/afriscribe_proofread-favicon.svg" alt="Afriscribe icon" class="h-9 w-9" loading="eager" decoding="async">
        </span>
        <div class="min-w-0">
          <span class="font-display font-bold text-sm sm:text-base text-academic-900 truncate block">TheOAsis Research Portal</span>
          
        </div>
      </a>

      <div class="flex items-center gap-2">
        <a href="/login" class="md:hidden btn-animate inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-academic-800 text-white shadow-sm">
          <i class="fa-solid fa-rocket text-[10px]"></i>
          Login
        </a>

        <button id="mobile-menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" class="md:hidden w-10 h-10 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 transition">
          <span class="sr-only">Toggle menu</span>
          <i id="mobile-menu-icon" class="fa-solid fa-bars"></i>
        </button>

        <div class="hidden md:flex items-center gap-6">
          <nav class="flex items-center gap-6 text-sm font-medium text-slate-600">
            <a href="#features" class="nav-link hover:text-academic-700">Features</a>
            <a href="#roles" class="nav-link hover:text-academic-700">Roles</a>
            <a href="#lifecycle" class="nav-link hover:text-academic-700">12 Stages</a>
            <a href="#faq" class="nav-link hover:text-academic-700">FAQ</a>
          </nav>

          <div class="flex items-center gap-2">
            <a href="/login" class="btn-animate px-3 py-2 text-sm font-semibold rounded-lg text-slate-700 hover:bg-slate-100 transition">Multi-tenant Login</a>
            <a href="/login#request-demo" class="btn-animate px-3.5 py-2 text-sm font-semibold rounded-lg bg-academic-800 hover:bg-academic-900 text-white transition shadow-sm">Request Demo</a>
          </div>
        </div>
      </div>
    </div>

    <div id="mobile-menu" class="md:hidden hidden border-t border-slate-200/80 bg-white/95">
      <nav class="px-4 py-4 flex flex-col gap-1.5 text-sm font-medium text-slate-700">
        <a href="#features" class="mobile-menu-link rounded-lg px-3 py-2 hover:bg-slate-100">Features</a>
        <a href="#roles" class="mobile-menu-link rounded-lg px-3 py-2 hover:bg-slate-100">Roles</a>
        <a href="#lifecycle" class="mobile-menu-link rounded-lg px-3 py-2 hover:bg-slate-100">12 Stages</a>
        <a href="#faq" class="mobile-menu-link rounded-lg px-3 py-2 hover:bg-slate-100">FAQ</a>

        <div class="mt-3 grid grid-cols-2 gap-2">
          <a href="/login" class="btn-animate inline-flex justify-center items-center rounded-lg px-3 py-2 font-semibold border border-slate-200 bg-white text-slate-700">Multi-tenant Login</a>
          <a href="/login#request-demo" class="btn-animate inline-flex justify-center items-center rounded-lg px-3 py-2 font-semibold bg-academic-800 text-white">Request Demo</a>
        </div>
      </nav>
    </div>
  </header>

  <section class="mesh-bg relative overflow-hidden section-reveal in-view">
    <span class="float-orb w-56 h-56 bg-amber-300/40 top-16 -right-20"></span>
    <span class="float-orb delay w-40 h-40 bg-sky-300/35 bottom-12 -left-12"></span>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-[calc(100vh-4rem)] min-h-[calc(100dvh-4rem)] py-10 sm:py-14 flex items-center">
      <div class="grid lg:grid-cols-2 gap-10 items-center">
        <div>
          <p class="reveal inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold tracking-wide bg-amber-100 text-amber-900">
            <i class="fa-solid fa-university"></i>
            for UG and PG Research
          </p>

          <div class="reveal delay-1 mt-4 inline-flex items-center gap-2 rounded-xl border border-academic-100 bg-white/85 backdrop-blur px-3 py-2 shadow-sm">
            <span class="text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-academic-700">An Afriscribe Product</span>
            <span class="inline-flex items-center rounded-md bg-academic-900 px-2 py-1">
              <img src="https://afriscribe.org/afriscribe/img/afriscribe-logo-main-logo-white.png" alt="Afriscribe" class="h-5 sm:h-6 w-auto" loading="lazy" decoding="async">
            </span>
          </div>

          <h1 class="reveal delay-2 mt-5 font-display text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight text-academic-900">
            Move Every Student
            <span class="text-amber-600">From Topic To Completion</span>
            With Clarity
          </h1>

          <p class="reveal delay-3 mt-5 text-base sm:text-lg text-slate-600 max-w-xl leading-relaxed">
            Replace scattered chats and manual tracking with a single supervision operating system: proposals, meeting logs, stage progression, analytics, and AI-assisted guidance.
          </p>

          <div class="reveal delay-3 mt-7 flex flex-col sm:flex-row gap-3">
            <a href="/login" class="btn-animate inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-academic-800 hover:bg-academic-900 text-white font-semibold shadow-glow transition">
              <i class="fa-solid fa-rocket"></i>
              Multi-tenant Login
            </a>
            <a href="/login#request-demo" class="btn-animate inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-semibold transition">
              <i class="fa-solid fa-calendar-check"></i>
              Request Demo
            </a>
            <a href="#features" class="btn-animate inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 hover:border-slate-300 font-semibold transition">
              <i class="fa-solid fa-circle-play"></i>
              Explore Features
            </a>
          </div>
        </div>

        <div class="reveal delay-2">
          <div class="relative elevate rounded-3xl bg-white border border-slate-200 shadow-2xl p-5 sm:p-6">
            <div class="absolute -top-3 -right-3 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">Production Ready</div>
            <div class="grid grid-cols-2 gap-3 text-sm">
              <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                <p class="text-slate-500 text-xs">Research Stages</p>
                <p class="mt-1 text-2xl font-bold text-academic-900">12</p>
              </div>
              <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                <p class="text-slate-500 text-xs">API Endpoints</p>
                <p class="mt-1 text-2xl font-bold text-academic-900">50+</p>
              </div>
              <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                <p class="text-slate-500 text-xs">Core Tables</p>
                <p class="mt-1 text-2xl font-bold text-academic-900">12</p>
              </div>
              <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                <p class="text-slate-500 text-xs">User Roles</p>
                <p class="mt-1 text-2xl font-bold text-academic-900">3</p>
              </div>
            </div>

            <div class="mt-4 rounded-xl bg-gradient-to-r from-academic-900 to-academic-700 text-white p-4">
              <p class="text-xs uppercase tracking-wide text-blue-100">Multi-Role Workflow</p>
              <div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold">
                <span class="px-2.5 py-1 rounded-full bg-white/15">Student</span>
                <span class="px-2.5 py-1 rounded-full bg-white/15">Supervisor</span>
                <span class="px-2.5 py-1 rounded-full bg-white/15">Admin</span>
                <span class="px-2.5 py-1 rounded-full bg-amber-400/20 text-amber-200">Gemini AI Assistant</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="features" class="section-reveal py-16 sm:py-20 bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl">
        <p class="text-xs font-bold tracking-wider text-academic-700 uppercase">What You Get</p>
        <h2 class="mt-2 font-display text-3xl sm:text-4xl font-bold text-academic-900">Everything Needed To Run Research Supervision At Scale</h2>
      </div>

      <div class="mt-10 grid md:grid-cols-2 xl:grid-cols-3 gap-4">
        <article class="elevate rounded-2xl p-5 border border-slate-200 bg-slate-50">
          <p class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 grid place-content-center"><i class="fa-solid fa-road"></i></p>
          <h3 class="mt-4 font-semibold text-lg text-academic-900">12-Stage Lifecycle Tracking</h3>
          <p class="mt-2 text-sm text-slate-600">Guide every student from ideation to sign-off with clear stage gates, progression history, and milestone visibility.</p>
        </article>

        <article class="elevate rounded-2xl p-5 border border-slate-200 bg-slate-50">
          <p class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 grid place-content-center"><i class="fa-solid fa-comments"></i></p>
          <h3 class="mt-4 font-semibold text-lg text-academic-900">Meeting Logs And Feedback Loop</h3>
          <p class="mt-2 text-sm text-slate-600">Capture supervision sessions with structured logs, review workflows, and rewrite requests that never get lost.</p>
        </article>

        <article class="elevate rounded-2xl p-5 border border-slate-200 bg-slate-50">
          <p class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 grid place-content-center"><i class="fa-solid fa-chart-line"></i></p>
          <h3 class="mt-4 font-semibold text-lg text-academic-900">Supervisor Analytics</h3>
          <p class="mt-2 text-sm text-slate-600">Monitor cohort progress, identify bottlenecks, and flag students at risk early with actionable dashboards.</p>
        </article>

        <article class="elevate rounded-2xl p-5 border border-slate-200 bg-slate-50">
          <p class="w-10 h-10 rounded-xl bg-violet-100 text-violet-700 grid place-content-center"><i class="fa-solid fa-file-circle-check"></i></p>
          <h3 class="mt-4 font-semibold text-lg text-academic-900">Proposal And Resource Governance</h3>
          <p class="mt-2 text-sm text-slate-600">Run proposal approvals, resource submissions, and supervisor sign-offs with complete auditability.</p>
        </article>

        <article class="elevate rounded-2xl p-5 border border-slate-200 bg-slate-50">
          <p class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 grid place-content-center"><i class="fa-solid fa-sparkles"></i></p>
          <h3 class="mt-4 font-semibold text-lg text-academic-900">AI Research Assistant</h3>
          <p class="mt-2 text-sm text-slate-600">Support students with guided prompts for methodology, literature framing, and writing feedback workflows.</p>
        </article>

        <article class="elevate rounded-2xl p-5 border border-slate-200 bg-slate-50">
          <p class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 grid place-content-center"><i class="fa-solid fa-shield-halved"></i></p>
          <h3 class="mt-4 font-semibold text-lg text-academic-900">University Multi-Tenant Isolation</h3>
          <p class="mt-2 text-sm text-slate-600">Keep data scoped by institution with role-based access controls, per-university configuration, and admin oversight.</p>
        </article>
      </div>
    </div>
  </section>

  <section id="roles" class="section-reveal py-16 sm:py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="font-display text-3xl sm:text-4xl font-bold text-academic-900">Built For Every Actor In The Research Journey</h2>
      <div class="mt-8 grid md:grid-cols-3 gap-4">
        <div class="elevate rounded-2xl border border-slate-200 bg-white p-5">
          <h3 class="font-semibold text-academic-900">Students</h3>
          <p class="mt-2 text-sm text-slate-600">Submit proposals, log meetings, complete stage resources, and follow an explicit roadmap with less uncertainty.</p>
        </div>
        <div class="elevate rounded-2xl border border-slate-200 bg-white p-5">
          <h3 class="font-semibold text-academic-900">Supervisors</h3>
          <p class="mt-2 text-sm text-slate-600">Review faster, provide structured feedback, track each supervisee's stage, and detect stagnation before it escalates.</p>
        </div>
        <div class="elevate rounded-2xl border border-slate-200 bg-white p-5">
          <h3 class="font-semibold text-academic-900">Admins</h3>
          <p class="mt-2 text-sm text-slate-600">Manage users, configure institutions, monitor audit logs, and maintain governance across departments.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="lifecycle" class="section-reveal py-16 sm:py-20 bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="font-display text-3xl sm:text-4xl font-bold text-academic-900">The 12-Stage Research Lifecycle</h2>
      <p class="mt-3 text-slate-600 max-w-2xl">Each stage has traceable progress and handoff clarity between student and supervisor.</p>

      <ol class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
        <li class="elevate p-4 rounded-xl bg-slate-50 border border-slate-200">1. Topic Ideation And Approval</li>
        <li class="elevate p-4 rounded-xl bg-slate-50 border border-slate-200">2. Research Gap Identification</li>
        <li class="elevate p-4 rounded-xl bg-slate-50 border border-slate-200">3. Chapter 1 - Introduction</li>
        <li class="elevate p-4 rounded-xl bg-slate-50 border border-slate-200">4. Chapter 2 - Literature Review</li>
        <li class="elevate p-4 rounded-xl bg-slate-50 border border-slate-200">5. Chapter 3 - Methodology</li>
        <li class="elevate p-4 rounded-xl bg-slate-50 border border-slate-200">6. Chapter 4 - Data Analysis</li>
        <li class="elevate p-4 rounded-xl bg-slate-50 border border-slate-200">7. Chapter 5 - Conclusion</li>
        <li class="elevate p-4 rounded-xl bg-slate-50 border border-slate-200">8. Supervisor Review And Feedback</li>
        <li class="elevate p-4 rounded-xl bg-slate-50 border border-slate-200">9. Revisions And Final Edits</li>
        <li class="elevate p-4 rounded-xl bg-slate-50 border border-slate-200">10. Defense Preparation</li>
        <li class="elevate p-4 rounded-xl bg-slate-50 border border-slate-200">11. Project/Thesis Defense</li>
        <li class="elevate p-4 rounded-xl bg-slate-50 border border-slate-200">12. Project Completion (Sign-off/Graduation)</li>
      </ol>
    </div>
  </section>

  <section id="faq" class="section-reveal py-16 sm:py-20 bg-slate-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="font-display text-3xl sm:text-4xl font-bold text-academic-900">FAQ</h2>
      <div class="mt-8 space-y-3">
        <details class="group elevate rounded-xl border border-slate-200 bg-white p-4">
          <summary class="font-semibold text-academic-900 cursor-pointer">Can this work for multiple universities?</summary>
          <p class="mt-2 text-sm text-slate-600">Yes. The architecture supports multi-tenant university configuration and role-scoped access.</p>
        </details>
        <details class="group elevate rounded-xl border border-slate-200 bg-white p-4">
          <summary class="font-semibold text-academic-900 cursor-pointer">Does it support audit and compliance needs?</summary>
          <p class="mt-2 text-sm text-slate-600">Yes. Key activities are logged and approval workflows retain historical traceability.</p>
        </details>
        <details class="group elevate rounded-xl border border-slate-200 bg-white p-4">
          <summary class="font-semibold text-academic-900 cursor-pointer">Is there role-based access control?</summary>
          <p class="mt-2 text-sm text-slate-600">Yes. Student, Supervisor, and Admin roles each have dedicated route protection and features.</p>
        </details>
      </div>
    </div>
  </section>

  <section class="section-reveal py-14 bg-academic-900 text-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <h2 class="font-display text-3xl sm:text-4xl font-bold">Ready To Run A Better Supervision Cycle?</h2>
      <p class="mt-3 text-blue-100">Open the portal and start your pilot rollout for supervisors and students.</p>
      <a href="/login" class="btn-animate mt-6 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-amber-400 text-slate-950 font-semibold hover:bg-amber-300 transition">
        <i class="fa-solid fa-right-to-bracket"></i>
        Go To Login
      </a>
    </div>
  </section>

  <footer class="border-t border-slate-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-slate-500 flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between">
      <p class="flex items-center gap-2 flex-wrap">
        <span>TheOAsis Research Supervision Portal (UG And PG)</span>
        <span class="inline-flex items-center gap-2 text-xs sm:text-sm">
          <span>by</span>
          <span class="inline-flex items-center rounded-md bg-academic-900 px-2 py-1 shadow-sm">
            <img src="/img/logo.svg" alt="Afriscribe" class="h-5 sm:h-6 w-auto" loading="lazy" decoding="async">
          </span>
        </span>
      </p>
      <p>Designed for structured, measurable academic supervision.</p>
    </div>
  </footer>

  <script>
    (function () {
      var menu = document.getElementById('mobile-menu');
      var toggle = document.getElementById('mobile-menu-toggle');
      var icon = document.getElementById('mobile-menu-icon');
      var links = document.querySelectorAll('.mobile-menu-link');

      function setMenuState(isOpen) {
        if (!menu || !toggle || !icon) return;
        menu.classList.toggle('hidden', !isOpen);
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        icon.classList.toggle('fa-bars', !isOpen);
        icon.classList.toggle('fa-xmark', isOpen);
      }

      if (toggle && menu && icon) {
        toggle.addEventListener('click', function () {
          var isOpen = toggle.getAttribute('aria-expanded') === 'true';
          setMenuState(!isOpen);
        });

        links.forEach(function (link) {
          link.addEventListener('click', function () {
            setMenuState(false);
          });
        });

        window.addEventListener('resize', function () {
          if (window.innerWidth >= 768) {
            setMenuState(false);
          }
        });

        window.addEventListener('keydown', function (event) {
          if (event.key === 'Escape') {
            setMenuState(false);
          }
        });
      }

      const sections = document.querySelectorAll('.section-reveal:not(.in-view)');
      if (!sections.length) return;

      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        sections.forEach(function (section) {
          section.classList.add('in-view');
        });
        return;
      }

      const observer = new IntersectionObserver(function (entries, obs) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('in-view');
          obs.unobserve(entry.target);
        });
      }, {
        threshold: 0.18,
        rootMargin: '0px 0px -8% 0px'
      });

      sections.forEach(function (section) {
        observer.observe(section);
      });
    })();
  </script>
</body>
</html>
<?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/landing.blade.php ENDPATH**/ ?>