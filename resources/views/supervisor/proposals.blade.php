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
                <span class="px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Approved: {{ $proposals->where('status', 'approved')->count() }}</span>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($proposals as $proposal)
            <article class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200">{{ $proposal->proposal_id }}</span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $proposal->status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : ($proposal->status === 'revision_required' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300') }}">{{ str_replace('_', ' ', ucfirst($proposal->status)) }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ optional($proposal->date_submitted)->diffForHumans() ?? 'No submission date' }}</span>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $proposal->title }}</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $proposal->student->full_name ?? 'Unknown student' }} · {{ $proposal->student->matric_number ?? 'No matric number' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Location Focus</p>
                            <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $proposal->location }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Student Email</p>
                            <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $proposal->student->email ?? 'No student email' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Abstract</p>
                        <p class="mt-1 text-sm leading-6 text-slate-700 dark:text-slate-200">{{ $proposal->abstract }}</p>
                    </div>

                    @if ($proposal->supervisor_comment)
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Supervisor Comment</p>
                            <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $proposal->supervisor_comment }}</p>
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
</section>
@endsection
