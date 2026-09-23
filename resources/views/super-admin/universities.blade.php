@extends('layouts.super-admin')

@section('title', 'Manage Universities')

@section('breadcrumbs')
    <x-super-admin.breadcrumbs :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Universities', 'url' => route('super-admin.universities'), 'icon' => 'fa-building-columns'],
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900 p-6 text-white shadow-2xl lg:p-8">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-200">
                    <i class="fa-solid fa-building-columns text-blue-300"></i>
                    Tenant Lifecycle
                </span>
                <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">Run University Onboarding, Health Review, and Recovery From One Registry</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">This page is the operating surface for tenant lifecycle work. Review active footprint, isolate suspended or archived universities, and move directly into inspection or intervention.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('super-admin.universities.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-plus"></i>
                    Add University
                </a>
                <a href="{{ route('super-admin.dashboard', ['tenant_status' => 'suspended']) }}" class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/15 transition-colors">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Review Tenant Risk
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 dark:bg-rose-900/20 px-4 py-3 text-sm text-rose-700 dark:text-rose-300">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Total Tenants</p>
            <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ $summary['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Active</p>
            <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300">{{ $summary['active'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Suspended</p>
            <p class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-300">{{ $summary['suspended'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Archived</p>
            <p class="mt-2 text-3xl font-black text-slate-700 dark:text-slate-300">{{ $summary['archived'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        @foreach ($queues as $queue)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">{{ $queue['label'] }}</p>
                        <p class="mt-2 text-3xl font-black {{ $queue['tone'] === 'amber' ? 'text-amber-700 dark:text-amber-300' : ($queue['tone'] === 'emerald' ? 'text-emerald-700 dark:text-emerald-300' : 'text-blue-700 dark:text-blue-300') }}">{{ $queue['count'] }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $queue['detail'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <form method="GET" action="{{ route('super-admin.universities') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 space-y-4 md:space-y-0 md:grid md:grid-cols-[1.6fr_1fr_auto] md:items-end md:gap-4 md:p-6">
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Search</label>
            <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search tenant name, code, or email" class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Status</label>
            <select name="status" class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                @foreach (['all' => 'All statuses', 'active' => 'Active', 'suspended' => 'Suspended', 'archived' => 'Archived'] as $value => $label)
                    <option value="{{ $value }}" {{ $filters['status'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700 transition-colors">Apply</button>
            <a href="{{ route('super-admin.universities') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700/50">Reset</a>
        </div>
    </form>

    <div class="flex flex-wrap gap-2">
        @foreach (['all' => 'All Tenants', 'active' => 'Active', 'suspended' => 'Suspended', 'archived' => 'Archived'] as $value => $label)
            <a href="{{ route('super-admin.universities', array_merge(request()->query(), ['status' => $value])) }}" class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $filters['status'] === $value ? 'bg-violet-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700/50' }}">{{ $label }}</a>
        @endforeach
    </div>

    <!-- Universities Table -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Tenant Registry</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Review lifecycle state, footprint, and direct interventions for each university.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Name</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Code</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Email</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Status</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Users</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Students</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Supervisors</th>
                        <th class="px-5 py-3 text-right font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($universities as $university)
                        @php
                            $statusLabel = $university->archived_at ? 'Archived' : ($university->is_active ? 'Active' : 'Suspended');
                            $statusClass = $university->archived_at
                                ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                                : ($university->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300');
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <a href="{{ route('super-admin.universities.show', $university) }}" class="font-medium text-slate-900 dark:text-white hover:text-violet-600 dark:hover:text-violet-400 transition-colors">{{ $university->name }}</a>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400 font-mono text-sm">{{ $university->code }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">{{ $university->email }}</td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                    @if ($university->archived_at)
                                        <i class="fa-solid fa-archive text-[10px]"></i>
                                    @elseif (!$university->is_active)
                                        <i class="fa-solid fa-pause text-[10px]"></i>
                                    @else
                                        <i class="fa-solid fa-circle text-[6px]"></i>
                                    @endif
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300">{{ $university->users_count ?? 0 }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300">{{ $university->students_count ?? 0 }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300">{{ $university->supervisors_count ?? 0 }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-2 flex-wrap">
                                    <a href="{{ route('super-admin.universities.show', $university) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">View</a>
                                    <a href="{{ route('super-admin.universities.edit', $university) }}" class="text-sm font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">Edit</a>

                                    @if ($university->archived_at)
                                        <form action="{{ route('super-admin.universities.activate', $university) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">Restore</button>
                                        </form>
                                    @elseif ($university->is_active)
                                        <form action="{{ route('super-admin.universities.suspend', $university) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-sm font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">Suspend</button>
                                        </form>
                                    @else
                                        <form action="{{ route('super-admin.universities.activate', $university) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">Activate</button>
                                        </form>
                                    @endif

                                    @if (! $university->archived_at)
                                        <form action="{{ route('super-admin.universities.archive', $university) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-sm font-medium text-slate-600 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">Archive</button>
                                        </form>
                                    @endif

                                    <form action="{{ route('super-admin.universities.destroy', $university) }}" method="POST" onsubmit="return confirm('Delete this university permanently? This only works when it has no related records.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-building-columns text-3xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
                                No universities found. <a href="{{ route('super-admin.universities.create') }}" class="text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300 font-medium">Create the first one</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($universities->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $universities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection