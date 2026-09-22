<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Http\Request;

class AnalyticsController extends BaseController
{
    public function supervisorAnalytics(Request $request)
    {
        $university = University::find(session('university_id'));
        $analytics = [
            'total_students' => \App\Models\Student::where('university_id', $university->id)->count(),
            'active_students' => \App\Models\Student::where('university_id', $university->id)->where('status', 'active')->count(),
            'completed_proposals' => \App\Models\Proposal::where(['university_id' => $university->id, 'status' => 'approved'])->count(),
            'pending_proposals' => \App\Models\Proposal::where(['university_id' => $university->id, 'status' => 'pending'])->count(),
            'stage_distribution' => \App\Models\Student::where('university_id', $university->id)
                ->selectRaw('current_stage, COUNT(*) as count')
                ->groupBy('current_stage')
                ->get()
                ->pluck('count', 'current_stage'),
        ];

        return $this->success($analytics, 'Analytics dashboard retrieved successfully');
    }

    public function studentProgress(Request $request, $id)
    {
        $student = \App\Models\Student::where([
            ['id', '=', $id],
            ['university_id', '=', session('university_id')],
        ])->with('proposals', 'meetingLogs', 'resourceProgress', 'stageHistory')->first();

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        return $this->success([
            'student_name' => $student->full_name,
            'current_stage' => $student->current_stage,
            'progress_percentage' => $student->progress_percentage,
            'points_earned' => $student->points_earned,
            'proposals_submitted' => $student->proposals->count(),
            'meetings_held' => $student->meetingLogs->count(),
            'resources_completed' => $student->resourceProgress->where('status', 'approved')->count(),
            'stage_history' => $student->stageHistory,
        ], 'Student progress retrieved successfully');
    }

    public function exportAnalytics(Request $request)
    {
        $students = \App\Models\Student::where('university_id', session('university_id'))->get();

        $csv = "Matric Number,Full Name,Current Stage,Progress %,Points Earned,Status,Last Meeting\n";
        foreach ($students as $student) {
            $csv .= "\"{$student->matric_number}\",\"{$student->full_name}\",{$student->current_stage},{$student->progress_percentage},{$student->points_earned},{$student->status},\"{$student->last_meeting_date}\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="analytics-' . now()->format('Y-m-d') . '.csv"');
    }
}
