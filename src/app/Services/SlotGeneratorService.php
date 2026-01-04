<?php

namespace App\Services;

use App\Models\Department;
use App\Models\GlobalSlotConfig;
use App\Models\Booking;
use Carbon\Carbon;

class SlotGeneratorService
{
    /**
     * Generate all time slots for a department on a specific date
     */
    public function generateSlotsForDate(Department $department, Carbon $date): array
    {
        $config = GlobalSlotConfig::current();
        $isPharmacy = $department->is_pharmacy_department;

        // Get time range based on department type
        if ($isPharmacy) {
            $startTime = $config->pharmacy_start_time;
            $endTime = $config->pharmacy_end_time;
        } else {
            $startTime = $config->non_pharmacy_start_time;
            $endTime = $config->non_pharmacy_end_time;
        }

        return $this->generateTimeSlots(
            $startTime,
            $endTime,
            $config->slot_duration_minutes
        );
    }

    /**
     * Generate time slots between start and end time
     */
    public function generateTimeSlots(string $startTime, string $endTime, int $durationMinutes): array
    {
        $slots = [];
        $current = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        while ($current->lessThan($end)) {
            $slots[] = [
                'time' => $current->format('H:i:s'),
                'formatted' => $current->format('g:i A'),
                'display' => $current->format('h:i A'),
            ];
            $current->addMinutes($durationMinutes);
        }

        return $slots;
    }

    /**
     * Get available slots for a department on a specific date
     * Checks bookings and returns only available slots
     */
    public function getAvailableSlots(Department $department, Carbon $date): array
    {
        $allSlots = $this->generateSlotsForDate($department, $date);
        $bookedSlots = $this->getBookedSlots($department, $date);

        // Filter out booked slots
        $availableSlots = array_filter($allSlots, function($slot) use ($bookedSlots) {
            return !in_array($slot['time'], $bookedSlots);
        });

        return array_values($availableSlots);
    }

    /**
     * Get booked time slots for a department on a specific date
     * Considers the pool type (pharmacy vs non-pharmacy)
     */
    public function getBookedSlots(Department $department, Carbon $date): array
    {
        $isPharmacy = $department->is_pharmacy_department;

        $query = Booking::where('booking_date', $date->format('Y-m-d'))
            ->whereIn('status', ['pending', 'approved']);

        if ($isPharmacy) {
            // Pharmacy pool: Only count pharmacy bookings
            $query->whereHas('department', function($q) {
                $q->where('is_pharmacy_department', true);
            });
        } else {
            // Non-pharmacy pool: Only count non-pharmacy bookings
            $query->whereHas('department', function($q) {
                $q->where('is_pharmacy_department', false);
            });
        }

        return $query->pluck('time_slot')->toArray();
    }

    /**
     * Get booking statistics for a department on a specific date
     */
    public function getSlotStatistics(Department $department, Carbon $date): array
    {
        $allSlots = $this->generateSlotsForDate($department, $date);
        $bookedSlots = $this->getBookedSlots($department, $date);
        $config = GlobalSlotConfig::current();

        $isPharmacy = $department->is_pharmacy_department;
        $dailyLimit = $isPharmacy 
            ? $config->pharmacy_daily_limit 
            : $config->non_pharmacy_daily_limit;

        return [
            'total_slots' => count($allSlots),
            'booked_slots' => count($bookedSlots),
            'available_slots' => count($allSlots) - count($bookedSlots),
            'daily_limit' => $dailyLimit,
            'bookings_count' => count($bookedSlots),
            'remaining_capacity' => $dailyLimit - count($bookedSlots),
            'percentage_booked' => count($allSlots) > 0 
                ? round((count($bookedSlots) / $dailyLimit) * 100, 2) 
                : 0,
        ];
    }

    /**
     * Check if a specific time slot is available
     */
    public function isSlotAvailable(Department $department, Carbon $date, string $timeSlot): bool
    {
        $isPharmacy = $department->is_pharmacy_department;

        $query = Booking::where('booking_date', $date->format('Y-m-d'))
            ->where('time_slot', $timeSlot)
            ->whereIn('status', ['pending', 'approved']);

        if ($isPharmacy) {
            $query->whereHas('department', function($q) {
                $q->where('is_pharmacy_department', true);
            });
        } else {
            $query->whereHas('department', function($q) {
                $q->where('is_pharmacy_department', false);
            });
        }

        return !$query->exists();
    }

    /**
     * Get next available slot for a department
     */
    public function getNextAvailableSlot(Department $department, Carbon $fromDate): ?array
    {
        $config = GlobalSlotConfig::current();
        $allowedDays = $config->getAllowedDays();
        $maxDaysToCheck = 30; // Look ahead 30 days
        $daysChecked = 0;
        $currentDate = $fromDate->copy();

        while ($daysChecked < $maxDaysToCheck) {
            $dayOfWeek = $currentDate->format('l');
            
            // Check if this day is allowed
            if (in_array($dayOfWeek, $allowedDays)) {
                // Check if department is available
                if ($department->isAvailableOn($currentDate)) {
                    $availableSlots = $this->getAvailableSlots($department, $currentDate);
                    
                    if (!empty($availableSlots)) {
                        return [
                            'date' => $currentDate->format('Y-m-d'),
                            'formatted_date' => $currentDate->format('l, F j, Y'),
                            'slot' => $availableSlots[0],
                        ];
                    }
                }
            }
            
            $currentDate->addDay();
            $daysChecked++;
        }

        return null; // No available slots found in the next 30 days
    }

    /**
     * Generate slots for multiple dates (for calendar view)
     */
    public function generateSlotsForDateRange(Department $department, Carbon $startDate, Carbon $endDate): array
    {
        $slots = [];
        $current = $startDate->copy();

        while ($current->lessThanOrEqualTo($endDate)) {
            $stats = $this->getSlotStatistics($department, $current);
            
            $slots[$current->format('Y-m-d')] = [
                'date' => $current->format('Y-m-d'),
                'formatted_date' => $current->format('l, M j'),
                'statistics' => $stats,
                'has_availability' => $stats['available_slots'] > 0,
            ];
            
            $current->addDay();
        }

        return $slots;
    }
}
