@extends('layouts.super-admin')

@section('title', $mode === 'edit' ? 'Edit User' : 'Add User')

@section('breadcrumbs')
    <x-super-admin.breadcrumbs :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Users', 'url' => route('super-admin.users'), 'icon' => 'fa-users-gear'],
        ['label' => $mode === 'edit' ? 'Edit User' : 'Add User', 'url' => $mode === 'edit' ? route('super-admin.users.edit', $user) : route('super-admin.users.create'), 'icon' => 'fa-user-pen'],
    ]" />
@endsection

@section('content')
    @php
        $currentRole = old('role', $user->role);
        $studentProfile = $user->student;
        $supervisorProfile = $user->supervisor;
    @endphp

<div class="space-y-6 max-w-6xl">
    <div class="rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-violet-900 p-6 text-white shadow-2xl lg:p-8">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <a href="{{ route('super-admin.users') }}" class="inline-flex items-center gap-2 text-sm font-medium text-violet-200 hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Users
                </a>
                <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">{{ $mode === 'edit' ? 'Edit User' : 'Add User' }}</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">{{ $mode === 'edit' ? 'Update identity details, tenant assignment, and any attached academic profile from one operator workflow.' : 'Create the account with name, email, role, and tenant assignment. The user receives an email invitation and completes the remaining details themselves.' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Mode</p>
                    <p class="mt-2 text-lg font-bold">{{ ucfirst($mode) }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Role</p>
                    <p class="mt-2 text-lg font-bold">{{ ucfirst(str_replace('_', ' ', $currentRole ?: 'user')) }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm col-span-2 sm:col-span-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Status</p>
                    <p class="mt-2 text-lg font-bold">{{ $mode === 'create' ? 'Pending Invite' : (old('is_active', $user->is_active ?? true) ? 'Active' : 'Suspended') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-300">
            {{ session('error') }}
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-300">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $mode === 'edit' ? route('super-admin.users.update', $user) : route('super-admin.users.store') }}" method="POST" class="space-y-6">
        @csrf
        @if ($mode === 'edit')
            @method('PUT')
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Identity & Access</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $mode === 'create' ? 'Capture the minimum identity data required to send an onboarding invitation.' : 'Set the account name, email, role, tenant, and activation state.' }}</p>
            </div>
            <div class="space-y-6 px-6 py-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Role</label>
                        <select id="role" name="role" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                            @php
                                $roleOptions = ['student' => 'Student', 'supervisor' => 'Supervisor', 'admin' => 'Admin'];
                                if (session('role') === 'super_admin') {
                                    $roleOptions['super_admin'] = 'Super Admin';
                                }
                            @endphp
                            @foreach ($roleOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('role', $user->role) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">University</label>
                        <select id="university_id" name="university_id" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                            <option value="">Select university</option>
                            @foreach ($universities as $university)
                                <option value="{{ $university->id }}" {{ (string) old('university_id', $user->university_id) === (string) $university->id ? 'selected' : '' }}>{{ $university->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if ($mode === 'create')
                    <div class="rounded-2xl border border-dashed border-violet-200 bg-violet-50/80 px-5 py-4 text-sm text-violet-900 dark:border-violet-900/40 dark:bg-violet-900/20 dark:text-violet-100">
                        The invite email will prompt the user to provide their password, phone, department, and any role-specific profile details before access is activated.
                    </div>

                    <div id="invite-supervisor-card" class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 shadow-sm dark:border-slate-700 dark:bg-slate-900/40 {{ $currentRole === 'student' ? '' : 'hidden' }}">
                        <div class="flex flex-col gap-4">
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Student Supervisor Assignment</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">If you do not require a manual choice, the platform will assign the least-loaded active supervisor in the selected university before the invite is sent.</p>
                            </div>
                            <label class="inline-flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                                <input type="hidden" name="require_supervisor_selection" value="0">
                                <input type="checkbox" id="require_supervisor_selection" name="require_supervisor_selection" value="1" {{ old('require_supervisor_selection') ? 'checked' : '' }} class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                Require supervisor selection before sending this student invite
                            </label>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Assigned Supervisor</label>
                                <select id="invite_supervisor_id" name="supervisor_id" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                                    <option value="">Auto-assign least-loaded supervisor</option>
                                    @foreach ($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}" data-university-id="{{ $supervisor->university_id }}" {{ (string) old('supervisor_id') === (string) $supervisor->id ? 'selected' : '' }}>
                                                {{ $supervisor->user->name ?? 'Supervisor' }} - {{ $supervisor->department ?: 'Pending supervisor setup' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Department</label>
                            @include('partials.auth.department-cascade', [
                                'dropdownType' => 'super-admin-user-form',
                                'universityCode' => $user->university->code ?? '',
                                'hasStructured' => $user->university && config("universities.presets.{$user->university->code}.has_structured_departments"),
                                'selectedDepartment' => old('department', $user->department),
                                'inputName' => 'department',
                                'required' => false,
                            ])
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-300">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        Active account
                    </label>
                @endif
            </div>
        </div>

        @if ($mode === 'edit')
        <div id="supervisor-profile-card" class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden {{ $currentRole === 'supervisor' ? '' : 'hidden' }}">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Supervisor Profile</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Define the supervisor identity, credentials, and booking profile.</p>
            </div>
            <div class="space-y-6 px-6 py-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Title</label>
                        <input type="text" name="supervisor_title" value="{{ old('supervisor_title', $supervisorProfile->title ?? 'Dr.') }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Supervisor Department</label>
                        @include('partials.auth.department-cascade', [
                            'dropdownType' => 'super-admin-supervisor-profile',
                            'universityCode' => $user->university->code ?? '',
                            'hasStructured' => $user->university && config("universities.presets.{$user->university->code}.has_structured_departments"),
                            'selectedDepartment' => old('supervisor_department', $supervisorProfile->department ?? $user->department),
                            'inputName' => 'supervisor_department',
                            'required' => false,
                        ])
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Security PIN</label>
                        <input type="text" name="pin_code" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white" placeholder="{{ $mode === 'edit' ? 'Leave blank to keep current PIN' : 'Enter PIN' }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Passphrase</label>
                        <input type="text" name="passphrase" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white" placeholder="{{ $mode === 'edit' ? 'Leave blank to keep current passphrase' : 'Enter passphrase' }}">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Research Areas</label>
                    <textarea name="research_areas" rows="3" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">{{ old('research_areas', $supervisorProfile->research_areas ?? null) }}</textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Booking URL</label>
                        <input type="url" name="booking_url" value="{{ old('booking_url', $supervisorProfile->booking_url ?? null) }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 md:mt-7 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-300">
                        <input type="hidden" name="supervisor_is_active" value="0">
                        <input type="checkbox" id="supervisor_is_active" name="supervisor_is_active" value="1" {{ old('supervisor_is_active', $supervisorProfile->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        Supervisor profile active
                    </label>
                </div>
            </div>
        </div>

        <div id="student-profile-card" class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden {{ $currentRole === 'student' ? '' : 'hidden' }}">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Student Profile</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create the academic profile and map the student to a registered supervisor. Changing the mapped supervisor automatically notifies both the student and the supervisor.</p>
            </div>
            <div class="space-y-6 px-6 py-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Matric Number</label>
                        <input type="text" name="matric_number" value="{{ old('matric_number', $studentProfile->matric_number ?? null) }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Degree Level</label>
                        <select name="degree_level" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                            @foreach (['BSc', 'MSc', 'PhD'] as $degree)
                                <option value="{{ $degree }}" {{ old('degree_level', $studentProfile->degree_level ?? 'BSc') === $degree ? 'selected' : '' }}>{{ $degree }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Last Name</label>
                        <input type="text" name="lastname" value="{{ old('lastname', $studentProfile->lastname ?? null) }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Full Name</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $studentProfile->full_name ?? $user->name) }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Student Email</label>
                        <input type="email" name="student_email" value="{{ old('student_email', $studentProfile->email ?? $user->email) }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Mapped Supervisor</label>
                        <select name="supervisor_id" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                            <option value="">Assign later</option>
                            @foreach ($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" data-university-id="{{ $supervisor->university_id }}" {{ (string) old('supervisor_id', $studentProfile->supervisor_id ?? null) === (string) $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->user->name ?? 'Supervisor' }} - {{ $supervisor->department ?: 'Pending supervisor setup' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Student Status</label>
                        <select name="student_status" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                            @foreach (['active', 'suspended', 'completed', 'graduated'] as $status)
                                <option value="{{ $status }}" {{ old('student_status', $studentProfile->status ?? 'active') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Account Status</label>
                        <select name="student_account_status" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                            @foreach (['active', 'archived'] as $status)
                                <option value="{{ $status }}" {{ old('student_account_status', $studentProfile->account_status ?? 'active') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Current Stage</label>
                        <input type="number" min="1" max="12" name="current_stage" value="{{ old('current_stage', $studentProfile->current_stage ?? 1) }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Progress %</label>
                        <input type="number" min="0" max="100" name="progress_percentage" value="{{ old('progress_percentage', $studentProfile->progress_percentage ?? 0) }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Points Earned</label>
                        <input type="number" min="0" name="points_earned" value="{{ old('points_earned', $studentProfile->points_earned ?? 0) }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Research Topic</label>
                        <input type="text" name="research_topic" value="{{ old('research_topic', $studentProfile->research_topic ?? null) }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Student Drive URL</label>
                    <input type="url" name="personal_drive_url" value="{{ old('personal_drive_url', $studentProfile->personal_drive_url ?? null) }}" placeholder="https://drive.google.com/..." class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                </div>
            </div>
        </div>

        <div id="password-reset" class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $mode === 'edit' ? 'Reset Password' : 'Initial Password' }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $mode === 'edit' ? 'Leave password fields blank to keep the current password.' : 'Set a secure password for the new user account.' }}</p>
            </div>
            <div class="grid grid-cols-1 gap-4 px-6 py-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                    <input type="password" name="password" {{ $mode === 'create' ? 'required' : '' }} class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Confirm Password</label>
                    <input type="password" name="password_confirmation" {{ $mode === 'create' ? 'required' : '' }} class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                </div>
            </div>
        </div>
        @endif

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 font-semibold text-white hover:bg-violet-700 transition-colors">
                <i class="fa-solid fa-save"></i>
                {{ $mode === 'edit' ? 'Save Changes' : 'Send Invite' }}
            </button>
            <a href="{{ route('super-admin.users') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 font-semibold text-slate-700 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700/50">
                <i class="fa-solid fa-xmark"></i>
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    (function () {
        var roleSelect = document.getElementById('role');
        var universitySelect = document.getElementById('university_id');
        var supervisorSelect = document.querySelector('select[name="supervisor_id"]');
        var inviteSupervisorSelect = document.getElementById('invite_supervisor_id');
        var inviteSupervisorCard = document.getElementById('invite-supervisor-card');
        var requireSupervisorCheckbox = document.getElementById('require_supervisor_selection');
        var supervisorCard = document.getElementById('supervisor-profile-card');
        var studentCard = document.getElementById('student-profile-card');

        function syncRolePanels() {
            if (!roleSelect) return;
            var role = roleSelect.value;
            if (supervisorCard && studentCard) {
                supervisorCard.classList.toggle('hidden', role !== 'supervisor');
                studentCard.classList.toggle('hidden', role !== 'student');
            }

            if (inviteSupervisorCard) {
                inviteSupervisorCard.classList.toggle('hidden', role !== 'student');
            }

            syncInviteSupervisorState();
        }

        function syncSupervisorOptions() {
            if (!universitySelect) return;
            var universityId = universitySelect.value;
            if (supervisorSelect) {
                Array.prototype.forEach.call(supervisorSelect.options, function (option) {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }

                    var matches = !universityId || option.dataset.universityId === universityId;
                    option.hidden = !matches;

                    if (!matches && option.selected) {
                        supervisorSelect.value = '';
                    }
                });
            }

            if (inviteSupervisorSelect) {
                Array.prototype.forEach.call(inviteSupervisorSelect.options, function (option) {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }

                    var matches = !universityId || option.dataset.universityId === universityId;
                    option.hidden = !matches;

                    if (!matches && option.selected) {
                        inviteSupervisorSelect.value = '';
                    }
                });
            }
        }

        function syncInviteSupervisorState() {
            if (!roleSelect || !inviteSupervisorSelect || !requireSupervisorCheckbox) return;

            var requireSelection = roleSelect.value === 'student' && requireSupervisorCheckbox.checked;
            inviteSupervisorSelect.required = requireSelection;

            if (!requireSelection) {
                inviteSupervisorSelect.value = '';
            }
        }

        if (roleSelect) {
            roleSelect.addEventListener('change', syncRolePanels);
            syncRolePanels();
        }

        if (universitySelect) {
            universitySelect.addEventListener('change', syncSupervisorOptions);
            syncSupervisorOptions();
        }

        if (requireSupervisorCheckbox) {
            requireSupervisorCheckbox.addEventListener('change', syncInviteSupervisorState);
            syncInviteSupervisorState();
        }
    })();
</script>
@endsection