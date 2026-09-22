<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAttachment extends Model
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
        'file_name',
        'file_path',
        'file_size',
        'file_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the task that owns the attachment
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who uploaded the attachment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
