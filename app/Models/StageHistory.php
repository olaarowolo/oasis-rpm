<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageHistory extends Model
{
    protected $table = 'stage_history';

    protected $fillable = [
        'university_id', 'student_id', 'stage_number', 'stage_name', 'action', 'note'
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
