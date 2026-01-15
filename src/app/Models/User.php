<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use App\Models\TrustedDevice;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company',
        'google2fa_secret',
        'google2fa_enabled',
        'two_factor_recovery_codes',
        'civil_id',
        'mobile_number',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'google2fa_enabled' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function approvedBookings()
    {
        return $this->hasMany(Booking::class, 'approved_by');
    }

    public function cancelledBookings()
    {
        return $this->hasMany(Booking::class, 'cancelled_by');
    }

    public function createdSchedules()
    {
        return $this->hasMany(Schedule::class, 'created_by');
    }

    public function trustedDevices()
    {
        return $this->hasMany(TrustedDevice::class);
    }

    /**
     * Scopes
     */
    public function scopeRepresentatives($query)
    {
        return $query->where('role', 'representative');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Helper Methods
     */
    
    /**
     * Check if user has an active cooldown period
     */
    public function hasActiveCooldown(): bool
    {
        $lastApprovedBooking = $this->bookings()
            ->where('status', 'approved')
            ->orderBy('booking_date', 'desc')
            ->first();

        if (!$lastApprovedBooking) {
            return false;
        }

        $config = GlobalSlotConfig::current();
        $cooldownEndDate = Carbon::parse($lastApprovedBooking->booking_date)
            ->addDays($config->cooldown_days);

        return now()->lessThan($cooldownEndDate);
    }

    /**
     * Get cooldown end date if in cooldown
     */
    public function getCooldownEndDate(): ?Carbon
    {
        $lastApprovedBooking = $this->bookings()
            ->where('status', 'approved')
            ->orderBy('booking_date', 'desc')
            ->first();

        if (!$lastApprovedBooking) {
            return null;
        }

        $config = GlobalSlotConfig::current();
        return Carbon::parse($lastApprovedBooking->booking_date)
            ->addDays($config->cooldown_days);
    }

    /**
     * Get complete cooldown information
     */
    public function getCooldownInfo(): array
    {
        $lastApprovedBooking = $this->bookings()
            ->where('status', 'approved')
            ->orderBy('booking_date', 'desc')
            ->first();

        if (!$lastApprovedBooking) {
            return [
                'in_cooldown' => false,
                'last_appointment' => null,
                'cooldown_end' => null,
                'days_remaining' => 0,
            ];
        }

        $config = GlobalSlotConfig::current();
        $cooldownEndDate = Carbon::parse($lastApprovedBooking->booking_date)
            ->addDays($config->cooldown_days);
        
        $inCooldown = now()->lessThan($cooldownEndDate);
        $daysRemaining = $inCooldown ? now()->diffInDays($cooldownEndDate) : 0;

        return [
            'in_cooldown' => $inCooldown,
            'last_appointment' => Carbon::parse($lastApprovedBooking->booking_date),
            'cooldown_end' => $cooldownEndDate,
            'days_remaining' => $daysRemaining,
        ];
    }

    /**
     * Get days remaining in cooldown
     */
    public function getCooldownDaysRemaining(): int
    {
        if (!$this->hasActiveCooldown()) {
            return 0;
        }

        $cooldownEnd = $this->getCooldownEndDate();
        return now()->diffInDays($cooldownEnd);
    }

    /**
     * Check if user has a pending booking
     */
    public function hasPendingBooking(): bool
    {
        return $this->bookings()->where('status', 'pending')->exists();
    }

    /**
     * Get pending booking if exists
     */
    public function getPendingBooking(): ?Booking
    {
        return $this->bookings()
            ->with('department')
            ->where('status', 'pending')
            ->first();
    }

    /**
     * Check if user can book a specific department
     * Returns true if can book, or error message string if cannot
     */
    public function canBookDepartment($department): bool|string
    {
        // Check if has pending booking
        if ($this->hasPendingBooking()) {
            return 'You have a pending booking. Please wait for approval or cancel it.';
        }

        // Check cooldown
        if ($this->hasActiveCooldown()) {
            $info = $this->getCooldownInfo();
            $formattedDate = $info['cooldown_end']->format('M d, Y');
            return "You are in cooldown period. Next booking available: {$formattedDate}";
        }

        return true;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'pharmacy_admin']);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is pharmacy admin
     */
    public function isPharmacyAdmin(): bool
    {
        return $this->role === 'pharmacy_admin';
    }

    /**
     * Check if user is representative
     */
    public function isRepresentative(): bool
    {
        return $this->role === 'representative';
    }

    /**
     * Two-Factor Authentication Methods
     */

    /**
     * Check if 2FA is enabled for this user
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->google2fa_enabled === true && !empty($this->google2fa_secret);
    }

    /**
     * Enable 2FA for the user
     */
    public function enableTwoFactor(string $secret): void
    {
        $this->google2fa_secret = encrypt($secret);
        $this->google2fa_enabled = true;
        $this->save();
    }

    /**
     * Disable 2FA for the user
     */
    public function disableTwoFactor(): void
    {
        $this->google2fa_secret = null;
        $this->google2fa_enabled = false;
        $this->two_factor_recovery_codes = null;
        $this->save();

        // Remove all trusted devices
        $this->trustedDevices()->delete();
    }

    /**
     * Get decrypted 2FA secret
     */
    public function getTwoFactorSecret(): ?string
    {
        if (empty($this->google2fa_secret)) {
            return null;
        }

        return decrypt($this->google2fa_secret);
    }

    /**
     * Generate and store recovery codes
     */
    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(5)));
        }

        $this->two_factor_recovery_codes = encrypt(json_encode($codes));
        $this->save();

        return $codes;
    }

    /**
     * Get recovery codes
     */
    public function getRecoveryCodes(): array
    {
        if (empty($this->two_factor_recovery_codes)) {
            return [];
        }

        return json_decode(decrypt($this->two_factor_recovery_codes), true);
    }

    /**
     * Use a recovery code
     */
    public function useRecoveryCode(string $code): bool
    {
        $codes = $this->getRecoveryCodes();
        $codeUpper = strtoupper($code);

        if (!in_array($codeUpper, $codes)) {
            return false;
        }

        // Remove the used code
        $codes = array_values(array_diff($codes, [$codeUpper]));
        $this->two_factor_recovery_codes = encrypt(json_encode($codes));
        $this->save();

        return true;
    }

    /**
     * Check if device is trusted
     */
    public function hasDeviceTrusted(string $deviceToken): bool
    {
        return $this->trustedDevices()
            ->where('device_token', $deviceToken)
            ->active()
            ->exists();
    }

    /**
     * Trust a device
     */
    public function trustDevice(string $deviceName = null): string
    {
        // Clean up expired devices first
        TrustedDevice::cleanExpiredForUser($this->id);

        $deviceToken = bin2hex(random_bytes(32));

        $this->trustedDevices()->create([
            'device_token' => $deviceToken,
            'device_name' => $deviceName ?? request()->header('User-Agent'),
            'user_agent' => request()->header('User-Agent'),
            'ip_address' => request()->ip(),
            'last_used_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        return $deviceToken;
    }

    /**
     * Update trusted device last used
     */
    public function updateTrustedDevice(string $deviceToken): void
    {
        $device = $this->trustedDevices()
            ->where('device_token', $deviceToken)
            ->first();

        if ($device) {
            $device->updateLastUsed();
        }
    }

    /**
     * Revoke all trusted devices
     */
    public function revokeAllTrustedDevices(): void
    {
        $this->trustedDevices()->delete();
    }
}
