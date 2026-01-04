<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
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
        'civil_id',
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
            return "You are in cooldown period. Next booking available: {$info['cooldown_end']->format('M d, Y')}";
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
}
