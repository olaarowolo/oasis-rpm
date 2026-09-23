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

        $assignmentSupervisors = $this->decorateSupervisorAssignments($this->availableSupervisors($selectedUniversityId));
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
            'relationshipHistory',
            'loadPolicy'
        ));
    }

    public function users(Request $request): View
    {
        $selectedUniversityId = $this->resolveScopeUniversityId($request);
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

        $supervisors = $this->decorateSupervisorAssignments($this->availableSupervisors($selectedUniversityId));
        $relationshipStudents = Student::with(['user', 'supervisor.user'])
            ->when($selectedUniversityId, fn ($query) => $query->where('university_id', $selectedUniversityId))
            ->orderByRaw('case when supervisor_id is null then 0 else 1 end')
            ->orderBy('full_name')
            ->limit(40)
            ->get();
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
            'relationshipHistory',
            'loadPolicy'
        ));
    }

    public function assignStudentSupervisor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'supervisor_id' => 'required|exists:supervisors,id',
            'scope_university_id' => 'nullable|exists:universities,id',
        ]);

        $student = Student::query()
            ->with(['user', 'university', 'supervisor.user'])
            ->findOrFail($validated['student_id']);
        $supervisor = Supervisor::query()
            ->with(['user', 'university'])
            ->findOrFail($validated['supervisor_id']);

        $scopeUniversityId = (int) ($validated['scope_university_id'] ?? $this->resolveScopeUniversityId($request) ?? 0);

        if ($scopeUniversityId > 0 && ((int) $student->university_id !== $scopeUniversityId || (int) $supervisor->university_id !== $scopeUniversityId)) {
            return redirect()
                ->back()
                ->with('error', 'You can only manage supervision links within the active university scope.');
        }

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

    protected function decorateSupervisorAssignments($supervisors)
    {
        return $supervisors->map(function (Supervisor $supervisor) {
            $studentsCount = (int) ($supervisor->students_count ?? 0);
            $loadBand = $this->loadBand($studentsCount);
            $supervisor->setAttribute('load_band', $loadBand);
            $supervisor->setAttribute('load_label', match ($loadBand) {
                'recommended' => 'Recommended',
                'watch' => 'Watch load',
                default => 'High load',
            });
            $supervisor->setAttribute('recommendation_reason', match ($loadBand) {
                'recommended' => 'Best candidate based on current active load.',
                'watch' => 'Still assignable, but capacity should be reviewed.',
                default => 'Assignment is allowed, but supervisor load is high.',
            });
            $supervisor->setAttribute('recommendation_score', max(0, 100 - ($studentsCount * 10)));

            return $supervisor;
        })->sortByDesc('recommendation_score')->values();
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

    protected function actingUser(): ?User
    {
        $userId = session('user_id');

        return $userId ? User::find($userId) : null;
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
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        if ($supervisorEmail) {
            try {
                Mail::to($supervisorEmail)->send(new PortalEmail('supervision-linked', [
                    'subject' => $supervisorSubject,
                    'title' => 'Student supervision assignment updated',
                    'recipientName' => $supervisor->user?->name ?? 'Supervisor',
                    'introText' => $wasReassigned
                        ? 'A student has been reassigned to you in the portal.'
                        : 'A student has been linked to you in the portal.',
                    'counterpartName' => $student->full_name ?: ($student->user?->name ?? 'Student'),
                    'counterpartRole' => 'Assigned student',
                    'counterpartMeta' => $student->matric_number ?: ($student->degree_level ?: 'Student profile'),
                    'relationshipNote' => $wasReassigned && $previousSupervisor?->user?->name
                        ? 'This student was previously assigned to ' . $previousSupervisor->user->name . '.'
                        : 'You can now continue supervision tasks with this student in the portal.',
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
                Log::warning('Failed to send supervisor supervision assignment email.', [
                    'student_id' => $student->id,
                    'supervisor_id' => $supervisor->id,
                    'email' => $supervisorEmail,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $status;
    }
}