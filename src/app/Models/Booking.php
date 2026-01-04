<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'department_id',
        'booking_date',
        'time_slot',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booking_date' => 'date',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->format('Y-m-d'))
                     ->whereIn('status', ['pending', 'approved']);
    }

    public function scopePast($query)
    {
        return $query->where('booking_date', '<', now()->format('Y-m-d'));
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('booking_date', Carbon::parse($date)->format('Y-m-d'));
    }

    public function scopeForTimeSlot($query, $timeSlot)
    {
        return $query->where('time_slot', $timeSlot);
    }

    /**
     * Helper Methods
     */

    /**
     * Check if booking can be cancelled by representative
     */
    public function canBeCancelledByRepresentative(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if booking can be cancelled by admin
     */
    public function canBeCancelledByAdmin(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    /**
     * Check if booking is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->booking_date >= now()->startOfDay() 
            && in_array($this->status, ['pending', 'approved']);
    }

    /**
     * Check if booking is past
     */
    public function isPast(): bool
    {
        return $this->booking_date < now()->startOfDay();
    }

    /**
     * Get formatted time slot
     */
    public function getFormattedTimeSlotAttribute(): string
    {
        return Carbon::parse($this->time_slot)->format('g:i A');
    }

    /**
     * Get formatted booking date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->booking_date->format('l, F j, Y');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get human-readable status
     */
    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Check if this booking is within cooldown calculation period
     * (Used for determining if future bookings are blocked)
     */
    public function triggersCooldown(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Get days until appointment
     */
    public function getDaysUntilAppointmentAttribute(): int
    {
        if ($this->isPast()) {
            return 0;
        }

        return now()->diffInDays($this->booking_date);
    }
}
