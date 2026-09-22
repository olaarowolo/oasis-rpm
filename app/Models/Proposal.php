<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    protected $fillable = [
        'university_id', 'student_id', 'proposal_id', 'title', 'location',
        'abstract', 'date_submitted', 'status', 'supervisor_comment', 'conditions'
    ];

    protected $casts = [
        'date_submitted' => 'datetime',
        'conditions' => 'array',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function topicHistory(): HasMany
    {
        return $this->hasMany(TopicHistory::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function requiresRevision(): bool
    {
        return $this->status === 'revision_required';
    }
}
