@extends('layouts.supervisor')

@section('title', 'Resource Approvals | Research Supervision Portal | LASU')

@section('content')
<section class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Resource Approvals</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Review submitted learning resources from students under your supervision.</p>
            </div>
            <span class="px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 text-xs font-semibold">Pending: {{ $pendingResources->count() }}</span>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($pendingResources as $progress)
            <article class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">{{ ucfirst($progress->status) }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ optional($progress->submitted_date)->diffForHumans() ?? 'No submission date' }}</span>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $progress->resource->title ?? 'Untitled resource' }}</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $progress->student->full_name ?? 'Unknown student' }} · {{ $progress->student->matric_number ?? 'No matric number' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Stage</p>
                            <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $progress->resource->stage ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Points</p>
                            <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $progress->resource->points ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Student Email</p>
                            <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $progress->student->email ?? 'No student email' }}</p>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-dashed border-slate-300 dark:border-slate-700 shadow-sm text-center">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">No pending resource approvals</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Submitted resources awaiting your review will appear here.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection