<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    protected $fillable = [
        'university_id', 'section', 'type', 'title', 'url', 'description',
        'sort_order', 'is_mandatory', 'stage', 'points'
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ResourceProgress::class);
    }

    public function isMandatory(): bool
    {
        return $this->is_mandatory === true;
    }

    public function getStudentProgress($studentId)
    {
        return $this->progress()
            ->where('student_id', $studentId)
            ->first();
    }
}
