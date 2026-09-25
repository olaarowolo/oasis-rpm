<?php

namespace App\Http\Controllers;

use App\Mail\PortalEmail;
use App\Models\ArchiveSubmission;
use App\Models\StageHistory;
use App\Models\Student;
use App\Models\Supervisor;
use App\Services\StudentBulkImportService;
use App\Services\StudentOnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SupervisorController extends BaseController
{
    public function createStudent(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'matric_number' => 'required|string|unique:students,matric_number',
            'lastname' => 'required|string|max:100',
            'degree_level' => 'sometimes|in:BSc,MSc,PhD',
            'phone' => 'sometimes|string|max:20|nullable',
        ]);

        $supervisor = Supervisor::find(session('supervisor_id'));
        if (! $supervisor) {
            return $this->error('Supervisor not found', 404);
        }

        $temporaryPassword = Str::random(48);

        $result = app(StudentOnboardingService::class)->create(array_merge($validated, [
            'university_id' => $supervisor->university_id,
            'supervisor_id' => $supervisor->id,
            'temporary_password' => $temporaryPassword,
        ]));

        return $this->success([
            'student' => $result->student->load('user'),
            'user_id' => $result->user->id,
            'temporary_password' => $temporaryPassword,
        ], 'Student onboarded successfully');
    }

    /**
     * Download a CSV template pre-populated with one sample row per LASU
     * faculty/school so users can map columns and see expected values.
     */
    public function downloadTemplate(Request $request)
    {
        $header = 'full_name,lastname,matric_number,email,degree_level,phone,supervisor_email,faculty,department,programme,research_topic';
        $rows = $this->templateSampleRows();
        $content = $header."\r\n".implode("\r\n", $rows);

        return response($content)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="student_bulk_import_template.csv"')
            ->header('Pragma', 'no-cache')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * Bulk-import students from an uploaded CSV, assigning each to a
     * supervisor / faculty / department / programme.
     */
    public function importCsv(Request $request)
    {
        $supervisor = Supervisor::find(session('supervisor_id'));
        if (! $supervisor) {
            return $this->error('Supervisor not found', 404);
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $universityCode = $supervisor->university?->code ?? config('universities.default');

        $result = app(StudentBulkImportService::class)->import([
            'university_id' => $supervisor->university_id,
            'university_code' => $universityCode,
            'default_supervisor_id' => $supervisor->id,
        ], $request->file('csv_file'));

        $payload = [
            'total' => $result->total,
            'created' => $result->created,
            'skipped' => $result->skipped,
            'duplicates' => $result->duplicates,
            'errors' => $result->errors,
        ];

        if ($request->expectsJson()) {
            return $this->success($payload, 'CSV import complete');
        }

        return redirect()
            ->route('supervisor.students')
            ->with('import_result', $payload)
            ->with('status', sprintf('Imported %d student(s): %d created, %d skipped.', $result->total, $result->created, $result->skipped));
    }

    /**
     * Build one sample CSV row per LASU faculty/school.
     */
    protected function templateSampleRows(): array
    {
        $config = config('lasu_departments.faculties', []) + config('lasu_departments.schools', []);
        $rows = [];
        $i = 1;

        foreach ($config as $faculty => $departments) {
            $department = is_array($departments) && count($departments) > 0 ? reset($departments) : 'General';
            $degree = ($i % 2 === 0) ? 'MSc' : 'BSc';
            $rows[] = sprintf(
                'Student %s,Lastname,MAT/%04d,%s@universe.edu,%s,080300%04d,,%s,%s,%s %s,',
                $i, $i, strtolower($faculty), $degree, $i, $faculty, $department, $degree, strtolower(preg_replace('/\s+/', '', $department))
            );
            $i++;
        }

        return $rows;
    }

    public function dashboard(Request $request)
    {
        $supervisor = Supervisor::with('user')->find(session('supervisor_id'));
        $students = Student::where('university_id', session('university_id'))
            ->where('supervisor_id', session('supervisor_id'))
            ->get();

        $stats = [
            'total_students' => $students->count(),
            'active_students' => $students->where('status', 'active')->count(),
            'completed_students' => $students->where('status', 'completed')->count(),
            'graduated_students' => $students->where('status', 'graduated')->count(),
            'archived_students' => $students->where('account_status', 'archived')->count(),
        ];

        return $this->success(['supervisor' => $supervisor, 'statistics' => $stats], 'Dashboard loaded successfully');
    }

    public function getProfile(Request $request)
    {
        $supervisor = Supervisor::with(['user', 'university'])->find(session('supervisor_id'));

        if (! $supervisor) {
            return $this->error('Supervisor not found', 404);
        }

        return $this->success($supervisor->makeHidden(['pin_code', 'passphrase']), 'Profile retrieved');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:50',
            'department' => 'sometimes|string|max:100',
            'research_areas' => 'sometimes|string|nullable',
            'booking_url' => 'sometimes|url|nullable',
        ]);

        $supervisor = Supervisor::find(session('supervisor_id'));
        $supervisor->update($validated);

        return $this->success($supervisor->makeHidden(['pin_code', 'passphrase']), 'Profile updated');
    }

    public function getRoster(Request $request)
    {
        $students = Student::where('university_id', session('university_id'))
            ->where('supervisor_id', session('supervisor_id'))
            ->with('user', 'proposals', 'meetingLogs')
            ->get();

        return $this->success($students, 'Student roster retrieved successfully');
    }

    public function getStudent(Request $request, $id)
    {
        $student = Student::where([
            ['id', '=', $id],
            ['university_id', '=', session('university_id')],
            ['supervisor_id', '=', session('supervisor_id')],
        ])->with('proposals', 'meetingLogs', 'resourceProgress', 'stageHistory')
            ->first();

        if (! $student) {
            return $this->error('Student not found', 404);
        }

        return $this->success($student, 'Student details retrieved successfully');
    }

    public function setStudentStage(Request $request, $id)
    {
        $maxStage = count(config('research.stages', []));
        $validated = $request->validate([
            'stage' => 'required|integer|min:1|max:'.$maxStage,
            'note' => 'sometimes|string|nullable',
        ]);

        $student = Student::find($id);
        if (! $student || $student->university_id != session('university_id') || $student->supervisor_id != session('supervisor_id')) {
            return $this->error('Student not found', 404);
        }

        $oldStage = $student->current_stage;
        $student->update(['current_stage' => $validated['stage']]);

        if ((int) $validated['stage'] === $maxStage) {
            $this->markStudentGraduatedAndCreateArchive($student);
        }

        StageHistory::create([
            'university_id' => session('university_id'),
            'student_id' => $id,
            'stage_number' => $validated['stage'],
            'stage_name' => config('research.stages')[$validated['stage'] - 1]['name'],
            'action' => 'entered',
            'note' => $validated['note'] ?? "Moved from stage {$oldStage}",
        ]);

        $student->loadMissing('user');

        $studentEmail = $student->user?->email ?: $student->email;
        if ($studentEmail) {
            $mail = new PortalEmail('stage-advanced', [
                'studentName' => $student->full_name,
                'stage' => $validated['stage'],
                'total' => $maxStage,
                'stageName' => config('research.stages')[$validated['stage'] - 1]['name'] ?? 'Stage '.$validated['stage'],
                'note' => $validated['note'] ?: 'Your research stage has been updated by your supervisor.',
                'url' => route('student.dashboard'),
            ]);

            Mail::to($studentEmail)->send($mail);
        }

        return $this->success($student, 'Student stage updated successfully');
    }

    public function updateStudentStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:active,suspended,completed,graduated',
            'account_status' => 'sometimes|in:active,inactive,archived',
        ]);

        if (! isset($validated['status']) && ! isset($validated['account_status'])) {
            return $this->error('Provide status or account_status', 422);
        }

        $student = Student::find($id);
        if (! $student || $student->university_id != session('university_id') || $student->supervisor_id != session('supervisor_id')) {
            return $this->error('Student not found', 404);
        }

        $student->update($validated);

        if (($validated['status'] ?? null) === 'graduated') {
            $this->markStudentGraduatedAndCreateArchive($student);
            $student->refresh();
        }

        return $this->success($student, 'Student status updated successfully');
    }

    public function advanceStudentStage(Request $request)
    {
        $validated = $request->validate(['student_id' => 'required|integer']);

        $student = Student::find($validated['student_id']);
        if (! $student || $student->university_id != session('university_id') || $student->supervisor_id != session('supervisor_id')) {
            return $this->error('Student not found', 404);
        }

        $maxStage = count(config('research.stages', []));
        if ($student->current_stage >= $maxStage) {
            return $this->error('Student is already at the final stage', 400);
        }

        $newStage = $student->current_stage + 1;
        $oldStage = $student->current_stage;
        $student->update(['current_stage' => $newStage]);

        if ($newStage === $maxStage) {
            $this->markStudentGraduatedAndCreateArchive($student);
            $student->refresh();
        }

        StageHistory::create([
            'university_id' => session('university_id'),
            'student_id' => $student->id,
            'stage_number' => $newStage,
            'stage_name' => config('research.stages')[$newStage - 1]['name'],
            'action' => 'entered',
            'note' => 'Advanced by supervisor',
        ]);

        // Send email notification to student
        $user = $student->user;
        $mail = new PortalEmail('stage-advanced', [
            'studentName' => $student->full_name,
            'stage' => $newStage,
            'total' => $maxStage,
            'stageName' => config('research.stages')[$newStage - 1]['name'] ?? 'Stage '.$newStage,
            'note' => 'Congratulations! You have advanced to the next stage of your research journey.',
            'url' => route('student.dashboard'),
        ]);

        Mail::to($user->email)->send($mail);

        return $this->success($student, 'Student advanced to next stage');
    }

    public function gateStage(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|integer',
            'gate_reason' => 'required|string',
        ]);

        $student = Student::find($validated['student_id']);
        if (! $student || $student->university_id != session('university_id') || $student->supervisor_id != session('supervisor_id')) {
            return $this->error('Student not found', 404);
        }

        StageHistory::create([
            'university_id' => session('university_id'),
            'student_id' => $student->id,
            'stage_number' => $student->current_stage,
            'stage_name' => $student->getCurrentStageName(),
            'action' => 'gated',
            'note' => $validated['gate_reason'],
        ]);

        return $this->success(null, 'Stage gated successfully');
    }

    public function listPendingArchiveSubmissions(Request $request)
    {
        $rows = ArchiveSubmission::where('university_id', session('university_id'))
            ->whereHas('student', function ($query) {
                $query->where('supervisor_id', session('supervisor_id'));
            })
            ->whereIn('submission_status', [
                ArchiveSubmission::STATUS_SUBMITTED,
                ArchiveSubmission::STATUS_UNDER_REVIEW,
            ])
            ->with('student')
            ->orderBy('submitted_at', 'asc')
            ->get();

        return $this->success($rows, 'Pending archive submissions retrieved successfully');
    }

    public function getArchiveSubmission(Request $request, $id)
    {
        $archive = ArchiveSubmission::where('university_id', session('university_id'))
            ->whereHas('student', function ($query) {
                $query->where('supervisor_id', session('supervisor_id'));
            })
            ->with('student', 'reviewer')
            ->find($id);

        if (! $archive) {
            return $this->error('Archive submission not found', 404);
        }

        return $this->success($archive, 'Archive submission retrieved successfully');
    }

    public function approveArchiveSubmission(Request $request, $id)
    {
        $validated = $request->validate([
            'publish_now' => 'sometimes|boolean',
            'reviewer_note' => 'sometimes|string|nullable',
        ]);

        $archive = ArchiveSubmission::where('university_id', session('university_id'))
            ->whereHas('student', function ($query) {
                $query->where('supervisor_id', session('supervisor_id'));
            })
            ->find($id);
        if (! $archive) {
            return $this->error('Archive submission not found', 404);
        }

        $publishNow = (bool) ($validated['publish_now'] ?? false);
        $newStatus = $publishNow ? ArchiveSubmission::STATUS_PUBLISHED : ArchiveSubmission::STATUS_APPROVED;

        $archive->update([
            'submission_status' => $newStatus,
            'reviewed_at' => now(),
            'published_at' => $publishNow ? now() : null,
            'reviewed_by_user_id' => session('user_id'),
            'reviewer_note' => $validated['reviewer_note'] ?? null,
        ]);

        $student = Student::find($archive->student_id);
        if ($student) {
            $student->update([
                'status' => 'graduated',
                'account_status' => 'archived',
                'archived_at' => now(),
            ]);
        }

        return $this->success($archive->fresh(), 'Archive submission approved successfully');
    }

    public function rejectArchiveSubmission(Request $request, $id)
    {
        $validated = $request->validate([
            'reviewer_note' => 'required|string|min:5',
        ]);

        $archive = ArchiveSubmission::where('university_id', session('university_id'))
            ->whereHas('student', function ($query) {
                $query->where('supervisor_id', session('supervisor_id'));
            })
            ->find($id);
        if (! $archive) {
            return $this->error('Archive submission not found', 404);
        }

        $archive->update([
            'submission_status' => ArchiveSubmission::STATUS_REJECTED,
            'reviewed_at' => now(),
            'reviewed_by_user_id' => session('user_id'),
            'reviewer_note' => $validated['reviewer_note'],
        ]);

        return $this->success($archive->fresh(), 'Archive submission rejected and returned for revision');
    }

    private function markStudentGraduatedAndCreateArchive(Student $student): void
    {
        $student->update([
            'status' => 'graduated',
            'account_status' => 'inactive',
            'graduated_at' => $student->graduated_at ?: now(),
        ]);

        ArchiveSubmission::firstOrCreate(
            [
                'university_id' => $student->university_id,
                'student_id' => $student->id,
            ],
            [
                'degree_level' => $student->degree_level ?: 'BSc',
                'project_type' => 'project',
                'submission_status' => ArchiveSubmission::STATUS_DRAFT,
                'visibility' => 'institution_only',
            ]
        );
    }
}
