@extends('layouts.student')

@section('title', 'My Proposals')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-6">
    <div class="flex items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700 pb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">My Proposals</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Track topic submissions, approval status, and supervisor feedback.</p>
        </div>
        <a href="{{ route('student.dashboard', ['tab' => 'student-proposals']) }}" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            Dashboard
        </a>
    </div>

    <div>
        <button onclick="window.location.href='{{ route('student.dashboard', ['tab' => 'student-proposals']) }}'" class="inline-flex items-center gap-2 rounded-xl bg-academic-700 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-academic-800">
            <i class="fa-solid fa-plus"></i>
            Create New Proposal
        </button>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-900/40">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Date Submitted</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($proposals as $proposal)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">{{ $proposal->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{ optional($proposal->date_submitted)->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{ $proposal->location }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $proposal->status === 'approved' ? 'bg-green-100 text-green-800' :
                               ($proposal->status === 'revision_required' ? 'bg-yellow-100 text-yellow-800' :
                               ($proposal->status === 'pending' ? 'bg-blue-100 text-blue-800' :
                               'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst(str_replace('_', ' ', $proposal->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-400 dark:text-slate-500">No proposals yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
