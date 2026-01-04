<?php

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use App\Models\GlobalSlotConfig;
use Carbon\Carbon;

class CooldownCalculatorService
{
    /**
     * Check if user is currently in cooldown
     * Rule 2: 14 days from APPOINTMENT DATE (booking_date field)
     * Only triggered by APPROVED bookings
     */
    public function isInCooldown(User $user): bool
    {
        $lastApprovedBooking = $this->getLastApprovedBooking($user);

        if (!$lastApprovedBooking) {
            return false;
        }

        $cooldownEndDate = $this->calculateCooldownEndDate($lastApprovedBooking->booking_date);
        
        return now()->lessThan($cooldownEndDate);
    }

    /**
     * Get the last approved booking for a user
     */
    public function getLastApprovedBooking(User $user): ?Booking
    {
        return $user->bookings()
            ->where('status', 'approved')
            ->orderBy('booking_date', 'desc')
            ->first();
    }

    /**
     * Calculate cooldown end date from appointment date
     * Rule 2: Can book again on the 15th day (14 days cooldown)
     */
    public function calculateCooldownEndDate(Carbon $appointmentDate): Carbon
    {
        $config = GlobalSlotConfig::current();
        return $appointmentDate->copy()->addDays($config->cooldown_days);
    }

    /**
     * Get days remaining in cooldown period
     */
    public function getDaysRemaining(User $user): int
    {
        if (!$this->isInCooldown($user)) {
            return 0;
        }

        $lastApprovedBooking = $this->getLastApprovedBooking($user);
        $cooldownEndDate = $this->calculateCooldownEndDate($lastApprovedBooking->booking_date);

        return now()->diffInDays($cooldownEndDate, false);
    }

    /**
     * Get cooldown end date for a user
     */
    public function getCooldownEndDate(User $user): ?Carbon
    {
        $lastApprovedBooking = $this->getLastApprovedBooking($user);

        if (!$lastApprovedBooking) {
            return null;
        }

        return $this->calculateCooldownEndDate($lastApprovedBooking->booking_date);
    }

    /**
     * Get complete cooldown information for a user
     */
    public function getCooldownInfo(User $user): array
    {
        $lastApprovedBooking = $this->getLastApprovedBooking($user);

        if (!$lastApprovedBooking) {
            return [
                'in_cooldown' => false,
                'has_previous_booking' => false,
                'last_appointment_date' => null,
                'cooldown_end_date' => null,
                'days_remaining' => 0,
                'can_book_from' => now(),
                'message' => 'No cooldown active. You can book an appointment.',
            ];
        }

        $cooldownEndDate = $this->calculateCooldownEndDate($lastApprovedBooking->booking_date);
        $inCooldown = now()->lessThan($cooldownEndDate);
        $daysRemaining = $inCooldown ? now()->diffInDays($cooldownEndDate, false) : 0;

        return [
            'in_cooldown' => $inCooldown,
            'has_previous_booking' => true,
            'last_appointment_date' => $lastApprovedBooking->booking_date,
            'cooldown_end_date' => $cooldownEndDate,
            'days_remaining' => $daysRemaining,
            'can_book_from' => $cooldownEndDate,
            'message' => $inCooldown 
                ? "You are in cooldown period. You can book again on {$cooldownEndDate->format('F j, Y')} ({$daysRemaining} days remaining)."
                : "Your cooldown period has ended. You can book an appointment.",
            'last_booking_details' => [
                'department' => $lastApprovedBooking->department->name,
                'date' => $lastApprovedBooking->booking_date->format('F j, Y'),
                'time' => $lastApprovedBooking->formatted_time_slot,
            ],
        ];
    }

    /**
     * Check if a specific date would be within cooldown
     * Useful for validating future booking dates
     */
    public function wouldBeInCooldownOn(User $user, Carbon $targetDate): bool
    {
        $lastApprovedBooking = $this->getLastApprovedBooking($user);

        if (!$lastApprovedBooking) {
            return false;
        }

        $cooldownEndDate = $this->calculateCooldownEndDate($lastApprovedBooking->booking_date);
        
        return $targetDate->lessThan($cooldownEndDate);
    }

    /**
     * Get the earliest date a user can book
     */
    public function getEarliestBookingDate(User $user): Carbon
    {
        if (!$this->isInCooldown($user)) {
            return now();
        }

        return $this->getCooldownEndDate($user);
    }

    /**
     * Calculate when cooldown will end if a booking is approved today
     * Useful for showing users when they can book next
     */
    public function calculateFutureCooldownEnd(Carbon $proposedAppointmentDate): Carbon
    {
        $config = GlobalSlotConfig::current();
        return $proposedAppointmentDate->copy()->addDays($config->cooldown_days);
    }

    /**
     * Get cooldown statistics for all representatives
     * Useful for admin dashboard
     */
    public function getCooldownStatistics(): array
    {
        $representatives = User::where('role', 'representative')
            ->where('is_active', true)
            ->get();

        $inCooldown = 0;
        $noCooldown = 0;
        $neverBooked = 0;

        foreach ($representatives as $rep) {
            if ($this->isInCooldown($rep)) {
                $inCooldown++;
            } elseif ($this->getLastApprovedBooking($rep)) {
                $noCooldown++;
            } else {
                $neverBooked++;
            }
        }

        return [
            'total_representatives' => $representatives->count(),
            'in_cooldown' => $inCooldown,
            'available_to_book' => $noCooldown + $neverBooked,
            'never_booked' => $neverBooked,
            'percentage_in_cooldown' => $representatives->count() > 0 
                ? round(($inCooldown / $representatives->count()) * 100, 2) 
                : 0,
        ];
    }

    /**
     * Get list of representatives currently in cooldown
     * Useful for admin reporting
     */
    public function getRepresentativesInCooldown(): \Illuminate\Support\Collection
    {
        $representatives = User::where('role', 'representative')
            ->where('is_active', true)
            ->get();

        return $representatives->filter(function($rep) {
            return $this->isInCooldown($rep);
        })->map(function($rep) {
            $info = $this->getCooldownInfo($rep);
            return [
                'user_id' => $rep->id,
                'name' => $rep->name,
                'email' => $rep->email,
                'company' => $rep->company,
                'cooldown_info' => $info,
            ];
        })->values();
    }
}
