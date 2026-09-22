<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicHistory extends Model
{
    protected $table = 'topic_history';

    protected $fillable = [
        'university_id', 'student_id', 'proposal_id', 'topic_title', 'action', 'note'
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
