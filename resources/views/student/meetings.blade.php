<x-layouts.app title="Meeting Logs | AfriScribe Supervise">

  <x-slot:head>
    @include('partials.dashboards.student-styles')
  </x-slot:head>

  <x-app-header role="student" page-title="Meeting Logs" />

  <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col md:flex-row gap-4 sm:gap-6">

    @include('partials.dashboards.student-sidebar')

    <main class="flex-1 min-w-0 space-y-6">

      <div id="student-drive-card" class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-5 py-4 shadow-sm text-xs text-slate-600 dark:text-slate-300">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="font-semibold text-slate-900 dark:text-white flex items-center gap-2">
              <i class="fa-brands fa-google-drive text-emerald-600"></i>
              Student Drive Folder
            </p>
            <p id="student-drive-text" class="mt-1">No drive folder has been added yet. Once you or your supervisor sets it, it will appear here.</p>
          </div>
          <a id="student-drive-link" href="#" target="_blank" rel="noopener" class="hidden shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 font-semibold text-white transition hover:bg-emerald-700">
            <i class="fa-solid fa-up-right-from-square"></i>
            Open Drive
          </a>
        </div>
      </div>

      <!-- Summary metrics -->
      <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm">
          <p class="text-[10px] uppercase tracking-wider text-slate-400 font-medium">Total Logs</p>
          <p id="metric-total" class="mt-1 text-2xl font-bold text-academic-900 dark:text-white">0</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm">
          <p class="text-[10px] uppercase tracking-wider text-slate-400 font-medium">Approved</p>
          <p id="metric-approved" class="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-400">0</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm">
          <p class="text-[10px] uppercase tracking-wider text-slate-400 font-medium">Awaiting Review</p>
          <p id="metric-pending" class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">0</p>
        </div>
      </div>

      <!-- Logs list -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white text-base">Supervision Logs</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Sessions logged with your supervisor.</p>
          </div>
          <div class="flex items-center gap-2">
            <button onclick="loadMeetings()" title="Refresh" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
              <i id="meetings-refresh-icon" class="fa-solid fa-rotate"></i> Refresh
            </button>
            <button onclick="openMeetingModal()" class="px-3 py-2 bg-academic-700 hover:bg-academic-800 text-white rounded-xl text-xs font-semibold shadow-md transition flex items-center gap-1.5">
              <i class="fa-solid fa-plus"></i> Log Progress Session
            </button>
          </div>
        </div>
        <div id="meetings-list" class="p-5 space-y-4">
          <div class="skeleton h-28 w-full"></div>
          <div class="skeleton h-28 w-full"></div>
        </div>
      </div>
    </main>
  </div>

  <!-- ================= MODAL: LOG PROGRESS SESSION ================= -->
  <div id="modal-meeting" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-4 fade-in">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3 sticky top-0 bg-white dark:bg-slate-800">
        <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
          <i class="fa-solid fa-clipboard-list text-academic-600"></i> Log Progress Session
        </h3>
        <button onclick="closeModal('modal-meeting')" class="text-slate-400 hover:text-slate-600 text-sm"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <form id="form-meeting" onsubmit="handleMeetingSubmit(event)" class="space-y-3 text-xs">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Meeting #</label>
            <input id="m-number" type="number" min="1" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
          </div>
          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Meeting Date</label>
            <input id="m-date" type="date" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
          </div>
          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Mode</label>
            <select id="m-mode" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
              <option value="in_person">In Person</option>
              <option value="virtual">Virtual</option>
              <option value="hybrid">Hybrid</option>
            </select>
          </div>
          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Duration (minutes)</label>
            <input id="m-duration" type="number" min="1" value="30" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
          </div>
        </div>

        <div>
          <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Chapter / Focus Area</label>
          <input id="m-chapter" type="text" placeholder="e.g. Chapter 2 - Literature Review" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
        </div>
        <div>
          <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Actions from Previous Session</label>
          <textarea id="m-previous" rows="2" placeholder="What did you agree to do last time?" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white"></textarea>
        </div>
        <div>
          <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Progress Since Last Session</label>
          <textarea id="m-progress" rows="2" placeholder="What have you completed?" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white"></textarea>
        </div>
        <div>
          <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Discussion Points</label>
          <textarea id="m-discussion" rows="2" placeholder="What was discussed in this session?" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white"></textarea>
        </div>
        <div>
          <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Work Reviewed</label>
          <textarea id="m-work" rows="2" placeholder="What work did your supervisor review?" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Risks / Blockers <span class="text-slate-400 font-normal">(optional)</span></label>
            <textarea id="m-risks" rows="2" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white"></textarea>
          </div>
          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Support Required <span class="text-slate-400 font-normal">(optional)</span></label>
            <textarea id="m-support" rows="2" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white"></textarea>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Next Meeting Date</label>
            <input id="m-next-date" type="date" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
          </div>
          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Next Meeting Focus</label>
            <input id="m-next-focus" type="text" placeholder="e.g. Draft methodology" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
          </div>
        </div>

        <div class="pt-2 flex justify-end gap-2">
          <button type="button" onclick="closeModal('modal-meeting')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold">Cancel</button>
          <button id="m-submit-btn" type="submit" class="px-4 py-2 bg-academic-700 hover:bg-academic-800 text-white rounded-xl font-semibold shadow-md flex items-center gap-2"><span>Submit Log</span></button>
        </div>
      </form>
    </div>
  </div>

  <!-- Toast container -->
  <div id="toast-container" class="fixed bottom-5 right-5 z-[60] space-y-2 pointer-events-none"></div>

  <script>
    const CSRF = '{{ csrf_token() }}';
    const API = '{{ url('/api') }}';
    let meetingsData = [];

    function escapeHtml(str) {
      return String(str ?? '').replace(/[&<>"']/g, c => (
        { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
      ));
    }
    function switchTab() {}

    function renderStudentDrive(student) {
      const text = document.getElementById('student-drive-text');
      const link = document.getElementById('student-drive-link');
      if (!text || !link) return;

      const driveUrl = student.personal_drive_url || '';

      if (driveUrl) {
        text.innerText = 'Your student drive folder is available for meeting notes, drafts, and shared files.';
        link.href = driveUrl;
        link.classList.remove('hidden');
      } else {
        text.innerText = 'No drive folder has been added yet. Once you or your supervisor sets it, it will appear here.';
        link.href = '#';
        link.classList.add('hidden');
      }
    }

    /* ---- toasts ---- */
    function showToast(message, type = 'success') {
      const container = document.getElementById('toast-container');
      if (!container) return;
      const colors = { success: 'bg-emerald-600', error: 'bg-rose-600', info: 'bg-academic-700' };
      const icons = { success: 'fa-circle-check', error: 'fa-circle-exclamation', info: 'fa-circle-info' };
      const toast = document.createElement('div');
      toast.className = `${colors[type] || colors.info} text-white px-4 py-2.5 rounded-xl shadow-xl text-xs font-semibold flex items-center gap-2 fade-in pointer-events-auto`;
      toast.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}"></i><span>${escapeHtml(message)}</span>`;
      container.appendChild(toast);
      setTimeout(() => { toast.style.transition = 'opacity 0.3s ease'; toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3500);
    }

    async function apiGet(path) {
      const res = await fetch(API + path, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
      if (!res.ok) throw new Error('Request failed: ' + res.status);
      return res.json();
    }
    async function apiPost(path, body) {
      const res = await fetch(API + path, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
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

    const STATUS_META = {
      approved:  { label: 'Approved',       cls: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' },
      submitted: { label: 'Awaiting Review',cls: 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' },
      reviewed:  { label: 'Revision Needed',cls: 'bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300' },
      draft:     { label: 'Draft',          cls: 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300' }
    };
    const MODE_LABEL = { in_person: 'In Person', virtual: 'Virtual', hybrid: 'Hybrid' };

    function fmtDate(d) { return d ? new Date(d).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) : '—'; }

    function meetingCard(m) {
      const meta = STATUS_META[m.status] || STATUS_META.draft;
      return `
        <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4 space-y-2">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h4 class="font-semibold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-academic-100 dark:bg-academic-900/40 text-academic-700 dark:text-academic-300 flex items-center justify-center text-[10px] font-bold">${escapeHtml(m.meeting_number)}</span>
                ${escapeHtml(m.chapter_focus || 'Session')}
              </h4>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                ${fmtDate(m.meeting_date)} &middot; ${escapeHtml(MODE_LABEL[m.meeting_mode] || m.meeting_mode || '')} &middot; ${escapeHtml(m.duration || 0)} min
              </p>
            </div>
            <span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold ${meta.cls}">${meta.label}</span>
          </div>
          ${m.progress_since ? `<p class="text-xs text-slate-600 dark:text-slate-300"><span class="font-semibold text-slate-500">Progress:</span> ${escapeHtml(m.progress_since)}</p>` : ''}
          ${m.next_meeting_date ? `<p class="text-[11px] text-slate-500 dark:text-slate-400"><i class="fa-solid fa-calendar-arrow-down mr-1"></i>Next: ${fmtDate(m.next_meeting_date)}${m.next_meeting_focus ? ' — ' + escapeHtml(m.next_meeting_focus) : ''}</p>` : ''}
          ${m.feedback ? `<div class="text-[11px] bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-lg p-2"><span class="font-semibold text-slate-500">Supervisor feedback:</span> ${escapeHtml(m.feedback)}</div>` : ''}
        </div>`;
    }

    function renderMeetings() {
      const list = document.getElementById('meetings-list');
      document.getElementById('metric-total').innerText = meetingsData.length;
      document.getElementById('metric-approved').innerText = meetingsData.filter(m => m.status === 'approved').length;
      document.getElementById('metric-pending').innerText = meetingsData.filter(m => m.status === 'submitted' || m.status === 'draft').length;

      if (!meetingsData.length) {
        list.innerHTML = '<div class="text-center text-slate-400 text-xs py-8"><i class="fa-solid fa-inbox mr-2"></i> No meeting logs yet. Log your first progress session.</div>';
        return;
      }
      list.innerHTML = meetingsData.map(meetingCard).join('');
    }

    async function loadMeetings() {
      const icon = document.getElementById('meetings-refresh-icon');
      icon?.classList.add('fa-spin');
      try {
        const res = await apiGet('/student/meetings');
        meetingsData = res.data || [];
        renderMeetings();
      } catch (err) {
        document.getElementById('meetings-list').innerHTML =
          '<div class="text-center text-rose-400 text-xs py-8"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Could not load meeting logs. Please refresh.</div>';
      } finally {
        icon?.classList.remove('fa-spin');
      }
    }

    async function loadStudentDrive() {
      try {
        const res = await apiGet('/student/profile');
        renderStudentDrive(res.data || {});
      } catch (err) {
        renderStudentDrive({});
      }
    }

    /* ---- modal ---- */
    function openModal(id) { const el = document.getElementById(id); if (el) { el.classList.remove('hidden'); el.classList.add('flex'); } }
    function closeModal(id) { const el = document.getElementById(id); if (el) { el.classList.add('hidden'); el.classList.remove('flex'); } }

    function openMeetingModal() {
      // Suggest the next meeting number and default the date to today.
      const maxNum = meetingsData.reduce((max, m) => Math.max(max, parseInt(m.meeting_number, 10) || 0), 0);
      document.getElementById('m-number').value = maxNum + 1;
      document.getElementById('m-date').value = new Date().toISOString().split('T')[0];
      openModal('modal-meeting');
    }

    async function handleMeetingSubmit(e) {
      e.preventDefault();
      const btn = document.getElementById('m-submit-btn');
      const payload = {
        meeting_number: parseInt(document.getElementById('m-number').value, 10),
        meeting_date: document.getElementById('m-date').value,
        meeting_mode: document.getElementById('m-mode').value,
        duration: parseInt(document.getElementById('m-duration').value, 10),
        chapter_focus: document.getElementById('m-chapter').value.trim(),
        previous_actions: document.getElementById('m-previous').value.trim(),
        progress_since: document.getElementById('m-progress').value.trim(),
        discussion_points: document.getElementById('m-discussion').value.trim(),
        work_reviewed: document.getElementById('m-work').value.trim(),
        risks: document.getElementById('m-risks').value.trim(),
        support_required: document.getElementById('m-support').value.trim(),
        next_meeting_date: document.getElementById('m-next-date').value,
        next_meeting_focus: document.getElementById('m-next-focus').value.trim()
      };

      btn.disabled = true;
      const original = btn.innerHTML;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Submitting...</span>';
      try {
        await apiPost('/student/meetings', payload);
        showToast('Meeting log submitted to your supervisor.', 'success');
        closeModal('modal-meeting');
        document.getElementById('form-meeting').reset();
        loadMeetings();
      } catch (err) {
        showToast(err.message || 'Submission failed.', 'error');
      } finally {
        btn.disabled = false;
        btn.innerHTML = original;
      }
    }

    /* ---- logout ---- */
    function logout() {
      if (!confirm('Are you sure you want to log out?')) return;
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '{{ url('/logout') }}';
      const csrf = document.createElement('input');
      csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = CSRF;
      form.appendChild(csrf);
      document.body.appendChild(form);
      form.submit();
    }

    document.addEventListener('DOMContentLoaded', () => {
      loadMeetings();
      loadStudentDrive();
    });
  </script>
</x-layouts.app>
