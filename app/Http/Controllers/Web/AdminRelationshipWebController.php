<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Mail\PortalEmail;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminRelationshipWebController extends BaseController
{
    protected const LOAD_RECOMMENDED_MAX = 5;
    protected const LOAD_WATCH_MAX = 8;

    public function assign(Request $request): RedirectResponse
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