<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'user_id',
        'field_name',
        'old_value',
        'new_value',
        'action',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * Get the task that owns the history record
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who made the change
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
