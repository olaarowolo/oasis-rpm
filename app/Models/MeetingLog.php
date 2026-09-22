<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingLog extends Model
{
    protected $fillable = [
        'university_id', 'student_id', 'log_id', 'meeting_number', 'meeting_date',
        'meeting_mode', 'duration', 'previous_actions', 'progress_since',
        'discussion_points', 'work_reviewed', 'chapter_focus', 'risks',
        'support_required', 'next_meeting_date', 'next_meeting_focus',
        'student_signature', 'feedback_summary', 'areas_revision', 'agreed_actions',
        'supervisor_signature', 'signoff_date', 'status', 'feedback'
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'next_meeting_date' => 'date',
        'signoff_date' => 'date',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function canEdit(): bool
    {
        return $this->status === 'draft' || $this->status === 'submitted';
    }
}
