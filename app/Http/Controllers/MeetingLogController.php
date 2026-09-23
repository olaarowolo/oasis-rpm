<?php

namespace App\Http\Controllers;

use App\Mail\PortalEmail;
use App\Models\MeetingLog;
use App\Models\Student;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MeetingLogController extends BaseController
{
    public function createMeetingLog(Request $request)
    {
        $validated = $request->validate([
            'meeting_number' => 'required|integer',
            'meeting_date' => 'required|date',
            'meeting_mode' => 'required|in:in_person,virtual,hybrid',
            'duration' => 'required|integer',
            'previous_actions' => 'required|string',
            'progress_since' => 'required|string',
            'discussion_points' => 'required|string',
            'work_reviewed' => 'required|string',
            'chapter_focus' => 'required|string',
            'risks' => 'sometimes|string|nullable',
            'support_required' => 'sometimes|string|nullable',
            'next_meeting_date' => 'required|date',
            'next_meeting_focus' => 'required|string',
        ]);

        $meeting = MeetingLog::create([
            'university_id' => session('university_id'),
            'student_id' => session('student_id'),
            'log_id' => 'LOG-' . Str::upper(Str::random(8)),
            ...$validated,
            'status' => 'draft',
        ]);

        // Get student info
        $student = Student::find(session('student_id'));
        $user = $student->user;
        
        // Find the assigned supervisor for this student.
        $supervisor = $student->supervisor()->with('user')->first();
        
        if ($supervisor && $supervisor->user) {
            // Send email notification to supervisor
            $mail = new PortalEmail('meeting-status', [
                'studentName' => $student->full_name,
                'meetingNumber' => $validated['meeting_number'],
                'logId' => $meeting->log_id,
                'status' => 'DRAFT',
                'feedback' => '',
                'url' => route('supervisor.meetings'),
                'event' => 'SUBMITTED',
            ]);
            
            Mail::to($supervisor->user->email)->send($mail);
        }

        return $this->success($meeting, 'Meeting log created successfully', 201);
    }

    public function listStudentMeetings(Request $request)
    {
        $meetings = MeetingLog::where([
            ['university_id', '=', session('university_id')],
            ['student_id', '=', session('student_id')],
        ])->orderBy('meeting_date', 'desc')->get();

        return $this->success($meetings, 'Meeting logs retrieved successfully');
    }

    public function getMeetingLog(Request $request, $id)
    {
        $meeting = MeetingLog::find($id);
        if (!$meeting || $meeting->university_id != session('university_id') || ($meeting->student && $meeting->student->supervisor_id != session('supervisor_id'))) {
            return $this->error('Meeting log not found', 404);
        }
        return $this->success($meeting, 'Meeting log retrieved successfully');
    }

    public function updateMeetingLog(Request $request, $id)
    {
        $meeting = MeetingLog::find($id);
        if (!$meeting || $meeting->university_id != session('university_id') || ($meeting->student && $meeting->student->supervisor_id != session('supervisor_id'))) {
            return $this->error('Meeting log not found', 404);
        }
        if (!$meeting->canEdit()) {
            return $this->error('Can only edit draft or submitted meeting logs', 400);
        }

        $validated = $request->validate([
            'previous_actions' => 'sometimes|string',
            'progress_since' => 'sometimes|string',
            'discussion_points' => 'sometimes|string',
            'work_reviewed' => 'sometimes|string',
            'chapter_focus' => 'sometimes|string',
            'risks' => 'sometimes|string|nullable',
            'support_required' => 'sometimes|string|nullable',
            'next_meeting_date' => 'sometimes|date',
            'next_meeting_focus' => 'sometimes|string',
        ]);

        $meeting->update($validated);
        return $this->success($meeting, 'Meeting log updated successfully');
    }

    public function listAllMeetings(Request $request)
    {
        $meetings = MeetingLog::where('university_id', session('university_id'))
            ->whereHas('student', function ($query) {
                $query->where('supervisor_id', session('supervisor_id'));
            })
            ->with('student')
            ->orderBy('meeting_date', 'desc')
            ->get();

        return $this->success($meetings, 'All meeting logs retrieved successfully');
    }

    public function approveMeetingLog(Request $request, $id)
    {
        $validated = $request->validate([
            'feedback' => 'sometimes|string|nullable',
        ]);

        $meeting = MeetingLog::find($id);
        if (!$meeting || $meeting->university_id != session('university_id') || ($meeting->student && $meeting->student->supervisor_id != session('supervisor_id'))) {
            return $this->error('Meeting log not found', 404);
        }

        $meeting->update([
            'status' => 'approved',
            'supervisor_signature' => session('user_id'),
            'signoff_date' => now(),
            'feedback' => $validated['feedback'] ?? null,
        ]);

        // Send email notification to student
        $student = $meeting->student;
        $user = $student->user;
        $mail = new PortalEmail('meeting-status', [
            'studentName' => $student->full_name,
            'meetingNumber' => $meeting->meeting_number,
            'logId' => $meeting->log_id,
            'status' => 'APPROVED',
            'feedback' => $validated['feedback'] ?? 'Your meeting log has been approved.',
            'url' => route('student.meetings'),
            'event' => 'APPROVED',
        ]);

        Mail::to($user->email)->send($mail);

        return $this->success($meeting, 'Meeting log approved successfully');
    }

    public function rejectMeetingLog(Request $request, $id)
    {
        $validated = $request->validate([
            'feedback' => 'required|string',
        ]);

        $meeting = MeetingLog::find($id);
        if (!$meeting || $meeting->university_id != session('university_id') || ($meeting->student && $meeting->student->supervisor_id != session('supervisor_id'))) {
            return $this->error('Meeting log not found', 404);
        }

        $meeting->update([
            'status' => 'reviewed',
            'feedback' => $validated['feedback'],
        ]);

        // Send email notification to student
        $student = $meeting->student;
        $user = $student->user;
        $mail = new PortalEmail('meeting-status', [
            'studentName' => $student->full_name,
            'meetingNumber' => $meeting->meeting_number,
            'logId' => $meeting->log_id,
            'status' => 'REVISION',
            'feedback' => $validated['feedback'],
            'url' => route('student.meetings'),
            'event' => 'REJECTED',
        ]);

        Mail::to($user->email)->send($mail);

        return $this->success($meeting, 'Meeting log sent back for revision');
    }

    public function provideFeedback(Request $request, $id)
    {
        $validated = $request->validate([
            'areas_revision' => 'sometimes|string|nullable',
            'agreed_actions' => 'sometimes|string|nullable',
        ]);

        $meeting = MeetingLog::find($id);
        if (!$meeting || $meeting->university_id != session('university_id') || ($meeting->student && $meeting->student->supervisor_id != session('supervisor_id'))) {
            return $this->error('Meeting log not found', 404);
        }

        $meeting->update($validated);
        return $this->success($meeting, 'Feedback provided successfully');
    }

    public function scheduleMeeting(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|integer',
            'scheduled_date' => 'required|date',
            'meeting_mode' => 'required|in:in_person,virtual,hybrid',
        ]);

        $student = Student::where('id', $validated['student_id'])
            ->where('university_id', session('university_id'))
            ->where('supervisor_id', session('supervisor_id'))
            ->first();

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $meeting = MeetingLog::create([
            'university_id' => session('university_id'),
            'student_id' => $validated['student_id'],
            'log_id' => 'LOG-' . Str::upper(Str::random(8)),
            'meeting_number' => 0,
            'meeting_date' => $validated['scheduled_date'],
            'meeting_mode' => $validated['meeting_mode'],
            'duration' => 0,
            'previous_actions' => 'Scheduled',
            'progress_since' => 'Scheduled',
            'discussion_points' => 'Scheduled',
            'work_reviewed' => 'Scheduled',
            'chapter_focus' => 'Scheduled',
            'next_meeting_date' => $validated['scheduled_date'],
            'next_meeting_focus' => 'To be determined',
            'status' => 'draft',
        ]);

        return $this->success($meeting, 'Meeting scheduled successfully', 201);
    }

    public function getSchedule(Request $request)
    {
        $meetings = MeetingLog::where('university_id', session('university_id'))
            ->where('status', 'draft')
            ->where('meeting_date', '>=', now())
            ->orderBy('meeting_date')
            ->get();

        return $this->success($meetings, 'Meeting schedule retrieved successfully');
    }
}
