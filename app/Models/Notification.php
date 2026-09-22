<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    public const TYPE_INFO = 'info';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_WARNING = 'warning';
    public const TYPE_ERROR = 'error';
    public const TYPE_MEETING_REQUEST = 'meeting_request';
    public const TYPE_MEETING_APPROVED = 'meeting_approved';
    public const TYPE_MEETING_REJECTED = 'meeting_rejected';
    public const TYPE_PROPOSAL_APPROVED = 'proposal_approved';
    public const TYPE_PROPOSAL_REJECTED = 'proposal_rejected';
    public const TYPE_RESOURCE_APPROVED = 'resource_approved';
    public const TYPE_RESOURCE_REJECTED = 'resource_rejected';
    public const TYPE_ARCHIVE_SUBMITTED = 'archive_submitted';
    public const TYPE_ARCHIVE_APPROVED = 'archive_approved';

    protected $fillable = [
        'university_id',
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
        'metadata',
        'expires_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function isRead(): bool
    {
        return $this->is_read === true;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
