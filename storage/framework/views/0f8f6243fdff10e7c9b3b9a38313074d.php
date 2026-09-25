<?php if (isset($component)) { $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.public','data' => ['title' => 'TheOAsis Research Supervision Portal | From Topic To Completion','description' => 'A multi-tenant research supervision portal for universities. Track 12 research stages, streamline supervisor feedback, and improve student outcomes.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('layouts.public'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'TheOAsis Research Supervision Portal | From Topic To Completion','description' => 'A multi-tenant research supervision portal for universities. Track 12 research stages, streamline supervisor feedback, and improve student outcomes.']); ?>
  <section id="hero-section" class="mesh-bg relative overflow-hidden section-reveal in-view">
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
              <img src="<?php echo e(asset('img/afriscribe-logo-white.png')); ?>" alt="Afriscribe" class="h-5 sm:h-6 w-auto" loading="lazy" decoding="async">
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
            <a href="<?php echo e(route('login')); ?>" class="btn-animate inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-academic-800 hover:bg-academic-900 text-white font-semibold shadow-glow transition">
              <i class="fa-solid fa-rocket"></i>
              Multi-tenant Login
            </a>
            <a href="<?php echo e(route('login')); ?>#request-demo" class="btn-animate inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-semibold transition">
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
        <a href="<?php echo e(route('public.solutions.students')); ?>" class="elevate rounded-2xl border border-slate-200 bg-white p-5 no-underline">
          <h3 class="font-semibold text-academic-900">Students</h3>
          <p class="mt-2 text-sm text-slate-600">Submit proposals, log meetings, complete stage resources, and follow an explicit roadmap with less uncertainty.</p>
        </a>
        <a href="<?php echo e(route('public.solutions.supervisors')); ?>" class="elevate rounded-2xl border border-slate-200 bg-white p-5 no-underline">
          <h3 class="font-semibold text-academic-900">Supervisors</h3>
          <p class="mt-2 text-sm text-slate-600">Review faster, provide structured feedback, track each supervisee's stage, and detect stagnation before it escalates.</p>
        </a>
        <a href="<?php echo e(route('public.solutions.administrators')); ?>" class="elevate rounded-2xl border border-slate-200 bg-white p-5 no-underline">
          <h3 class="font-semibold text-academic-900">Admins</h3>
          <p class="mt-2 text-sm text-slate-600">Manage users, configure institutions, monitor audit logs, and maintain governance across departments.</p>
        </a>
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
      <a href="<?php echo e(route('login')); ?>" class="btn-animate mt-6 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-amber-400 text-slate-950 font-semibold hover:bg-amber-300 transition">
        <i class="fa-solid fa-right-to-bracket"></i>
        Go To Login
      </a>
    </div>
  </section>

  <script>
    (function () {
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd)): ?>
<?php $attributes = $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd; ?>
<?php unset($__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd)): ?>
<?php $component = $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd; ?>
<?php unset($__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd); ?>
<?php endif; ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/landing.blade.php ENDPATH**/ ?>