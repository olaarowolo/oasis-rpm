<?php

namespace App\Services;

use App\Models\University;
use App\Models\Student;
use App\Models\MeetingLog;
use App\Models\Proposal;

class AnalyticsService
{
    protected University $university;

    public function __construct(University $university)
    {
        $this->university = $university;
    }

    public function supervisorDashboard(): array
    {
        $students = Student::where('university_id', $this->university->id)->get();

        return [
            'total_students' => $students->count(),
            'active_students' => $students->where('status', 'active')->count(),
            'completed_students' => $students->where('status', 'completed')->count(),
            'average_progress' => $this->getAverageProgress($students),
            'stage_distribution' => $this->getStageDistribution($students),
            'topic_approval_rate' => $this->getTopicApprovalRate(),
            'meeting_schedule' => $this->getUpcomingMeetings(),
            'risk_flags' => $this->identifyRiskFlags(),
            'bottlenecks' => $this->identifyBottlenecks(),
        ];
    }

    public function studentProgress(int $studentId): array
    {
        $student = Student::find($studentId);

        if (!$student || $student->university_id !== $this->university->id) {
            return ['error' => 'Student not found'];
        }

        $meetings = $student->meetingLogs()->get();
        $resources = $student->resourceProgress()->get();

        return [
            'student_name' => $student->full_name,
            'matric_number' => $student->matric_number,
            'current_stage' => $student->current_stage,
            'progress_percentage' => $student->progress_percentage,
            'points_earned' => $student->points_earned,
            'topic_approved' => $student->hasApprovedTopic(),
            'topic_approval_date' => $student->research_topic_approved_date,
            'total_meetings' => $meetings->count(),
            'last_meeting_date' => $student->last_meeting_date,
            'meetings_this_month' => $meetings->where('meeting_date', '>=', now()->startOfMonth())->count(),
            'resources_completed' => $resources->where('status', 'approved')->count(),
            'total_resources_assigned' => $resources->count(),
            'time_in_current_stage' => $this->getTimeInStage($student),
            'stage_timeline' => $this->getStageTimeline($student),
        ];
    }

    public function exportAnalytics(string $format = 'csv'): string
    {
        $students = Student::where('university_id', $this->university->id)
            ->with('proposals', 'meetingLogs', 'resourceProgress')
            ->get();

        if ($format === 'csv') {
            return $this->exportAsCSV($students);
        }

        return json_encode($students);
    }

    protected function getAverageProgress($students): float
    {
        if ($students->isEmpty()) {
            return 0;
        }

        return round($students->avg('progress_percentage'), 2);
    }

    protected function getStageDistribution($students): array
    {
        $stages = config('research.stages', []);
        $distribution = [];

        foreach ($stages as $stage) {
            $count = $students->where('current_stage', $stage['number'] ?? 0)->count();
            $distribution[$stage['name'] ?? 'Unknown'] = $count;
        }

        return $distribution;
    }

    protected function getTopicApprovalRate(): float
    {
        $totalStudents = Student::where('university_id', $this->university->id)->count();
        if ($totalStudents === 0) {
            return 0;
        }

        $approvedTopics = Student::where('university_id', $this->university->id)
            ->whereNotNull('research_topic_approved_date')
            ->count();

        return round(($approvedTopics / $totalStudents) * 100, 2);
    }

    protected function getUpcomingMeetings(): array
    {
        return MeetingLog::where('university_id', $this->university->id)
            ->where('next_meeting_date', '>=', now())
            ->orderBy('next_meeting_date')
            ->take(10)
            ->get()
            ->toArray();
    }

    protected function identifyRiskFlags(): array
    {
        $risks = [];

        $noTopicStudents = Student::where('university_id', $this->university->id)
            ->whereNull('research_topic_approved_date')
            ->where('created_at', '<', now()->subMonths(2))
            ->get();

        if ($noTopicStudents->isNotEmpty()) {
            $risks[] = [
                'type' => 'NO_APPROVED_TOPIC',
                'severity' => 'high',
                'count' => $noTopicStudents->count(),
                'message' => "{$noTopicStudents->count()} students still without approved topics",
            ];
        }

        $noRecentMeetings = Student::where('university_id', $this->university->id)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('last_meeting_date')
                    ->orWhere('last_meeting_date', '<', now()->subWeeks(4));
            })
            ->get();

        if ($noRecentMeetings->isNotEmpty()) {
            $risks[] = [
                'type' => 'NO_RECENT_MEETINGS',
                'severity' => 'medium',
                'count' => $noRecentMeetings->count(),
                'message' => "{$noRecentMeetings->count()} students without recent supervision meetings",
            ];
        }

        return $risks;
    }

    protected function identifyBottlenecks(): array
    {
        $bottlenecks = [];

        $oldProposals = Proposal::where('university_id', $this->university->id)
            ->where('status', 'pending')
            ->where('date_submitted', '<', now()->subWeek())
            ->count();

        if ($oldProposals > 0) {
            $bottlenecks[] = [
                'area' => 'Proposal Review',
                'count' => $oldProposals,
                'message' => "$oldProposals proposals pending review for over a week",
            ];
        }

        $avgStageTime = 4; // weeks
        $stuckStudents = Student::where('university_id', $this->university->id)
            ->where('status', 'active')
            ->get()
            ->filter(function ($student) use ($avgStageTime) {
                return $this->getTimeInStage($student) > ($avgStageTime * 7);
            })->count();

        if ($stuckStudents > 0) {
            $bottlenecks[] = [
                'area' => 'Stage Progression',
                'count' => $stuckStudents,
                'message' => "$stuckStudents students stuck in current stage longer than typical",
            ];
        }

        return $bottlenecks;
    }

    protected function getTimeInStage(Student $student): int
    {
        $latestStageEntry = $student->stageHistory()
            ->where('stage_number', $student->current_stage)
            ->latest()
            ->first();

        if (!$latestStageEntry) {
            return 0;
        }

        return $latestStageEntry->created_at->diffInDays(now());
    }

    protected function getStageTimeline(Student $student): array
    {
        return $student->stageHistory()
            ->orderBy('created_at')
            ->get()
            ->groupBy('stage_number')
            ->map(function ($entries) {
                $first = $entries->first();
                $last = $entries->last();
                return [
                    'stage' => $first->stage_name,
                    'entered' => $first->created_at,
                    'exited' => $last->created_at,
                    'duration_days' => $first->created_at->diffInDays($last->created_at),
                ];
            })
            ->values()
            ->toArray();
    }

    protected function exportAsCSV($students): string
    {
        $csv = "Matric Number,Full Name,Current Stage,Progress %,Points,Topic Approved,Meetings Count,Last Meeting,Status\n";

        foreach ($students as $student) {
            $csv .= "{$student->matric_number},{$student->full_name},{$student->current_stage},{$student->progress_percentage},{$student->points_earned},";
            $csv .= ($student->hasApprovedTopic() ? 'Yes' : 'No') . ",";
            $csv .= $student->meetingLogs()->count() . ",";
            $csv .= ($student->last_meeting_date ? $student->last_meeting_date->format('Y-m-d') : 'N/A') . ",";
            $csv .= $student->status . "\n";
        }

        return $csv;
    }
}
