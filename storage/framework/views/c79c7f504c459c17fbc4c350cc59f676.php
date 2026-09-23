<script>
  /* ============================================================
   * Student Dashboard — wired to the Laravel student API.
   * Endpoints (all under /api, session-authenticated):
   *   GET  /api/student/dashboard   → profile, supervisor, progress, counts
   *   GET  /api/student/roadmap     → 12-stage lifecycle w/ status
   *   GET  /api/student/proposals   → this student's proposals
   *   POST /api/student/proposals   → submit a new topic proposal
   * ============================================================ */

  const CSRF = '<?php echo e(csrf_token()); ?>';
  const API = '<?php echo e(url('/api')); ?>';

  // Canonical 12-stage list mirrors config/research.php (fallback labels).
  const STAGE_LABELS = [
    'Topic Ideation & Approval',
    'Research Gap Identification',
    'Chapter 1 - Introduction',
    'Chapter 2 - Literature Review',
    'Chapter 3 - Methodology',
    'Chapter 4 - Data Analysis',
    'Chapter 5 - Conclusion',
    'Supervisor Review & Feedback',
    'Revisions & Final Edits',
    'Defense Preparation',
    'Project/Thesis Defense',
    'Project Completion (Sign-off/Graduation)'
  ];

  let studentState = null;      // last dashboard payload
  let topicApproved = false;    // gate on submitting new proposals

  /* ---------- small helpers ---------- */
  function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => (
      { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
    ));
  }
  function getInitials(name) {
    if (!name) return 'ST';
    const p = String(name).trim().split(/\s+/);
    return ((p[0]?.[0] || '') + (p[1]?.[0] || '')).toUpperCase() || 'ST';
  }
  async function apiGet(path) {
    const res = await fetch(API + path, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    });
    if (!res.ok) throw new Error('Request failed: ' + res.status);
    return res.json();
  }
  async function apiPost(path, body) {
    const res = await fetch(API + path, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF
      },
      credentials: 'same-origin',
      body: JSON.stringify(body)
    });
    const json = await res.json().catch(() => ({}));
    if (!res.ok) {
      const msg = json?.message || (json?.errors ? Object.values(json.errors).flat()[0] : 'Request failed');
      throw new Error(msg);
    }
    return json;
  }

  /* ---------- toasts ---------- */
  function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const colors = {
      success: 'bg-emerald-600',
      error: 'bg-rose-600',
      info: 'bg-academic-700'
    };
    const icons = {
      success: 'fa-circle-check',
      error: 'fa-circle-exclamation',
      info: 'fa-circle-info'
    };
    const toast = document.createElement('div');
    toast.className = `${colors[type] || colors.info} text-white px-4 py-2.5 rounded-xl shadow-xl text-xs font-semibold flex items-center gap-2 fade-in pointer-events-auto`;
    toast.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}"></i><span>${escapeHtml(message)}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
      toast.style.transition = 'opacity 0.3s ease';
      toast.style.opacity = '0';
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  }

  /* ---------- loading overlay ---------- */
  function showOverlay(show) {
    const el = document.getElementById('loading-overlay');
    if (!el) return;
    if (show) {
      el.classList.remove('hidden');
      el.style.opacity = '1';
    } else {
      el.style.opacity = '0';
      setTimeout(() => el.classList.add('hidden'), 300);
    }
  }

  /* ---------- tab switching ---------- */
  function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(sec => sec.classList.add('hidden'));
    const active = document.getElementById('tab-' + tabId);
    if (active) {
      active.classList.remove('hidden');
      active.classList.remove('fade-in'); void active.offsetWidth; active.classList.add('fade-in');
    }
    document.querySelectorAll('.nav-btn').forEach(btn => {
      btn.classList.remove('bg-academic-50', 'text-academic-700', 'dark:bg-academic-900/40', 'dark:text-academic-100', 'font-semibold');
      btn.classList.add('text-slate-600', 'dark:text-slate-300', 'font-medium');
    });
    const navBtn = document.getElementById('nav-' + tabId);
    if (navBtn) {
      navBtn.classList.add('bg-academic-50', 'text-academic-700', 'dark:bg-academic-900/40', 'dark:text-academic-100', 'font-semibold');
      navBtn.classList.remove('text-slate-600', 'dark:text-slate-300', 'font-medium');
    }
    closeMobileNav();
  }

  /* ---------- mobile drawer ---------- */
  function openMobileNav() {
    document.getElementById('app-sidebar')?.classList.add('drawer-open');
    document.getElementById('nav-backdrop')?.classList.remove('hidden');
    document.body.classList.add('nav-drawer-locked');
    requestAnimationFrame(() => { const b = document.getElementById('nav-backdrop'); if (b) b.style.opacity = '1'; });
  }
  function closeMobileNav() {
    document.getElementById('app-sidebar')?.classList.remove('drawer-open');
    const b = document.getElementById('nav-backdrop');
    if (b) { b.style.opacity = '0'; setTimeout(() => b.classList.add('hidden'), 250); }
    document.body.classList.remove('nav-drawer-locked');
  }

  /* ---------- roadmap rendering ---------- */
  function renderStudentRoadmap(currentStage, roadmap) {
    const grid = document.getElementById('student-roadmap-grid');
    if (!grid) return;
    const total = STAGE_LABELS.length;
    const current = Math.min(Math.max(parseInt(currentStage, 10) || 1, 1), total);
    grid.innerHTML = '';

    for (let n = 1; n <= total; n++) {
      const label = (roadmap && roadmap[n - 1] && roadmap[n - 1].name) || STAGE_LABELS[n - 1];
      let stateClass, icon, statusLabel;
      if (n < current) {
        stateClass = 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-300';
        icon = '<i class="fa-solid fa-circle-check text-emerald-600"></i>';
        statusLabel = 'Completed';
      } else if (n === current) {
        stateClass = 'bg-amber-50 dark:bg-amber-900/30 border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-300 ring-2 ring-amber-400/50 stage-active';
        icon = '<i class="fa-solid fa-spinner animate-spin text-amber-600"></i>';
        statusLabel = 'In Progress';
      } else {
        stateClass = 'bg-slate-50 dark:bg-slate-700/40 border-slate-200 dark:border-slate-700 text-slate-400';
        icon = '<i class="fa-solid fa-lock text-slate-400"></i>';
        statusLabel = 'Locked';
      }
      const cell = document.createElement('div');
      cell.className = 'p-3 rounded-xl border flex flex-col ' + stateClass;
      cell.innerHTML =
        '<div class="flex justify-between items-center font-bold mb-1"><span>Stage ' + n + '</span>' + icon + '</div>' +
        '<p class="text-[11px] font-medium flex-1">' + escapeHtml(label) + '</p>' +
        '<span class="text-[10px] font-medium block mt-2">' + statusLabel + '</span>';
      grid.appendChild(cell);
    }
  }

  /* ---------- proposals rendering ---------- */
  function statusBadge(status) {
    const map = {
      approved: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300',
      pending: 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300',
      revision_required: 'bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300',
      conditional: 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300'
    };
    return map[status] || map.pending;
  }
  function renderProposals(proposals) {
    const list = document.getElementById('student-proposals-list');
    const count = document.getElementById('nav-proposals-count');
    if (count) count.innerText = proposals.length;
    if (!list) return;
    if (!proposals.length) {
      list.innerHTML = '<div class="text-center text-slate-400 text-xs py-6"><i class="fa-solid fa-inbox mr-2"></i> No proposals yet. Submit your first research topic.</div>';
      return;
    }
    list.innerHTML = proposals.map(p => {
      const st = String(p.status || 'pending');
      const label = st.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
      const date = p.date_submitted ? new Date(p.date_submitted).toLocaleDateString() : '';
      return `<div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <h4 class="font-semibold text-sm text-slate-900 dark:text-white">${escapeHtml(p.title)}</h4>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">${escapeHtml(p.location || '')} &middot; ${escapeHtml(date)}</p>
          </div>
          <span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold ${statusBadge(st)}">${escapeHtml(label)}</span>
        </div>
        ${p.abstract ? `<p class="text-xs text-slate-600 dark:text-slate-300 mt-2 line-clamp-3">${escapeHtml(p.abstract)}</p>` : ''}
        ${p.supervisor_comment ? `<div class="mt-2 text-[11px] bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-lg p-2"><span class="font-semibold text-slate-500">Supervisor:</span> ${escapeHtml(p.supervisor_comment)}</div>` : ''}
      </div>`;
    }).join('');
  }

  /* ---------- bind dashboard payload to the DOM ---------- */
  function bindDashboard(data) {
    studentState = data;
    const student = data.student || {};
    const supervisor = data.supervisor || {};
    const progress = data.progress || {};

    const name = student.full_name || 'Student';
    document.getElementById('student-portal-name').innerText = name;
    document.getElementById('student-portal-avatar').innerText = getInitials(name);
    document.getElementById('student-portal-matric').innerText = 'MATRIC: ' + (student.matric_number || '—');

    // Header user chip
    const hName = document.getElementById('header-user-name');
    const hAvatar = document.getElementById('header-user-avatar');
    const hRole = document.getElementById('header-user-role');
    if (hName) hName.innerText = name;
    if (hAvatar) hAvatar.innerText = getInitials(name);
    if (hRole) hRole.innerText = student.degree_level ? (student.degree_level + ' Student') : 'Research Student';

    // Supervisor
    document.getElementById('student-supervisor-name').innerText = supervisor.name || '—';
    document.getElementById('student-supervisor-email').innerText = supervisor.email || '—';
    document.getElementById('sidebar-supervisor-name').innerText = supervisor.name || 'Supervisor';
    document.getElementById('sidebar-supervisor-title').innerText = supervisor.title || 'Research Supervisor';
    document.getElementById('sidebar-supervisor-dept').innerText = supervisor.department || 'Department';
    document.getElementById('sidebar-supervisor-email').innerText = supervisor.email || '—';
    document.getElementById('sidebar-supervisor-avatar').innerText = getInitials(supervisor.name);

    // Topic + status
    const topic = student.research_topic;
    topicApproved = Boolean(student.research_topic_approved_date);
    const topicEl = document.getElementById('student-active-topic');
    const statusEl = document.getElementById('student-topic-status');
    if (topic) {
      topicEl.innerText = '"' + topic + '"';
    } else {
      topicEl.innerText = '"Topic pending approval"';
    }
    if (topicApproved) {
      statusEl.innerText = 'APPROVED';
      statusEl.className = 'px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-semibold text-[10px]';
      const appr = document.getElementById('student-topic-approved');
      appr.classList.remove('hidden');
      document.getElementById('student-topic-approved-date').innerText =
        new Date(student.research_topic_approved_date).toLocaleDateString();
    } else {
      statusEl.innerText = 'PENDING';
      statusEl.className = 'px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-semibold text-[10px]';
    }

    // Milestone + progress
    const stageName = progress.stage_name || (data.current_stage && data.current_stage.name) || '—';
    document.getElementById('student-milestone').innerText = stageName;
    document.getElementById('metric-stage').innerText = stageName;

    const pct = Math.round(progress.percentage || student.progress_percentage || 0);
    document.getElementById('student-progress-bar').style.width = pct + '%';
    document.getElementById('student-progress-pct').innerText = pct + '%';
    document.getElementById('metric-progress').innerText = pct + '%';
    document.getElementById('metric-points').innerText = progress.points ?? student.points_earned ?? 0;
    document.getElementById('metric-meetings').innerText = (data.recent_meetings || []).length;

    // Roadmap uses the student's current stage
    renderStudentRoadmap(student.current_stage || 1, data.roadmap);

    // Gate the submit-topic buttons if a topic is already approved
    updateSubmitTopicButtonState();
  }

  function updateSubmitTopicButtonState() {
    ['btn-submit-topic', 'btn-submit-topic-2'].forEach(id => {
      const btn = document.getElementById(id);
      if (!btn) return;
      if (topicApproved) {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        btn.title = 'Topic already approved';
      } else {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        btn.title = '';
      }
    });
  }

  /* ---------- data loading ---------- */
  async function loadStudentData(isRefresh = false) {
    const refreshIcon = document.getElementById('refresh-icon');
    if (isRefresh && refreshIcon) refreshIcon.classList.add('fa-spin');
    else showOverlay(true);

    try {
      const [dashRes, roadmapRes] = await Promise.all([
        apiGet('/student/dashboard'),
        apiGet('/student/roadmap').catch(() => null)
      ]);
      const data = dashRes.data || {};
      if (roadmapRes && roadmapRes.data) data.roadmap = roadmapRes.data;

      bindDashboard(data);

      // Reveal live content, hide skeleton
      document.getElementById('student-loading')?.classList.add('hidden');
      document.getElementById('student-live')?.classList.remove('hidden');

      // Proposals load independently (non-blocking)
      loadProposals();

      if (isRefresh) showToast('Dashboard updated', 'success');
    } catch (err) {
      console.error(err);
      showToast('Could not load your dashboard. Please refresh.', 'error');
      // Still reveal the shell so the user isn't stuck on skeletons
      document.getElementById('student-loading')?.classList.add('hidden');
      document.getElementById('student-live')?.classList.remove('hidden');
    } finally {
      if (isRefresh && refreshIcon) refreshIcon.classList.remove('fa-spin');
      else showOverlay(false);
    }
  }

  async function loadProposals() {
    try {
      const res = await apiGet('/student/proposals');
      renderProposals(res.data || []);
    } catch (err) {
      const list = document.getElementById('student-proposals-list');
      if (list) list.innerHTML = '<div class="text-center text-rose-400 text-xs py-6"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Could not load proposals.</div>';
    }
  }

  /* ---------- modal control ---------- */
  function openModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('hidden');
    el.classList.add('flex');
  }
  function closeModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hidden');
    el.classList.remove('flex');
  }

  function openSubmitTopicModal() {
    if (topicApproved) {
      showToast('Your topic is already approved. You cannot submit another proposal.', 'error');
      return;
    }
    const disp = document.getElementById('proposal-student-display');
    const student = studentState?.student;
    if (disp) disp.value = student ? `${student.full_name} (${student.matric_number})` : '—';
    openModal('modal-submit-topic');
  }

  async function handleSubmitProposal(e) {
    e.preventDefault();
    const btn = document.getElementById('proposal-submit-btn');
    const title = document.getElementById('proposal-title').value.trim();
    const location = document.getElementById('proposal-location').value.trim();
    const abstract = document.getElementById('proposal-abstract').value.trim();
    if (!title || !location || !abstract) {
      showToast('Please fill in all fields.', 'error');
      return;
    }
    btn.disabled = true;
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Submitting...</span>';
    try {
      await apiPost('/student/proposals', { title, location, abstract });
      showToast('Proposal submitted to your supervisor.', 'success');
      closeModal('modal-submit-topic');
      document.getElementById('form-submit-proposal').reset();
      loadProposals();
    } catch (err) {
      showToast(err.message || 'Submission failed.', 'error');
    } finally {
      btn.disabled = false;
      btn.innerHTML = original;
    }
  }

  /* ---------- logout ---------- */
  function logout() {
    if (!confirm('Are you sure you want to log out?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo e(url('/logout')); ?>';
    const csrf = document.createElement('input');
    csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = CSRF;
    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
  }

  /* ---------- boot ---------- */
  document.addEventListener('DOMContentLoaded', () => loadStudentData(false));
</script>
<?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/partials/dashboards/student-script.blade.php ENDPATH**/ ?>