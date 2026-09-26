<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Mail\PortalEmail;
use App\Models\AuditLog;
use App\Models\PlatformSetting;
use App\Models\Resource;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\University;
use App\Models\User;
use App\Services\BulkUserActionService;
use App\Services\UserInvitationService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminWebController extends BaseController
{
    protected const LOAD_RECOMMENDED_MAX = 5;
    protected const LOAD_WATCH_MAX = 8;

    public function __construct(
        private UserInvitationService $userInvitationService,
        private BulkUserActionService $bulkUserActionService
    ) {
    }

    public function dashboard(Request $request): View
    {
        $currentUser = $this->actingUser()?->load('university');
        $selectedStudentId = (int) $request->query('student_id', 0);
        $databaseStatus = $this->checkDatabaseStatus();
        $mailConfigured = (bool) config('mail.default');
        $queueDriver = (string) config('queue.default', 'sync');
        $cacheDriver = (string) config('cache.default', 'file');
        $failedJobsCount = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : null;
        $dashboardFilters = [
            'q' => trim((string) request('q', '')),
            'tenant_status' => (string) request('tenant_status', 'all'),
            'user_focus' => (string) request('user_focus', 'all'),
            'trend_window' => in_array((int) request('trend_window', 7), [7, 14, 30], true) ? (int) request('trend_window', 7) : 7,
        ];
        $geminiKeyConfigured = PlatformSetting::where('setting_key', 'gemini_api_key')->whereNotNull('setting_value')->exists();

        $summary = [
            [
                'label' => 'Universities',
                'value' => University::count(),
                'tone' => 'academic',
                'icon' => 'fa-building-columns',
                'caption' => University::where('is_active', true)->whereNull('archived_at')->count() . ' active tenants',
            ],
            [
                'label' => 'Privileged Users',
                'value' => User::whereIn('role', ['super_admin', 'admin', 'supervisor'])->where('is_active', true)->count(),
                'tone' => 'emerald',
                'icon' => 'fa-user-shield',
                'caption' => User::whereIn('role', ['super_admin', 'admin'])->count() . ' admin-level accounts',
            ],
            [
                'label' => 'Suspended Accounts',
                'value' => User::where('is_active', false)->count(),
                'tone' => 'amber',
                'icon' => 'fa-user-lock',
                'caption' => University::where('is_active', false)->whereNull('archived_at')->count() . ' suspended tenants',
            ],
            [
                'label' => 'Audit Events (24h)',
                'value' => AuditLog::where('created_at', '>=', now()->subDay())->count(),
                'tone' => 'violet',
                'icon' => 'fa-timeline',
                'caption' => PlatformSetting::count() . ' platform settings stored',
            ],
        ];

        $quickActions = [
            [
                'title' => 'Add University',
                'description' => 'Onboard a new tenant and define its identity.',
                'icon' => 'fa-building-circle-plus',
                'route' => route('super-admin.universities.create'),
                'tone' => 'academic',
            ],
            [
                'title' => 'Add Privileged User',
                'description' => 'Create an admin, supervisor, or super admin account.',
                'icon' => 'fa-user-plus',
                'route' => route('super-admin.users.create'),
                'tone' => 'purple',
            ],
            [
                'title' => 'Register Supervisor',
                'description' => 'Create a supervisor account with PIN and passphrase.',
                'icon' => 'fa-chalkboard-user',
                'route' => route('super-admin.users.create', ['role' => 'supervisor']),
                'tone' => 'purple',
            ],
            [
                'title' => 'Register Student',
                'description' => 'Create a student profile and map a supervisor.',
                'icon' => 'fa-user-graduate',
                'route' => route('super-admin.users.create', ['role' => 'student']),
                'tone' => 'academic',
            ],
            [
                'title' => 'Link Supervision',
                'description' => 'Pair students with supervisors and notify both parties instantly.',
                'icon' => 'fa-link',
                'route' => route('super-admin.dashboard') . '#relationship-orchestrator',
                'tone' => 'emerald',
            ],
            [
                'title' => 'Platform Config',
                'description' => 'Update AI, email, and default platform settings.',
                'icon' => 'fa-sliders',
                'route' => route('super-admin.config'),
                'tone' => 'emerald',
            ],
            [
                'title' => 'System Status',
                'description' => 'Inspect database, queue, cache, and mail health.',
                'icon' => 'fa-server',
                'route' => route('super-admin.system-status'),
                'tone' => 'amber',
            ],
        ];

        $alerts = [
            [
                'label' => 'Suspended Universities',
                'value' => University::where('is_active', false)->whereNull('archived_at')->count(),
                'tone' => 'amber',
                'detail' => 'Tenants paused but not archived.',
                'route' => route('super-admin.universities'),
            ],
            [
                'label' => 'Archived Universities',
                'value' => University::whereNotNull('archived_at')->count(),
                'tone' => 'slate',
                'detail' => 'Tenants removed from active operations.',
                'route' => route('super-admin.universities'),
            ],
            [
                'label' => 'Inactive Admin Accounts',
                'value' => User::whereIn('role', ['admin', 'super_admin'])->where('is_active', false)->count(),
                'tone' => 'rose',
                'detail' => 'Admin-level access requiring review.',
                'route' => route('super-admin.users', ['status' => 'inactive']),
            ],
            [
                'label' => 'Queue Risk',
                'value' => $failedJobsCount ?? 0,
                'tone' => $queueDriver === 'sync' ? 'amber' : 'emerald',
                'detail' => $failedJobsCount === null ? 'Failed jobs table not installed.' : 'Failed jobs currently tracked.',
                'route' => route('super-admin.system-status'),
            ],
            [
                'label' => 'AI Key State',
                'value' => $geminiKeyConfigured ? 1 : 0,
                'tone' => $geminiKeyConfigured ? 'emerald' : 'amber',
                'detail' => $geminiKeyConfigured ? 'Gemini API key is configured.' : 'Gemini API key still needs attention.',
                'route' => route('super-admin.config'),
            ],
        ];

        $tenantQuery = University::withCount(['users', 'students', 'supervisors', 'resources', 'proposals'])
            ->addSelect([
                'last_audit_at' => AuditLog::select('created_at')
                    ->whereColumn('university_id', 'universities.id')
                    ->latest()
                    ->limit(1),
            ]);

        if ($dashboardFilters['q'] !== '') {
            $search = $dashboardFilters['q'];
            $tenantQuery->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($dashboardFilters['tenant_status'] === 'active') {
            $tenantQuery->where('is_active', true)->whereNull('archived_at');
        }

        if ($dashboardFilters['tenant_status'] === 'suspended') {
            $tenantQuery->where('is_active', false)->whereNull('archived_at');
        }

        if ($dashboardFilters['tenant_status'] === 'archived') {
            $tenantQuery->whereNotNull('archived_at');
        }

        $tenantHealth = $tenantQuery
            ->orderByDesc('users_count')
            ->limit(8)
            ->get()
            ->map(function (University $university) {
                $configChecks = [
                    !empty($university->email),
                    !empty($university->department),
                    !empty($university->phone),
                    !empty($university->branding_color),
                    !empty($university->features_enabled),
                    !empty($university->email_config),
                ];

                $completeness = (int) round((collect($configChecks)->filter()->count() / count($configChecks)) * 100);

                return [
                    'id' => $university->id,
                    'name' => $university->name,
                    'code' => $university->code,
                    'status' => $university->archived_at ? 'archived' : ($university->is_active ? 'active' : 'suspended'),
                    'users_count' => $university->users_count,
                    'students_count' => $university->students_count,
                    'supervisors_count' => $university->supervisors_count,
                    'resources_count' => $university->resources_count,
                    'proposals_count' => $university->proposals_count,
                    'config_completeness' => $completeness,
                    'last_audit_at' => $university->last_audit_at,
                    'route' => route('super-admin.universities.show', $university),
                    'suspend_route' => route('super-admin.universities.suspend', $university),
                    'activate_route' => route('super-admin.universities.activate', $university),
                ];
            });

        $securityQuery = User::with(['university', 'student.supervisor.user', 'supervisor.students'])
            ->orderByRaw("case when is_active = 0 then 0 else 1 end")
            ->orderByRaw('case when role = ? then 0 when role = ? then 1 else 2 end', ['super_admin', 'admin'])
            ->orderBy('name');

        if ($dashboardFilters['q'] !== '') {
            $search = $dashboardFilters['q'];
            $securityQuery->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        switch ($dashboardFilters['user_focus']) {
            case 'inactive':
                $securityQuery->where('is_active', false);
                break;
            case 'supervisors':
                $securityQuery->where('role', 'supervisor');
                break;
            case 'students':
                $securityQuery->where('role', 'student');
                break;
            case 'admins':
                $securityQuery->whereIn('role', ['super_admin', 'admin']);
                break;
            case 'privileged':
                $securityQuery->whereIn('role', ['super_admin', 'admin', 'supervisor']);
                break;
            default:
                $securityQuery->where(function ($query) {
                    $query
                        ->where('is_active', false)
                        ->orWhereIn('role', ['super_admin', 'admin']);
                });
                break;
        }

        $securityWatch = $securityQuery
            ->limit(8)
            ->get();

        $recentAuditLogs = AuditLog::with(['user', 'university'])
            ->when($dashboardFilters['q'] !== '', function ($query) use ($dashboardFilters) {
                $search = $dashboardFilters['q'];
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('model_type', 'like', '%' . $search . '%')
                        ->orWhere('action', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->limit(8)
            ->get();

        $recentProvisioning = User::with('university')
            ->latest()
            ->limit(6)
            ->get(['id', 'university_id', 'name', 'email', 'role', 'is_active', 'created_at']);

        $relationshipStudents = Student::query()
            ->with(['user', 'university', 'supervisor.user'])
            ->whereHas('user', fn ($query) => $query->where('is_active', true))
            ->orderByRaw('case when supervisor_id is null then 0 else 1 end')
            ->orderBy('full_name')
            ->limit(40)
            ->get();
        $recommendationStudent = $this->resolveRecommendationStudent($selectedStudentId, null, $relationshipStudents);

        $assignmentSupervisors = Supervisor::query()
            ->with(['user', 'university'])
            ->withCount('students')
            ->where('is_active', true)
            ->whereHas('user', fn ($query) => $query->where('is_active', true))
            ->orderBy('university_id')
            ->orderBy('students_count')
            ->orderBy('department')
            ->get();
        $assignmentSupervisors = $this->decorateSupervisorAssignments($assignmentSupervisors, $recommendationStudent);

        $unassignedStudents = $relationshipStudents
            ->whereNull('supervisor_id')
            ->take(8)
            ->values();
        $recommendedSupervisors = $assignmentSupervisors
            ->filter(fn (Supervisor $supervisor) => ! $recommendationStudent || (int) $supervisor->university_id === (int) $recommendationStudent->university_id)
            ->take(6)
            ->values();
        $relationshipHistory = $this->recentRelationshipHistory();
        $loadPolicy = $this->loadPolicy();

        $assignmentSummary = [
            'students' => Student::count(),
            'unassigned_students' => Student::whereNull('supervisor_id')->count(),
            'active_supervisors' => Supervisor::where('is_active', true)
                ->whereHas('user', fn ($query) => $query->where('is_active', true))
                ->count(),
            'avg_load' => $assignmentSupervisors->count() > 0
                ? round($assignmentSupervisors->avg('students_count') ?? 0, 1)
                : 0,
        ];

        $systemSnapshot = [
            ['label' => 'Database', 'value' => $databaseStatus['message'], 'state' => $databaseStatus['ok'] ? 'healthy' : 'error'],
            ['label' => 'Mail', 'value' => $mailConfigured ? 'Configured' : 'Missing configuration', 'state' => $mailConfigured ? 'healthy' : 'warning'],
            ['label' => 'Queue Driver', 'value' => $queueDriver, 'state' => $queueDriver === 'sync' ? 'warning' : 'healthy'],
            ['label' => 'Cache Driver', 'value' => $cacheDriver, 'state' => 'healthy'],
            ['label' => 'Failed Jobs', 'value' => $failedJobsCount === null ? 'N/A' : (string) $failedJobsCount, 'state' => $failedJobsCount === null ? 'warning' : ($failedJobsCount > 0 ? 'warning' : 'healthy')],
            ['label' => 'App Version', 'value' => app()->version(), 'state' => 'healthy'],
        ];

        $roleDistribution = [
            ['label' => 'Super Admins', 'value' => User::where('role', 'super_admin')->count(), 'tone' => 'purple'],
            ['label' => 'Admins', 'value' => User::where('role', 'admin')->count(), 'tone' => 'emerald'],
            ['label' => 'Supervisors', 'value' => User::where('role', 'supervisor')->count(), 'tone' => 'amber'],
            ['label' => 'Students', 'value' => User::where('role', 'student')->count(), 'tone' => 'blue'],
        ];
        $totalRoleCount = max(1, collect($roleDistribution)->sum('value'));
        $roleDistribution = collect($roleDistribution)
            ->map(function (array $item) use ($totalRoleCount) {
                $item['percentage'] = (int) round(($item['value'] / $totalRoleCount) * 100);

                return $item;
            })
            ->all();

        $auditTrend = $this->buildTrendSeries(AuditLog::query(), 'created_at', $dashboardFilters['trend_window'], 'Audit Events');
        $provisioningTrend = $this->buildTrendSeries(User::query(), 'created_at', $dashboardFilters['trend_window'], 'User Provisioning');
        $tenantActivity = University::withCount(['users', 'students', 'supervisors'])
            ->orderByDesc('users_count')
            ->limit(5)
            ->get();

        $supportTools = [
            ['title' => 'Audit Explorer', 'description' => 'Trace config changes, suspensions, and role updates.', 'route' => route('super-admin.audit-logs'), 'icon' => 'fa-shield-halved'],
            ['title' => 'Tenant Operations', 'description' => 'Review tenant health and act on archived or suspended universities.', 'route' => route('super-admin.universities'), 'icon' => 'fa-building-shield'],
            ['title' => 'Identity Operations', 'description' => 'Create, correct, or suspend privileged accounts.', 'route' => route('super-admin.users'), 'icon' => 'fa-users-gear'],
            ['title' => 'Runtime Diagnostics', 'description' => 'Inspect platform services and operational warnings.', 'route' => route('super-admin.system-status'), 'icon' => 'fa-wave-square'],
        ];

        $operatingModel = [
            [
                'eyebrow' => 'Observe',
                'title' => 'Read the platform before changing it',
                'description' => 'Start with health signals, tenant drift, and audit velocity so interventions are driven by evidence instead of guesswork.',
                'icon' => 'fa-binoculars',
                'tone' => 'slate',
                'items' => [
                    'Scan critical alerts and audit trends.',
                    'Spot tenant configuration gaps and suspicious inactivity.',
                    'Use watchlists to isolate privileged or inactive accounts.',
                ],
            ],
            [
                'eyebrow' => 'Operate',
                'title' => 'Run tenant and identity operations',
                'description' => 'Move from diagnosis into controlled actions that affect universities, privileged users, supervisors, and student onboarding.',
                'icon' => 'fa-sitemap',
                'tone' => 'academic',
                'items' => [
                    'Provision universities from a single tenant lane.',
                    'Create or correct admin, supervisor, and student records.',
                    'Keep the highest-risk actions reachable within one click.',
                ],
            ],
            [
                'eyebrow' => 'Govern',
                'title' => 'Protect trust and accountability',
                'description' => 'Keep platform access, configuration, and interventions auditable, with a clear review path for every privileged action.',
                'icon' => 'fa-shield-halved',
                'tone' => 'violet',
                'items' => [
                    'Review platform changes through audit logs.',
                    'Track privileged role concentration and disabled accounts.',
                    'Surface compliance-relevant issues before they spread across tenants.',
                ],
            ],
            [
                'eyebrow' => 'Stabilize',
                'title' => 'Keep runtime services healthy',
                'description' => 'Configuration, queues, cache, and mail should be treated as operating dependencies, not hidden settings.',
                'icon' => 'fa-server',
                'tone' => 'emerald',
                'items' => [
                    'Expose system status where operators already work.',
                    'Treat failed jobs and missing keys as action queues.',
                    'Link diagnostics directly to platform configuration.',
                ],
            ],
        ];

        $priorityQueues = [
            [
                'label' => 'Tenant Lifecycle',
                'count' => University::where('is_active', false)->whereNull('archived_at')->count() + University::whereNotNull('archived_at')->count(),
                'detail' => 'Suspended and archived universities that may need intervention or restoration.',
                'route' => route('super-admin.universities'),
                'icon' => 'fa-building-circle-exclamation',
                'tone' => 'academic',
            ],
            [
                'label' => 'Privileged Access Review',
                'count' => User::whereIn('role', ['super_admin', 'admin'])->where('is_active', false)->count(),
                'detail' => 'Admin-level accounts currently inactive and ready for security review.',
                'route' => route('super-admin.users', ['status' => 'inactive', 'role' => 'admin']),
                'icon' => 'fa-user-shield',
                'tone' => 'violet',
            ],
            [
                'label' => 'Runtime Follow-up',
                'count' => ($failedJobsCount ?? 0) + ($geminiKeyConfigured ? 0 : 1),
                'detail' => 'Failed jobs and missing AI configuration that can degrade operator workflows.',
                'route' => route('super-admin.system-status'),
                'icon' => 'fa-wave-square',
                'tone' => 'emerald',
            ],
            [
                'label' => 'Unassigned Students',
                'count' => $assignmentSummary['unassigned_students'],
                'detail' => 'Students still waiting for an assigned supervisor relationship.',
                'route' => route('super-admin.dashboard') . '#relationship-orchestrator',
                'icon' => 'fa-user-plus',
                'tone' => 'academic',
            ],
        ];

        $workstreamOwnership = [
            [
                'label' => 'Platform Operations',
                'metric' => University::count(),
                'unit' => 'tenant lanes',
                'description' => 'Own onboarding, footprint review, suspension, restoration, and tenant continuity.',
                'route' => route('super-admin.universities'),
                'icon' => 'fa-buildings',
                'tone' => 'academic',
            ],
            [
                'label' => 'Identity and Access',
                'metric' => User::whereIn('role', ['super_admin', 'admin', 'supervisor'])->count(),
                'unit' => 'privileged accounts',
                'description' => 'Control privileged access, supervisor capacity, and account-state intervention.',
                'route' => route('super-admin.users'),
                'icon' => 'fa-user-shield',
                'tone' => 'emerald',
            ],
            [
                'label' => 'Governance and Audit',
                'metric' => AuditLog::where('created_at', '>=', now()->subDays($dashboardFilters['trend_window']))->count(),
                'unit' => 'recent events',
                'description' => 'Trace decisions, review changes, and keep risk and accountability visible.',
                'route' => route('super-admin.audit-logs'),
                'icon' => 'fa-scale-balanced',
                'tone' => 'violet',
            ],
            [
                'label' => 'Configuration and Runtime',
                'metric' => PlatformSetting::count() + ($failedJobsCount ?? 0),
                'unit' => 'service touchpoints',
                'description' => 'Maintain shared settings, runtime dependencies, queues, mail, and AI readiness.',
                'route' => route('super-admin.system-status'),
                'icon' => 'fa-sliders',
                'tone' => 'amber',
            ],
        ];

        $operatorLoop = [
            [
                'step' => '01',
                'title' => 'Detect',
                'description' => 'Use alerts, trends, and summary signals to spot the issue worth attention.',
                'tone' => 'slate',
            ],
            [
                'step' => '02',
                'title' => 'Classify',
                'description' => 'Route it into tenant lifecycle, identity review, governance, or runtime follow-up.',
                'tone' => 'academic',
            ],
            [
                'step' => '03',
                'title' => 'Inspect',
                'description' => 'Open the relevant board, watchlist, or diagnostics panel to assess context.',
                'tone' => 'violet',
            ],
            [
                'step' => '04',
                'title' => 'Act',
                'description' => 'Suspend, activate, edit, configure, or provision directly from the operating lane.',
                'tone' => 'emerald',
            ],
            [
                'step' => '05',
                'title' => 'Verify',
                'description' => 'Confirm the platform is stable again through audit evidence and runtime state.',
                'tone' => 'amber',
            ],
        ];

        return view('super-admin.dashboard', compact(
            'currentUser',
            'summary',
            'quickActions',
            'alerts',
            'tenantHealth',
            'securityWatch',
            'recentAuditLogs',
            'recentProvisioning',
            'systemSnapshot',
            'roleDistribution',
            'supportTools',
            'dashboardFilters',
            'auditTrend',
            'provisioningTrend',
            'tenantActivity',
            'totalRoleCount',
            'operatingModel',
            'priorityQueues',
            'workstreamOwnership',
            'operatorLoop',
            'relationshipStudents',
            'assignmentSupervisors',
            'unassignedStudents',
            'assignmentSummary',
            'recommendedSupervisors',
            'recommendationStudent',
            'relationshipHistory',
            'loadPolicy',
        ));
    }

    public function showUniversity(University $university): View
    {
        $university->loadCount(['users', 'students', 'supervisors', 'resources', 'proposals', 'meetingLogs']);

        $recentUsers = $university->users()
            ->orderByDesc('created_at')
            ->limit(6)
            ->get(['id', 'name', 'email', 'role', 'is_active', 'created_at']);

        $recentResources = $university->resources()
            ->orderBy('stage')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get(['id', 'title', 'stage', 'type', 'updated_at']);

        $recentAuditLogs = AuditLog::with('user')
            ->where('university_id', $university->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('super-admin.university-show', compact('university', 'recentUsers', 'recentResources', 'recentAuditLogs'));
    }

    public function universities(): View
    {
        $filters = [
            'search' => trim((string) request('search', '')),
            'status' => (string) request('status', 'all'),
        ];

        $universitiesQuery = University::withCount(['users', 'students', 'supervisors', 'resources', 'proposals'])
            ->orderBy('name');

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $universitiesQuery->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($filters['status'] === 'active') {
            $universitiesQuery->where('is_active', true)->whereNull('archived_at');
        }

        if ($filters['status'] === 'suspended') {
            $universitiesQuery->where('is_active', false)->whereNull('archived_at');
        }

        if ($filters['status'] === 'archived') {
            $universitiesQuery->whereNotNull('archived_at');
        }

        $universities = $universitiesQuery->paginate(15)->withQueryString();

        $summary = [
            'total' => University::count(),
            'active' => University::where('is_active', true)->whereNull('archived_at')->count(),
            'suspended' => University::where('is_active', false)->whereNull('archived_at')->count(),
            'archived' => University::whereNotNull('archived_at')->count(),
        ];

        $queues = [
            [
                'label' => 'Lifecycle Review',
                'count' => $summary['suspended'] + $summary['archived'],
                'detail' => 'Tenants requiring recovery, restoration, or archival follow-up.',
                'tone' => 'amber',
            ],
            [
                'label' => 'Active Footprint',
                'count' => $summary['active'],
                'detail' => 'Universities currently operating on the platform.',
                'tone' => 'emerald',
            ],
            [
                'label' => 'Filtered Results',
                'count' => $universities->total(),
                'detail' => 'Tenant records currently visible under the active search and status filters.',
                'tone' => 'blue',
            ],
        ];

        return view('super-admin.universities', compact('universities', 'filters', 'summary', 'queues'));
    }

    public function createUniversity(): View
    {
        $university = new University([
            'branding_color' => '#3B82F6',
        ]);

        return view('super-admin.university-form', [
            'mode' => 'create',
            'university' => $university,
        ]);
    }

    public function storeUniversity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:universities,name',
            'code' => 'required|string|max:20|unique:universities,code',
            'email' => 'required|email|max:255',
            'department' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'branding_color' => 'required|string|max:20',
        ]);

        $university = University::create($validated);

        $this->writeAuditLog(
            $university,
            'University',
            $university->id,
            'created',
            null,
            $university->fresh()->toArray()
        );

        return redirect()
            ->route('super-admin.universities')
            ->with('success', 'University created successfully.');
    }

    public function suspendUniversity(University $university): RedirectResponse
    {
        if ($university->archived_at) {
            return redirect()
                ->route('super-admin.universities')
                ->with('error', 'Archived universities cannot be suspended. Restore the university first.');
        }

        $oldValues = $university->toArray();
        $university->update(['is_active' => false]);

        $this->writeAuditLog(
            $university,
            'University',
            $university->id,
            'updated',
            $oldValues,
            $university->fresh()->toArray() + ['transition' => 'suspended']
        );

        return redirect()
            ->route('super-admin.universities')
            ->with('success', 'University suspended successfully.');
    }

    public function activateUniversity(University $university): RedirectResponse
    {
        $oldValues = $university->toArray();
        $university->update([
            'is_active' => true,
            'archived_at' => null,
        ]);

        $this->writeAuditLog(
            $university,
            'University',
            $university->id,
            'updated',
            $oldValues,
            $university->fresh()->toArray() + ['transition' => 'activated']
        );

        return redirect()
            ->route('super-admin.universities')
            ->with('success', 'University activated successfully.');
    }

    public function archiveUniversity(University $university): RedirectResponse
    {
        if ($this->hasUniversityDependencies($university)) {
            return redirect()
                ->route('super-admin.universities')
                ->with('error', 'University cannot be archived while it still has active platform data. Suspend or migrate its records first.');
        }

        $oldValues = $university->toArray();
        $university->update([
            'is_active' => false,
            'archived_at' => now(),
        ]);

        $this->writeAuditLog(
            $university,
            'University',
            $university->id,
            'updated',
            $oldValues,
            $university->fresh()->toArray() + ['transition' => 'archived']
        );

        return redirect()
            ->route('super-admin.universities')
            ->with('success', 'University archived successfully.');
    }

    public function destroyUniversity(University $university): RedirectResponse
    {
        if ($this->hasUniversityDependencies($university)) {
            return redirect()
                ->route('super-admin.universities')
                ->with('error', 'University cannot be deleted while related users or academic records still exist.');
        }

        $oldValues = $university->toArray();
        $universityId = $university->id;
        $university->delete();

        $this->writeAuditLog(
            $university,
            'University',
            $universityId,
            'deleted',
            $oldValues,
            null
        );

        return redirect()
            ->route('super-admin.universities')
            ->with('success', 'University deleted successfully.');
    }

    public function editUniversity(University $university): View
    {
        return view('super-admin.university-form', [
            'mode' => 'edit',
            'university' => $university,
        ]);
    }

    public function updateUniversity(Request $request, University $university): RedirectResponse
    {
        $oldValues = $university->toArray();
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:universities,name,' . $university->id,
            'code' => 'required|string|max:20|unique:universities,code,' . $university->id,
            'email' => 'required|email|max:255',
            'department' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'branding_color' => 'required|string|max:20',
        ]);

        $university->update($validated);

        $this->writeAuditLog(
            $university,
            'University',
            $university->id,
            'updated',
            $oldValues,
            $university->fresh()->toArray()
        );

        return redirect()
            ->route('super-admin.universities')
            ->with('success', 'University updated successfully.');
    }

    public function users(): View
    {
        $filters = $this->userFilters(request());
        $users = $this->buildUserQuery(request())->paginate(20)->withQueryString();
        $universities = University::orderBy('name')->get(['id', 'name']);
        $summary = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'privileged' => User::whereIn('role', ['super_admin', 'admin'])->count(),
            'super_admins' => User::where('role', 'super_admin')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        $queues = [
            [
                'label' => 'Privileged Review',
                'count' => User::whereIn('role', ['super_admin', 'admin'])->where('is_active', false)->count(),
                'detail' => 'Inactive privileged accounts needing security review.',
                'tone' => 'violet',
            ],
            [
                'label' => 'Supervisor Capacity',
                'count' => User::where('role', 'supervisor')->where('is_active', true)->count(),
                'detail' => 'Active supervisors available across tenant operations.',
                'tone' => 'blue',
            ],
            [
                'label' => 'Filtered Results',
                'count' => $users->total(),
                'detail' => 'User records currently visible under the active filter set.',
                'tone' => 'emerald',
            ],
        ];

        return view('super-admin.users', compact('users', 'universities', 'filters', 'summary', 'queues'));
    }

    public function createUser(): View
    {
        $selectedRole = in_array((string) request('role'), ['student', 'supervisor', 'admin'], true)
            ? (string) request('role')
            : 'admin';

        return view('super-admin.user-form', [
            'mode' => 'create',
            'user' => new User(['is_active' => true, 'role' => $selectedRole]),
            'universities' => University::orderBy('name')->get(['id', 'name']),
            'supervisors' => $this->availableSupervisors(),
        ]);
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|in:student,supervisor,admin,super_admin',
            'require_supervisor_selection' => 'nullable|boolean',
            'supervisor_id' => [
                Rule::requiredIf(fn () => $request->input('role') === 'student' && $request->boolean('require_supervisor_selection')),
                'nullable',
                'exists:supervisors,id',
            ],
        ]);

        $validated['require_supervisor_selection'] = $request->boolean('require_supervisor_selection');

        // Only platform-level operators may assign the privileged roles. The
        // enum above keeps super_admin selectable so an existing super admin
        // can still create peers; the explicit guard below refuses anyone
        // else, including an admin reaching this endpoint through the API.
        if (! $this->actingUserIsSuperAdmin() && in_array((string) ($validated['role'] ?? null), ['admin', 'super_admin'], true)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'You are not authorised to assign the selected role.');
        }

        if (($validated['role'] ?? null) === 'student' && !$this->validateSupervisorMapping($validated)) {
            return redirect()->back()->withInput()->with('error', 'Selected supervisor must belong to the same university as the student.');
        }

        if (($validated['role'] ?? null) === 'student' && empty($validated['supervisor_id']) && !$this->userInvitationService->findLeastLoadedSupervisorId((int) $validated['university_id'])) {
            return redirect()->back()->withInput()->with('error', 'No active supervisors are available for this university. Select a supervisor first or create an active supervisor account.');
        }

        $invitation = $this->userInvitationService->createInvitedUser($validated);
        $user = $invitation['user'];

        $this->writeAuditLog(
            $user->university,
            'User',
            $user->id,
            'created',
            null,
            $user->fresh()->toArray() + ['onboarding_state' => 'pending_invitation']
        );

        return redirect()
            ->route('super-admin.users')
            ->with('success', 'User invite sent successfully. The recipient can now complete their own details.');
    }

    public function editUser(User $user): View
    {
        $user->load(['student', 'supervisor']);

        return view('super-admin.user-form', [
            'mode' => 'edit',
            'user' => $user,
            'universities' => University::orderBy('name')->get(['id', 'name']),
            'supervisors' => $this->availableSupervisors(),
        ]);
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $oldValues = $user->toArray();
        $user->load(['student.supervisor.user', 'supervisor']);
        $previousStudentSupervisor = $user->student?->supervisor;
        $previousStudentSupervisorId = $previousStudentSupervisor?->id;
        $validated = $request->validate(array_merge([
            'university_id' => 'required|exists:universities,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:student,supervisor,admin,super_admin',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:12|confirmed',
            'is_active' => 'nullable|boolean',
        ], $this->roleSpecificRules($request->input('role'), $user)));

        // Only platform-level operators may assign the privileged roles. This
        // screen is super-admin only by middleware, but the guard is stated
        // explicitly so the policy cannot silently regress if the middleware
        // broadens, and so a non-super-admin reaching this endpoint is refused
        // rather than silently downgraded to a lesser role.
        if (! $this->actingUserIsSuperAdmin() && in_array((string) ($validated['role'] ?? $user->role), ['admin', 'super_admin'], true)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'You are not authorised to assign the selected role.');
        }

        if (!$this->validateSupervisorMapping($validated)) {
            return redirect()->back()->withInput()->with('error', 'Selected supervisor must belong to the same university as the student.');
        }

        if (in_array($user->role, ['student', 'supervisor'], true) && $validated['role'] !== $user->role) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Changing an existing student or supervisor to a different role is not supported from this screen. Create a new account instead.');
        }

        if ($user->id === (int) session('user_id') && ($validated['role'] ?? $user->role) !== 'super_admin') {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'You cannot remove your own super admin role.');
        }

        if ($user->role === 'super_admin' && ($validated['role'] ?? 'super_admin') !== 'super_admin' && $this->activeSuperAdminCount() <= 1) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'At least one active super admin account must remain.');
        }

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        $this->syncUserRoleProfile($user->fresh(), $validated, false);

        $refreshedUser = $user->fresh()->load(['student.supervisor.user', 'university']);
        $updatedStudentSupervisorId = $refreshedUser->student?->supervisor_id;

        if (($validated['role'] ?? null) === 'student'
            && $updatedStudentSupervisorId
            && (int) $updatedStudentSupervisorId !== (int) ($previousStudentSupervisorId ?? 0)
            && $refreshedUser->student
            && $refreshedUser->student->supervisor) {
            $this->notifyRelationshipParties(
                $refreshedUser->student,
                $refreshedUser->student->supervisor,
                $previousStudentSupervisor
            );
        }

        $this->writeAuditLog(
            $refreshedUser->university,
            'User',
            $user->id,
            'updated',
            $oldValues,
            $refreshedUser->toArray()
        );

        return redirect()
            ->route('super-admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function assignStudentSupervisor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'supervisor_id' => 'required|exists:supervisors,id',
        ]);

        $student = Student::query()
            ->with(['user', 'university', 'supervisor.user'])
            ->findOrFail($validated['student_id']);
        $supervisor = Supervisor::query()
            ->with(['user', 'university'])
            ->findOrFail($validated['supervisor_id']);

        if ((int) $student->university_id !== (int) $supervisor->university_id) {
            return redirect()
                ->back()
                ->with('error', 'Student and supervisor must belong to the same university before they can be linked.');
        }

        if (! $supervisor->is_active || ! $supervisor->user?->is_active) {
            return redirect()
                ->back()
                ->with('error', 'Only active supervisors can receive new student links.');
        }

        $previousSupervisor = $student->supervisor;
        if ($previousSupervisor && (int) $previousSupervisor->id === (int) $supervisor->id) {
            return redirect()
                ->back()
                ->with('success', $student->full_name . ' is already linked to ' . ($supervisor->user?->name ?? 'the selected supervisor') . '.');
        }

        $oldValues = [
            'student_id' => $student->id,
            'student_name' => $student->full_name,
            'previous_supervisor_id' => $previousSupervisor?->id,
            'previous_supervisor_name' => $previousSupervisor?->user?->name,
        ];

        $student->update(['supervisor_id' => $supervisor->id]);
        $student->load(['user', 'university', 'supervisor.user']);

        $notificationStatus = $this->notifyRelationshipParties($student, $supervisor, $previousSupervisor);

        $this->writeAuditLog(
            $student->university,
            'Student',
            $student->id,
            'updated',
            $oldValues,
            [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'supervisor_id' => $supervisor->id,
                'supervisor_name' => $supervisor->user?->name,
                'transition' => $previousSupervisor ? 'supervisor_reassigned' : 'supervisor_linked',
                'supervisor_load_band' => $this->loadBand((int) $supervisor->students()->count()),
                'notifications' => $notificationStatus,
            ]
        );

        return redirect()
            ->back()
            ->with('success', 'Linked ' . $student->full_name . ' to ' . ($supervisor->user?->name ?? 'the selected supervisor') . ' and sent notifications to both parties.');
    }

    public function toggleUserStatus(User $user): RedirectResponse
    {
        if (!$user->is_active && $user->email_verified_at === null) {
            return redirect()
                ->route('super-admin.users')
                ->with('error', 'Pending invitations cannot be activated manually. The invited user must complete onboarding first.');
        }

        if ($user->id === (int) session('user_id') && $user->is_active) {
            return redirect()
                ->route('super-admin.users')
                ->with('error', 'You cannot suspend your own account.');
        }

        if ($user->role === 'super_admin' && $user->is_active && $this->activeSuperAdminCount() <= 1) {
            return redirect()
                ->route('super-admin.users')
                ->with('error', 'At least one active super admin account must remain.');
        }

        $oldValues = $user->toArray();
        $user->update(['is_active' => !$user->is_active]);

        $this->writeAuditLog(
            $user->fresh()->university,
            'User',
            $user->id,
            'updated',
            $oldValues,
            $user->fresh()->toArray() + ['transition' => $user->fresh()->is_active ? 'activated' : 'suspended']
        );

        return redirect()
            ->route('super-admin.users', request()->query())
            ->with('success', $user->is_active ? 'User reactivated successfully.' : 'User suspended successfully.');
    }

    public function resendUserInvitation(User $user): RedirectResponse
    {
        if ($user->email_verified_at !== null || $user->is_active) {
            return redirect()
                ->route('super-admin.users', request()->query())
                ->with('error', 'Only pending invitations can be resent.');
        }

        $this->userInvitationService->resendInvitation($user);

        $this->writeAuditLog(
            $user->fresh()->university,
            'User',
            $user->id,
            'updated',
            $user->toArray(),
            $user->fresh()->toArray() + ['transition' => 'invite_resent']
        );

        return redirect()
            ->route('super-admin.users', request()->query())
            ->with('success', 'Invitation resent successfully.');
    }

    public function bulkActivateUsers(Request $request): RedirectResponse
    {
        $result = $this->bulkUserActionService->activate(
            $this->validatedBulkUserIds($request),
            $this->actingUser()
        );

        return $this->redirectAfterBulkAction($request, $result, 'accounts', 'activated');
    }

    public function bulkSuspendUsers(Request $request): RedirectResponse
    {
        $result = $this->bulkUserActionService->suspend(
            $this->validatedBulkUserIds($request),
            $this->actingUser()
        );

        return $this->redirectAfterBulkAction($request, $result, 'accounts', 'suspended');
    }

    public function bulkResendInvitations(Request $request): RedirectResponse
    {
        $result = $this->bulkUserActionService->resendInvitations(
            $this->validatedBulkUserIds($request),
            $this->actingUser()
        );

        return $this->redirectAfterBulkAction($request, $result, 'invitations', 'resent');
    }

    public function exportUsers(Request $request): Response
    {
        $users = $this->buildUserQuery($request)->get();
        $filename = 'users-export-' . now()->format('Ymd-His') . '.csv';
        $this->writeExportAudit($users->count(), [
            'selection' => 'filtered',
            'filters' => $this->userFilters($request),
        ]);

        return $this->streamUserExport($users, $filename);
    }

    public function exportSelectedUsers(Request $request): Response
    {
        $userIds = $this->validatedBulkUserIds($request);
        $users = User::with(['university', 'student', 'supervisor'])
            ->whereKey($userIds)
            ->orderBy('name')
            ->orderBy('email')
            ->get();
        $filename = 'selected-users-export-' . now()->format('Ymd-His') . '.csv';
        $this->writeExportAudit($users->count(), [
            'selection' => 'selected',
            'user_ids' => $userIds,
        ]);

        return $this->streamUserExport($users, $filename);
    }

    private function validatedBulkUserIds(Request $request): array
    {
        $validated = $request->validate([
            'user_ids' => [
                'required',
                'array',
                'min:1',
                'max:' . BulkUserActionService::MAX_BATCH_SIZE,
            ],
            'user_ids.*' => [
                'required',
                'integer',
                'distinct',
                'between:1,2147483647',
                'exists:users,id',
            ],
        ]);

        return array_map('intval', $validated['user_ids']);
    }

    private function redirectAfterBulkAction(Request $request, array $result, string $noun, string $verb): RedirectResponse
    {
        $processed = (int) $result['processed'];
        $skipped = (int) $result['skipped'];
        $errorCount = count($result['errors']);
        $message = ucfirst($noun) . ' ' . $verb . ': ' . $processed . ' changed, ' . $skipped . ' skipped.';

        if ($errorCount > 0) {
            $message .= ' ' . $errorCount . ' could not be processed.';
        }

        $flashType = $processed > 0 ? 'success' : 'error';
        if ($processed === 0 && $errorCount === 0) {
            $message = 'No accounts were changed.';
        }

        return redirect()
            ->route('super-admin.users', $request->query())
            ->with($flashType, $message)
            ->with('bulk_result', $result);
    }

    private function generateUserExportCsv($users): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, [
            'User ID',
            'Name',
            'Email',
            'Role',
            'University',
            'University Code',
            'Status',
            'Email Verified',
            'Created At',
            'Updated At',
        ]);

        foreach ($users as $user) {
            fputcsv($output, [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->university?->name,
                $user->university?->code,
                $user->is_active ? 'Active' : ($user->email_verified_at === null ? 'Pending invite' : 'Suspended'),
                $user->email_verified_at?->toDateTimeString() ?? '',
                $user->created_at?->toDateTimeString() ?? '',
                $user->updated_at?->toDateTimeString() ?? '',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        return $csv;
    }

    private function streamUserExport($users, string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $csv = $this->generateUserExportCsv($users);

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function writeExportAudit(int $count, array $selection): void
    {
        $university = $this->actingUser()?->university ?: University::orderBy('id')->first();

        if (!$university) {
            return;
        }

        AuditLog::logAction(
            $university,
            $this->actingUser(),
            'User',
            0,
            'exported',
            null,
            [
                'transition' => 'bulk_export',
                'count' => $count,
                'selection' => $selection,
            ]
        );
    }

    private function userFilters(Request $request): array
    {
        return [
            'search' => trim((string) $request->query('search', '')),
            'role' => (string) $request->query('role', ''),
            'university_id' => (string) $request->query('university_id', ''),
            'status' => (string) $request->query('status', ''),
        ];
    }

    private function buildUserQuery(Request $request)
    {
        $filters = $this->userFilters($request);
        $query = User::with(['university', 'student.supervisor.user', 'supervisor.students'])
            ->orderBy('name')
            ->orderBy('email');

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($filters['role'] !== '') {
            $query->where('role', $filters['role']);
        }

        if ($filters['university_id'] !== '') {
            $query->where('university_id', $filters['university_id']);
        }

        if ($filters['status'] === 'active') {
            $query->where('is_active', true);
        }

        if ($filters['status'] === 'inactive') {
            $query->where('is_active', false);
        }

        return $query;
    }

    public function config(): View
    {
        $defaults = [
            'app_name' => config('app.name'),
            'default_university_code' => 'LASU',
            'support_email' => config('mail.from.address'),
            'ai_provider' => 'gemini',
            'ai_default_model' => config('gemini.model', 'gemini-pro'),
            'gemini_api_key' => '',
        ];

        $stored = PlatformSetting::query()->get()->mapWithKeys(function (PlatformSetting $setting) {
            return [$setting->setting_key => $setting->getTypedValue()];
        })->all();

        $settings = array_merge($defaults, $stored);
        $hasGeminiApiKey = PlatformSetting::where('setting_key', 'gemini_api_key')->whereNotNull('setting_value')->exists();

        return view('super-admin.config', compact('settings', 'hasGeminiApiKey'));
    }

    public function updateConfig(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'default_university_code' => 'required|string|max:20|exists:universities,code',
            'support_email' => 'required|email|max:255',
            'ai_provider' => 'required|string|max:50',
            'ai_default_model' => 'required|string|max:100',
            'gemini_api_key' => 'nullable|string|max:2048',
        ]);

        $dataTypes = [
            'app_name' => 'string',
            'default_university_code' => 'string',
            'support_email' => 'string',
            'ai_provider' => 'string',
            'ai_default_model' => 'string',
            'gemini_api_key' => 'string',
        ];

        $changes = [];

        foreach ($validated as $key => $value) {
            if ($key === 'gemini_api_key' && $value === null) {
                continue;
            }

            if ($key === 'gemini_api_key' && $value === '') {
                continue;
            }

            $existing = PlatformSetting::where('setting_key', $key)->first();

            $setting = PlatformSetting::updateOrCreate(
                ['setting_key' => $key],
                [
                    'setting_value' => is_array($value) ? json_encode($value) : $value,
                    'data_type' => $dataTypes[$key],
                ]
            );

            $changes[] = [
                'key' => $key,
                'before' => $existing?->getTypedValue(),
                'after' => $setting->getTypedValue(),
            ];
        }

        $auditUniversity = $this->auditContextUniversity();
        if ($auditUniversity) {
            $this->writeAuditLog(
                $auditUniversity,
                'PlatformSetting',
                0,
                'updated',
                ['changes' => array_map(fn ($change) => ['key' => $change['key'], 'before' => $change['before']], $changes)],
                ['changes' => array_map(fn ($change) => ['key' => $change['key'], 'after' => $change['after']], $changes)]
            );
        }

        return redirect()
            ->route('super-admin.config')
            ->with('success', 'Platform configuration updated successfully.');
    }

    public function auditLogs(): View
    {
        $auditLogs = AuditLog::with('user')
            ->latest()
            ->limit(100)
            ->get();

        return view('super-admin.audit-logs', compact('auditLogs'));
    }

    public function resources(): View
    {
        $resources = Resource::query()
            ->with('university')
            ->orderBy('stage')
            ->orderBy('title')
            ->paginate(25)
            ->withQueryString();

        return view('super-admin.resources', compact('resources'));
    }

    public function systemStatus(): View
    {
        $databaseStatus = $this->checkDatabaseStatus();
        $mailStatus = config('mail.default') ? 'configured' : 'not_configured';
        $queueDriver = (string) config('queue.default', 'sync');
        $cacheDriver = (string) config('cache.default', 'file');
        $failedJobsCount = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : null;
        $geminiConfigured = PlatformSetting::where('setting_key', 'gemini_api_key')->whereNotNull('setting_value')->exists();

        $summary = [
            'universities' => University::count(),
            'active_universities' => University::where('is_active', true)->whereNull('archived_at')->count(),
            'users' => User::count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'platform_settings' => PlatformSetting::count(),
            'resources' => Resource::count(),
        ];

        $healthCards = [
            [
                'label' => 'Database',
                'state' => $databaseStatus['ok'] ? 'healthy' : 'degraded',
                'detail' => $databaseStatus['message'],
            ],
            [
                'label' => 'Mail',
                'state' => $mailStatus === 'configured' ? 'healthy' : 'warning',
                'detail' => $mailStatus === 'configured' ? 'Mailer configured' : 'Mailer not configured',
            ],
            [
                'label' => 'Queue',
                'state' => $queueDriver === 'sync' ? 'warning' : 'healthy',
                'detail' => 'Driver: ' . $queueDriver,
            ],
        ];

        $components = [
            ['name' => 'Laravel', 'status' => 'running', 'value' => app()->version()],
            ['name' => 'PHP', 'status' => 'running', 'value' => phpversion()],
            ['name' => 'Database Driver', 'status' => $databaseStatus['ok'] ? 'running' : 'error', 'value' => (string) config('database.default')],
            ['name' => 'Cache Driver', 'status' => 'running', 'value' => $cacheDriver],
            ['name' => 'Mail Driver', 'status' => $mailStatus === 'configured' ? 'running' : 'warning', 'value' => (string) config('mail.default', 'none')],
            ['name' => 'Failed Jobs', 'status' => $failedJobsCount === null ? 'warning' : 'running', 'value' => $failedJobsCount === null ? 'Table not installed' : (string) $failedJobsCount],
        ];

        $queues = [
            [
                'label' => 'Runtime Follow-up',
                'count' => ($failedJobsCount ?? 0) + ($queueDriver === 'sync' ? 1 : 0),
                'detail' => 'Follow up on failed jobs and queue settings that can slow platform operations.',
                'tone' => 'amber',
                'route' => route('super-admin.system-status'),
            ],
            [
                'label' => 'Configuration Gaps',
                'count' => ($mailStatus === 'configured' ? 0 : 1) + ($geminiConfigured ? 0 : 1),
                'detail' => 'Shared services that still need configuration attention.',
                'tone' => 'violet',
                'route' => route('super-admin.config'),
            ],
            [
                'label' => 'Identity Risk',
                'count' => $summary['inactive_users'],
                'detail' => 'Inactive accounts that can affect support, access, or tenant continuity.',
                'tone' => 'blue',
                'route' => route('super-admin.users', ['status' => 'inactive']),
            ],
        ];

        return view('super-admin.system-status', compact('summary', 'healthCards', 'components', 'queues'));
    }

    protected function checkDatabaseStatus(): array
    {
        try {
            DB::connection()->getPdo();

            return ['ok' => true, 'message' => 'Connected'];
        } catch (QueryException $exception) {
            return ['ok' => false, 'message' => 'Database query failure'];
        } catch (\Throwable $exception) {
            return ['ok' => false, 'message' => 'Connection unavailable'];
        }
    }

    protected function hasUniversityDependencies(University $university): bool
    {
        return $university->users()->count() > 0
            || $university->students()->count() > 0
            || $university->supervisors()->count() > 0
            || $university->resources()->count() > 0
            || $university->proposals()->count() > 0
            || $university->meetingLogs()->count() > 0
            || $university->archiveSubmissions()->count() > 0;
    }

    protected function activeSuperAdminCount(): int
    {
        return User::where('role', 'super_admin')
            ->where('is_active', true)
            ->count();
    }

    protected function writeAuditLog(?University $university, string $modelType, int $modelId, string $action, ?array $oldValues, ?array $newValues): void
    {
        if (!$university) {
            return;
        }

        AuditLog::logAction(
            $university,
            $this->actingUser(),
            $modelType,
            $modelId,
            $action,
            $oldValues,
            $newValues
        );
    }

    protected function actingUser(): ?User
    {
        $userId = session('user_id');

        return $userId ? User::find($userId) : null;
    }

    protected function auditContextUniversity(): ?University
    {
        $actingUser = $this->actingUser();

        if ($actingUser?->university) {
            return $actingUser->university;
        }

        return University::orderBy('id')->first();
    }

    protected function actingUserIsSuperAdmin(): bool
    {
        return session('role') === 'super_admin';
    }

    protected function roleSpecificRules(string $role, ?User $user = null): array
    {
        if ($role === 'supervisor') {
            return [
                'supervisor_title' => 'required|string|max:255',
                'supervisor_department' => 'required|string|max:255',
                'research_areas' => 'nullable|string',
                'booking_url' => 'nullable|url|max:2048',
                'pin_code' => ($user?->supervisor ? 'nullable' : 'required') . '|string|min:4|max:20',
                'passphrase' => ($user?->supervisor ? 'nullable' : 'required') . '|string|min:8|max:255',
                'supervisor_is_active' => 'nullable|boolean',
            ];
        }

        if ($role === 'student') {
            return [
                'matric_number' => 'required|string|max:255|unique:students,matric_number,' . ($user?->student?->id ?? 'NULL'),
                'lastname' => 'required|string|max:255',
                'full_name' => 'required|string|max:255',
                'student_email' => 'nullable|email|max:255',
                'personal_drive_url' => 'nullable|url|max:2048',
                'degree_level' => 'required|string|max:10',
                'student_status' => 'required|in:active,suspended,completed,graduated',
                'student_account_status' => 'required|string|max:20',
                'supervisor_id' => 'nullable|exists:supervisors,id',
                'research_topic' => 'nullable|string',
                'current_stage' => 'nullable|integer|min:1|max:12',
                'progress_percentage' => 'nullable|integer|min:0|max:100',
                'points_earned' => 'nullable|integer|min:0',
            ];
        }

        return [];
    }

    protected function validateSupervisorMapping(array $validated): bool
    {
        if (empty($validated['supervisor_id'])) {
            return true;
        }

        $supervisor = Supervisor::find($validated['supervisor_id']);

        if (!$supervisor) {
            return false;
        }

        return (int) $supervisor->university_id === (int) $validated['university_id'];
    }

    protected function syncUserRoleProfile(User $user, array $validated, bool $isCreate): void
    {
        if ($validated['role'] === 'supervisor') {
            $attributes = [
                'user_id' => $user->id,
                'university_id' => $user->university_id,
                'title' => $validated['supervisor_title'],
                'department' => $validated['supervisor_department'],
                'research_areas' => $validated['research_areas'] ?? null,
                'booking_url' => $validated['booking_url'] ?? null,
                'is_active' => (bool) ($validated['supervisor_is_active'] ?? $validated['is_active'] ?? true),
            ];

            if (!empty($validated['pin_code'])) {
                $attributes['pin_code'] = Hash::make($validated['pin_code']);
            }

            if (!empty($validated['passphrase'])) {
                $attributes['passphrase'] = Hash::make($validated['passphrase']);
            }

            if ($isCreate && empty($attributes['pin_code']) || $isCreate && empty($attributes['passphrase'])) {
                throw new \InvalidArgumentException('Supervisor credentials are required for new supervisor profiles.');
            }

            Supervisor::updateOrCreate(['user_id' => $user->id], $attributes);
        }

        if ($validated['role'] === 'student') {
            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'university_id' => $user->university_id,
                    'supervisor_id' => $validated['supervisor_id'] ?? null,
                    'matric_number' => $validated['matric_number'],
                    'lastname' => $validated['lastname'],
                    'full_name' => $validated['full_name'],
                    'email' => $validated['student_email'] ?: $user->email,
                    'degree_level' => $validated['degree_level'],
                    'phone' => $validated['phone'] ?? null,
                    'personal_drive_url' => $validated['personal_drive_url'] ?? null,
                    'research_topic' => $validated['research_topic'] ?? null,
                    'current_stage' => (int) ($validated['current_stage'] ?? 1),
                    'progress_percentage' => (int) ($validated['progress_percentage'] ?? 0),
                    'points_earned' => (int) ($validated['points_earned'] ?? 0),
                    'status' => $validated['student_status'],
                    'account_status' => $validated['student_account_status'],
                ]
            );
        }
    }

    protected function availableSupervisors(?int $universityId = null)
    {
        return Supervisor::with('user')
            ->withCount('students')
            ->when($universityId, function ($query) use ($universityId) {
                $query->where('university_id', $universityId);
            })
            ->where('is_active', true)
            ->whereHas('user', fn ($query) => $query->where('is_active', true))
            ->orderBy('students_count')
            ->orderBy('department')
            ->get();
    }

    protected function loadPolicy(): array
    {
        return [
            'recommended_max' => self::LOAD_RECOMMENDED_MAX,
            'watch_max' => self::LOAD_WATCH_MAX,
        ];
    }

    protected function decorateSupervisorAssignments($supervisors, ?Student $recommendationStudent = null)
    {
        return $supervisors->map(function (Supervisor $supervisor) use ($recommendationStudent) {
            $studentsCount = (int) ($supervisor->students_count ?? 0);
            $loadBand = $this->loadBand($studentsCount);
            $recommendationScore = max(0, 100 - ($studentsCount * 10));
            $recommendationReasons = [];

            if ($recommendationStudent) {
                $fitSignals = $this->buildSupervisorFitSignals($recommendationStudent, $supervisor, $loadBand);
                $recommendationScore += $fitSignals['score'];
                $recommendationReasons = $fitSignals['reasons'];
            }

            if (empty($recommendationReasons)) {
                $recommendationReasons[] = match ($loadBand) {
                    'recommended' => 'Best next assignment based on current active load.',
                    'watch' => 'Eligible for linking, but load should be reviewed.',
                    default => 'Assignment is still possible, but supervisor load is high.',
                };
            }

            if ($recommendationStudent && (int) $supervisor->university_id !== (int) $recommendationStudent->university_id) {
                $recommendationScore -= 200;
                array_unshift($recommendationReasons, 'Different university from the selected student, so this supervisor is not eligible for direct assignment.');
            }

            $supervisor->setAttribute('load_band', $loadBand);
            $supervisor->setAttribute('load_label', match ($loadBand) {
                'recommended' => 'Recommended',
                'watch' => 'Watch load',
                default => 'High load',
            });
            $supervisor->setAttribute('recommendation_reason', implode(' ', array_slice($recommendationReasons, 0, 3)));
            $supervisor->setAttribute('recommendation_score', $recommendationScore);

            return $supervisor;
        })->sortByDesc('recommendation_score')->values();
    }

    protected function resolveRecommendationStudent(int $selectedStudentId, ?int $universityId = null, $relationshipStudents = null): ?Student
    {
        if ($selectedStudentId > 0) {
            $selectedStudent = Student::query()
                ->with(['user', 'university', 'supervisor.user'])
                ->when($universityId, fn ($query) => $query->where('university_id', $universityId))
                ->whereKey($selectedStudentId)
                ->first();

            if ($selectedStudent) {
                return $selectedStudent;
            }
        }

        $relationshipStudents ??= Student::query()
            ->with(['user', 'university', 'supervisor.user'])
            ->when($universityId, fn ($query) => $query->where('university_id', $universityId))
            ->orderByRaw('case when supervisor_id is null then 0 else 1 end')
            ->orderBy('full_name')
            ->limit(40)
            ->get();

        return $relationshipStudents->firstWhere('supervisor_id', null) ?: $relationshipStudents->first();
    }

    protected function buildSupervisorFitSignals(Student $student, Supervisor $supervisor, string $loadBand): array
    {
        $score = 0;
        $reasons = [];

        $researchAreaTokens = $this->tokenizeRecommendationText($supervisor->research_areas);
        $departmentTokens = $this->tokenizeRecommendationText($supervisor->department);
        $topicTokens = $this->tokenizeRecommendationText($student->research_topic);

        if ($topicTokens !== []) {
            $topicAreaOverlap = array_values(array_intersect($topicTokens, $researchAreaTokens));
            $topicDepartmentOverlap = array_values(array_diff(array_intersect($topicTokens, $departmentTokens), $topicAreaOverlap));

            if ($topicAreaOverlap !== []) {
                $score += min(36, count($topicAreaOverlap) * 18);
                $reasons[] = 'Topic fit: ' . implode(', ', array_slice($topicAreaOverlap, 0, 2)) . '.';
            }

            if ($topicDepartmentOverlap !== []) {
                $score += min(12, count($topicDepartmentOverlap) * 6);
                $reasons[] = 'Department fit: ' . implode(', ', array_slice($topicDepartmentOverlap, 0, 2)) . '.';
            }

            if ($topicAreaOverlap === [] && $topicDepartmentOverlap === []) {
                $reasons[] = 'No direct topic overlap detected, so load and degree experience drive this recommendation.';
            }
        } else {
            $reasons[] = 'No research topic declared yet, so recommendations lean on supervisor capacity and current lane coverage.';
        }

        $sameDegreeStudentsCount = $supervisor->students()
            ->where('degree_level', $student->degree_level)
            ->count();

        if ($sameDegreeStudentsCount > 0) {
            $score += min(12, $sameDegreeStudentsCount * 4);
            $reasons[] = 'Already supervises ' . $sameDegreeStudentsCount . ' ' . $student->degree_level . ' student' . ($sameDegreeStudentsCount === 1 ? '' : 's') . '.';
        }

        if ((int) $student->current_stage <= 2 && $loadBand === 'recommended') {
            $score += 8;
            $reasons[] = 'Low-load fit for early-stage onboarding.';
        }

        if ((int) $student->current_stage >= 4 && $loadBand !== 'high') {
            $score += 5;
            $reasons[] = 'Capacity supports later-stage milestone follow-through.';
        }

        if ($loadBand === 'high') {
            $score -= 18;
            $reasons[] = 'Current load is high and should be reviewed before assigning another student.';
        }

        return [
            'score' => $score,
            'reasons' => $reasons,
        ];
    }

    protected function tokenizeRecommendationText(?string $value): array
    {
        if (! $value) {
            return [];
        }

        $stopwords = [
            'a', 'an', 'and', 'are', 'as', 'at', 'by', 'dept', 'department', 'for', 'from',
            'in', 'into', 'of', 'on', 'or', 'the', 'to', 'with', 'studies', 'study',
        ];

        $normalized = preg_split('/[^a-z0-9]+/i', strtolower($value)) ?: [];

        return array_values(array_unique(array_filter($normalized, function (string $token) use ($stopwords) {
            if ((strlen($token) < 3 && $token !== 'ai') || in_array($token, $stopwords, true)) {
                return false;
            }

            return true;
        })));
    }

    protected function loadBand(int $studentsCount): string
    {
        if ($studentsCount <= self::LOAD_RECOMMENDED_MAX) {
            return 'recommended';
        }

        if ($studentsCount <= self::LOAD_WATCH_MAX) {
            return 'watch';
        }

        return 'high';
    }

    protected function recentRelationshipHistory(?int $universityId = null)
    {
        return AuditLog::with(['user', 'university'])
            ->when($universityId, fn ($query) => $query->where('university_id', $universityId))
            ->where('model_type', 'Student')
            ->where('action', 'updated')
            ->latest()
            ->limit(25)
            ->get()
            ->filter(function (AuditLog $log) {
                return in_array(data_get($log->new_values, 'transition'), ['supervisor_linked', 'supervisor_reassigned'], true);
            })
            ->take(6)
            ->values();
    }

    protected function notifyRelationshipParties(Student $student, Supervisor $supervisor, ?Supervisor $previousSupervisor = null): array
    {
        $student->loadMissing(['user', 'university', 'supervisor.user']);
        $supervisor->loadMissing(['user', 'university']);

        $studentEmail = $student->email ?: $student->user?->email;
        $supervisorEmail = $supervisor->user?->email;
        $universityCode = strtoupper((string) ($student->university?->code ?? 'LASU'));
        $wasReassigned = $previousSupervisor && (int) $previousSupervisor->id !== (int) $supervisor->id;
        $studentSubject = $wasReassigned
            ? 'Your supervisor assignment has been updated'
            : 'You have been linked to a supervisor';
        $supervisorSubject = $wasReassigned
            ? 'A student has been reassigned to you'
            : 'A student has been linked to you';

        $status = [
            'student' => ['email' => $studentEmail, 'status' => $studentEmail ? 'pending' : 'skipped'],
            'supervisor' => ['email' => $supervisorEmail, 'status' => $supervisorEmail ? 'pending' : 'skipped'],
        ];

        if ($studentEmail) {
            try {
                Mail::to($studentEmail)->send(new PortalEmail('supervision-linked', [
                    'subject' => $studentSubject,
                    'title' => 'Supervisor assignment updated',
                    'recipientName' => $student->full_name ?: ($student->user?->name ?? 'Student'),
                    'introText' => $wasReassigned
                        ? 'Your supervision relationship has been updated. You now have a new assigned supervisor in the portal.'
                        : 'Your supervision relationship is now active in the portal.',
                    'counterpartName' => $supervisor->user?->name ?? 'Supervisor',
                    'counterpartRole' => 'Assigned supervisor',
                    'counterpartMeta' => $supervisor->department ?: 'Supervisor profile',
                    'relationshipNote' => $wasReassigned && $previousSupervisor?->user?->name
                        ? 'Previous supervisor: ' . $previousSupervisor->user->name
                        : 'You can now continue your research workflow with a mapped supervisor.',
                    'ctaLabel' => 'Open student dashboard',
                    'url' => url('/student/dashboard'),
                    'studentName' => $student->full_name ?: ($student->user?->name ?? 'Student'),
                    'supervisorName' => $supervisor->user?->name ?? 'Supervisor',
                    'universityCode' => $universityCode,
                ]));
                $status['student']['status'] = 'sent';
            } catch (\Throwable $exception) {
                $status['student']['status'] = 'failed';
                $status['student']['error'] = $exception->getMessage();
                Log::warning('Failed to send student supervision assignment email.', [
                    'student_id' => $student->id,
                    'supervisor_id' => $supervisor->id,
                    'email' => $studentEmail,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if ($supervisorEmail) {
            try {
                Mail::to($supervisorEmail)->send(new PortalEmail('supervision-linked', [
                    'subject' => $supervisorSubject,
                    'title' => 'Student relationship updated',
                    'recipientName' => $supervisor->user?->name ?? 'Supervisor',
                    'introText' => $wasReassigned
                        ? 'A student has been reassigned to you from another supervision lane.'
                        : 'A student has been linked to your supervision lane in the portal.',
                    'counterpartName' => $student->full_name ?: ($student->user?->name ?? 'Student'),
                    'counterpartRole' => 'Assigned student',
                    'counterpartMeta' => trim(($student->matric_number ? $student->matric_number . ' · ' : '') . ($student->degree_level ?: 'Research student')),
                    'relationshipNote' => 'Review the student dashboard, proposal lane, and pending milestones from your supervisor hub.',
                    'ctaLabel' => 'Open supervisor dashboard',
                    'url' => url('/supervisor/dashboard'),
                    'studentName' => $student->full_name ?: ($student->user?->name ?? 'Student'),
                    'supervisorName' => $supervisor->user?->name ?? 'Supervisor',
                    'universityCode' => $universityCode,
                ]));
                $status['supervisor']['status'] = 'sent';
            } catch (\Throwable $exception) {
                $status['supervisor']['status'] = 'failed';
                $status['supervisor']['error'] = $exception->getMessage();
                Log::warning('Failed to send supervisor relationship assignment email.', [
                    'student_id' => $student->id,
                    'supervisor_id' => $supervisor->id,
                    'email' => $supervisorEmail,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return $status;
    }

    protected function buildTrendSeries($query, string $column, int $days, string $label): array
    {
        $start = now()->startOfDay()->subDays($days - 1);
        $buckets = [];

        for ($offset = 0; $offset < $days; $offset++) {
            $date = $start->copy()->addDays($offset);
            $buckets[$date->format('Y-m-d')] = [
                'label' => $date->format($days > 14 ? 'M j' : 'D'),
                'value' => 0,
            ];
        }

        $query->where($column, '>=', $start)
            ->get([$column])
            ->each(function ($record) use (&$buckets, $column) {
                $dateValue = $record->{$column};
                if (!$dateValue) {
                    return;
                }

                $key = $dateValue instanceof \DateTimeInterface
                    ? $dateValue->format('Y-m-d')
                    : date('Y-m-d', strtotime((string) $dateValue));

                if (isset($buckets[$key])) {
                    $buckets[$key]['value']++;
                }
            });

        $points = collect(array_values($buckets));
        $max = max(1, $points->max('value'));
        $chartPoints = $points->values()->map(function (array $point, int $index) use ($points, $max) {
            $x = $points->count() === 1 ? 120 : (240 / max(1, $points->count() - 1)) * $index;
            $y = 50 - (($point['value'] / $max) * 42);

            return round($x, 2) . ',' . round($y, 2);
        })->implode(' ');

        return [
            'label' => $label,
            'window' => $days,
            'total' => $points->sum('value'),
            'max' => $max,
            'series' => $points->all(),
            'points' => $chartPoints,
        ];
    }
}