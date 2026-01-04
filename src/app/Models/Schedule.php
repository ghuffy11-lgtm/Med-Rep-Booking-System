<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'department_id',
        'created_by',
        'start_date',
        'end_date',
        'is_closed',
        'override_days',
        'override_start_time',  // After 'override_days'
        'override_end_time',    // After 'override_start_time'
        'notes',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_closed' => 'boolean',
        'override_days' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    public function scopeForDate($query, Carbon $date)
    {
        return $query->where('start_date', '<=', $date->format('Y-m-d'))
                     ->where('end_date', '>=', $date->format('Y-m-d'));
    }

    /**
     * Helper Methods
     */

    /**
     * Check if this schedule affects a specific date
     */
    public function affectsDate(Carbon $date): bool
    {
        return $date->between($this->start_date, $this->end_date);
    }

    /**
     * Check if a specific day is allowed in this schedule
     * (Only applies if override_days is set)
     */
    public function isDayAllowed(string $dayName): bool
    {
        if (!$this->override_days) {
            return true; // No override, all days allowed
        }

        return in_array($dayName, $this->override_days);
    }

    /**
     * Get human-readable date range
     */
    public function getDateRangeAttribute(): string
    {
        return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y');
    }

    /**
     * Get human-readable status
     */
    public function getStatusTextAttribute(): string
    {
        if ($this->is_closed) {
            return 'Closed';
        }

        if ($this->override_days) {
            return 'Custom Schedule';
        }

        return 'Active';
    }

    /**
     * Check if schedule is currently active (within date range)
     */
    public function isCurrentlyActive(): bool
    {
        return now()->between($this->start_date, $this->end_date) && $this->is_active;
    }
// New methods to add at end (after line 134):
public function hasCustomTimes(): bool
{
    return !is_null($this->override_start_time) && !is_null($this->override_end_time);
}

public function getOverrideTimes(): ?array
{
    if ($this->hasCustomTimes()) {
        return [
            'start' => $this->override_start_time,
            'end' => $this->override_end_time,
        ];
    }
    return null;
}
}
