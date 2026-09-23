<?php

namespace App\Http\Controllers;

use App\Models\ArchiveSubmission;
use App\Models\Student;
use App\Models\StageHistory;
use App\Models\TopicHistory;
use Illuminate\Http\Request;

class StudentController extends BaseController
{
    public function dashboard(Request $request)
    {
        $student = Student::with('proposals', 'meetingLogs', 'resourceProgress', 'archiveSubmission', 'supervisor.user')
            ->find(session('student_id'));

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $currentStage = config('research.stages')[$student->current_stage - 1] ?? null;

        return $this->success([
            'student' => $student,
            'supervisor' => $student->supervisor ? [
                'id' => $student->supervisor->id,
                'name' => $student->supervisor->user?->name ?? '',
                'title' => $student->supervisor->title,
                'department' => $student->supervisor->department,
                'email' => $student->supervisor->user?->email ?? '',
                'university_id' => $student->supervisor->university_id,
            ] : null,
            'current_stage' => $currentStage,
            'progress' => [
                'percentage' => $student->progress_percentage,
                'points' => $student->points_earned,
                'stage_name' => $student->getCurrentStageName(),
            ],
            'recent_proposals' => $student->proposals()->latest()->take(3)->get(),
            'recent_meetings' => $student->meetingLogs()->latest()->take(3)->get(),
            'completed_resources' => $student->resourceProgress()->where('status', 'approved')->count(),
            'archive_submission' => $student->archiveSubmission,
        ], 'Dashboard loaded successfully');
    }

    public function getRoadmap(Request $request)
    {
        $stages = config('research.stages');
        $student = Student::find(session('student_id'));
        $stageHistory = StageHistory::where('student_id', $student->id)->orderBy('created_at')->get();

        $roadmapData = collect($stages)->map(function ($stage) use ($stageHistory) {
            $history = $stageHistory->where('stage_number', $stage['number'])->first();
            return [
                ...$stage,
                'status' => $history ? 'completed' : 'pending',
                'completed_date' => $history?->created_at,
            ];
        });

        return $this->success($roadmapData, 'Roadmap retrieved successfully');
    }

    public function getProgress(Request $request)
    {
        $student = Student::find(session('student_id'));
        $resourceProgress = $student->resourceProgress()->with('resource')->get();

        $completedResources = $resourceProgress->where('status', 'approved')->count();
        $totalResources = $resourceProgress->count();

        return $this->success([
            'current_stage' => $student->current_stage,
            'stage_name' => $student->getCurrentStageName(),
            'progress_percentage' => $student->progress_percentage,
            'points_earned' => $student->points_earned,
            'resources_completed' => $completedResources,
            'total_resources' => $totalResources,
            'topic_approved' => $student->hasApprovedTopic(),
            'last_meeting' => $student->last_meeting_date,
        ], 'Progress retrieved successfully');
    }

