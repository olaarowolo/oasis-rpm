<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'start_date',
        'end_date',
        'budget',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'budget' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who created the project
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the tasks for the project
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the project members
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get the resources allocated to the project
     */
    public function resources()
    {
        return $this->hasMany(ProjectResource::class);
    }

    /**
     * Get project progress
     */
    public function getProgressAttribute()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return 0;
        }
        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return round(($completedTasks / $totalTasks) * 100);
    }
}
