<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefenseReadinessSectionVersion extends Model
{
    protected $fillable = [
        'section_id',
        'document_id',
        'version_number',
        'content',
        'word_count',
        'submitted_by_user_id',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(DefenseReadinessSection::class, 'section_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(DefenseReadinessDocument::class, 'document_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }
}