    public function getProfile(Request $request)
    {
        $student = Student::find(session('student_id'));
        return $this->success($student, 'Profile retrieved successfully');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'personal_drive_url' => 'sometimes|url|nullable',
            'degree_level' => 'sometimes|in:BSc,MSc,PhD',
        ]);

        $student = Student::find(session('student_id'));
        $student->update($validated);

        return $this->success($student, 'Profile updated successfully');
    }

    public function getStageHistory(Request $request)
    {
        $stageHistory = StageHistory::where('student_id', session('student_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($stageHistory, 'Stage history retrieved successfully');
    }

    public function getCurrentStage(Request $request)
    {
        $student = Student::find(session('student_id'));
        $stage = config('research.stages')[$student->current_stage - 1] ?? null;

        if (!$stage) {
            return $this->error('Stage not found', 404);
        }

        return $this->success($stage, 'Current stage retrieved successfully');
    }

    public function getTopicHistory(Request $request)
    {
        $topicHistory = TopicHistory::where('student_id', session('student_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($topicHistory, 'Topic history retrieved successfully');
    }

    public function getArchiveSubmission(Request $request)
    {
        $student = Student::find(session('student_id'));
        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $archive = $this->getOrCreateArchiveSubmission($student);

        return $this->success($archive, 'Archive submission retrieved successfully');
    }

    public function upsertArchiveSubmission(Request $request)
    {
        $student = Student::find(session('student_id'));
        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string|min:30',
            'keywords' => 'required|string|max:500',
            'department' => 'sometimes|string|max:255|nullable',
            'degree_level' => 'required|in:BSc,MSc,PhD',
            'project_type' => 'sometimes|in:project,thesis,dissertation|nullable',
            'visibility' => 'sometimes|in:private,institution_only,public|nullable',
        ]);

        $archive = $this->getOrCreateArchiveSubmission($student);

        if (in_array($archive->submission_status, [ArchiveSubmission::STATUS_SUBMITTED, ArchiveSubmission::STATUS_UNDER_REVIEW, ArchiveSubmission::STATUS_APPROVED, ArchiveSubmission::STATUS_PUBLISHED], true)) {
            return $this->error('Archive submission cannot be edited in its current status', 422);
        }

        $student->update([
            'degree_level' => $validated['degree_level'],
        ]);

        $archive->fill([
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'keywords' => $validated['keywords'],
            'department' => $validated['department'] ?? $archive->department,
            'degree_level' => $validated['degree_level'],
            'project_type' => $validated['project_type'] ?? ($archive->project_type ?: 'project'),
            'visibility' => $validated['visibility'] ?? ($archive->visibility ?: 'institution_only'),
            'submission_status' => ArchiveSubmission::STATUS_DRAFT,
        ]);
        $archive->save();

        return $this->success($archive, 'Archive draft saved successfully');
    }

    public function uploadArchiveDocument(Request $request)
    {
        $student = Student::find(session('student_id'));
        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $validated = $request->validate([
            'final_document' => 'required|file|mimes:pdf|max:20480',
        ]);

        $archive = $this->getOrCreateArchiveSubmission($student);

        if (in_array($archive->submission_status, [ArchiveSubmission::STATUS_APPROVED, ArchiveSubmission::STATUS_PUBLISHED], true)) {
            return $this->error('Archive is already finalized', 422);
        }

        $degreeSegment = strtolower($student->degree_level ?: 'bsc');
        $safeMatric = preg_replace('/[^A-Za-z0-9\-]/', '_', $student->matric_number);
        $path = $validated['final_document']->storeAs(
            "archives/{$student->university_id}/{$degreeSegment}/{$safeMatric}",
            'final_manuscript.pdf',
            'public'
        );

        $archive->update([
            'final_document_path' => $path,
            'submission_status' => ArchiveSubmission::STATUS_DRAFT,
        ]);

        return $this->success([
            'path' => $path,
            'url' => asset('storage/' . $path),
            'archive_submission' => $archive->fresh(),
        ], 'Archive document uploaded successfully');
    }

    public function submitArchiveSubmission(Request $request)
    {
        $student = Student::find(session('student_id'));
        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $archive = $this->getOrCreateArchiveSubmission($student);

        if (!$archive->title || !$archive->abstract || !$archive->keywords || !$archive->final_document_path) {
            return $this->error('Please complete archive metadata and upload final document before submission', 422);
        }

        $archive->update([
            'submission_status' => ArchiveSubmission::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'reviewed_by_user_id' => null,
            'reviewer_note' => null,
        ]);

        return $this->success($archive->fresh(), 'Archive submitted for review successfully');
    }

    private function getOrCreateArchiveSubmission(Student $student): ArchiveSubmission
    {
        return ArchiveSubmission::firstOrCreate(
            [
                'university_id' => $student->university_id,
                'student_id' => $student->id,
            ],
            [
                'degree_level' => $student->degree_level ?: 'BSc',
                'project_type' => 'project',
                'submission_status' => ArchiveSubmission::STATUS_DRAFT,
                'visibility' => 'institution_only',
                'department' => null,
            ]
        );
    }
}
