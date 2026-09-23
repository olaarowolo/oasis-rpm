<?php

namespace App\Http\Controllers;

use App\Mail\PortalEmail;
use App\Mail\ResourceApprovalNotification;
use App\Models\Supervisor;
use App\Models\Resource;
use App\Models\ResourceProgress;
use App\Models\Student;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ResourceController extends BaseController
{
    public function listStudentResources(Request $request)
    {
        $student = Student::find(session('student_id'));
        $universityId = session('university_id');

        // The student-facing UI renders all university resources and then locks
        // future-stage items client-side. Filtering here by current stage removes
        // valid resources from the page and triggers the empty-state message.
        $resources = Resource::where('university_id', $universityId)
            ->orderBy('stage')
            ->orderBy('sort_order')
            ->get();

        $resourcesWithProgress = $resources->map(function ($resource) use ($student) {
            $progress = $student ? $resource->progress()->where('student_id', $student->id)->first() : null;
            return [
                'id' => $resource->id,
                'section' => $resource->section,
                'type' => $resource->type,
                'title' => $resource->title,
                'url' => $resource->url,
                'description' => $resource->description,
                'stage' => $resource->stage,
                'points' => $resource->points,
                'is_mandatory' => $resource->is_mandatory,
                'progress_status' => $progress?->status ?? 'pending',
                'points_earned' => $progress?->points_earned ?? 0,
                'submitted_date' => $progress?->submitted_date,
            ];
        });

        return $this->success($resourcesWithProgress, 'Resources retrieved successfully');
    }

    public function getResource(Request $request, $id)
    {
        $resource = Resource::where([
            ['id', '=', $id],
            ['university_id', '=', session('university_id')],
        ])->first();

        if (!$resource) {
            return $this->error('Resource not found', 404);
        }

        return $this->success($resource, 'Resource retrieved successfully');
    }

    public function markResourceComplete(Request $request, $id)
    {
        $resource = Resource::where([
            ['id', '=', $id],
            ['university_id', '=', session('university_id')],
        ])->first();

        if (!$resource) {
            return $this->error('Resource not found', 404);
        }

        $progress = ResourceProgress::updateOrCreate(
            [
                'student_id' => session('student_id'),
                'resource_id' => $id,
            ],
            [
                'university_id' => session('university_id'),
                'status' => 'submitted',
                'submitted_date' => now(),
            ]
        );

        // Notify the assigned supervisor for this student.
        $student = Student::find(session('student_id'));
        $supervisor = $student ? $student->supervisor()->with('user')->first() : null;

        if ($supervisor && $supervisor->user) {
            
            // Send email notification to supervisor
            $mail = new PortalEmail('resource-submitted', [
                'studentName' => $student->full_name ?? 'Student',
                'matric' => $student->matric_number ?? '',
                'resourceTitle' => $resource->title,
                'points' => $resource->points ?? 0,
                'submittedDate' => now()->format('F d, Y'),
                'url' => route('supervisor.resources.pending'),
            ]);

            Mail::to($supervisor->user->email)->send($mail);
        }

        return $this->success($progress, 'Resource marked as submitted, awaiting approval');
    }

    public function getResourceProgress(Request $request, $id)
    {
        $progress = ResourceProgress::where([
            ['resource_id', '=', $id],
            ['student_id', '=', session('student_id')],
            ['university_id', '=', session('university_id')],
        ])->first();

        if (!$progress) {
            return $this->error('No progress record found', 404);
        }

        return $this->success($progress, 'Resource progress retrieved successfully');
    }

    public function listPendingResources(Request $request)
    {
        $resources = ResourceProgress::where([
            ['university_id', '=', session('university_id')],
            ['status', '=', 'submitted'],
        ])->whereHas('student', function ($query) {
            $query->where('supervisor_id', session('supervisor_id'));
        })->with('resource', 'student')->orderBy('submitted_date')->get();

        return $this->success($resources, 'Pending resources retrieved successfully');
    }

    public function listResourceSubmissions(Request $request)
    {
        $resources = ResourceProgress::where('university_id', session('university_id'))
            ->whereHas('student', function ($query) {
                $query->where('supervisor_id', session('supervisor_id'));
            })
            ->with('resource', 'student')
            ->orderBy('submitted_date', 'desc')
            ->get();

        return $this->success($resources, 'Resource submissions retrieved successfully');
    }

    public function approveResource(Request $request, $id)
    {
        $validated = $request->validate([
            'points_earned' => 'required|integer|min:0',
            'comment' => 'sometimes|string|nullable',
        ]);

        $progress = ResourceProgress::find($id);
        if (!$progress || $progress->university_id != session('university_id') || ($progress->student && $progress->student->supervisor_id != session('supervisor_id'))) {
            return $this->error('Resource progress not found', 404);
        }

        $student = $progress->student;
        $resource = $progress->resource;

        $progress->update([
            'status' => 'approved',
            'points_earned' => $validated['points_earned'],
            'reviewed_date' => now(),
            'supervisor_comment' => $validated['comment'] ?? null,
        ]);

        $totalPoints = $student->resourceProgress()->where('status', 'approved')->sum('points_earned');
        $student->update(['points_earned' => $totalPoints]);

        // Create notification for student
        Notification::create([
            'university_id' => $student->university_id,
            'user_id' => $student->user_id,
            'type' => Notification::TYPE_RESOURCE_APPROVED,
            'title' => 'Resource Approved',
            'message' => 'Your resource submission "' . $resource->title . '" has been approved and ' . $validated['points_earned'] . ' points have been added to your profile.',
            'is_read' => false,
            'metadata' => [
                'resource_id' => $resource->id,
                'resource_progress_id' => $progress->id,
                'points_earned' => $validated['points_earned'],
                'supervisor_comment' => $validated['comment'] ?? null,
            ],
        ]);

        // Send email notification to student
        try {
            Mail::to($student->email)->send(new ResourceApprovalNotification(
                $resource->title,
                'approved',
                $validated['points_earned'],
                $validated['comment'] ?? null
            ));
        } catch (\Exception $e) {
            \Log::warning('Email notification failed: ' . $e->getMessage());
        }

        return $this->success($progress, 'Resource approved successfully');
    }

    public function rejectResource(Request $request, $id)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $progress = ResourceProgress::find($id);
        if (!$progress || $progress->university_id != session('university_id') || ($progress->student && $progress->student->supervisor_id != session('supervisor_id'))) {
            return $this->error('Resource progress not found', 404);
        }

        $student = $progress->student;
        $resource = $progress->resource;

        $progress->update([
            'status' => 'rejected',
            'reviewed_date' => now(),
            'supervisor_comment' => $validated['comment'],
        ]);

        // Create notification for student
        Notification::create([
            'university_id' => $student->university_id,
            'user_id' => $student->user_id,
            'type' => Notification::TYPE_RESOURCE_REJECTED,
            'title' => 'Resource Rejected',
            'message' => 'Your resource submission "' . $resource->title . '" has been rejected. Supervisor comment: ' . $validated['comment'],
            'is_read' => false,
            'metadata' => [
                'resource_id' => $resource->id,
                'resource_progress_id' => $progress->id,
                'supervisor_comment' => $validated['comment'],
            ],
        ]);

        // Send email notification to student
        try {
            Mail::to($student->email)->send(new ResourceApprovalNotification(
                $resource->title,
                'rejected',
                null,
                $validated['comment']
            ));
        } catch (\Exception $e) {
            \Log::warning('Email notification failed: ' . $e->getMessage());
        }

        return $this->success($progress, 'Resource rejected, student notified');
    }
}
