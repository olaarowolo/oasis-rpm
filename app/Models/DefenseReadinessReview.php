<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefenseReadinessReview extends Model
{
    protected $fillable = [
        'section_id',
        'document_id',
        'version_id',
        'reviewer_user_id',
        'action',
        'comment',
        'conditions',
    ];

    protected $casts = [
        'conditions' => 'array',
    ];

    public const ACTION_ACCEPTED = 'accepted';
    public const ACTION_CONDITIONAL = 'conditional';
    public const ACTION_REVISION_REQUESTED = 'revision_requested';
    public const ACTION_REJECTED = 'rejected';
    public const ACTION_COMMENTED = 'commented';

    public function section(): BelongsTo
    {
        return $this->belongsTo(DefenseReadinessSection::class, 'section_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(DefenseReadinessDocument::class, 'document_id');
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(DefenseReadinessSectionVersion::class, 'version_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }

    public function isDecision(): bool
    {
        return in_array($this->action, [
            self::ACTION_ACCEPTED,
            self::ACTION_CONDITIONAL,
            self::ACTION_REVISION_REQUESTED,
            self::ACTION_REJECTED,
        ], true);
    }
}
