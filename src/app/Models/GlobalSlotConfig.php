<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GlobalSlotConfig extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'global_slot_config';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'non_pharmacy_start_time',
        'non_pharmacy_end_time',
        'pharmacy_start_time',
        'pharmacy_end_time',
        'slot_duration_minutes',
        'allowed_days',
        'non_pharmacy_daily_limit',
        'pharmacy_daily_limit',
        'booking_advance_days',
        'cooldown_days',
        'is_active',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'allowed_days' => 'array',
        'is_active' => 'boolean',
        'slot_duration_minutes' => 'integer',
        'non_pharmacy_daily_limit' => 'integer',
        'pharmacy_daily_limit' => 'integer',
        'cooldown_days' => 'integer',
    ];

    /**
     * Relationships
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the current active configuration
     * This is a single-row table, so we always return the first active record
     */
    public static function current(): self
    {
        $config = static::where('is_active', true)->first();
        
        if (!$config) {
            // Create default config if none exists
            $config = static::create([
                'non_pharmacy_start_time' => '13:00:00',
                'non_pharmacy_end_time' => '16:00:00',
                'pharmacy_start_time' => '13:00:00',
                'pharmacy_end_time' => '14:40:00',
                'slot_duration_minutes' => 10,
                'allowed_days' => ['Tuesday', 'Thursday'],
                'non_pharmacy_daily_limit' => 20,
                'pharmacy_daily_limit' => 10,
                'cooldown_days' => 14,
                'is_active' => true,
            ]);
        }
        
        return $config;
    }

    /**
     * Get allowed days as array
     */
    public function getAllowedDays(): array
    {
        return $this->allowed_days ?? [];
    }

    /**
     * Generate time slots for pharmacy
     */
    public function getPharmacyTimeSlots(): array
    {
        return $this->generateTimeSlots(
            $this->pharmacy_start_time,
            $this->pharmacy_end_time
        );
    }

    /**
     * Generate time slots for non-pharmacy departments
     */
    public function getNonPharmacyTimeSlots(): array
    {
        return $this->generateTimeSlots(
            $this->non_pharmacy_start_time,
            $this->non_pharmacy_end_time
        );
    }

    /**
     * Generate time slots between start and end time
     */
    public function generateTimeSlots(string $startTime, string $endTime): array
    {
        $slots = [];
        $current = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        while ($current->lessThan($end)) {
            $slots[] = [
                'time' => $current->format('H:i:s'),
                'formatted' => $current->format('g:i A'),
            ];
            $current->addMinutes($this->slot_duration_minutes);
        }

        return $slots;
    }

    /**
     * Check if a specific day is allowed
     */
    public function isDayAllowed(string $dayName): bool
    {
        return in_array($dayName, $this->allowed_days);
    }

    /**
     * Check if a date is allowed (considering allowed days)
     */
    public function isDateAllowed(Carbon $date): bool
    {
        $dayOfWeek = $date->format('l'); // "Monday", "Tuesday", etc.
        return $this->isDayAllowed($dayOfWeek);
    }

    /**
     * Get time range for a department type
     */
    public function getTimeRangeFor(bool $isPharmacy): array
    {
        if ($isPharmacy) {
            return [
                'start' => $this->pharmacy_start_time,
                'end' => $this->pharmacy_end_time,
            ];
        }
        
        return [
            'start' => $this->non_pharmacy_start_time,
            'end' => $this->non_pharmacy_end_time,
        ];
    }

    /**
     * Get daily limit for a department type
     */
    public function getDailyLimitFor(bool $isPharmacy): int
    {
        return $isPharmacy 
            ? $this->pharmacy_daily_limit 
            : $this->non_pharmacy_daily_limit;
    }
/**
 * Get allowed days as day numbers (0=Sunday, 1=Monday, etc.)
 */
public function getAllowedDaysAsNumbers(): array
{
    $dayMap = [
        'Sunday' => 0,
        'Monday' => 1,
        'Tuesday' => 2,
        'Wednesday' => 3,
        'Thursday' => 4,
        'Friday' => 5,
        'Saturday' => 6,
    ];

    $allowedDays = $this->allowed_days ?? ['Tuesday', 'Thursday'];
    
    return array_map(function($day) use ($dayMap) {
        return $dayMap[$day] ?? null;
    }, $allowedDays);
}
}
