<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DefenseReadinessSection extends Model
{
    protected $fillable = [
        'document_id',
        'parent_id',
        'key',
        'title',
        'position',
        'kind',
        'guidance',
        'target_min_words',
        'target_max_words',
        'word_count_label',
        'content',
        'word_count',
        'status',
        'conditions_acknowledged_at',
        'submitted_at',
        'decided_at',
        'current_version_id',
    ];

    protected $casts = [
        'conditions_acknowledged_at' => 'datetime',
        'submitted_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(DefenseReadinessDocument::class, 'document_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DefenseReadinessSection::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DefenseReadinessSection::class, 'parent_id')
            ->orderBy('position');
    }

    public function currentVersion(): HasOne
    {
        return $this->hasOne(DefenseReadinessSectionVersion::class, 'id', 'current_version_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DefenseReadinessSectionVersion::class, 'section_id')
            ->orderBy('version_number');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(DefenseReadinessReview::class, 'section_id')
            ->orderBy('created_at', 'desc');
    }

    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'revision_requested'], true);
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function hasBeenReviewed(): bool
    {
        return in_array($this->status, ['accepted', 'conditional', 'revision_requested', 'rejected'], true);
    }

    public function isGroup(): bool
    {
        return $this->kind === 'group';
    }

    public function targetRange(): ?string
    {
        if ($this->target_min_words === null && $this->target_max_words === null) {
            return $this->word_count_label;
        }

        return $this->target_min_words . '–' . $this->target_max_words;
    }
}
