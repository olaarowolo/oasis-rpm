@extends('layouts.supervisor')

@section('title', 'Manuscripts | Research Supervision Portal')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Manuscripts</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Review defense-readiness manuscripts from students under your supervision.</p>
            </div>
            <div id="manuscript-filter-tabs" class="flex flex-wrap gap-1 text-xs">
                <button data-filter="all" class="filter-tab px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold">All</button>
                <button data-filter="in_review" class="filter-tab px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">Awaiting Review</button>
                <button data-filter="in_revision" class="filter-tab px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">Revision Requested</button>
                <button data-filter="halted" class="filter-tab px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">Halted</button>
                <button data-filter="completed" class="filter-tab px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">Complete</button>
            </div>
        </div>
    </div>

    <div id="manuscripts-list" class="space-y-4">
        @forelse ($documents as $doc)
            @php
                $student = $doc->student;
                $awaitingCount = 0;
                $sectionPills = '';
            @endphp
            @php
                $awaitingCount = 0;
            @endphp
            <article class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $student->full_name ?? 'Unknown' }}</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $student->matric_number ?? '' }} · {{ $student->programme ?? '' }}</p>
                    </div>
                    <span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold
                        @if($doc->status === 'draft') bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300
                        @elseif($doc->status === 'in_review') bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300
                        @elseif($doc->status === 'in_revision') bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300
                        @elseif($doc->status === 'halted') bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300
                        @elseif($doc->status === 'completed') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300
                        @endif">
                        {{ ucfirst($doc->status) }}
                    </span>
                </div>

                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    @foreach($doc->sections as $section)
                        @if (!$section->isGroup())
                            <span title="{{ $section->title }}" class="px-2 py-1 rounded text-[10px] font-medium
                                @if($section->status === 'accepted') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                                @elseif($section->status === 'conditional') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                @elseif($section->status === 'submitted') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                                @elseif($section->status === 'revision_requested') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300
                                @elseif($section->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                @else bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400 @endif">
                            {{ $section->title }}
                        </span>
                    @endif
                    @if($section->status === 'submitted')
                        @php $awaitingCount++ @endphp
                    @endif
                @endforeach
                </div>

                @if($doc->status === 'halted')
                    <div class="mt-2 p-2 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <span class="text-xs font-semibold text-red-800 dark:text-red-300">Halted: </span>
                        <span class="text-xs text-red-700 dark:text-red-400">{{ Str::limit($doc->halted_reason, 120) }}</span>
                    </div>
                @endif

                <div class="mt-3 flex items-center justify-end gap-3 text-xs text-slate-500 dark:text-slate-400">
                    @if($awaitingCount > 0)
                        <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">{{ $awaitingCount }} awaiting review</span>
                    @endif
                    <a href="{{ route('supervisor.manuscripts.show', $doc) }}" class="font-semibold text-academic-600 dark:text-academic-400 hover:underline">Open manuscript →</a>
                </div>
            </article>
        @empty
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-dashed border-slate-300 dark:border-slate-700 shadow-sm text-center">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">No manuscripts yet</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Manuscripts submitted by your students will appear here.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    const CSRF = '{{ csrf_token() }}';
    const API = '{{ url('/api') }}';
    let manuscriptsData = [];

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
    }

    async function apiGet(path) {
        const res = await fetch(API + path, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
        if (!res.ok) throw new Error('Request failed: ' + res.status);
        return res.json();
    }

    async function loadManuscripts() {
        try {
            const res = await apiGet('/supervisor/manuscripts');
            manuscriptsData = res.data || [];
        } catch (err) {
            manuscriptsData = [];
        }
    }

    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('bg-academic-700','text-white'));
            this.classList.add('bg-academic-700','text-white');
        });
    });
</script>
@endsection
