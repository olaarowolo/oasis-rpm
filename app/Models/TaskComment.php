<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskComment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'user_id',
        'content',
        'is_pinned',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_pinned' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the task that owns the comment
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who made the comment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
