<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceProgress extends Model
{
    protected $fillable = [
        'university_id', 'student_id', 'resource_id', 'status',
        'points_earned', 'submitted_date', 'reviewed_date', 'supervisor_comment'
    ];

    protected $casts = [
        'submitted_date' => 'datetime',
        'reviewed_date' => 'datetime',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'approved';
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted', 'reviewed', 'approved']);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
