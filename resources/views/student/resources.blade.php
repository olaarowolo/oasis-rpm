<x-layouts.app title="Learning Resources | TheOAsis Research Supervision System">

  <x-slot:head>
    @include('partials.dashboards.student-styles')
  </x-slot:head>

  @include('partials.dashboards.student-header')

  <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col md:flex-row gap-4 sm:gap-6">

    @include('partials.dashboards.student-sidebar')

    <main class="flex-1 min-w-0 space-y-6">
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-6">
        <div class="flex items-center justify-between gap-3">
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
              <i class="fa-solid fa-graduation-cap text-academic-600"></i> Learning Resources
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Complete resources to earn points and unlock later stages.</p>
          </div>
          <button onclick="loadResources()" title="Refresh" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
            <i id="resources-refresh-icon" class="fa-solid fa-rotate"></i> Refresh
          </button>
        </div>

        <div id="resources-container" class="space-y-6">
          <!-- Loading skeleton -->
          <div class="skeleton h-24 w-full"></div>
          <div class="skeleton h-24 w-full"></div>
          <div class="skeleton h-24 w-full"></div>
        </div>
      </div>
    </main>
  </div>

  <!-- Toast container -->
  <div id="toast-container" class="fixed bottom-5 right-5 z-[60] space-y-2 pointer-events-none"></div>

  <script>
    const CSRF = '{{ csrf_token() }}';
    const API = '{{ url('/api') }}';

    function escapeHtml(str) {
      return String(str ?? '').replace(/[&<>"']/g, c => (
        { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
      ));
    }
    // switchTab is a no-op here (single-page); nav links navigate via href.
    function switchTab() {}

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
      const res = await fetch(API + path, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      });
      if (!res.ok) throw new Error('Request failed: ' + res.status);
      return res.json();
    }
    async function apiPost(path) {
      const res = await fetch(API + path, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
        credentials: 'same-origin'
      });
      const json = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(json?.message || 'Request failed');
      return json;
    }

    const TYPE_ICON = { video: 'fa-play', document: 'fa-file-lines', link: 'fa-link', worksheet: 'fa-list-check' };
    const STATUS_META = {
      approved:  { label: 'Completed',        cls: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' },
      submitted: { label: 'Awaiting Review',  cls: 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' },
      rejected:  { label: 'Needs Rework',     cls: 'bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300' },
      pending:   { label: 'Not Started',      cls: 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }
    };

    function resourceCard(r) {
      const meta = STATUS_META[r.progress_status] || STATUS_META.pending;
      const icon = TYPE_ICON[r.type] || 'fa-book-open';
      const canComplete = r.progress_status === 'pending' || r.progress_status === 'rejected';
      return `
        <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4 flex items-start gap-3">
          <div class="w-10 h-10 shrink-0 rounded-lg bg-academic-100 dark:bg-academic-900/30 text-academic-600 dark:text-academic-400 flex items-center justify-center">
            <i class="fa-solid ${icon}"></i>
          </div>
          <div class="min-w-0 flex-1">
            <div class="flex items-start justify-between gap-2">
              <div class="min-w-0">
                <h4 class="font-semibold text-sm text-slate-900 dark:text-white">${escapeHtml(r.title)}</h4>
                ${r.description ? `<p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">${escapeHtml(r.description)}</p>` : ''}
              </div>
              <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold ${meta.cls}">${meta.label}</span>
            </div>
            <div class="flex items-center flex-wrap gap-2 mt-2 text-[11px]">
              ${r.is_mandatory ? '<span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 font-semibold">Mandatory</span>' : ''}
              <span class="text-slate-400">${r.points ?? 0} pts</span>
              ${r.url ? `<a href="${escapeHtml(r.url)}" target="_blank" rel="noopener" class="text-academic-600 dark:text-academic-400 font-semibold hover:underline"><i class="fa-solid fa-up-right-from-square mr-1"></i>Open</a>` : ''}
              ${canComplete ? `<button onclick="markComplete(${r.id}, this)" class="ml-auto px-2.5 py-1 rounded-lg bg-academic-700 hover:bg-academic-800 text-white font-semibold transition">Mark Complete</button>` : ''}
            </div>
          </div>
        </div>`;
    }

    function renderResources(resources) {
      const container = document.getElementById('resources-container');
      if (!resources.length) {
        container.innerHTML = '<div class="text-center text-slate-400 text-xs py-8"><i class="fa-solid fa-inbox mr-2"></i> No learning resources have been added yet.</div>';
        return;
      }
      // Group by stage
      const byStage = {};
      resources.forEach(r => { (byStage[r.stage] = byStage[r.stage] || []).push(r); });
      const stages = Object.keys(byStage).sort((a, b) => a - b);
      container.innerHTML = stages.map(stage => `
        <div class="space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-academic-100 dark:bg-academic-900/40 text-academic-700 dark:text-academic-300 flex items-center justify-center text-[10px]">${stage}</span>
            Stage ${stage}
          </h4>
          ${byStage[stage].map(resourceCard).join('')}
        </div>
      `).join('');
    }

    async function loadResources() {
      const icon = document.getElementById('resources-refresh-icon');
      icon?.classList.add('fa-spin');
      try {
        const res = await apiGet('/student/resources');
        renderResources(res.data || []);
      } catch (err) {
        document.getElementById('resources-container').innerHTML =
          '<div class="text-center text-rose-400 text-xs py-8"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Could not load resources. Please refresh.</div>';
      } finally {
        icon?.classList.remove('fa-spin');
      }
    }

    async function markComplete(id, btn) {
      if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>'; }
      try {
        await apiPost('/student/resources/' + id + '/complete');
        showToast('Submitted for supervisor review.', 'success');
        loadResources();
      } catch (err) {
        showToast(err.message || 'Could not submit.', 'error');
        if (btn) { btn.disabled = false; btn.innerHTML = 'Mark Complete'; }
      }
    }

    /* ---- logout (used by sidebar) ---- */
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

    document.addEventListener('DOMContentLoaded', loadResources);
  </script>
</x-layouts.app>
