@extends('layouts.supervisor')

@section('title', 'Topic Approvals | Research Supervision Portal | LASU')

@section('content')
<section class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Topic Approvals</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Review student submissions assigned to your supervision roster.</p>
            </div>
            <div class="flex gap-2 text-xs font-semibold">
                <span class="px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">Pending: {{ $proposals->where('status', 'pending')->count() }}</span>
                <span class="px-3 py-1.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Conditional: {{ $proposals->where('status', 'conditional')->count() }}</span>
                <span class="px-3 py-1.5 rounded-full bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">Revision Required: {{ $proposals->where('status', 'revision_required')->count() }}</span>
                <span class="px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Approved: {{ $proposals->where('status', 'approved')->count() }}</span>
            </div>
        </div>
    </div>

    <div id="proposals-container" class="space-y-4">
        @forelse ($proposals as $proposal)
            <article class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden" data-proposal-id="{{ $proposal->id }}">
                <!-- Proposal Header (always visible) -->
                <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/20">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200">{{ $proposal->proposal_id }}</span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                            @if($proposal->status === 'approved') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300
                            @elseif($proposal->status === 'conditional') bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300
                            @elseif($proposal->status === 'revision_required') bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300
                            @else bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300
                            @endif">
                            {{ str_replace('_', ' ', ucfirst($proposal->status)) }}
                        </span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ optional($proposal->date_submitted)->diffForHumans() ?? 'No submission date' }}</span>
                    </div>

                    <div class="mt-3">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $proposal->title }}</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $proposal->student->full_name ?? 'Unknown student' }} · {{ $proposal->student->matric_number ?? 'No matric number' }}</p>
                    </div>

                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Location Focus</p>
                            <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $proposal->location }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Student Email</p>
                            <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $proposal->student->email ?? 'No student email' }}</p>
                        </div>
                    </div>

                    <!-- Abstract preview (hint of content) -->
                    <div class="mt-3">
                        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Abstract</p>
                        <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-300 line-clamp-2" id="abstract-preview-{{ $proposal->id }}">{{ $proposal->abstract }}</p>
                    </div>

                    @if ($proposal->supervisor_comment)
                        <div class="mt-3 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Supervisor Comment</p>
                            <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $proposal->supervisor_comment }}</p>
                        </div>
                    @endif

                    @if ($proposal->status === 'conditional' && $proposal->conditions)
                        <div class="mt-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4">
                            <p class="text-xs uppercase tracking-wide text-blue-800 dark:text-blue-300 font-semibold">Conditions:</p>
                            <ul class="mt-1 text-xs text-blue-700 dark:text-blue-400 list-disc list-inside">
                                @foreach($proposal->conditions as $condition)
                                    <li>{{ $condition }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Expand/Collapse Button -->
                    <button type="button"
                        class="mt-3 inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
                        onclick="toggleProposal({{ $proposal->id }})"
                        aria-expanded="false"
                        aria-controls="proposal-details-{{ $proposal->id }}">
                        <i class="fa-solid fa-chevron-down transition-transform duration-200" id="chevron-{{ $proposal->id }}"></i>
                        <span id="toggle-text-{{ $proposal->id }}">Show details</span>
                    </button>
                </div>

                <!-- Collapsible Details Section -->
                <div id="proposal-details-{{ $proposal->id }}" class="hidden p-5 space-y-4">
                    <div class="prose prose-sm dark:prose-invert max-w-none">
                        <p class="text-sm leading-7 text-slate-700 dark:text-slate-300">{{ $proposal->abstract }}</p>
                    </div>

                    @if ($proposal->topicHistory && $proposal->topicHistory->count() > 0)
                        <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                            <h4 class="font-semibold text-slate-900 dark:text-white mb-3">Revision History</h4>
                            <div class="space-y-3">
                                @foreach($proposal->topicHistory as $history)
                                    <div class="p-3 bg-slate-50 dark:bg-slate-900/30 rounded-lg border border-slate-200 dark:border-slate-700">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex items-center gap-2 text-xs">
                                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ ucfirst(str_replace('_', ' ', $history->action)) }}</span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">{{ optional($history->created_at)->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        @if($history->note)
                                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">{{ $history->note }}</p>
                                        @endif
                                        @if($history->topic_title)
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $history->topic_title }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($proposal->status === 'pending' || $proposal->status === 'conditional' || $proposal->status === 'revision_required')
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700 flex flex-wrap gap-2">
                            @if ($proposal->status !== 'approved')
                                <button type="button"
                                    data-proposal-id="{{ $proposal->id }}"
                                    data-action="approve"
                                    class="px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 text-xs font-semibold hover:bg-emerald-200 transition-colors">
                                    Approve
                                </button>
                            @endif

                            @if ($proposal->status !== 'revision_required')
                                <button type="button"
                                    data-proposal-id="{{ $proposal->id }}"
                                    data-action="reject"
                                    class="px-3 py-1.5 rounded-lg bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300 text-xs font-semibold hover:bg-rose-200 transition-colors">
                                    Request Revision
                                </button>
                            @endif

                            @if ($proposal->status !== 'conditional')
                                <button type="button"
                                    data-proposal-id="{{ $proposal->id }}"
                                    data-action="conditional"
                                    class="px-3 py-1.5 rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 text-xs font-semibold hover:bg-blue-200 transition-colors">
                                    Conditional Approval
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-dashed border-slate-300 dark:border-slate-700 shadow-sm text-center">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">No proposals found</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">New topic submissions assigned to you will appear here.</p>
            </div>
        @endforelse
    </div>

    @if($proposals->hasPages())
    <div class="px-4 py-3">
        {{ $proposals->links() }}
    </div>
    @endif
