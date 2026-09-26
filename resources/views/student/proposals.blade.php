@extends('layouts.student')

@section('title', 'My Proposals')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">My Proposals</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Track topic submissions, approval status, and supervisor feedback.</p>
            </div>
            <a href="{{ route('student.dashboard', ['tab' => 'student-proposals']) }}" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i>
                Dashboard
            </a>
        </div>

        <div class="mt-4">
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 rounded-xl bg-academic-700 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-academic-800">
                <i class="fa-solid fa-plus"></i>
                Create New Proposal
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-900/40">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Date Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($proposals as $proposal)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $proposal->title }}</div>
                            <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">ID: {{ $proposal->proposal_id }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ optional($proposal->date_submitted)->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $proposal->location }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($proposal->status === 'approved') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300
                                @elseif($proposal->status === 'conditional') bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300
                                @elseif($proposal->status === 'revision_required') bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300
                                @else bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300
                                @endif">
                                {{ str_replace('_', ' ', ucfirst($proposal->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button type="button"
                                    onclick="openDetailModal({{ $proposal->id }})"
                                    class="px-3 py-1.5 text-xs font-medium text-academic-700 hover:text-academic-800 dark:text-academic-400 transition-colors">
                                    View
                                </button>

                                @if(in_array($proposal->status, ['revision_required', 'conditional']))
                                    <button type="button"
                                        onclick="openEditModal({{ $proposal->id }})"
                                        class="px-3 py-1.5 text-xs font-medium text-slate-700 hover:text-slate-900 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg transition-colors">
                                        Edit
                                    </button>
                                @endif

                                @if($proposal->status !== 'approved')
                                    <button type="button"
                                        onclick="confirmDelete({{ $proposal->id }}, '{{ addslashes($proposal->title) }}')"
                                        class="px-3 py-1.5 text-xs font-medium text-rose-600 hover:text-rose-800 bg-rose-50 dark:bg-rose-900/30 rounded-lg transition-colors">
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-400 dark:text-slate-500">No proposals yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($proposals->hasPages())
        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
            {{ $proposals->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create Proposal Modal -->
<div id="create-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">
            <h3 class="font-bold text-slate-900 dark:text-white">Create New Proposal</h3>
            <button onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="create-form" onsubmit="submitProposal(event)" class="space-y-4">
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white" placeholder="Enter proposal title">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Location <span class="text-red-500">*</span></label>
                <input type="text" name="location" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white" placeholder="Enter location focus">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Abstract <span class="text-red-500">*</span></label>
                <textarea name="abstract" rows="5" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white" placeholder="Enter abstract..."></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-academic-700 hover:bg-academic-800 text-white rounded-xl font-semibold">Submit Proposal</button>
            </div>
        </form>
    </div>
</div>

<!-- Detail Modal (View + History) -->
<div id="detail-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-3xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">
            <h3 id="detail-modal-title" class="font-bold text-slate-900 dark:text-white">Proposal Details</h3>
            <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="detail-content" class="space-y-4">
            <!-- Content loaded via JS -->
        </div>
    </div>
</div>

<!-- Edit Proposal Modal -->
<div id="edit-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">
            <h3 class="font-bold text-slate-900 dark:text-white">Edit Proposal</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="edit-form" onsubmit="submitEdit(event)" class="space-y-4">
            <input type="hidden" id="edit-proposal-id" name="proposal_id" value="">
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" id="edit-title" name="title" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Location <span class="text-red-500">*</span></label>
                <input type="text" id="edit-location" name="location" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Abstract <span class="text-red-500">*</span></label>
                <textarea id="edit-abstract" name="abstract" rows="5" required class="w-full p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-academic-700 hover:bg-academic-800 text-white rounded-xl font-semibold">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">
            <h3 class="font-bold text-slate-900 dark:text-white">Delete Proposal</h3>
            <button onclick="closeDeleteModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <p class="text-slate-700 dark:text-slate-300 mb-4">Are you sure you want to delete this proposal? This action cannot be undone.</p>
        <p id="delete-proposal-title" class="font-semibold text-slate-900 dark:text-white mb-6"></p>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold">Cancel</button>
            <button type="button" onclick="executeDelete()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-semibold">Delete</button>
        </div>
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

    // Create Modal
    function openCreateModal() {
        document.getElementById('create-modal').classList.remove('hidden');
        document.getElementById('create-form').reset();
    }
    function closeCreateModal() {
        document.getElementById('create-modal').classList.add('hidden');
    }

    async function submitProposal(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const body = Object.fromEntries(formData);

        try {
            await apiRequest('/student/proposals', 'POST', body);
            showToast('Proposal submitted successfully.', 'success');
            closeCreateModal();
            setTimeout(() => window.location.reload(), 1500);
        } catch (err) {
            showToast(err.message || 'Failed to submit proposal.', 'error');
        }
    }

    // Detail Modal
    async function openDetailModal(proposalId) {
        const modal = document.getElementById('detail-modal');
        const content = document.getElementById('detail-content');

        content.innerHTML = '<div class="text-center py-8"><i class="fa-solid fa-spinner fa-spin text-2xl text-slate-400"></i><p class="mt-2 text-slate-500 dark:text-slate-400">Loading...</p></div>';
        modal.classList.remove('hidden');

        try {
            const response = await apiRequest('/student/proposals/' + proposalId, 'GET');
            const proposal = response.data;

            const statusColors = {
                'pending': 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                'conditional': 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                'revision_required': 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
                'approved': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
            };

            let historyHtml = '';
            if (proposal.topic_history && proposal.topic_history.length > 0) {
                historyHtml = `
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                        <h4 class="font-semibold text-slate-900 dark:text-white mb-3">Revision History</h4>
                        <div class="space-y-3">
                            ${proposal.topic_history.map(h => `
                                <div class="p-3 bg-slate-50 dark:bg-slate-900/30 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="font-semibold text-slate-700 dark:text-slate-300">${h.action.charAt(0).toUpperCase() + h.action.slice(1).replace('_', ' ')}</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">${optional(h.created_at).diffForHumans ? optional(h.created_at).diffForHumans() : ''}</span>
                                        </div>
                                    </div>
                                    ${h.note ? `<p class="mt-1 text-xs text-slate-600 dark:text-slate-300">${escapeHtml(h.note)}</p>` : ''}
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">${h.topic_title ? escapeHtml(h.topic_title) : ''}</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            content.innerHTML = `
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200">${proposal.proposal_id}</span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full ${statusColors[proposal.status] || 'bg-slate-100 text-slate-700'}">${proposal.status.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase())}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">${proposal.date_submitted ? new Date(proposal.date_submitted).toLocaleDateString() : 'No date'}</span>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white">${escapeHtml(proposal.title)}</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Location Focus</p>
                            <p class="mt-1 text-slate-700 dark:text-slate-200">${escapeHtml(proposal.location)}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</p>
                            <p class="mt-1 text-slate-700 dark:text-slate-200">${proposal.status.replace('_', ' ')}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Abstract</p>
                        <p class="mt-1 text-sm leading-6 text-slate-700 dark:text-slate-300">${escapeHtml(proposal.abstract)}</p>
                    </div>

                    ${proposal.supervisor_comment ? `
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Supervisor Comment</p>
                            <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">${escapeHtml(proposal.supervisor_comment)}</p>
                        </div>
                    ` : ''}

                    ${proposal.status === 'conditional' && proposal.conditions && proposal.conditions.length > 0 ? `
                        <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4">
                            <p class="text-xs uppercase tracking-wide text-blue-800 dark:text-blue-300 font-semibold">Conditions:</p>
                            <ul class="mt-1 text-xs text-blue-700 dark:text-blue-400 list-disc list-inside">
                                ${proposal.conditions.map(c => `<li>${escapeHtml(c)}</li>`).join('')}
                            </ul>
                        </div>
                    ` : ''}

                    ${historyHtml}
                </div>
            `;
        } catch (err) {
            content.innerHTML = '<div class="text-center py-8 text-rose-600"><i class="fa-solid fa-circle-exclamation text-2xl"></i><p class="mt-2">Failed to load proposal: ' + escapeHtml(err.message) + '</p></div>';
        }
    }

    function closeDetailModal() {
        document.getElementById('detail-modal').classList.add('hidden');
    }

    // Edit Modal
    async function openEditModal(proposalId) {
        try {
            const response = await apiRequest('/student/proposals/' + proposalId, 'GET');
            const proposal = response.data;

            document.getElementById('edit-proposal-id').value = proposal.id;
            document.getElementById('edit-title').value = proposal.title;
            document.getElementById('edit-location').value = proposal.location;
            document.getElementById('edit-abstract').value = proposal.abstract;

            document.getElementById('edit-modal').classList.remove('hidden');
        } catch (err) {
            showToast(err.message || 'Failed to load proposal for editing.', 'error');
        }
    }

    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
        document.getElementById('edit-form').reset();
    }

    async function submitEdit(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const body = Object.fromEntries(formData);
        const proposalId = body.proposal_id;
        delete body.proposal_id;

        try {
            await apiRequest('/student/proposals/' + proposalId, 'PUT', body);
            showToast('Proposal updated successfully.', 'success');
            closeEditModal();
            setTimeout(() => window.location.reload(), 1500);
        } catch (err) {
            showToast(err.message || 'Failed to update proposal.', 'error');
        }
    }

    // Delete Modal
    let deleteProposalId = null;

    function confirmDelete(proposalId, title) {
        deleteProposalId = proposalId;
        document.getElementById('delete-proposal-title').textContent = title;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
        deleteProposalId = null;
    }

    async function executeDelete() {
        if (!deleteProposalId) return;

        try {
            await apiRequest('/student/proposals/' + deleteProposalId, 'DELETE');
            showToast('Proposal deleted successfully.', 'success');
            closeDeleteModal();
            setTimeout(() => window.location.reload(), 1500);
        } catch (err) {
            showToast(err.message || 'Failed to delete proposal.', 'error');
        }
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeDetailModal();
            closeEditModal();
            closeDeleteModal();
        }
    });

    // Close modals on backdrop click
    ['create-modal', 'detail-modal', 'edit-modal', 'delete-modal'].forEach(id => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    if (id === 'create-modal') closeCreateModal();
                    else if (id === 'detail-modal') closeDetailModal();
                    else if (id === 'edit-modal') closeEditModal();
                    else if (id === 'delete-modal') closeDeleteModal();
                }
            });
        }
    });
</script>
@endsection