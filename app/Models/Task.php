<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'due_date',
        'estimated_hours',
        'actual_hours',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'estimated_hours' => 'float',
        'actual_hours' => 'float',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the project that owns the task
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user the task is assigned to
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created the task
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the comments on the task
     */
    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    /**
     * Get the attachments for the task
     */
    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    /**
     * Get the history logs for the task
     */
    public function history()
    {
        return $this->hasMany(TaskHistory::class);
    }
}