</section>

<!-- Decision Modal -->
<div id="decision-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
            <h3 id="decision-modal-title" class="font-bold text-slate-900 dark:text-white">Proposal Decision</h3>
            <button onclick="closeDecisionModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="decision-form" onsubmit="submitDecision(event)" class="space-y-4 mt-4">
            <input type="hidden" id="decision-action" name="action" value="">
            <input type="hidden" id="decision-proposal-id" name="proposal_id" value="">

            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Comment <span class="text-red-500">*</span></label>
                <textarea id="decision-comment" rows="3" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white" placeholder="Provide your feedback..."></textarea>
            </div>

            <div id="conditions-field" class="hidden">
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Conditions <span class="text-xs text-slate-500 dark:text-slate-400">(one per line)</span></label>
                <textarea id="decision-conditions" rows="3" class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white" placeholder="e.g. Clarify the research methodology"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeDecisionModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-academic-700 hover:bg-academic-800 text-white rounded-xl font-semibold">Submit Decision</button>
            </div>
        </form>
    </div>
</div>

<div id="toast-container" class="fixed bottom-5 right-5 z-[60] space-y-2 pointer-events-none"></div>

<script>
    const CSRF = '{{ csrf_token() }}';
    const API = '{{ url('/api') }}';

                                                                                function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, c => ({ '&':'&','<':'<','>':'>','"':'"',"'":''' }[c]));
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

    async function apiRequest(path, method, body) {
        const res = await fetch(API + path, {
            method: method,
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

    let pendingDecision = null;

    function toggleProposal(proposalId) {
        const details = document.getElementById('proposal-details-' + proposalId);
        const chevron = document.getElementById('chevron-' + proposalId);
        const toggleText = document.getElementById('toggle-text-' + proposalId);
        const btn = event.currentTarget;

        if (details.classList.contains('hidden')) {
            details.classList.remove('hidden');
            chevron.style.transform = 'rotate(180deg)';
            toggleText.textContent = 'Hide details';
            btn.setAttribute('aria-expanded', 'true');
        } else {
            details.classList.add('hidden');
            chevron.style.transform = 'rotate(0deg)';
            toggleText.textContent = 'Show details';
            btn.setAttribute('aria-expanded', 'false');
        }
    }

    function openDecisionModal(proposalId, action, proposalTitle) {
        pendingDecision = { proposalId, action, proposalTitle };
        const modal = document.getElementById('decision-modal');
        const title = document.getElementById('decision-modal-title');
        const needsConditions = action === 'conditional';

        const actionLabels = {
            'approve': 'Approve',
            'reject': 'Request Revision',
            'conditional': 'Conditional Approval'
        };

        title.textContent = actionLabels[action] + ': ' + proposalTitle;

        document.getElementById('decision-action').value = action;
        document.getElementById('decision-proposal-id').value = proposalId;
        document.getElementById('decision-comment').value = '';
        document.getElementById('decision-conditions').value = '';
        document.getElementById('conditions-field').classList.toggle('hidden', !needsConditions);

        if (modal) modal.classList.remove('hidden');
    }

    function closeDecisionModal() {
        const modal = document.getElementById('decision-modal');
        if (modal) modal.classList.add('hidden');
        pendingDecision = null;
    }

    async function submitDecision(event) {
        event.preventDefault();

        const action = document.getElementById('decision-action').value;
        const proposalId = document.getElementById('decision-proposal-id').value;
        const comment = document.getElementById('decision-comment').value.trim();
        const conditions = document.getElementById('decision-conditions').value.trim();

        const isDecision = action !== 'commented';

        if (isDecision && !comment) {
            showToast('A comment is required for this action.', 'error');
            return;
        }

        if (action === 'conditional' && !conditions) {
            showToast('At least one condition is required for conditional approval.', 'error');
            return;
        }

        const endpointMap = {
            'approve': { path: '/supervisor/proposals/' + proposalId + '/approve', method: 'PATCH' },
            'reject': { path: '/supervisor/proposals/' + proposalId + '/reject', method: 'PATCH' },
            'conditional': { path: '/supervisor/proposals/' + proposalId + '/request-revision', method: 'PATCH' },
        };

        const body = {
            comment: comment || null,
        };

        if (action === 'conditional') {
            body.conditions = conditions.split('\n').map(c => c.trim()).filter(c => c.length > 0);
        }

        try {
            await apiRequest(endpointMap[action].path, endpointMap[action].method, body);
            showToast('Decision recorded successfully.', 'success');
            closeDecisionModal();
            setTimeout(() => window.location.reload(), 1500);
        } catch (err) {
            showToast(err.message || 'Failed to record decision.', 'error');
        }
    }

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-proposal-id][data-action]');
        if (btn) {
            const proposalId = btn.getAttribute('data-proposal-id');
            const action = btn.getAttribute('data-action');
            const proposalTitle = btn.closest('article').querySelector('h2')?.textContent || 'Proposal';
            openDecisionModal(proposalId, action, proposalTitle);
        }
    });

    // Close modal on backdrop click
    document.getElementById('decision-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeDecisionModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDecisionModal();
        }
    });
</script>
@endsection