<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemConfig extends Model
{
    protected $fillable = [
        'university_id', 'config_key', 'config_value', 'data_type'
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function getTypedValue(): mixed
    {
        return match($this->data_type) {
            'integer' => (int) $this->config_value,
            'boolean' => $this->config_value === '1' || $this->config_value === 'true',
            'json' => json_decode($this->config_value, true),
            'array' => json_decode($this->config_value, true),
            default => $this->config_value,
        };
    }
}
