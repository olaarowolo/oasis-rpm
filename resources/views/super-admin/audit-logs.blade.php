@extends('layouts.super-admin')

@section('title', 'Audit Logs')

@section('breadcrumbs')
    <x-super-admin.breadcrumbs :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Audit Logs', 'url' => route('super-admin.audit-logs'), 'icon' => 'fa-clipboard-list'],
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Audit Logs</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Platform-wide audit trail for all tenant and system changes.</p>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Date</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">User</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">University</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Action</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Model</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-5 py-4 whitespace-nowrap font-medium text-slate-900 dark:text-white">{{ $log->user->name ?? 'System' }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">{{ $log->university->name ?? 'Platform' }}</td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $actionClasses = [
                                        'created' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                        'updated' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        'deleted' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
                                        'suspended' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                        'activated' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                        'archived' => 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $actionClasses[$log->action] ?? 'bg-slate-100 text-slate-800' }}">
                                    <i class="fa-solid {{ $log->action === 'created' ? 'fa-plus' : ($log->action === 'deleted' ? 'fa-trash' : ($log->action === 'updated' ? 'fa-pen' : 'fa-circle')) }} text-[10px]"></i>
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300 font-mono text-sm">{{ $log->model_type }}</td>
                            <td class="px-5 py-4 max-w-xs">
                                @if ($log->description)
                                    <p class="text-slate-600 dark:text-slate-400 truncate">{{ $log->description }}</p>
                                @elseif ($log->changes)
                                    <div class="text-xs text-slate-500 dark:text-slate-400 font-mono bg-slate-50 dark:bg-slate-700/50 rounded p-2 max-h-12 overflow-auto">{{ json_encode($log->changes) }}</div>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 italic">No details</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-clipboard-list text-3xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
                                No audit logs recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($auditLogs->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection