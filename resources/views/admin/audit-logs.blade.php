@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl p-6 sm:p-8 text-white shadow-xl bg-gradient-to-br from-slate-950 via-academic-900 to-academic-800 relative overflow-hidden">
        <div class="relative flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <p class="text-xs uppercase tracking-[0.3em] text-amber-300 font-semibold">Audit trail</p>
                <h1 class="text-3xl sm:text-4xl font-black leading-tight">Inspect changes across users, resources, and system settings.</h1>
                <p class="text-slate-300 text-sm sm:text-base">Use filters to narrow events and export a scoped CSV when you need an offline review.</p>
            </div>
            <a href="{{ route('admin.audit-logs.export', request()->only(['action_type', 'start_date', 'end_date', 'university_id'])) }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-slate-100">
            <i class="fa-solid fa-file-export"></i>
            Export Logs
        </a>
    </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-700">
            <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="flex space-x-4">
                <select name="action_type" class="block w-48 rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm focus:border-academic-600 focus:ring-academic-600 dark:text-white">
                    <option value="">All Actions</option>
                    <option value="created" {{ request('action_type') === 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('action_type') === 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('action_type') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                    <option value="viewed" {{ request('action_type') === 'viewed' ? 'selected' : '' }}>Viewed</option>
                    <option value="exported" {{ request('action_type') === 'exported' ? 'selected' : '' }}>Exported</option>
                </select>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-48 rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm focus:border-academic-600 focus:ring-academic-600 dark:text-white">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-48 rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm focus:border-academic-600 focus:ring-academic-600 dark:text-white">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-academic-700 hover:bg-academic-800">Filter</button>
            </form>
        </div>

        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-900/40">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Model</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Changes</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($auditLogs as $log)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">{{ $log->user->name ?? 'Unknown' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $log->action === 'created' ? 'bg-green-100 text-green-800' :
                               ($log->action === 'updated' ? 'bg-blue-100 text-blue-800' :
                               ($log->action === 'deleted' ? 'bg-red-100 text-red-800' :
                               'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{ $log->model_type }} #{{ $log->model_id }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                        @php($changedKeys = array_keys((array) ($log->new_values ?? $log->old_values ?? [])))
                        {{ count($changedKeys) ? implode(', ', array_slice($changedKeys, 0, 5)) : '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-400 dark:text-slate-500">No audit log entries match the current filters.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/40 border-t border-slate-200 dark:border-slate-700">
            {{ $auditLogs->links() }}
        </div>
    </div>
</div>
@endsection
