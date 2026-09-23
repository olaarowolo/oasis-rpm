<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class Supervisor extends Model
{
    protected $fillable = [
        'user_id', 'university_id', 'title', 'department', 'research_areas', 'booking_url',
        'pin_code', 'passphrase', 'is_active', 'google_oauth_id'
    ];

    protected $hidden = [
        'pin_code', 'passphrase',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function students(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Student::class, 'supervisor_id');
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Verify credentials using timing-safe comparison
     */
    public function verifyCredentials(string $pin, string $passphrase): bool
    {
        $pinValid = Hash::check($pin, $this->pin_code);
        $passphraseValid = Hash::check($passphrase, $this->passphrase);
        
        // Log failed attempts for security monitoring
        if (!$pinValid || !$passphraseValid) {
            Log::warning('Supervisor credential verification failed', [
                'supervisor_id' => $this->id,
                'pin_valid' => $pinValid,
                'passphrase_valid' => $passphraseValid,
            ]);
        }
        
        return $pinValid && $passphraseValid;
    }
}
