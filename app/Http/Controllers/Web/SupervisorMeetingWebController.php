<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Models\MeetingLog;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SupervisorMeetingWebController extends BaseController
{
    /**
     * List all meeting logs for students supervised by the current supervisor.
     */
    public function index()
    {
        $meetings = MeetingLog::where('university_id', session('university_id'))
            ->whereHas('student', function ($query) {
                $query->where('supervisor_id', session('supervisor_id'));
            })
            ->with('student')
            ->orderByDesc('meeting_date')
            ->get();

        return view('supervisor.meetings', compact('meetings'));
    }

    /**
     * Show the "log a meeting" form.
     */
    public function create()
    {
        $students = $this->supervisedStudents();

        return view('supervisor.meeting-form', compact('students'));
    }

    /**
     * Persist a new meeting log.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|integer',
            'meeting_number' => 'required|integer|min:1',
            'meeting_date' => 'required|date',
            'meeting_mode' => 'required|in:in_person,virtual,hybrid',
            'duration' => 'required|integer|min:1',
            'previous_actions' => 'required|string',
            'progress_since' => 'required|string',
            'discussion_points' => 'required|string',
            'work_reviewed' => 'required|string',
            'chapter_focus' => 'required|string|max:255',
            'risks' => 'sometimes|string|nullable',
            'support_required' => 'sometimes|string|nullable',
            'next_meeting_date' => 'required|date',
            'next_meeting_focus' => 'required|string|max:255',
        ]);

        // Ensure the student belongs to this supervisor and university.
        $student = $this->supervisedStudents()->firstWhere('id', (int) $validated['student_id']);
        if (!$student) {
            throw ValidationException::withMessages([
                'student_id' => 'The selected student is not assigned to you.',
            ]);
        }

        $meeting = MeetingLog::create([
            'university_id' => session('university_id'),
            'student_id' => $student->id,
            'log_id' => 'LOG-' . Str::upper(Str::random(8)),
            'meeting_number' => $validated['meeting_number'],
            'meeting_date' => $validated['meeting_date'],
            'meeting_mode' => $validated['meeting_mode'],
            'duration' => $validated['duration'],
            'previous_actions' => $validated['previous_actions'],
            'progress_since' => $validated['progress_since'],
            'discussion_points' => $validated['discussion_points'],
            'work_reviewed' => $validated['work_reviewed'],
            'chapter_focus' => $validated['chapter_focus'],
            'risks' => $validated['risks'] ?? null,
            'support_required' => $validated['support_required'] ?? null,
            'next_meeting_date' => $validated['next_meeting_date'],
            'next_meeting_focus' => $validated['next_meeting_focus'],
            'status' => 'draft',
        ]);

        return redirect()
            ->route('supervisor.meetings.view', $meeting->id)
            ->with('status', 'Meeting log ' . $meeting->log_id . ' created successfully.');
    }

    /**
     * Show a single meeting log.
     */
    public function show($id)
    {
        $meeting = MeetingLog::where('university_id', session('university_id'))
            ->whereHas('student', function ($query) {
                $query->where('supervisor_id', session('supervisor_id'));
            })
            ->with('student')
            ->findOrFail($id);

        return view('supervisor.meeting-detail', compact('meeting'));
    }

    /**
     * Students assigned to the current supervisor within the active university.
     */
    private function supervisedStudents()
    {
        return Student::where('university_id', session('university_id'))
            ->where('supervisor_id', session('supervisor_id'))
            ->orderBy('full_name')
            ->get();
    }
}
