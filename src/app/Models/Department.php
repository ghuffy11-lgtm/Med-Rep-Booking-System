<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_pharmacy_department',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_pharmacy_department' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function activeSchedules()
    {
        return $this->hasMany(Schedule::class)->where('is_active', true);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePharmacy($query)
    {
        return $query->where('is_pharmacy_department', true);
    }

    public function scopeNonPharmacy($query)
    {
        return $query->where('is_pharmacy_department', false);
    }

    /**
     * Helper Methods
     */

    /**
     * Check if department is available on a specific date
     */
    public function isAvailableOn(Carbon $date): bool
    {
        // Check if department has any closure schedule for this date
        $closure = $this->schedules()
            ->where('is_closed', true)
            ->where('start_date', '<=', $date->format('Y-m-d'))
            ->where('end_date', '>=', $date->format('Y-m-d'))
            ->where('is_active', true)
            ->first();

        if ($closure) {
            return false;
        }

        // Check if department has override days for this date
        $override = $this->schedules()
            ->where('is_closed', false)
            ->whereNotNull('override_days')
            ->where('start_date', '<=', $date->format('Y-m-d'))
            ->where('end_date', '>=', $date->format('Y-m-d'))
            ->where('is_active', true)
            ->first();

        if ($override) {
            $allowedDays = $override->override_days;
            $dayOfWeek = $date->format('l');
            return in_array($dayOfWeek, $allowedDays);
        }

        return true;
    }

    /**
     * Check if department has a closure on a specific date
     * Returns false if available, or closure reason if closed
     */
    public function hasClosureOn(Carbon $date): bool|string
    {
        $closure = $this->schedules()
            ->where('is_closed', true)
            ->where('start_date', '<=', $date->format('Y-m-d'))
            ->where('end_date', '>=', $date->format('Y-m-d'))
            ->where('is_active', true)
            ->first();

        if ($closure) {
            return $closure->notes ?? 'Department is closed during this period';
        }

        return false;
    }

    /**
     * Get active closure or override for a date
     */
    public function getScheduleForDate(Carbon $date): ?Schedule
    {
        return $this->schedules()
            ->where('start_date', '<=', $date->format('Y-m-d'))
            ->where('end_date', '>=', $date->format('Y-m-d'))
            ->where('is_active', true)
            ->first();
    }

// New method after getScheduleForDate() (after line 140):
public function getOverrideScheduleFor(Carbon $date): ?Schedule
{
    return $this->schedules()
        ->where('is_closed', false)
        ->whereNotNull('override_days')
        ->where('start_date', '<=', $date->format('Y-m-d'))
        ->where('end_date', '>=', $date->format('Y-m-d'))
        ->where('is_active', true)
        ->first();
}

    /**
     * Get booking statistics for a date
     */
    public function getBookingStatsForDate(Carbon $date): array
    {
        $config = GlobalSlotConfig::current();
        
        if ($this->is_pharmacy_department) {
            $count = $this->bookings()
                ->where('booking_date', $date->format('Y-m-d'))
                ->whereIn('status', ['pending', 'approved'])
                ->count();
            
            return [
                'booked' => $count,
                'available' => $config->pharmacy_daily_limit - $count,
                'limit' => $config->pharmacy_daily_limit,
            ];
        } else {
            $count = Booking::whereHas('department', function($q) {
                    $q->where('is_pharmacy_department', false);
                })
                ->where('booking_date', $date->format('Y-m-d'))
                ->whereIn('status', ['pending', 'approved'])
                ->count();
            
            return [
                'booked' => $count,
                'available' => $config->non_pharmacy_daily_limit - $count,
                'limit' => $config->non_pharmacy_daily_limit,
            ];
        }
    }
}
