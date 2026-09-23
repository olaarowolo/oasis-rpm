<?php

namespace Database\Seeders;

use App\Models\MeetingLog;
use Illuminate\Database\Seeder;

class MeetingLogSeeder extends Seeder
{
    public function run(): void
    {
        $meetingLog = [
            'university_id' => 1,
            'student_id' => 1,
            'log_id' => 'LOG-LASU-001',
            'meeting_number' => 1,
            'meeting_date' => now()->subDays(7),
            'meeting_mode' => 'in_person',
            'duration' => 60,
            'previous_actions' => 'Literature review started',
            'progress_since' => 'Completed initial literature review',
            'discussion_points' => 'Research methodology discussed',
            'work_reviewed' => 'Draft introduction chapter',
            'chapter_focus' => 'Introduction',
            'risks' => 'Time management concerns',
            'support_required' => 'Database access support',
            'next_meeting_date' => now()->addDays(14),
            'next_meeting_focus' => 'Review Chapter 2 draft',
            'status' => 'approved',
            'student_signature' => 'Student Signature',
            'supervisor_signature' => 'Supervisor Signature',
            'signoff_date' => now()->subDays(5),
            'feedback_summary' => 'Good progress. Continue with current pace.',
            'areas_revision' => 'Refine introduction conclusion',
            'agreed_actions' => 'Complete draft of Chapter 2 by next meeting',
        ];

        MeetingLog::updateOrCreate(
            ['log_id' => $meetingLog['log_id']],
            $meetingLog
        );
    }
}
