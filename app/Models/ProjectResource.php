<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectResource extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'resource_name',
        'resource_type',
        'quantity',
        'unit',
        'cost_per_unit',
        'total_cost',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'float',
        'cost_per_unit' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the project that owns the resource
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
