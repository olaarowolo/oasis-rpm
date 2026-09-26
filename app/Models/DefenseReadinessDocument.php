<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DefenseReadinessDocument extends Model
{
    protected $fillable = [
        'university_id',
        'student_id',
        'study_approach',
        'primary_data_collection',
        'status',
        'halted_at',
        'halted_section_id',
        'halted_reason',
        'completed_at',
    ];

    protected $casts = [
        'study_approach' => 'string',
        'primary_data_collection' => 'boolean',
        'halted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(DefenseReadinessSection::class, 'document_id');
    }

    public function haltedSection(): BelongsTo
    {
        return $this->belongsTo(DefenseReadinessSection::class, 'halted_section_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(DefenseReadinessReview::class, 'document_id');
    }

    public function isHalted(): bool
    {
        return $this->status === 'halted';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canEditSettings(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        return $this->sections()
            ->whereIn('status', ['submitted', 'accepted', 'conditional', 'revision_requested', 'rejected'])
            ->doesntExist();
    }
}
