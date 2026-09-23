<script>
    const IS_DEPLOYED = (typeof google !== 'undefined' && google.script && google.script.run);
    const IS_LARAVEL_MODE = !IS_DEPLOYED && typeof window !== 'undefined' && /^https?:$/.test(window.location.protocol);

    let universitiesCache = null;
    let selectedUniversityCode = null;
    let pendingStudentEmail = '';
    let otpTimerInterval = null;
    let otpTimeLeft = 30;
    let pendingAdminMfaChallengeId = null;
    let pendingAdminMfaUniversityId = null;
    let pendingSupervisorEmail = '';
    let supervisorOtpTimerInterval = null;
    let supervisorOtpTimeLeft = 30;
    let pendingAdminEmail = '';
    let adminOtpTimerInterval = null;
    let adminOtpTimeLeft = 30;

    function normalizeUniversityCode(value) {
      return String(value || '').trim().toUpperCase();
    }

    function getSelectedUniversityCode(inputId, fallbackValue) {
      const input = document.getElementById(inputId);
      const typed = normalizeUniversityCode(input ? input.value : '');
      if (typed) return typed;
      return normalizeUniversityCode(fallbackValue || '');
    }

    function setGateStatus(text, ready) {
      const dot = document.getElementById('gate-status-dot');
      const txt = document.getElementById('gate-status-text');
      if (txt) txt.innerText = text;
      if (dot) {
        dot.className = ready
          ? 'w-2 h-2 rounded-full bg-emerald-500'
          : 'w-2 h-2 rounded-full bg-amber-500 animate-pulse';
      }
    }

    async function apiRequest(path, options) {
      const csrfTokenMatch = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
      const csrfToken = csrfTokenMatch ? decodeURIComponent(csrfTokenMatch[1]) : null;
      const csrfHeaders = csrfToken ? {
        'X-CSRF-TOKEN': csrfToken,
        'X-XSRF-TOKEN': csrfToken
      } : {};

      const universityId = sessionStorage.getItem('university_id') || selectedUniversityCode || '';

      const res = await fetch(path, {
        method: (options && options.method) || 'GET',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-University-ID': universityId,
          ...csrfHeaders,
          ...(options && options.headers ? options.headers : {})
        },
        credentials: 'same-origin',
        body: options && options.body ? JSON.stringify(options.body) : undefined
      });

      let payload = null;
      try {
        payload = await res.json();
      } catch (e) {
        payload = null;
      }

      if (!res.ok || !payload || payload.success === false) {
        const msg = payload && payload.message ? payload.message : ('Request failed: ' + res.status);
        throw new Error(msg);
      }

      return payload;
    }

    async function loadUniversities() {
      if (universitiesCache) return universitiesCache;
      try {
        const response = await apiRequest('/api/universities/search');
        universitiesCache = (response.data && response.data.universities) || [];
      } catch (error) {
        universitiesCache = [];
      }
      return universitiesCache;
    }

    function renderUniversityList(dropdownType, universities) {
      const universityList = document.getElementById('university-list-' + dropdownType);
      if (!universityList) return;

      universityList.innerHTML = '';

      if (!universities || universities.length === 0) {
        const empty = document.createElement('div');
        empty.className = 'p-3 text-xs text-slate-500 dark:text-slate-400 text-center';
        empty.textContent = 'No universities found';
        universityList.appendChild(empty);
        return;
      }

      universities.forEach(function (uni) {
        const code = String(uni.code || '');
        const name = String(uni.name || '');

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'w-full px-3 py-2.5 text-left text-xs rounded-lg hover:bg-academic-50 dark:hover:bg-academic-900/30 hover:text-academic-700 dark:hover:text-academic-300 transition flex items-center justify-between group';
        btn.addEventListener('click', function () {
          selectUniversity(dropdownType, code, name);
        });

        const info = document.createElement('div');
        info.className = 'flex flex-col';

        const codeEl = document.createElement('span');
        codeEl.className = 'font-bold text-slate-900 dark:text-white';
        codeEl.textContent = code;

        const nameEl = document.createElement('span');
        nameEl.className = 'text-slate-500 dark:text-slate-400 truncate max-w-[200px]';
        nameEl.textContent = name;

        info.appendChild(codeEl);
        info.appendChild(nameEl);

        const check = document.createElement('i');
        check.className = 'fa-solid fa-check text-academic-500 opacity-0 group-hover:opacity-100 transition';

        btn.appendChild(info);
        btn.appendChild(check);
        universityList.appendChild(btn);
      });
    }

    function filterUniversities(dropdownType) {
      const searchInput = document.getElementById('university-search-' + dropdownType);
      const query = (searchInput && searchInput.value ? searchInput.value : '').trim().toUpperCase();
      const list = universitiesCache || [];
      if (!query) {
        renderUniversityList(dropdownType, list);
        return;
      }
      const filtered = list.filter(function (uni) {
        return String(uni.code || '').includes(query) || String(uni.name || '').toUpperCase().includes(query);
      });
      renderUniversityList(dropdownType, filtered);
    }

    function toggleUniversityDropdown(dropdownType) {
      const dropdown = document.getElementById('university-dropdown-' + dropdownType);
      const searchInput = document.getElementById('university-search-' + dropdownType);
      if (!dropdown) return;

      if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        const afterLoad = function () {
          if (searchInput) {
            searchInput.value = '';
            filterUniversities(dropdownType);
            searchInput.focus();
          }
        };
        if (!universitiesCache) {
          loadUniversities().then(afterLoad);
        } else {
          afterLoad();
        }
      } else {
        dropdown.classList.add('hidden');
      }
    }

    function selectUniversity(dropdownType, code, name) {
      const normalizedCode = normalizeUniversityCode(code);
      const input = document.getElementById('login-university-code-' + dropdownType);
      if (input) {
        input.value = normalizedCode;
        if (name) input.setAttribute('title', name);
      }
      selectedUniversityCode = normalizedCode;
      const dropdown = document.getElementById('university-dropdown-' + dropdownType);
      if (dropdown) dropdown.classList.add('hidden');
    }

    function switchLoginTab(tab) {
      const isStudent = tab === 'student';
      const isAdmin = tab === 'admin';
      const isSupervisor = tab === 'supervisor';

      const studentBox = document.getElementById('gate-student-box');
      const adminBox = document.getElementById('gate-admin-box');
      const supervisorBox = document.getElementById('gate-supervisor-box');

      if (studentBox) studentBox.classList.toggle('hidden', !isStudent);
      if (adminBox) adminBox.classList.toggle('hidden', !isAdmin);
      if (supervisorBox) supervisorBox.classList.toggle('hidden', !isSupervisor);

      const studentBtn = document.getElementById('tab-btn-student');
      const adminBtn = document.getElementById('tab-btn-admin');
      const supervisorBtn = document.getElementById('tab-btn-supervisor');
      const activeCls = ['bg-white', 'dark:bg-slate-700', 'text-academic-700', 'dark:text-white', 'shadow-sm'];
      const idleCls = ['text-slate-500', 'dark:text-slate-400'];

      [studentBtn, adminBtn, supervisorBtn].forEach(function (btn) {
        if (!btn) return;
        btn.classList.remove(...activeCls, ...idleCls);
      });

      if (isStudent && studentBtn) studentBtn.classList.add(...activeCls);
      if (isAdmin && adminBtn) adminBtn.classList.add(...activeCls);
      if (isSupervisor && supervisorBtn) supervisorBtn.classList.add(...activeCls);

      if (!isStudent && studentBtn) studentBtn.classList.add(...idleCls);
      if (!isAdmin && adminBtn) adminBtn.classList.add(...idleCls);
      if (!isSupervisor && supervisorBtn) supervisorBtn.classList.add(...idleCls);

      // Reset each role to its first (email verification) step on tab switch
      if (isSupervisor) showSupervisorEmailStep();
      if (isAdmin) showAdminEmailStep();
    }

    function showEmailStep() {
      const emailStep = document.getElementById('student-email-step');
      const otpStep = document.getElementById('student-otp-step');
      const credentialsStep = document.getElementById('student-credentials-step');
      if (emailStep) emailStep.classList.remove('hidden');
      if (otpStep) otpStep.classList.add('hidden');
      if (credentialsStep) credentialsStep.classList.add('hidden');
      stopOtpTimer();
    }

    function showOtpStep() {
      const emailStep = document.getElementById('student-email-step');
      const otpStep = document.getElementById('student-otp-step');
      const credentialsStep = document.getElementById('student-credentials-step');
      if (emailStep) emailStep.classList.add('hidden');
      if (otpStep) otpStep.classList.remove('hidden');
      if (credentialsStep) credentialsStep.classList.add('hidden');
    }

    function showCredentialsStep() {
      const emailStep = document.getElementById('student-email-step');
      const otpStep = document.getElementById('student-otp-step');
      const credentialsStep = document.getElementById('student-credentials-step');
      if (emailStep) emailStep.classList.add('hidden');
      if (otpStep) otpStep.classList.add('hidden');
      if (credentialsStep) credentialsStep.classList.remove('hidden');
      stopOtpTimer();
    }

    function setError(boxId, textId, message) {
      const box = document.getElementById(boxId);
      const text = document.getElementById(textId);
      if (!box || !text) return;
      if (message) {
        text.innerText = message;
        box.classList.remove('hidden');
      } else {
        text.innerText = '';
        box.classList.add('hidden');
      }
    }

    function startOtpTimer() {
      stopOtpTimer();
      otpTimeLeft = 30;
      const timerWrap = document.getElementById('otp-timer');
      const timerCount = document.getElementById('otp-timer-count');
      const resendBtn = document.querySelector('button[onclick="resendOtpCode()"]');
      if (timerWrap) timerWrap.classList.remove('hidden');
      if (resendBtn) resendBtn.classList.add('hidden');
      if (timerCount) timerCount.innerText = String(otpTimeLeft);

      otpTimerInterval = setInterval(function () {
        otpTimeLeft -= 1;
        if (timerCount) timerCount.innerText = String(Math.max(otpTimeLeft, 0));
        if (otpTimeLeft <= 0) {
          stopOtpTimer();
          if (timerWrap) timerWrap.classList.add('hidden');
          if (resendBtn) resendBtn.classList.remove('hidden');
        }
      }, 1000);
    }

    function stopOtpTimer() {
      if (otpTimerInterval) {
        clearInterval(otpTimerInterval);
        otpTimerInterval = null;
      }
    }

    async function handleStudentEmailSubmit(event) {
      event.preventDefault();
      const email = String((document.getElementById('login-student-email') || {}).value || '').trim();
      setError('email-verification-error', 'email-verification-error-text', '');

      if (!email) {
        setError('email-verification-error', 'email-verification-error-text', 'Please enter your university email.');
        return;
      }

      try {
        await apiRequest('/api/auth/student/send-otp', {
          method: 'POST',
          body: { email: email }
        });
        pendingStudentEmail = email;
        showOtpStep();
        startOtpTimer();
        showToast('Verification code sent to your email.', 'success');
      } catch (err) {
        setError('email-verification-error', 'email-verification-error-text', err && err.message ? err.message : 'Could not send verification code.');
      }
    }

    async function handleStudentOtpSubmit(event) {
      event.preventDefault();
      const otp = String((document.getElementById('login-student-otp') || {}).value || '').trim();
      setError('otp-verification-error', 'otp-verification-error-text', '');

      if (!pendingStudentEmail || !otp) {
        setError('otp-verification-error', 'otp-verification-error-text', 'Enter the verification code sent to your email.');
        return;
      }

      try {
        await apiRequest('/api/auth/student/verify-otp', {
          method: 'POST',
          body: { email: pendingStudentEmail, otp: otp }
        });
        showCredentialsStep();
        showToast('Verification successful.', 'success');
      } catch (err) {
        setError('otp-verification-error', 'otp-verification-error-text', err && err.message ? err.message : 'Invalid verification code.');
      }
    }

    async function resendOtpCode() {
      if (!pendingStudentEmail) return;
      try {
        await apiRequest('/api/auth/student/send-otp', {
          method: 'POST',
          body: { email: pendingStudentEmail }
        });
        startOtpTimer();
        showToast('Verification code resent.', 'success');
      } catch (err) {
        setError('otp-verification-error', 'otp-verification-error-text', err && err.message ? err.message : 'Could not resend code.');
      }
    }

    async function handleStudentCredentialsSubmit(event) {
      event.preventDefault();
      const universityCode = getSelectedUniversityCode('login-university-code-student', '');
      const matric = String((document.getElementById('login-matric') || {}).value || '').trim();
      const lastname = String((document.getElementById('login-lastname') || {}).value || '').trim();
      setError('credentials-login-error', 'credentials-login-error-text', '');

      if (!universityCode || !matric || !lastname) {
        setError('credentials-login-error', 'credentials-login-error-text', 'Please enter your university code, matric number, and surname.');
        return;
      }

      try {
        const res = await apiRequest('/api/auth/login-student', {
          method: 'POST',
          body: {
            university_code: universityCode,
            matric_number: matric,
            lastname: lastname
          }
        });
        const d = (res && res.data) || {};
        if (d.university_id) sessionStorage.setItem('university_id', d.university_id);
        window.location.href = d.dashboard_url || '/home';
      } catch (err) {
        setError('credentials-login-error', 'credentials-login-error-text', err && err.message ? err.message : 'Invalid credentials.');
      }
    }

    function showSupervisorEmailStep() {
      const emailStep = document.getElementById('supervisor-email-step');
      const otpStep = document.getElementById('supervisor-otp-step');
      const credentialsStep = document.getElementById('supervisor-credentials-step');
      if (emailStep) emailStep.classList.remove('hidden');
      if (otpStep) otpStep.classList.add('hidden');
      if (credentialsStep) credentialsStep.classList.add('hidden');
      stopSupervisorOtpTimer();
    }

    function showSupervisorOtpStep() {
      const emailStep = document.getElementById('supervisor-email-step');
      const otpStep = document.getElementById('supervisor-otp-step');
      const credentialsStep = document.getElementById('supervisor-credentials-step');
      if (emailStep) emailStep.classList.add('hidden');
      if (otpStep) otpStep.classList.remove('hidden');
      if (credentialsStep) credentialsStep.classList.add('hidden');
    }

    function showSupervisorCredentialsStep() {
      const emailStep = document.getElementById('supervisor-email-step');
      const otpStep = document.getElementById('supervisor-otp-step');
      const credentialsStep = document.getElementById('supervisor-credentials-step');
      if (emailStep) emailStep.classList.add('hidden');
      if (otpStep) otpStep.classList.add('hidden');
      if (credentialsStep) credentialsStep.classList.remove('hidden');
      stopSupervisorOtpTimer();
    }

    function startSupervisorOtpTimer() {
      stopSupervisorOtpTimer();
      supervisorOtpTimeLeft = 30;
      const timerWrap = document.getElementById('supervisor-otp-timer');
      const timerCount = document.getElementById('supervisor-otp-timer-count');
      const resendBtn = document.querySelector('button[onclick="resendSupervisorOtpCode()"]');
      if (timerWrap) timerWrap.classList.remove('hidden');
      if (resendBtn) resendBtn.classList.add('hidden');
      if (timerCount) timerCount.innerText = String(supervisorOtpTimeLeft);

      supervisorOtpTimerInterval = setInterval(function () {
        supervisorOtpTimeLeft -= 1;
        if (timerCount) timerCount.innerText = String(Math.max(supervisorOtpTimeLeft, 0));
        if (supervisorOtpTimeLeft <= 0) {
          stopSupervisorOtpTimer();
          if (timerWrap) timerWrap.classList.add('hidden');
          if (resendBtn) resendBtn.classList.remove('hidden');
        }
      }, 1000);
    }

    function stopSupervisorOtpTimer() {
      if (supervisorOtpTimerInterval) {
        clearInterval(supervisorOtpTimerInterval);
        supervisorOtpTimerInterval = null;
      }
    }

    async function handleSupervisorEmailSubmit(event) {
      event.preventDefault();
      const email = String((document.getElementById('login-supervisor-email') || {}).value || '').trim().toLowerCase();
      setError('supervisor-email-error', 'supervisor-email-error-text', '');

      if (!email) {
        setError('supervisor-email-error', 'supervisor-email-error-text', 'Please enter your supervisor email.');
        return;
      }

      try {
        await apiRequest('/api/auth/supervisor/send-otp', {
          method: 'POST',
          body: { email: email }
        });
        pendingSupervisorEmail = email;
        showSupervisorOtpStep();
        startSupervisorOtpTimer();
        showToast('Verification code sent to your email.', 'success');
      } catch (err) {
        setError('supervisor-email-error', 'supervisor-email-error-text', err && err.message ? err.message : 'Could not send verification code.');
      }
    }

    async function handleSupervisorOtpSubmit(event) {
      event.preventDefault();
      const otp = String((document.getElementById('login-supervisor-otp') || {}).value || '').trim();
      setError('supervisor-otp-error', 'supervisor-otp-error-text', '');

      if (!pendingSupervisorEmail || !otp) {
        setError('supervisor-otp-error', 'supervisor-otp-error-text', 'Enter the verification code sent to your email.');
        return;
      }

      try {
        await apiRequest('/api/auth/supervisor/verify-otp', {
          method: 'POST',
          body: { email: pendingSupervisorEmail, otp: otp }
        });
        showSupervisorCredentialsStep();
        showToast('Verification successful.', 'success');
      } catch (err) {
        setError('supervisor-otp-error', 'supervisor-otp-error-text', err && err.message ? err.message : 'Invalid verification code.');
      }
    }

    async function resendSupervisorOtpCode() {
      if (!pendingSupervisorEmail) return;
      try {
        await apiRequest('/api/auth/supervisor/send-otp', {
          method: 'POST',
          body: { email: pendingSupervisorEmail }
        });
        startSupervisorOtpTimer();
        showToast('Verification code resent.', 'success');
      } catch (err) {
        setError('supervisor-otp-error', 'supervisor-otp-error-text', err && err.message ? err.message : 'Could not resend code.');
      }
    }

    async function handleSupervisorLoginDirect(event) {
      event.preventDefault();
      const universityCode = getSelectedUniversityCode('login-university-code-supervisor', '');
      const email = (pendingSupervisorEmail || String((document.getElementById('login-supervisor-email') || {}).value || '').trim().toLowerCase());
      const pin = String((document.getElementById('login-supervisor-pin') || {}).value || '').trim();
      const passphrase = String((document.getElementById('login-supervisor-passphrase') || {}).value || '').trim();
      setError('supervisor-login-error', 'supervisor-login-error-text', '');

      if (!universityCode || !email || !pin || !passphrase) {
        setError('supervisor-login-error', 'supervisor-login-error-text', 'Please enter university code, email, PIN, and passphrase.');
        return;
      }

      try {
        const res = await apiRequest('/api/auth/login-supervisor', {
          method: 'POST',
          body: {
            university_code: universityCode,
            email: email,
            pin_code: pin,
            passphrase: passphrase
          }
        });
        const d = (res && res.data) || {};
        if (d.university_id) sessionStorage.setItem('university_id', d.university_id);
        window.location.href = d.dashboard_url || '/home';
      } catch (err) {
        setError('supervisor-login-error', 'supervisor-login-error-text', err && err.message ? err.message : 'Incorrect supervisor credentials.');
      }
    }

    function showAdminEmailStep() {
      const emailStep = document.getElementById('admin-email-step');
      const otpStep = document.getElementById('admin-otp-step');
      const credentialsStep = document.getElementById('admin-credentials-step');
      if (emailStep) emailStep.classList.remove('hidden');
      if (otpStep) otpStep.classList.add('hidden');
      if (credentialsStep) credentialsStep.classList.add('hidden');
      stopAdminOtpTimer();
    }

    function showAdminOtpStep() {
      const emailStep = document.getElementById('admin-email-step');
      const otpStep = document.getElementById('admin-otp-step');
      const credentialsStep = document.getElementById('admin-credentials-step');
      if (emailStep) emailStep.classList.add('hidden');
      if (otpStep) otpStep.classList.remove('hidden');
      if (credentialsStep) credentialsStep.classList.add('hidden');
    }

    function showAdminCredentialsStep() {
      const emailStep = document.getElementById('admin-email-step');
      const otpStep = document.getElementById('admin-otp-step');
      const credentialsStep = document.getElementById('admin-credentials-step');
      if (emailStep) emailStep.classList.add('hidden');
      if (otpStep) otpStep.classList.add('hidden');
      if (credentialsStep) credentialsStep.classList.remove('hidden');
      stopAdminOtpTimer();
    }

    function startAdminOtpTimer() {
      stopAdminOtpTimer();
      adminOtpTimeLeft = 30;
      const timerWrap = document.getElementById('admin-otp-timer');
      const timerCount = document.getElementById('admin-otp-timer-count');
      const resendBtn = document.querySelector('button[onclick="resendAdminOtpCode()"]');
      if (timerWrap) timerWrap.classList.remove('hidden');
      if (resendBtn) resendBtn.classList.add('hidden');
      if (timerCount) timerCount.innerText = String(adminOtpTimeLeft);

      adminOtpTimerInterval = setInterval(function () {
        adminOtpTimeLeft -= 1;
        if (timerCount) timerCount.innerText = String(Math.max(adminOtpTimeLeft, 0));
        if (adminOtpTimeLeft <= 0) {
          stopAdminOtpTimer();
          if (timerWrap) timerWrap.classList.add('hidden');
          if (resendBtn) resendBtn.classList.remove('hidden');
        }
      }, 1000);
    }

    function stopAdminOtpTimer() {
      if (adminOtpTimerInterval) {
        clearInterval(adminOtpTimerInterval);
        adminOtpTimerInterval = null;
      }
    }

    async function handleAdminEmailSubmit(event) {
      event.preventDefault();
      const email = String((document.getElementById('login-admin-email') || {}).value || '').trim();
      setError('admin-email-error', 'admin-email-error-text', '');

      if (!email) {
        setError('admin-email-error', 'admin-email-error-text', 'Please enter your admin email.');
        return;
      }

      try {
        await apiRequest('/api/auth/admin/send-otp', {
          method: 'POST',
          body: { email: email }
        });
        pendingAdminEmail = email;
        showAdminOtpStep();
        startAdminOtpTimer();
        showToast('Verification code sent to your email.', 'success');
      } catch (err) {
        setError('admin-email-error', 'admin-email-error-text', err && err.message ? err.message : 'Could not send verification code.');
      }
    }

    async function handleAdminOtpSubmit(event) {
      event.preventDefault();
      const otp = String((document.getElementById('login-admin-otp') || {}).value || '').trim();
      setError('admin-otp-error', 'admin-otp-error-text', '');

      if (!pendingAdminEmail || !otp) {
        setError('admin-otp-error', 'admin-otp-error-text', 'Enter the verification code sent to your email.');
        return;
      }

      try {
        await apiRequest('/api/auth/admin/verify-otp', {
          method: 'POST',
          body: { email: pendingAdminEmail, otp: otp }
        });
        showAdminCredentialsStep();
        showToast('Verification successful.', 'success');
      } catch (err) {
        setError('admin-otp-error', 'admin-otp-error-text', err && err.message ? err.message : 'Invalid verification code.');
      }
    }

    async function resendAdminOtpCode() {
      if (!pendingAdminEmail) return;
      try {
        await apiRequest('/api/auth/admin/send-otp', {
          method: 'POST',
          body: { email: pendingAdminEmail }
        });
        startAdminOtpTimer();
        showToast('Verification code resent.', 'success');
      } catch (err) {
        setError('admin-otp-error', 'admin-otp-error-text', err && err.message ? err.message : 'Could not resend code.');
      }
    }

    async function handleAdminLogin(event) {
      event.preventDefault();
      const email = (pendingAdminEmail || String((document.getElementById('login-admin-email') || {}).value || '').trim());
      const password = String((document.getElementById('login-admin-password') || {}).value || '').trim();
      setError('admin-login-error', 'admin-login-error-text', '');

      if (!email || !password) {
        setError('admin-login-error', 'admin-login-error-text', 'Please enter your admin email and password.');
        return;
      }

      try {
        const res = await apiRequest('/api/auth/login-admin', {
          method: 'POST',
          body: { email: email, password: password }
        });
        const d = (res && res.data) || {};

        if (d.requires_mfa && d.challenge_id) {
          pendingAdminMfaChallengeId = d.challenge_id;
          pendingAdminMfaUniversityId = d.university_id || null;
          openAdminMfaModal();
          return;
        }

        window.location.href = d.dashboard_url || '/home';
      } catch (err) {
        setError('admin-login-error', 'admin-login-error-text', err && err.message ? err.message : 'Invalid admin credentials.');
      }
    }

    function openAdminMfaModal() {
      const modal = document.getElementById('admin-mfa-modal');
      const input = document.getElementById('admin-mfa-code');
      setError('admin-mfa-error', 'admin-mfa-error-text', '');
      if (input) input.value = '';
      if (modal) modal.classList.remove('hidden');
      if (input) setTimeout(function () { input.focus(); }, 50);
    }

    function closeAdminMfaModal() {
      const modal = document.getElementById('admin-mfa-modal');
      if (modal) modal.classList.add('hidden');
    }

    async function handleAdminMfaSubmit(event) {
      event.preventDefault();
      const code = String((document.getElementById('admin-mfa-code') || {}).value || '').trim();
      setError('admin-mfa-error', 'admin-mfa-error-text', '');

      if (!pendingAdminMfaChallengeId || !code) {
        setError('admin-mfa-error', 'admin-mfa-error-text', 'Enter the verification code sent to your email.');
        return;
      }

      try {
        const res = await apiRequest('/api/auth/admin/verify-mfa', {
          method: 'POST',
          body: {
            challenge_id: pendingAdminMfaChallengeId,
            code: code
          }
        });
        const d = (res && res.data) || {};
        if (d.university_id) {
          sessionStorage.setItem('university_id', d.university_id);
        } else if (pendingAdminMfaUniversityId) {
          sessionStorage.setItem('university_id', pendingAdminMfaUniversityId);
        }
        closeAdminMfaModal();
        window.location.href = d.dashboard_url || '/home';
      } catch (err) {
        setError('admin-mfa-error', 'admin-mfa-error-text', err && err.message ? err.message : 'Unable to verify code.');
      }
    }

    function openDemoRequestModal() {
      const tenantInput = document.getElementById('demo-tenant');
      if (tenantInput && !tenantInput.value) {
        tenantInput.value = getSelectedUniversityCode('login-university-code-student', '');
      }
      const modal = document.getElementById('demo-request-modal');
      if (modal) modal.classList.remove('hidden');
      const nameInput = document.getElementById('demo-name');
      if (nameInput) setTimeout(function () { nameInput.focus(); }, 50);
    }

    function closeDemoRequestModal() {
      const modal = document.getElementById('demo-request-modal');
      if (modal) modal.classList.add('hidden');
    }

    function closeDemoRequestModalOnBackdrop(event) {
      if (event && event.target && event.target.id === 'demo-request-modal') {
        closeDemoRequestModal();
      }
    }

    async function submitDemoRequest(event) {
      event.preventDefault();
      const errorEl = document.getElementById('demo-request-error');
      const name = String((document.getElementById('demo-name') || {}).value || '').trim();
      const email = String((document.getElementById('demo-email') || {}).value || '').trim();
      const tenant = normalizeUniversityCode((document.getElementById('demo-tenant') || {}).value || '');
      const department = String((document.getElementById('demo-department') || {}).value || '').trim();
      const notes = String((document.getElementById('demo-notes') || {}).value || '').trim();

      if (errorEl) {
        errorEl.classList.add('hidden');
        errorEl.innerText = '';
      }

      if (!name || !email || !tenant) {
        if (errorEl) {
          errorEl.innerText = 'Please provide your name, work email, and university code.';
          errorEl.classList.remove('hidden');
        }
        return;
      }

      try {
        await apiRequest('/api/demo/request', {
          method: 'POST',
          body: {
            name: name,
            email: email,
            university_code: tenant,
            department: department || null,
            notes: notes || null
          }
        });
        const form = document.getElementById('demo-request-form');
        if (form) form.reset();
        closeDemoRequestModal();
        showToast('Demo request submitted successfully. We will contact you shortly.', 'success');
      } catch (err) {
        if (errorEl) {
          errorEl.innerText = err && err.message ? err.message : 'Could not submit your demo request right now.';
          errorEl.classList.remove('hidden');
        }
      }
    }

    function showToast(msg, type) {
      const kind = type || 'info';
      const container = document.getElementById('toast-container');
      if (!container) return;

      const toast = document.createElement('div');
      const bgColor = kind === 'success' ? 'bg-emerald-600' : kind === 'error' ? 'bg-rose-600' : 'bg-slate-800';
      toast.className = bgColor + ' text-white px-4 py-2.5 rounded-xl shadow-xl text-xs font-semibold flex items-center gap-2 fade-in pointer-events-auto';
      toast.innerHTML = '<i class="fa-solid ' + (kind === 'success' ? 'fa-circle-check' : kind === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-info') + '"></i><span>' + msg + '</span>';
      container.appendChild(toast);
      setTimeout(function () { toast.remove(); }, 3500);
    }

    document.addEventListener('click', function (event) {
      ['student', 'supervisor', 'admin', 'demo'].forEach(function (type) {
        const dropdown = document.getElementById('university-dropdown-' + type);
        const input = document.getElementById('login-university-code-' + type);
        if (dropdown && !dropdown.contains(event.target) && (!input || !input.contains(event.target))) {
          dropdown.classList.add('hidden');
        }
      });
    });

    window.addEventListener('DOMContentLoaded', function () {
      setGateStatus('Connected to Laravel API — choose your university and log in', true);
      switchLoginTab('student');
      loadUniversities();

      if (window.location.hash === '#request-demo') {
        openDemoRequestModal();
      }
    });
  </script>