<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'university_id', 'supervisor_id', 'matric_number', 'lastname', 'full_name',
        'email', 'degree_level', 'phone', 'research_topic', 'research_topic_approved_date',
        'current_stage', 'progress_percentage', 'points_earned', 'status',
        'account_status', 'personal_drive_url', 'last_meeting_date',
        'graduated_at', 'archived_at'
    ];

    protected $casts = [
        'research_topic_approved_date' => 'datetime',
        'last_meeting_date' => 'datetime',
        'graduated_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function meetingLogs(): HasMany
    {
        return $this->hasMany(MeetingLog::class);
    }

    public function resourceProgress(): HasMany
    {
        return $this->hasMany(ResourceProgress::class);
    }

    public function stageHistory(): HasMany
    {
        return $this->hasMany(StageHistory::class);
    }

    public function topicHistory(): HasMany
    {
        return $this->hasMany(TopicHistory::class);
    }

    public function archiveSubmission(): HasOne
    {
        return $this->hasOne(ArchiveSubmission::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id', 'user_id');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'user_id')->where('is_read', false);
    }

    public function userNotifications()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->account_status === 'active';
    }

    public function isGraduated(): bool
    {
        return $this->status === 'graduated';
    }

    public function isArchived(): bool
    {
        return $this->account_status === 'archived';
    }

    public function hasApprovedTopic(): bool
    {
        return !is_null($this->research_topic_approved_date);
    }

    public function canAdvanceStage(): bool
    {
        $config = $this->university->getConfig('stage_advancement_rules', []);
        return true; // Implementation depends on stage-specific rules
    }

    public function getCurrentStageName(): string
    {
        $stages = config('research.stages');
        return $stages[$this->current_stage - 1]['name'] ?? 'Unknown Stage';
    }
}
