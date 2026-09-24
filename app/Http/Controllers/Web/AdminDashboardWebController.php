<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Mail\PortalEmail;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\SystemConfig;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminDashboardWebController extends BaseController
{
    protected const LOAD_RECOMMENDED_MAX = 5;
    protected const LOAD_WATCH_MAX = 8;

    public function dashboard(Request $request): View
    {
        $currentUser = $this->actingUser()?->load('university');
        $selectedUniversityId = $this->resolveScopeUniversityId($request);
        $selectedStudentId = (int) $request->query('student_id', 0);

        $userQuery = User::query();
        $studentQuery = Student::query();
        $supervisorQuery = Supervisor::query();

        if ($selectedUniversityId) {
            $userQuery->where('university_id', $selectedUniversityId);
            $studentQuery->where('university_id', $selectedUniversityId);
            $supervisorQuery->where('university_id', $selectedUniversityId);
        }

        $stats = [
            'total_universities' => University::count(),
            'active_users' => (clone $userQuery)->where('is_active', true)->count(),
            'supervisors' => (clone $supervisorQuery)->where('is_active', true)->count(),
            'students' => (clone $studentQuery)->whereIn('status', ['active', 'completed', 'graduated'])->count(),
            'pending_approvals' => (clone $studentQuery)->where('status', 'suspended')->count() + (clone $supervisorQuery)->where('is_active', false)->count(),
            'unassigned_students' => (clone $studentQuery)->whereNull('supervisor_id')->count(),
            'inactive_supervisors' => (clone $supervisorQuery)->where('is_active', false)->count(),
            'pending_invites' => (clone $userQuery)->where('is_active', false)->whereNull('email_verified_at')->count(),
            'suspended_students' => (clone $studentQuery)->where('status', 'suspended')->count(),
        ];

        $recentUsers = (clone $userQuery)
            ->with(['university', 'student', 'supervisor'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $unassignedStudents = Student::query()
            ->with(['user', 'supervisor.user'])
            ->when($selectedUniversityId, fn ($query) => $query->where('university_id', $selectedUniversityId))
            ->whereNull('supervisor_id')
            ->orderBy('full_name')
            ->limit(8)
            ->get();

        $supervisorCapacity = Supervisor::query()
            ->with(['user'])
            ->withCount('students')
            ->when($selectedUniversityId, fn ($query) => $query->where('university_id', $selectedUniversityId))
            ->whereHas('user', fn ($query) => $query->where('is_active', true))
            ->orderBy('students_count')
            ->orderBy('department')
            ->limit(8)
            ->get();

        $relationshipStudents = Student::query()
            ->with(['user', 'supervisor.user'])
            ->when($selectedUniversityId, fn ($query) => $query->where('university_id', $selectedUniversityId))
            ->whereHas('user', fn ($query) => $query->where('is_active', true))
            ->orderByRaw('case when supervisor_id is null then 0 else 1 end')
            ->orderBy('full_name')
            ->limit(40)
            ->get();

        $recommendationStudent = $this->resolveRecommendationStudent($selectedStudentId, $selectedUniversityId, $relationshipStudents);
        $assignmentSupervisors = $this->decorateSupervisorAssignments($this->availableSupervisors($selectedUniversityId), $recommendationStudent);
        $recommendedSupervisors = $assignmentSupervisors->take(4);
        $relationshipHistory = $this->recentRelationshipHistory($selectedUniversityId);
        $loadPolicy = $this->loadPolicy();

        return view('admin-dashboard', compact(
            'currentUser',
            'stats',
            'recentUsers',
            'selectedUniversityId',
            'unassignedStudents',
            'supervisorCapacity',
            'relationshipStudents',
            'assignmentSupervisors',
            'recommendedSupervisors',
            'recommendationStudent',
            'relationshipHistory',
            'loadPolicy'
        ));
    }

    public function users(Request $request): View
    {
        $selectedUniversityId = $this->resolveScopeUniversityId($request);
        $selectedStudentId = (int) $request->query('student_id', 0);
        $selectedRole = (string) $request->query('role', '');
        $selectedStatus = (string) $request->query('status', '');
        $queue = (string) $request->query('queue', '');

        $universities = $this->availableUniversities($request);
        $userQuery = User::with(['university', 'student.supervisor.user', 'supervisor'])
            ->orderBy('name');

        if ($selectedUniversityId) {
            $userQuery->where('university_id', $selectedUniversityId);
        }

        if ($selectedRole) {
            $userQuery->where('role', $selectedRole);
        }

        if ($selectedStatus === 'active') {
            $userQuery->where('is_active', true);
        }

        if ($selectedStatus === 'inactive') {
            $userQuery->where('is_active', false);
        }

        if ($queue === 'unassigned_students') {
            $userQuery->where('role', 'student')->whereHas('student', fn ($query) => $query->whereNull('supervisor_id'));
        }

        if ($queue === 'inactive_supervisors') {
            $userQuery->where('role', 'supervisor')->where(function ($query) {
                $query->where('is_active', false)
                    ->orWhereHas('supervisor', fn ($supervisorQuery) => $supervisorQuery->where('is_active', false));
            });
        }

        if ($queue === 'suspended_students') {
            $userQuery->where('role', 'student')->whereHas('student', fn ($query) => $query->where('status', 'suspended'));
        }

        if ($queue === 'pending_invites') {
            $userQuery->where('is_active', false)->whereNull('email_verified_at');
        }

        $users = $userQuery->get();

        $stats = [
            'total' => $users->count(),
            'active' => $users->where('is_active', true)->count(),
            'admins' => $users->whereIn('role', ['admin', 'super_admin'])->count(),
            'supervisors' => $users->where('role', 'supervisor')->count(),
            'students' => $users->where('role', 'student')->count(),
            'unassigned_students' => $users->filter(fn (User $user) => $user->role === 'student' && !$user->student?->supervisor_id)->count(),
            'inactive_supervisors' => $users->filter(fn (User $user) => $user->role === 'supervisor' && (!$user->is_active || !$user->supervisor?->is_active))->count(),
        ];

        $roles = [
            'student' => 'Student',
            'supervisor' => 'Supervisor',
            'admin' => 'Admin',
            'super_admin' => 'Super Admin',
        ];

        $relationshipStudents = Student::with(['user', 'supervisor.user'])
            ->when($selectedUniversityId, fn ($query) => $query->where('university_id', $selectedUniversityId))
            ->orderByRaw('case when supervisor_id is null then 0 else 1 end')
            ->orderBy('full_name')
            ->limit(40)
            ->get();
        $recommendationStudent = $this->resolveRecommendationStudent($selectedStudentId, $selectedUniversityId, $relationshipStudents);
        $supervisors = $this->decorateSupervisorAssignments($this->availableSupervisors($selectedUniversityId), $recommendationStudent);
        $recommendedSupervisors = $supervisors->take(4);
        $relationshipHistory = $this->recentRelationshipHistory($selectedUniversityId);
        $loadPolicy = $this->loadPolicy();

        return view('admin.users', compact(
            'users',
            'universities',
            'stats',
            'roles',
            'selectedUniversityId',
            'selectedRole',
            'selectedStatus',
            'queue',
            'supervisors',
            'relationshipStudents',
            'recommendedSupervisors',
            'recommendationStudent',
            'relationshipHistory',
            'loadPolicy'
        ));
    }

    protected function resolveScopeUniversityId(Request $request): ?int
    {
        $actingUser = $this->actingUser();
        $sessionUniversityId = session('university_id');
        $queryUniversityId = $request->query('university_id');

        if ($actingUser && $actingUser->role !== 'super_admin') {
            return (int) ($sessionUniversityId ?: $actingUser->university_id ?: 0) ?: null;
        }

        return (int) ($queryUniversityId ?: $sessionUniversityId ?: $actingUser?->university_id ?: 0) ?: null;
    }

    protected function availableUniversities(Request $request)
    {
        $actingUser = $this->actingUser();

        if ($actingUser && $actingUser->role !== 'super_admin') {
            return University::whereKey($this->resolveScopeUniversityId($request))->get();
        }

        return University::orderBy('name')->get();
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
                    'recommended' => 'Best candidate based on current active load.',
                    'watch' => 'Still assignable, but capacity should be reviewed.',
                    default => 'Assignment is allowed, but supervisor load is high.',
                };
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
                ->with(['user', 'supervisor.user'])
                ->when($universityId, fn ($query) => $query->where('university_id', $universityId))
                ->whereKey($selectedStudentId)
                ->first();

            if ($selectedStudent) {
                return $selectedStudent;
            }
        }

        $relationshipStudents ??= Student::query()
            ->with(['user', 'supervisor.user'])
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

    protected function actingUser(): ?User
    {
        $userId = session('user_id');

        return $userId ? User::find($userId) : null;
    }

    protected function writeAuditLog(?University $university, string $modelType, int $modelId, string $action, ?array $oldValues, ?array $newValues): void
    {
        if (! $university) {
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
}