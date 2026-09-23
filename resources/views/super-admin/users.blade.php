@extends('layouts.super-admin')

@section('title', 'All Users')

@section('breadcrumbs')
    <x-super-admin.breadcrumbs :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Users', 'url' => route('super-admin.users'), 'icon' => 'fa-users-gear'],
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-900 p-6 text-white shadow-2xl lg:p-8">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-200">
                    <i class="fa-solid fa-users-gear text-emerald-300"></i>
                    Identity Operations
                </span>
                <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">Manage Platform Access, Privilege, and Account State Across Tenants</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">Use this page to review privileged access, activate or suspend accounts, and move directly into user correction workflows without leaving the super admin workspace.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('super-admin.users.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-user-plus"></i>
                    Add User
                </a>
                <a href="{{ route('super-admin.users', ['status' => 'inactive']) }}" class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/15 transition-colors">
                    <i class="fa-solid fa-user-shield"></i>
                    Review Inactive Access
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 dark:bg-rose-900/20 px-4 py-3 text-sm text-rose-700 dark:text-rose-300">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        @foreach ($queues as $queue)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">{{ $queue['label'] }}</p>
                <p class="mt-2 text-3xl font-black {{ $queue['tone'] === 'violet' ? 'text-violet-700 dark:text-violet-300' : ($queue['tone'] === 'blue' ? 'text-blue-700 dark:text-blue-300' : 'text-emerald-700 dark:text-emerald-300') }}">{{ $queue['count'] }}</p>
                <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $queue['detail'] }}</p>
            </div>
        @endforeach
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Total Users</p>
            <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ $summary['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Active</p>
            <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300">{{ $summary['active'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Super Admins</p>
            <p class="mt-2 text-3xl font-black text-violet-700 dark:text-violet-300">{{ $summary['super_admins'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Admins</p>
            <p class="mt-2 text-3xl font-black text-blue-700 dark:text-blue-300">{{ $summary['admins'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Inactive</p>
            <p class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-300">{{ $summary['inactive'] }}</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach (['' => 'All Users', 'super_admin' => 'Super Admins', 'admin' => 'Admins', 'supervisor' => 'Supervisors', 'student' => 'Students'] as $value => $label)
            <a href="{{ route('super-admin.users', array_merge(request()->query(), ['role' => $value])) }}" class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $filters['role'] === $value ? 'bg-violet-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700/50' }}">{{ $label }}</a>
        @endforeach
        @foreach (['' => 'Any Status', 'active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
            <a href="{{ route('super-admin.users', array_merge(request()->query(), ['status' => $value])) }}" class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $filters['status'] === $value ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700/50' }}">{{ $label }}</a>
        @endforeach
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('super-admin.users') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm space-y-4 md:space-y-0 md:grid md:grid-cols-[1.5fr_1fr_1fr_1fr_auto] md:items-end md:p-6">
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Search</label>
            <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search name or email" class="block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Role</label>
            <select name="role" class="block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-white focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20">
                <option value="">All roles</option>
                @foreach (['super_admin' => 'Super Admin', 'admin' => 'Admin', 'supervisor' => 'Supervisor', 'student' => 'Student'] as $value => $label)
                    <option value="{{ $value }}" {{ $filters['role'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">University</label>
            <select name="university_id" class="block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-white focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20">
                <option value="">All universities</option>
                @foreach ($universities as $university)
                    <option value="{{ $university->id }}" {{ $filters['university_id'] === (string) $university->id ? 'selected' : '' }}>{{ $university->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Status</label>
            <select name="status" class="block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-900 dark:text-white focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20">
                <option value="">All statuses</option>
                <option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700 transition-colors">Apply Filters</button>
            <a href="{{ route('super-admin.users') }}" class="rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">Reset</a>
        </div>
    </form>

    <!-- Users Table -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Identity Directory</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Review account role, tenant, status, and direct intervention actions.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Name</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Email</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Role</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">University</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Status</th>
                        <th class="px-5 py-3 text-right font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap font-medium text-slate-900 dark:text-white">{{ $user->name }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">
                                <div>{{ $user->email }}</div>
                                @if ($user->role === 'student' && $user->student)
                                    <div class="mt-1 text-xs text-slate-400 dark:text-slate-500">{{ $user->student->matric_number }} · {{ $user->student->degree_level }}</div>
                                @elseif ($user->role === 'supervisor' && $user->supervisor)
                                    <div class="mt-1 text-xs text-slate-400 dark:text-slate-500">{{ $user->supervisor->department }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $roleClasses = [
                                        'super_admin' => 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-300',
                                        'admin' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                        'supervisor' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        'student' => 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $roleClasses[$user->role] ?? 'bg-slate-100 text-slate-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">{{ $user->university->name ?? 'N/A' }}</td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if ($user->role === 'student' && $user->student)
                                    <div class="mb-2 text-xs text-slate-500 dark:text-slate-400">Supervisor: {{ $user->student->supervisor?->user?->name ?? 'Unassigned' }}</div>
                                @elseif ($user->role === 'supervisor' && $user->supervisor)
                                    <div class="mb-2 text-xs text-slate-500 dark:text-slate-400">{{ $user->supervisor->students->count() }} supervisees</div>
                                @endif
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ !$user->is_active && is_null($user->email_verified_at) ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : ($user->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300') }}">
                                    {{ !$user->is_active && is_null($user->email_verified_at) ? 'Pending invite' : ($user->is_active ? 'Active' : 'Suspended') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('super-admin.users.edit', $user) }}" class="text-sm font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">Edit</a>
                                    @if (!$user->is_active && is_null($user->email_verified_at))
                                        <form action="{{ route('super-admin.users.resend-invite', $user) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                                Resend Invite
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-sm font-medium {{ $user->is_active ? 'text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300' : 'text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300' }}">
                                                {{ $user->is_active ? 'Suspend' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('super-admin.users.edit', $user) }}#password-reset" class="text-sm font-medium text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300">Reset Password</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-users text-3xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
                                No users found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection