<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TrustedDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_token',
        'device_name',
        'user_agent',
        'ip_address',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the trusted device.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the device is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Update the last used timestamp.
     */
    public function updateLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Scope to get only active (non-expired) devices.
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Clean up expired devices for a user.
     */
    public static function cleanExpiredForUser(int $userId): int
    {
        return static::where('user_id', $userId)
            ->where('expires_at', '<', now())
            ->delete();
    }
}
