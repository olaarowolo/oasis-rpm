@extends('layouts.supervisor')

@section('title', 'Manuscript Review | Research Supervision Portal')

@section('content')
<div class="space-y-6">
    @if($document->status === 'halted')
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-red-600 dark:text-red-400 mt-0.5"></i>
                <div>
                    <h3 class="font-semibold text-red-900 dark:text-red-300">Manuscript Halted</h3>
                    <p class="text-sm text-red-800 dark:text-red-400 mt-1">{{ $document->halted_reason }}</p>
                    <p class="text-xs text-red-700 dark:text-red-400 mt-1">Issuing <em>Request revision</em> on the halted section will release the halt and make it editable again.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $document->student->full_name ?? 'Unknown Student' }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Matric: {{ $document->student->matric_number ?? '' }} · {{ $document->student->programme ?? '' }}</p>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="px-2.5 py-1 rounded-full
                    @if($document->status === 'draft') bg-slate-100 text-slate-700 dark:bg-slate-700
                    @elseif($document->status === 'in_review') bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300
                    @elseif($document->status === 'in_revision') bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300
                    @elseif($document->status === 'halted') bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300
                    @elseif($document->status === 'completed') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300
                    @endif">
                    {{ ucfirst($document->status) }}
                </span>
            </div>
        </div>
    </div>

    <div id="sections-container" class="space-y-4">
        @foreach($document->sections->where('parent_id', null) as $section)
            @include('supervisor.manuscripts.partials.section-card', ['section' => $section, 'document' => $document])
        @endforeach
    </div>

    <div id="toast-container" class="fixed bottom-5 right-5 z-[60] space-y-2 pointer-events-none"></div>
</div>

<!-- Decision Modal -->
<div id="decision-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
            <h3 id="decision-modal-title" class="font-bold text-slate-900 dark:text-white">Section Decision</h3>
            <button onclick="closeDecisionModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="decision-form" onsubmit="submitDecision(event)" class="space-y-4 mt-4">
            <input type="hidden" id="decision-action" name="action" value="">
            <input type="hidden" id="decision-section-id" name="section_id" value="">

            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Comment <span class="text-red-500">*</span></label>
                <textarea id="decision-comment" rows="3" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white" placeholder="Provide your feedback..."></textarea>
            </div>

            <div id="conditions-field" class="hidden">
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Conditions <span class="text-xs text-slate-500 dark:text-slate-400">(one per line)</span></label>
                <textarea id="decision-conditions" rows="3" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white" placeholder="e.g. Revise the methodology to include sample size justification"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeDecisionModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-academic-700 hover:bg-academic-800 text-white rounded-xl font-semibold">Record Decision</button>
            </div>
        </form>
    </div>
</div>

<script>
    const CSRF = '{{ csrf_token() }}';
    const API = '{{ url('/api') }}';
    const DOCUMENT_ID = {{ $document->id }};
    const IS_HALTED = {{ $document->status === 'halted' ? 'true' : 'false' }};

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
    }

    function showToast(message, type) {
        type = type || 'success';
        const container = document.getElementById('toast-container');
        if (!container) return;
        const colors = { success: 'bg-emerald-600', error: 'bg-rose-600', info: 'bg-academic-700' };
        const icons = { success: 'fa-circle-check', error: 'fa-circle-exclamation', info: 'fa-circle-info' };
        const toast = document.createElement('div');
        toast.className = (colors[type] || colors.info) + ' text-white px-4 py-2.5 rounded-xl shadow-xl text-xs font-semibold flex items-center gap-2 fade-in pointer-events-auto';
        toast.innerHTML = '<i class="fa-solid ' + (icons[type] || icons.info) + '"></i><span>' + escapeHtml(message) + '</span>';
        container.appendChild(toast);
        setTimeout(() => { toast.style.transition = 'opacity 0.3s ease'; toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3500);
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

    let pendingAction = null;

    function openDecisionModal(sectionId, action, sectionTitle) {
        pendingAction = { sectionId, action, sectionTitle };
        const modal = document.getElementById('decision-modal');
        const title = document.getElementById('decision-modal-title');
        const needsConditions = action === 'conditional';

        title.textContent = action.charAt(0).toUpperCase() + action.slice(1) + ': ' + sectionTitle;

        document.getElementById('decision-action').value = action;
        document.getElementById('decision-section-id').value = sectionId;
        document.getElementById('decision-comment').value = '';
        document.getElementById('decision-conditions').value = '';
        document.getElementById('conditions-field').classList.toggle('hidden', !needsConditions);

        if (modal) modal.classList.remove('hidden');
    }

    function closeDecisionModal() {
        const modal = document.getElementById('decision-modal');
        if (modal) modal.classList.add('hidden');
        pendingAction = null;
    }

    async function submitDecision() {
        const action = document.getElementById('decision-action').value;
        const sectionId = document.getElementById('decision-section-id').value;
        const comment = document.getElementById('decision-comment').value.trim();
        const conditions = document.getElementById('decision-conditions').value.trim();

        const isDecision = action !== 'commented';

        if (isDecision && !comment) {
            showToast('A comment is required for this action.', 'error');
            return;
        }

        try {
            await apiPost('/supervisor/sections/' + sectionId + '/decision', {
                action: action,
                comment: comment || null,
                conditions: conditions || null,
            });
            showToast('Decision recorded.', 'success');
            closeDecisionModal();
            setTimeout(() => window.location.reload(), 1500);
        } catch (err) {
            showToast(err.message || 'Failed to record decision.', 'error');
        }
    }

    function openCommentModal(sectionId, sectionTitle) {
        pendingAction = { sectionId, action: 'commented', sectionTitle };
        document.getElementById('decision-modal-title').textContent = 'Comment: ' + sectionTitle;
        document.getElementById('decision-action').value = 'commented';
        document.getElementById('decision-section-id').value = sectionId;
        document.getElementById('decision-comment').value = '';
        document.getElementById('decision-conditions').value = '';
        document.getElementById('conditions-field').classList.add('hidden');
        document.getElementById('decision-modal').classList.remove('hidden');
    }

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-decision-btn]');
        if (btn) {
            const sectionId = btn.getAttribute('data-section-id');
            const action = btn.getAttribute('data-decision-action');
            const sectionTitle = btn.getAttribute('data-section-title');
            openDecisionModal(sectionId, action, sectionTitle);
        }

        const commentBtn = e.target.closest('[data-comment-btn]');
        if (commentBtn) {
            const sectionId = commentBtn.getAttribute('data-section-id');
            const sectionTitle = commentBtn.getAttribute('data-section-title');
            openCommentModal(sectionId, sectionTitle);
        }
    });
</script>
@endsection
