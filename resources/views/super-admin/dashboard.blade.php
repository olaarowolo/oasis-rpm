@extends('layouts.super-admin')

@section('title', 'Super Admin Dashboard')

@section('breadcrumbs')
    <x-super-admin.breadcrumbs :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
    ]" />
@endsection

@section('content')
<div class="space-y-8">
    <!-- Hero Section -->
    <div class="rounded-3xl bg-gradient-to-br from-slate-950 via-academic-900 to-slate-800 p-6 text-white shadow-2xl lg:p-8">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-blue-100/80">
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1">Platform Command Center</span>
                    <span class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-emerald-200">Live Super Admin Surface</span>
                </div>
                <h1 class="mt-4 text-3xl font-black tracking-tight text-white sm:text-4xl">Operate Every Tenant, Identity, and Runtime From One Surface</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">This dashboard is the platform nerve center: provision tenants, intervene on identity risks, inspect runtime health, and move directly into tenant or user operations without leaving the super-admin workspace.</p>
                <div class="mt-5 flex flex-wrap items-center gap-3 text-sm text-slate-200">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5">
                        <i class="fa-solid fa-user-shield text-amber-300"></i>
                        {{ $currentUser?->name ?? 'Platform Operator' }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5">
                        <i class="fa-solid fa-building"></i>
                        {{ $currentUser?->university?->name ?? 'Cross-tenant access' }}
                    </span>
                </div>
                <div class="mt-5 flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-300">
                    @foreach (['Orient', 'Operate', 'Govern', 'Configure'] as $lane)
                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5">{{ $lane }}</span>
                    @endforeach
                </div>
            </div>

            <div class="w-full xl:max-w-md">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Operator Workbench</p>
                        <span class="rounded-full border border-white/10 bg-white/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-200">1-click actions</span>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ($quickActions as $action)
                            <a href="{{ $action['route'] }}" class="group rounded-2xl border border-white/10 bg-slate-900/30 p-4 transition hover:border-white/20 hover:bg-white/10">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-xl {{ $action['tone'] === 'academic' ? 'bg-blue-400/15 text-blue-200' : ($action['tone'] === 'purple' ? 'bg-purple-400/15 text-purple-200' : ($action['tone'] === 'emerald' ? 'bg-emerald-400/15 text-emerald-200' : 'bg-amber-400/15 text-amber-200')) }}">
                                        <i class="fa-solid {{ $action['icon'] }}"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-white group-hover:text-blue-100">{{ $action['title'] }}</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-300">{{ $action['description'] }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('super-admin.dashboard') }}" class="mt-6 rounded-2xl border border-white/10 bg-slate-900/30 p-4">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.4fr_0.8fr_0.8fr_0.8fr_auto]">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Search Tenants Or Users</label>
                    <input type="text" name="q" value="{{ $dashboardFilters['q'] }}" placeholder="University code, tenant name, user name, or email" class="block w-full rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400/40">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Tenant Filter</label>
                    <select name="tenant_status" class="block w-full rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400/40">
                        @foreach (['all' => 'All Tenants', 'active' => 'Active', 'suspended' => 'Suspended', 'archived' => 'Archived'] as $value => $label)
                            <option value="{{ $value }}" {{ $dashboardFilters['tenant_status'] === $value ? 'selected' : '' }} class="text-slate-900">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">User Focus</label>
                    <select name="user_focus" class="block w-full rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400/40">
                        @foreach (['all' => 'Watchlist', 'privileged' => 'Privileged', 'admins' => 'Admins', 'inactive' => 'Inactive', 'supervisors' => 'Supervisors', 'students' => 'Students'] as $value => $label)
                            <option value="{{ $value }}" {{ $dashboardFilters['user_focus'] === $value ? 'selected' : '' }} class="text-slate-900">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Trend Window</label>
                    <select name="trend_window" class="block w-full rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400/40">
                        @foreach ([7 => '7 days', 14 => '14 days', 30 => '30 days'] as $value => $label)
                            <option value="{{ $value }}" {{ (int) $dashboardFilters['trend_window'] === $value ? 'selected' : '' }} class="text-slate-900">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="rounded-xl bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-100">Apply</button>
                    <a href="{{ route('super-admin.dashboard') }}" class="rounded-xl border border-white/10 px-4 py-3 text-sm font-semibold text-white hover:bg-white/10">Reset</a>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                @foreach (['all' => 'All Tenants', 'active' => 'Active Only', 'suspended' => 'Suspended', 'archived' => 'Archived'] as $value => $label)
                    <a href="{{ route('super-admin.dashboard', array_merge(request()->query(), ['tenant_status' => $value])) }}" class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $dashboardFilters['tenant_status'] === $value ? 'bg-blue-400 text-slate-950' : 'bg-white/10 text-slate-200 hover:bg-white/20' }}">{{ $label }}</a>
                @endforeach
                @foreach (['all' => 'Watchlist', 'privileged' => 'Privileged', 'admins' => 'Admins', 'inactive' => 'Inactive', 'supervisors' => 'Supervisors', 'students' => 'Students'] as $value => $label)
                    <a href="{{ route('super-admin.dashboard', array_merge(request()->query(), ['user_focus' => $value])) }}" class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $dashboardFilters['user_focus'] === $value ? 'bg-emerald-400 text-slate-950' : 'bg-white/10 text-slate-200 hover:bg-white/20' }}">{{ $label }}</a>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($summary as $item)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $item['label'] }}</p>
                        <p class="mt-3 text-3xl font-black text-slate-900">{{ $item['value'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $item['caption'] }}</p>
                    </div>
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl {{ $item['tone'] === 'academic' ? 'bg-blue-100 text-blue-700' : ($item['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-700' : ($item['tone'] === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-violet-100 text-violet-700')) }}">
                        <i class="fa-solid {{ $item['icon'] }} text-lg"></i>
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <div id="relationship-orchestrator" class="grid grid-cols-1 gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Supervision Link Center</h2>
                    <p class="mt-1 text-sm text-slate-500">Assign or reassign students to supervisors from one platform control point. Student and supervisor email notifications are sent automatically when the link changes.</p>
                </div>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Live linking</span>
            </div>

            <div class="space-y-5 p-5">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Students</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $assignmentSummary['students'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Unassigned</p>
                        <p class="mt-2 text-3xl font-black text-amber-700">{{ $assignmentSummary['unassigned_students'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Active Supervisors</p>
                        <p class="mt-2 text-3xl font-black text-emerald-700">{{ $assignmentSummary['active_supervisors'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-700">Average Load</p>
                        <p class="mt-2 text-3xl font-black text-blue-700">{{ $assignmentSummary['avg_load'] }}</p>
                    </div>
                </div>

                <form action="{{ route('super-admin.relationships.assign') }}" method="POST" class="grid grid-cols-1 gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 xl:grid-cols-[1fr_1fr_auto] xl:items-end">
                    @csrf
                    <div>
                        <label for="relationship-student-id" class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Student</label>
                        <select id="relationship-student-id" name="student_id" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20">
                            <option value="">Select student</option>
                            @foreach ($relationshipStudents as $student)
                                <option value="{{ $student->id }}" data-university-id="{{ $student->university_id }}" @selected((int) optional($recommendationStudent)->id === (int) $student->id)>
                                    {{ $student->full_name ?: $student->user?->name ?: 'Student' }}
                                    @if ($student->matric_number)
                                        · {{ $student->matric_number }}
                                    @endif
                                    · {{ $student->university->code ?? 'UNI' }}
                                    · {{ $student->supervisor?->user?->name ? 'Current: ' . $student->supervisor->user->name : 'Unassigned' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="relationship-supervisor-id" class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Supervisor</label>
                        <select id="relationship-supervisor-id" name="supervisor_id" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20">
                            <option value="">Select supervisor</option>
                            @foreach ($assignmentSupervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" data-university-id="{{ $supervisor->university_id }}">
                                    {{ $supervisor->user?->name ?? 'Supervisor' }} · {{ $supervisor->university->code ?? 'UNI' }} · {{ $supervisor->students_count }} students · {{ $supervisor->load_label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs text-blue-700 xl:col-span-2">
                        Recommended load is up to {{ $loadPolicy['recommended_max'] }} students. Above {{ $loadPolicy['watch_max'] }} students, supervisors are flagged as high load for manual review.
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white hover:bg-violet-700">
                        <i class="fa-solid fa-link"></i>
                        Link and notify
                    </button>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Unassigned Students</h2>
                    <p class="mt-1 text-sm text-slate-500">Students currently waiting for a supervisor relationship.</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($unassignedStudents as $student)
                        <div class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $student->full_name ?: $student->user?->name ?: 'Student' }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $student->university->name ?? 'No university' }} @if($student->matric_number) · {{ $student->matric_number }} @endif</p>
                            <a href="{{ route('super-admin.dashboard', ['student_id' => $student->id]) }}#relationship-orchestrator" class="mt-3 inline-flex text-xs font-semibold text-violet-600 hover:underline">Focus recommendations</a>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-sm text-slate-500">Every active student currently has a linked supervisor.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Recommended Supervisors</h2>
                    <p class="mt-1 text-sm text-slate-500">Ranked by current active load so the next assignment goes to the strongest capacity candidate first.</p>
                    @if($recommendationStudent)
                        <p class="mt-2 text-xs text-slate-400">Recommendation focus: {{ $recommendationStudent->full_name ?: $recommendationStudent->user?->name ?: 'Student' }} @if($recommendationStudent->research_topic)· {{ $recommendationStudent->research_topic }} @else · {{ $recommendationStudent->degree_level }} stage {{ $recommendationStudent->current_stage }} @endif</p>
                    @endif
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach ($recommendedSupervisors as $supervisor)
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $supervisor->user?->name ?? 'Supervisor' }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $supervisor->university->name ?? 'No university' }} · {{ $supervisor->department ?: 'Department pending' }}</p>
                                <p class="mt-1 text-xs {{ $supervisor->load_band === 'recommended' ? 'text-emerald-600' : ($supervisor->load_band === 'watch' ? 'text-amber-600' : 'text-rose-600') }}">{{ $supervisor->recommendation_reason }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $supervisor->load_band === 'recommended' ? 'bg-emerald-100 text-emerald-700' : ($supervisor->load_band === 'watch' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">{{ $supervisor->students_count }} students</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Recent Relationship Changes</h2>
                    <p class="mt-1 text-sm text-slate-500">Latest links and reassignments with notification delivery visibility.</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($relationshipHistory as $history)
                        <div class="px-5 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ data_get($history->new_values, 'student_name', 'Student') }} · {{ data_get($history->new_values, 'transition') === 'supervisor_reassigned' ? 'Reassigned' : 'Linked' }}</p>
                                    <p class="mt-1 text-sm text-slate-500">Supervisor: {{ data_get($history->new_values, 'supervisor_name', 'Unknown') }} · {{ $history->university?->name ?? 'No university' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">Actor: {{ $history->user?->name ?? 'System' }}</p>
                                </div>
                                <span class="text-xs text-slate-400">{{ optional($history->created_at)->diffForHumans() }}</span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                <span class="rounded-full px-2.5 py-1 {{ data_get($history->new_values, 'notifications.student.status') === 'sent' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">Student email: {{ data_get($history->new_values, 'notifications.student.status', 'unknown') }}</span>
                                <span class="rounded-full px-2.5 py-1 {{ data_get($history->new_values, 'notifications.supervisor.status') === 'sent' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">Supervisor email: {{ data_get($history->new_values, 'notifications.supervisor.status', 'unknown') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-sm text-slate-500">No relationship changes recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Command Model -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Command Model</h2>
                    <p class="mt-1 text-sm text-slate-500">One view for ownership, operating pillars, and the operator decision path.</p>
                </div>
                <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white">Architecture view</span>
            </div>

            <div class="space-y-6 p-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Ownership Lanes</p>
                    <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                        @foreach ($workstreamOwnership as $stream)
                            <a href="{{ $stream['route'] }}" class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:border-slate-300 hover:bg-white">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] {{ $stream['tone'] === 'academic' ? 'text-blue-700' : ($stream['tone'] === 'emerald' ? 'text-emerald-700' : ($stream['tone'] === 'violet' ? 'text-violet-700' : 'text-amber-700')) }}">{{ $stream['label'] }}</p>
                                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $stream['metric'] }}</p>
                                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $stream['unit'] }}</p>
                                    </div>
                                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl {{ $stream['tone'] === 'academic' ? 'bg-blue-100 text-blue-700' : ($stream['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-700' : ($stream['tone'] === 'violet' ? 'bg-violet-100 text-violet-700' : 'bg-amber-100 text-amber-700')) }}">
                                        <i class="fa-solid {{ $stream['icon'] }}"></i>
                                    </span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-slate-500">{{ $stream['description'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Operating Pillars</p>
                    <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                        @foreach ($operatingModel as $pillar)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] {{ $pillar['tone'] === 'academic' ? 'text-blue-700' : ($pillar['tone'] === 'violet' ? 'text-violet-700' : ($pillar['tone'] === 'emerald' ? 'text-emerald-700' : 'text-slate-600')) }}">{{ $pillar['eyebrow'] }}</p>
                                        <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $pillar['title'] }}</h3>
                                    </div>
                                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl {{ $pillar['tone'] === 'academic' ? 'bg-blue-100 text-blue-700' : ($pillar['tone'] === 'violet' ? 'bg-violet-100 text-violet-700' : ($pillar['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700')) }}">
                                        <i class="fa-solid {{ $pillar['icon'] }}"></i>
                                    </span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-slate-500">{{ $pillar['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Priority Queues</h2>
                    <p class="mt-1 text-sm text-slate-500">The top action lanes on the dashboard should always reflect tenant lifecycle, identity review, and runtime follow-up.</p>
                </div>
                <div class="space-y-3 p-5">
                    @foreach ($priorityQueues as $queue)
                        <a href="{{ $queue['route'] }}" class="block rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:border-slate-300 hover:bg-white">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl {{ $queue['tone'] === 'academic' ? 'bg-blue-100 text-blue-700' : ($queue['tone'] === 'violet' ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700') }}">
                                        <i class="fa-solid {{ $queue['icon'] }}"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $queue['label'] }}</p>
                                        <p class="mt-1 text-sm leading-6 text-slate-500">{{ $queue['detail'] }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex min-w-[2.75rem] items-center justify-center rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">{{ $queue['count'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Operator Loop</h2>
                    <p class="mt-1 text-sm text-slate-500">A compact decision path from signal to verified recovery.</p>
                </div>
                <div class="space-y-3 p-5">
                    @foreach ($operatorLoop as $step)
                        <div class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <span class="inline-flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl {{ $step['tone'] === 'academic' ? 'bg-blue-100 text-blue-700' : ($step['tone'] === 'violet' ? 'bg-violet-100 text-violet-700' : ($step['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-700' : ($step['tone'] === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700'))) }} text-sm font-black">
                                {{ $step['step'] }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $step['title'] }}</p>
                                <p class="mt-1 text-sm leading-6 text-slate-500">{{ $step['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.25fr_0.75fr]">
        <!-- Tenant Health Board -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Tenant Health Board</h2>
                    <p class="mt-1 text-sm text-slate-500">The primary operating board for tenant footprint, configuration health, and intervention readiness.</p>
                </div>
                <a href="{{ route('super-admin.universities') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Open all tenants</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-500">Tenant</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-500">Status</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-500">Users</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-500">Config</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-500">Last Activity</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($tenantHealth as $tenant)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $tenant['name'] }}</p>
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $tenant['code'] }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $tenant['status'] === 'active' ? 'bg-emerald-100 text-emerald-700' : ($tenant['status'] === 'suspended' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">{{ ucfirst($tenant['status']) }}</span>
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    <p>{{ $tenant['users_count'] }} users</p>
                                    <p class="text-xs text-slate-400">{{ $tenant['students_count'] }} students · {{ $tenant['supervisors_count'] }} supervisors</p>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="w-24 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-2 rounded-full {{ $tenant['config_completeness'] >= 80 ? 'bg-emerald-500' : ($tenant['config_completeness'] >= 60 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $tenant['config_completeness'] }}%"></div>
                                    </div>
                                    <p class="mt-2 text-xs text-slate-500">{{ $tenant['config_completeness'] }}% complete</p>
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $tenant['last_audit_at'] ? \Illuminate\Support\Carbon::parse($tenant['last_audit_at'])->diffForHumans() : 'No audit trail yet' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-3 text-sm font-semibold">
                                        <a href="{{ $tenant['route'] }}" class="text-blue-600 hover:text-blue-800">Inspect</a>
                                        @if ($tenant['status'] === 'active')
                                            <form action="{{ $tenant['suspend_route'] }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-amber-600 hover:text-amber-800">Suspend</button>
                                            </form>
                                        @elseif ($tenant['status'] !== 'archived')
                                            <form action="{{ $tenant['activate_route'] }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-emerald-600 hover:text-emerald-800">Activate</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Side Cards -->
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Critical Alerts</h2>
                    <p class="mt-1 text-sm text-slate-500">Immediate issues surfaced before deeper inspection or configuration work.</p>
                </div>
                <div class="space-y-3 p-5">
                    @foreach ($alerts as $alert)
                        <a href="{{ $alert['route'] }}" class="flex items-start justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 transition hover:border-slate-300 hover:bg-white">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $alert['label'] }}</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $alert['detail'] }}</p>
                            </div>
                            <span class="inline-flex min-w-[2.5rem] items-center justify-center rounded-full px-3 py-1 text-xs font-bold {{ $alert['tone'] === 'emerald' ? 'bg-emerald-100 text-emerald-700' : ($alert['tone'] === 'amber' ? 'bg-amber-100 text-amber-700' : ($alert['tone'] === 'rose' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700')) }}">{{ $alert['value'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Role Distribution</h2>
                    <p class="mt-1 text-sm text-slate-500">Privilege concentration and access mix across the platform.</p>
                </div>
                <div class="space-y-4 p-5">
                    @foreach ($roleDistribution as $role)
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-slate-700">{{ $role['label'] }}</span>
                                <span class="font-semibold text-slate-900">{{ $role['value'] }}</span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-2 rounded-full {{ $role['tone'] === 'purple' ? 'bg-violet-500' : ($role['tone'] === 'emerald' ? 'bg-emerald-500' : ($role['tone'] === 'amber' ? 'bg-amber-500' : 'bg-blue-500')) }}" style="width: {{ max(8, min(100, $summary[1]['value'] > 0 ? ($role['value'] / max(1, collect($roleDistribution)->sum('value'))) * 100 : 0)) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Charts -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $auditTrend['label'] }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $auditTrend['window'] }}-day trend line for platform changes and interventions.</p>
                </div>
                <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">{{ $auditTrend['total'] }} total</span>
            </div>
            <div class="p-5">
                <svg viewBox="0 0 240 56" class="h-20 w-full" preserveAspectRatio="none">
                    <polyline fill="none" stroke="#cbd5e1" stroke-width="1" points="0,50 240,50"></polyline>
                    <polyline fill="none" stroke="#7c3aed" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="{{ $auditTrend['points'] }}"></polyline>
                </svg>
                <div class="mt-4 grid gap-2 text-center text-[11px] text-slate-500" style="grid-template-columns: repeat({{ count($auditTrend['series']) }}, minmax(0, 1fr));">
                    @foreach ($auditTrend['series'] as $point)
                        <div>
                            <div class="font-semibold text-slate-900">{{ $point['value'] }}</div>
                            <div class="mt-1">{{ $point['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $provisioningTrend['label'] }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $provisioningTrend['window'] }}-day line for new account and identity creation.</p>
                </div>
                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">{{ $provisioningTrend['total'] }} total</span>
            </div>
            <div class="p-5">
                <svg viewBox="0 0 240 56" class="h-20 w-full" preserveAspectRatio="none">
                    <polyline fill="none" stroke="#cbd5e1" stroke-width="1" points="0,50 240,50"></polyline>
                    <polyline fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="{{ $provisioningTrend['points'] }}"></polyline>
                </svg>
                <div class="mt-4 grid gap-2 text-center text-[11px] text-slate-500" style="grid-template-columns: repeat({{ count($provisioningTrend['series']) }}, minmax(0, 1fr));">
                    @foreach ($provisioningTrend['series'] as $point)
                        <div>
                            <div class="font-semibold text-slate-900">{{ $point['value'] }}</div>
                            <div class="mt-1">{{ $point['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Security & Audit -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[0.92fr_1.08fr]">
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Security Watchlist</h2>
                        <p class="mt-1 text-sm text-slate-500">Privileged identities and inactive accounts requiring review.</p>
                    </div>
                    <a href="{{ route('super-admin.users') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Open user admin</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($securityWatch as $user)
                        <div class="flex items-start justify-between gap-4 px-5 py-4">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
                                <p class="mt-2 text-xs uppercase tracking-[0.2em] text-slate-400">{{ $user->university->name ?? 'No university' }}</p>
                                @if ($user->role === 'student' && $user->student)
                                    <p class="mt-2 text-xs text-slate-500">Supervisor: {{ $user->student->supervisor?->user?->name ?? 'Unassigned' }}</p>
                                @elseif ($user->role === 'supervisor' && $user->supervisor)
                                    <p class="mt-2 text-xs text-slate-500">{{ $user->supervisor->students->count() }} mapped students</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $user->is_active ? 'Active' : 'Suspended' }}</span>
                                <p class="mt-2 text-xs font-semibold uppercase tracking-[0.2em] {{ $user->role === 'super_admin' ? 'text-violet-600' : ($user->role === 'admin' ? 'text-emerald-600' : 'text-slate-500') }}">{{ str_replace('_', ' ', $user->role) }}</p>
                                <div class="mt-3 flex justify-end gap-3 text-sm font-semibold">
                                    <a href="{{ route('super-admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-800">Manage</a>
                                    @if (!$user->is_active && is_null($user->email_verified_at))
                                        <form action="{{ route('super-admin.users.resend-invite', $user) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-800">Resend Invite</button>
                                        </form>
                                    @else
                                        <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="{{ $user->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }}">{{ $user->is_active ? 'Suspend' : 'Activate' }}</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-sm text-slate-500">No flagged accounts right now.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-bold text-slate-900">Runtime Snapshot</h2>
                    <p class="mt-1 text-sm text-slate-500">Critical platform services and execution defaults kept close to operator decisions.</p>
                </div>
                <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2">
                    @foreach ($systemSnapshot as $item)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-slate-900">{{ $item['label'] }}</p>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] {{ $item['state'] === 'healthy' ? 'bg-emerald-100 text-emerald-700' : ($item['state'] === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">{{ $item['state'] }}</span>
                            </div>
                            <p class="mt-3 text-sm text-slate-500">{{ $item['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ $auditTrend['label'] }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $auditTrend['window'] }}-day trend line for platform changes and interventions.</p>
                        </div>
                        <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">{{ $auditTrend['total'] }} total</span>
                    </div>
                    <div class="p-5">
                        <svg viewBox="0 0 240 56" class="h-20 w-full" preserveAspectRatio="none">
                            <polyline fill="none" stroke="#cbd5e1" stroke-width="1" points="0,50 240,50"></polyline>
                            <polyline fill="none" stroke="#7c3aed" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="{{ $auditTrend['points'] }}"></polyline>
                        </svg>
                        <div class="mt-4 grid gap-2 text-center text-[11px] text-slate-500" style="grid-template-columns: repeat({{ count($auditTrend['series']) }}, minmax(0, 1fr));">
                            @foreach ($auditTrend['series'] as $point)
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $point['value'] }}</div>
                                    <div class="mt-1">{{ $point['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ $provisioningTrend['label'] }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $provisioningTrend['window'] }}-day line for new account and identity creation.</p>
                        </div>
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">{{ $provisioningTrend['total'] }} total</span>
                    </div>
                    <div class="p-5">
                        <svg viewBox="0 0 240 56" class="h-20 w-full" preserveAspectRatio="none">
                            <polyline fill="none" stroke="#cbd5e1" stroke-width="1" points="0,50 240,50"></polyline>
                            <polyline fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="{{ $provisioningTrend['points'] }}"></polyline>
                        </svg>
                        <div class="mt-4 grid gap-2 text-center text-[11px] text-slate-500" style="grid-template-columns: repeat({{ count($provisioningTrend['series']) }}, minmax(0, 1fr));">
                            @foreach ($provisioningTrend['series'] as $point)
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $point['value'] }}</div>
                                    <div class="mt-1">{{ $point['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Recent Audit Activity</h2>
                        <p class="mt-1 text-sm text-slate-500">What changed most recently across tenants and platform settings.</p>
                    </div>
                    <a href="{{ route('super-admin.audit-logs') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Audit explorer</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($recentAuditLogs as $log)
                        <div class="flex items-start gap-4 px-5 py-4">
                            <span class="mt-1 inline-flex h-10 w-10 items-center justify-center rounded-2xl {{ $log->action === 'created' ? 'bg-emerald-100 text-emerald-700' : ($log->action === 'deleted' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700') }}">
                                <i class="fa-solid {{ $log->action === 'created' ? 'fa-plus' : ($log->action === 'deleted' ? 'fa-trash' : 'fa-pen') }}"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-slate-900">{{ ucfirst($log->action) }} {{ $log->model_type }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $log->user->name ?? 'System' }} · {{ $log->university->name ?? 'Platform context' }}</p>
                                <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400">{{ $log->created_at?->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-sm text-slate-500">No audit activity recorded yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Provisioning Feed</h2>
                        <p class="mt-1 text-sm text-slate-500">Recently created or updated operator-facing accounts.</p>
                    </div>
                    <a href="{{ route('super-admin.users.create') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Create account</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($recentProvisioning as $user)
                        <div class="flex items-start justify-between gap-4 px-5 py-4">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
                                <p class="mt-2 text-xs uppercase tracking-[0.2em] text-slate-400">{{ $user->role }} · {{ $user->university->name ?? 'No university' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                <p class="mt-2 text-xs text-slate-400">{{ $user->created_at?->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-sm text-slate-500">No recent provisioning activity.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Support Tools -->
    <div class="mt-8 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-lg font-bold text-slate-900">Support Tools</h2>
            <p class="mt-1 text-sm text-slate-500">Secondary utilities for investigations, recovery, and direct jumps into specialist workflows.</p>
        </div>
        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($supportTools as $tool)
                <a href="{{ $tool['route'] }}" class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:border-slate-300 hover:bg-white">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white">
                        <i class="fa-solid {{ $tool['icon'] }}"></i>
                    </span>
                    <h3 class="mt-4 text-base font-bold text-slate-900 group-hover:text-blue-700">{{ $tool['title'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">{{ $tool['description'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</div>

<script>
    (function () {
        var studentSelect = document.getElementById('relationship-student-id');
        var supervisorSelect = document.getElementById('relationship-supervisor-id');

        function syncSupervisorAssignmentOptions() {
            if (!studentSelect || !supervisorSelect) return;

            var selectedStudent = studentSelect.options[studentSelect.selectedIndex];
            var universityId = selectedStudent ? selectedStudent.dataset.universityId : '';

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

        if (studentSelect) {
            studentSelect.addEventListener('change', syncSupervisorAssignmentOptions);
            syncSupervisorAssignmentOptions();
        }
    })();
</script>
@endsection