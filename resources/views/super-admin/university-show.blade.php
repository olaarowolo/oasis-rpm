@extends('layouts.super-admin')

@section('title', $university->name)

@section('breadcrumbs')
    <x-super-admin.breadcrumbs :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Universities', 'url' => route('super-admin.universities'), 'icon' => 'fa-building-columns'],
        ['label' => $university->name, 'url' => route('super-admin.universities.show', $university), 'icon' => 'fa-building-shield'],
    ]" />
@endsection

@section('content')
@php
    $statusLabel = $university->archived_at ? 'Archived' : ($university->is_active ? 'Active' : 'Suspended');
    $statusClass = $university->archived_at
        ? 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'
        : ($university->is_active
            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
            : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300');
@endphp

<div class="space-y-6">
    <div class="rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900 p-6 text-white shadow-2xl lg:p-8">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-3xl">
                <a href="{{ route('super-admin.universities') }}" class="inline-flex items-center gap-2 text-sm font-medium text-blue-200 hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Universities
                </a>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">{{ $university->name }}</h1>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                        <i class="fa-solid {{ $university->archived_at ? 'fa-box-archive' : ($university->is_active ? 'fa-circle-check' : 'fa-pause') }} text-[10px]"></i>
                        {{ $statusLabel }}
                    </span>
                </div>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">{{ $university->code }} is configured as a tenant on the platform. Review profile settings, recent identities, academic assets, and the latest audit activity from one operator surface.</p>
                <div class="mt-5 flex flex-wrap gap-3 text-sm text-slate-200">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5">
                        <i class="fa-solid fa-envelope text-blue-200"></i>
                        {{ $university->email }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5">
                        <i class="fa-solid fa-building-user text-amber-200"></i>
                        {{ $university->department }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('super-admin.universities.edit', $university) }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-pen"></i>
                    Edit University
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Users</p>
            <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $university->users_count }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Students</p>
            <p class="mt-3 text-3xl font-black text-blue-700 dark:text-blue-300">{{ $university->students_count }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Supervisors</p>
            <p class="mt-3 text-3xl font-black text-amber-700 dark:text-amber-300">{{ $university->supervisors_count }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Resources</p>
            <p class="mt-3 text-3xl font-black text-violet-700 dark:text-violet-300">{{ $university->resources_count }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Proposals</p>
            <p class="mt-3 text-3xl font-black text-emerald-700 dark:text-emerald-300">{{ $university->proposals_count }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Meetings</p>
            <p class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ $university->meeting_logs_count }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[0.85fr_1.15fr]">
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Tenant Profile</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Core contact and lifecycle metadata.</p>
                    </div>
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900/40">
                        <span class="h-6 w-6 rounded-full border border-white/60" style="background-color: {{ $university->branding_color }}"></span>
                    </span>
                </div>

                <dl class="mt-6 space-y-4 text-sm">
                <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-900/40">
                    <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Department</dt>
                    <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $university->department ?: 'Structured selection enabled' }}</dd>
                    @if ($university->has_structured_departments)
                        <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">Structured faculty → department cascade active</p>
                    @endif
                </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-900/40">
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Phone</dt>
                        <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $university->phone ?: 'Not set' }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-900/40">
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Branding Color</dt>
                        <dd class="mt-1 flex items-center gap-2 font-medium text-slate-900 dark:text-white">
                            <span class="inline-block h-4 w-4 rounded-full border border-slate-200 dark:border-slate-600" style="background-color: {{ $university->branding_color }}"></span>
                            {{ $university->branding_color }}
                        </dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-900/40">
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Created</dt>
                        <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $university->created_at?->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-900/40">
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Archived At</dt>
                        <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $university->archived_at?->format('Y-m-d H:i') ?: 'Not archived' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent Audit Activity</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Latest operator actions affecting this tenant.</p>
                <ul class="mt-5 space-y-3 text-sm">
                    @forelse ($recentAuditLogs as $log)
                        <li class="rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-700">
                            <p class="font-semibold text-slate-900 dark:text-white">{{ ucfirst($log->action) }} {{ $log->model_type }}</p>
                            <p class="mt-1 text-slate-500 dark:text-slate-400">{{ $log->user->name ?? 'System' }} · {{ $log->created_at?->format('Y-m-d H:i') }}</p>
                        </li>
                    @empty
                        <li class="rounded-2xl border border-dashed border-slate-200 px-4 py-5 text-slate-500 dark:border-slate-700 dark:text-slate-400">No audit activity recorded yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent User Accounts</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Newest tenant identities and their current role state.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold uppercase tracking-[0.2em] text-xs text-slate-500 dark:text-slate-400">Name</th>
                                <th class="px-5 py-3 text-left font-semibold uppercase tracking-[0.2em] text-xs text-slate-500 dark:text-slate-400">Email</th>
                                <th class="px-5 py-3 text-left font-semibold uppercase tracking-[0.2em] text-xs text-slate-500 dark:text-slate-400">Role</th>
                                <th class="px-5 py-3 text-left font-semibold uppercase tracking-[0.2em] text-xs text-slate-500 dark:text-slate-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @forelse ($recentUsers as $user)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="px-5 py-4 font-medium text-slate-900 dark:text-white">{{ $user->name }}</td>
                                    <td class="px-5 py-4 text-slate-500 dark:text-slate-400">{{ $user->email }}</td>
                                    <td class="px-5 py-4 text-slate-500 dark:text-slate-400">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' }}">
                                            {{ $user->is_active ? 'Active' : 'Suspended' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-slate-500 dark:text-slate-400">No users yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent Resources</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Most recently updated academic assets for this tenant.</p>
                    </div>
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">
                        <i class="fa-solid fa-book-open"></i>
                    </span>
                </div>

                <ul class="mt-5 space-y-3 text-sm">
                    @forelse ($recentResources as $resource)
                        <li class="flex items-start justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-700">
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $resource->title }}</p>
                                <p class="mt-1 text-slate-500 dark:text-slate-400">Stage {{ $resource->stage }} · {{ ucfirst($resource->type) }}</p>
                            </div>
                            <span class="text-xs text-slate-400 dark:text-slate-500">{{ $resource->updated_at?->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="rounded-2xl border border-dashed border-slate-200 px-4 py-5 text-slate-500 dark:border-slate-700 dark:text-slate-400">No resources found for this university.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection