<!-- ================= FULL-SCREEN LOGIN GATE ================= -->
<div id="login-gate" class="fixed inset-0 z-[60] bg-gradient-to-br from-slate-900 via-academic-900 to-academic-800 flex items-center justify-center p-4 overflow-y-auto">

  <!-- Decorative background accents -->
  <div class="pointer-events-none absolute inset-0 overflow-hidden">
    <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-academic-600/20 blur-3xl"></div>
    <div class="absolute -bottom-32 -right-16 w-[28rem] h-[28rem] rounded-full bg-amber-500/10 blur-3xl"></div>
  </div>

  <div class="relative w-full max-w-md my-6 bg-white dark:bg-slate-800 rounded-3xl shadow-2xl ring-1 ring-black/5 dark:ring-white/10 border border-slate-200/60 dark:border-slate-700 overflow-hidden fade-in">

    <!-- Brand banner (platform-level, tenant-neutral) -->
    <div class="relative bg-gradient-to-tr from-academic-900 via-academic-800 to-amber-600 px-7 pt-8 pb-10 text-center">
      <div class="pointer-events-none absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_top_right,white,transparent_55%)]"></div>
      <div class="relative">
        <div class="h-12 inline-flex items-center justify-center px-4 rounded-2xl bg-white/10 backdrop-blur-sm ring-1 ring-white/20 shadow-lg">
          <img src="{{ asset('img/afriscribe-logo-white.png') }}" alt="AfriScribe" class="h-6 w-auto object-contain" loading="lazy" />
        </div>
        <h1 class="mt-4 font-bold text-lg text-white leading-tight">
          Research Supervision Portal
        </h1>
        <p class="mt-1 text-xs text-white/70 flex items-center justify-center gap-1.5">
          <i class="fa-solid fa-building-columns text-amber-300/90"></i>
          Multi-tenant supervision platform for universities
        </p>
      </div>
    </div>

    <div class="px-7 pt-5 pb-7 space-y-5 -mt-4 relative bg-white dark:bg-slate-800 rounded-t-3xl">

      <!-- Session status line -->
      <div class="flex items-center justify-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400">
        <span id="gate-status-dot" class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
        <span id="gate-status-text">Checking session...</span>
      </div>

      <!-- Tab switcher -->
      <div class="flex p-1 gap-1 bg-slate-100 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-700">
        <button id="tab-btn-student" type="button" onclick="switchLoginTab('student')"
          class="flex-1 px-2 py-2 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition bg-white dark:bg-slate-700 text-academic-700 dark:text-white shadow-sm">
          <i class="fa-solid fa-user-graduate"></i> Student
        </button>
        <button id="tab-btn-supervisor" type="button" onclick="switchLoginTab('supervisor')"
          class="flex-1 px-2 py-2 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
          <i class="fa-solid fa-chalkboard-user"></i> Supervisor
        </button>
        <button id="tab-btn-admin" type="button" onclick="switchLoginTab('admin')"
          class="flex-1 px-2 py-2 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
          <i class="fa-solid fa-user-shield"></i> Admin
        </button>
      </div>

      {{-- ============================================================ --}}
      {{-- STUDENT TAB: choose tenant -> email -> OTP -> credentials --}}
      {{-- ============================================================ --}}
      <div id="gate-student-box" class="space-y-4">

        <!-- Tenant / University selector -->
        @include('partials.auth.university-selector', ['type' => 'student'])

        <!-- Step 1: University email verification -->
        <div id="student-email-step" class="space-y-3.5">
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
            Verify your <strong class="text-slate-700 dark:text-slate-200">university email</strong> to receive a one-time code.
          </p>
          <form onsubmit="handleStudentEmailSubmit(event)" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">University Email</label>
              <div class="relative">
                <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input id="login-student-email" type="email" placeholder="name@university.edu" autocomplete="email" class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 focus:border-academic-600 outline-none transition dark:text-white">
              </div>
            </div>
            <div id="email-verification-error" class="hidden text-[11px] text-rose-600 dark:text-rose-400 font-medium items-center gap-1.5 flex">
              <i class="fa-solid fa-triangle-exclamation"></i>
              <span id="email-verification-error-text"></span>
            </div>
            <button type="submit" class="w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-sm font-bold shadow-md shadow-amber-500/20 hover:shadow-amber-400/30 transition flex items-center justify-center gap-2">
              <i class="fa-solid fa-paper-plane"></i> Send Verification Code
            </button>
          </form>
        </div>

        <!-- Step 2: OTP verification -->
        <div id="student-otp-step" class="hidden space-y-3.5">
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
            Enter the 6-digit code we emailed you.
          </p>
          <form onsubmit="handleStudentOtpSubmit(event)" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Verification Code</label>
              <div class="relative">
                <i class="fa-solid fa-shield-halved absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input id="login-student-otp" type="text" inputmode="numeric" maxlength="6" placeholder="000000" autocomplete="one-time-code" class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 focus:border-academic-600 outline-none transition dark:text-white tracking-[0.4em] font-semibold">
              </div>
            </div>
            <div class="flex items-center justify-between text-[11px]">
              <span id="otp-timer" class="hidden text-slate-500 dark:text-slate-400">Resend available in <span id="otp-timer-count">30</span>s</span>
              <button type="button" onclick="resendOtpCode()" class="hidden text-academic-600 dark:text-academic-300 font-semibold hover:underline">Resend code</button>
              <button type="button" onclick="showEmailStep()" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 ml-auto">Change email</button>
            </div>
            <div id="otp-verification-error" class="hidden text-[11px] text-rose-600 dark:text-rose-400 font-medium items-center gap-1.5 flex">
              <i class="fa-solid fa-triangle-exclamation"></i>
              <span id="otp-verification-error-text"></span>
            </div>
            <button type="submit" class="w-full px-4 py-2.5 bg-academic-700 hover:bg-academic-800 text-white rounded-xl text-sm font-semibold shadow-md transition flex items-center justify-center gap-2">
              <i class="fa-solid fa-check text-amber-400"></i> Verify Code
            </button>
          </form>
        </div>

        <!-- Step 3: Credentials -->
        <div id="student-credentials-step" class="hidden space-y-3.5">
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
            Enter your <strong class="text-slate-700 dark:text-slate-200">Matric Number</strong> and your <strong class="text-slate-700 dark:text-slate-200">Surname</strong> (the first name on the roster).
          </p>
          <form onsubmit="handleStudentCredentialsSubmit(event)" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Matric Number</label>
              <div class="relative">
                <i class="fa-solid fa-id-card absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input id="login-matric" type="text" placeholder="Enter your matric number" autocomplete="off" class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 focus:border-academic-600 outline-none transition dark:text-white">
              </div>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Surname (Lastname)</label>
              <div class="relative">
                <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input id="login-lastname" type="text" placeholder="Enter your surname" autocomplete="off" class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 focus:border-academic-600 outline-none transition dark:text-white">
              </div>
            </div>
            <div id="credentials-login-error" class="hidden text-[11px] text-rose-600 dark:text-rose-400 font-medium items-center gap-1.5 flex">
              <i class="fa-solid fa-triangle-exclamation"></i>
              <span id="credentials-login-error-text"></span>
            </div>
            <button type="submit" class="w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-sm font-bold shadow-md shadow-amber-500/20 hover:shadow-amber-400/30 transition flex items-center justify-center gap-2">
              <i class="fa-solid fa-right-to-bracket"></i> Log In
            </button>
          </form>
        </div>
      </div>

      {{-- ============================================================ --}}
      {{-- SUPERVISOR TAB --}}
      {{-- ============================================================ --}}
      <div id="gate-supervisor-box" class="hidden space-y-4">
        <!-- Tenant / University selector -->
        @include('partials.auth.university-selector', ['type' => 'supervisor'])

        <!-- Step 1: Email verification -->
        <div id="supervisor-email-step" class="space-y-3.5">
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
            Verify your <strong class="text-slate-700 dark:text-slate-200">supervisor email</strong> to receive a one-time code.
          </p>
          <form onsubmit="handleSupervisorEmailSubmit(event)" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
              <div class="relative">
                <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input id="login-supervisor-email" type="email" placeholder="you@university.edu" autocomplete="email" class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 focus:border-academic-600 outline-none transition dark:text-white">
              </div>
            </div>
            <div id="supervisor-email-error" class="hidden text-[11px] text-rose-600 dark:text-rose-400 font-medium items-center gap-1.5 flex">
              <i class="fa-solid fa-triangle-exclamation"></i>
              <span id="supervisor-email-error-text"></span>
            </div>
            <button type="submit" class="w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-sm font-bold shadow-md shadow-amber-500/20 hover:shadow-amber-400/30 transition flex items-center justify-center gap-2">
              <i class="fa-solid fa-paper-plane"></i> Send Verification Code
            </button>
          </form>
        </div>

        <!-- Step 2: OTP verification -->
        <div id="supervisor-otp-step" class="hidden space-y-3.5">
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
            Enter the 6-digit code we emailed you.
          </p>
          <form onsubmit="handleSupervisorOtpSubmit(event)" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Verification Code</label>
              <div class="relative">
                <i class="fa-solid fa-shield-halved absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input id="login-supervisor-otp" type="text" inputmode="numeric" maxlength="6" placeholder="000000" autocomplete="one-time-code" class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 focus:border-academic-600 outline-none transition dark:text-white tracking-[0.4em] font-semibold">
              </div>
            </div>
            <div class="flex items-center justify-between text-[11px]">
              <span id="supervisor-otp-timer" class="hidden text-slate-500 dark:text-slate-400">Resend available in <span id="supervisor-otp-timer-count">30</span>s</span>
              <button type="button" onclick="resendSupervisorOtpCode()" class="hidden text-academic-600 dark:text-academic-300 font-semibold hover:underline">Resend code</button>
              <button type="button" onclick="showSupervisorEmailStep()" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 ml-auto">Change email</button>
            </div>
            <div id="supervisor-otp-error" class="hidden text-[11px] text-rose-600 dark:text-rose-400 font-medium items-center gap-1.5 flex">
              <i class="fa-solid fa-triangle-exclamation"></i>
              <span id="supervisor-otp-error-text"></span>
            </div>
            <button type="submit" class="w-full px-4 py-2.5 bg-academic-700 hover:bg-academic-800 text-white rounded-xl text-sm font-semibold shadow-md transition flex items-center justify-center gap-2">
              <i class="fa-solid fa-check text-amber-400"></i> Verify Code
            </button>
          </form>
        </div>

        <!-- Step 3: Credentials -->
        <div id="supervisor-credentials-step" class="hidden space-y-3.5">
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
            Enter your <strong class="text-slate-700 dark:text-slate-200">security PIN</strong> and <strong class="text-slate-700 dark:text-slate-200">passphrase</strong> to continue.
          </p>
          <form onsubmit="handleSupervisorLoginDirect(event)" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Security PIN</label>
              <div class="relative">
                <i class="fa-solid fa-shield-halved absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input id="login-supervisor-pin" type="password" inputmode="numeric" placeholder="Enter your PIN" autocomplete="off" class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 focus:border-academic-600 outline-none transition dark:text-white tracking-widest">
              </div>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Passphrase</label>
              <div class="relative">
                <i class="fa-solid fa-key absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input id="login-supervisor-passphrase" type="password" placeholder="Enter supervisor passphrase" autocomplete="off" class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 focus:border-academic-600 outline-none transition dark:text-white">
              </div>
            </div>
            <div id="supervisor-login-error" class="hidden text-[11px] text-rose-600 dark:text-rose-400 font-medium items-center gap-1.5 flex">
              <i class="fa-solid fa-triangle-exclamation"></i>
              <span id="supervisor-login-error-text"></span>
            </div>
            <button type="submit" class="w-full px-4 py-2.5 bg-academic-700 hover:bg-academic-800 text-white rounded-xl text-sm font-semibold shadow-md transition flex items-center justify-center gap-2">
              <i class="fa-solid fa-right-to-bracket text-amber-400"></i> Enter Supervisor Hub
            </button>
          </form>
        </div>
      </div>

      {{-- ============================================================ --}}
      {{-- ADMIN TAB --}}
      {{-- ============================================================ --}}
      <div id="gate-admin-box" class="hidden space-y-4">
        <!-- Tenant / University selector -->
        @include('partials.auth.university-selector', ['type' => 'admin'])

        <!-- Step 1: Email verification -->
        <div id="admin-email-step" class="space-y-3.5">
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
            Verify your <strong class="text-slate-700 dark:text-slate-200">admin email</strong> to receive a one-time code.
          </p>
          <form onsubmit="handleAdminEmailSubmit(event)" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Admin Email</label>
              <div class="relative">
                <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input id="login-admin-email" type="email" placeholder="admin@university.edu" autocomplete="email" class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 focus:border-academic-600 outline-none transition dark:text-white">
              </div>
            </div>
            <div id="admin-email-error" class="hidden text-[11px] text-rose-600 dark:text-rose-400 font-medium items-center gap-1.5 flex">
              <i class="fa-solid fa-triangle-exclamation"></i>
              <span id="admin-email-error-text"></span>
            </div>
            <button type="submit" class="w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-sm font-bold shadow-md shadow-amber-500/20 hover:shadow-amber-400/30 transition flex items-center justify-center gap-2">
              <i class="fa-solid fa-paper-plane"></i> Send Verification Code
            </button>
          </form>
        </div>

        <!-- Step 2: OTP verification -->
        <div id="admin-otp-step" class="hidden space-y-3.5">
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
            Enter the 6-digit code we emailed you.
          </p>
          <form onsubmit="handleAdminOtpSubmit(event)" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Verification Code</label>
              <div class="relative">
                <i class="fa-solid fa-shield-halved absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input id="login-admin-otp" type="text" inputmode="numeric" maxlength="6" placeholder="000000" autocomplete="one-time-code" class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 focus:border-academic-600 outline-none transition dark:text-white tracking-[0.4em] font-semibold">
              </div>
            </div>
            <div class="flex items-center justify-between text-[11px]">
              <span id="admin-otp-timer" class="hidden text-slate-500 dark:text-slate-400">Resend available in <span id="admin-otp-timer-count">30</span>s</span>
              <button type="button" onclick="resendAdminOtpCode()" class="hidden text-academic-600 dark:text-academic-300 font-semibold hover:underline">Resend code</button>
              <button type="button" onclick="showAdminEmailStep()" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 ml-auto">Change email</button>
            </div>
            <div id="admin-otp-error" class="hidden text-[11px] text-rose-600 dark:text-rose-400 font-medium items-center gap-1.5 flex">
              <i class="fa-solid fa-triangle-exclamation"></i>
              <span id="admin-otp-error-text"></span>
            </div>
            <button type="submit" class="w-full px-4 py-2.5 bg-academic-700 hover:bg-academic-800 text-white rounded-xl text-sm font-semibold shadow-md transition flex items-center justify-center gap-2">
              <i class="fa-solid fa-check text-amber-400"></i> Verify Code
            </button>
          </form>
        </div>

        <!-- Step 3: Credentials -->
        <div id="admin-credentials-step" class="hidden space-y-3.5">
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
            Enter your password to continue. Multi-factor verification may be required.
          </p>
          <form onsubmit="handleAdminLogin(event)" class="space-y-3.5 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Password</label>
              <div class="relative">
                <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input id="login-admin-password" type="password" placeholder="Enter your password" autocomplete="current-password" class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-academic-600 focus:border-academic-600 outline-none transition dark:text-white">
              </div>
            </div>
            <div id="admin-login-error" class="hidden text-[11px] text-rose-600 dark:text-rose-400 font-medium items-center gap-1.5 flex">
              <i class="fa-solid fa-triangle-exclamation"></i>
              <span id="admin-login-error-text"></span>
            </div>
            <button type="submit" class="w-full px-4 py-2.5 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-xl text-sm font-semibold shadow-md transition flex items-center justify-center gap-2">
              <i class="fa-solid fa-right-to-bracket text-amber-400"></i> Sign In
            </button>
          </form>
        </div>
      </div>

      <!-- Footer: demo request + platform note -->
      <div class="pt-1 space-y-2 text-center">
        <button type="button" onclick="openDemoRequestModal()" class="text-[11px] font-semibold text-academic-600 dark:text-academic-300 hover:underline inline-flex items-center gap-1.5">
          <i class="fa-solid fa-circle-play"></i> Request a demo for your university
        </button>
        <p class="text-[10px] text-slate-400 dark:text-slate-500">
          Powered by <span class="font-semibold text-slate-500 dark:text-slate-400">AfriScribe</span> &bull; Secure multi-tenant access
        </p>
      </div>
    </div>
  </div>
</div>
