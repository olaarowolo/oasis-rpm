<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'university_id', 'user_id', 'model_type', 'model_id', 'action',
        'old_values', 'new_values'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function logAction(
        University $university,
        ?User $user,
        string $modelType,
        int $modelId,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        self::create([
            'university_id' => $university->id,
            'user_id' => $user?->id,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
