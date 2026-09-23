<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    protected $fillable = [
        'name', 'code', 'email', 'department', 'phone', 'logo_url',
        'branding_color', 'ai_model_config', 'email_config',
        'google_chat_webhook_url', 'is_active', 'archived_at', 'features_enabled'
    ];

    protected $casts = [
        'ai_model_config' => 'array',
        'email_config' => 'array',
        'is_active' => 'boolean',
        'archived_at' => 'datetime',
        'features_enabled' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function supervisors(): HasMany
    {
        return $this->hasMany(Supervisor::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function meetingLogs(): HasMany
    {
        return $this->hasMany(MeetingLog::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function resourceProgress(): HasMany
    {
        return $this->hasMany(ResourceProgress::class);
    }

    public function stageHistory(): HasMany
    {
        return $this->hasMany(StageHistory::class);
    }

    public function topicHistory(): HasMany
    {
        return $this->hasMany(TopicHistory::class);
    }

    public function systemConfigs(): HasMany
    {
        return $this->hasMany(SystemConfig::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function archiveSubmissions(): HasMany
    {
        return $this->hasMany(ArchiveSubmission::class);
    }

    public function getConfig(string $key, mixed $default = null): mixed
    {
        $config = $this->systemConfigs()->where('config_key', $key)->first();
        return $config ? $config->getTypedValue() : $default;
    }

    public function setConfig(string $key, mixed $value, string $dataType = 'string'): void
    {
        $this->systemConfigs()->updateOrCreate(
            ['config_key' => $key],
            ['config_value' => is_array($value) ? json_encode($value) : $value, 'data_type' => $dataType]
        );
    }
}
