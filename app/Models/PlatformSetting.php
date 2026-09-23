<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'data_type',
    ];

    public function getTypedValue(): mixed
    {
        return match ($this->data_type) {
            'integer' => (int) $this->setting_value,
            'boolean' => $this->setting_value === '1' || $this->setting_value === 'true',
            'json', 'array' => json_decode((string) $this->setting_value, true),
            default => $this->setting_value,
        };
    }
}