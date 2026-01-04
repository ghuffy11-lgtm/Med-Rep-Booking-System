<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;

class SchedulePolicy
{
    /**
     * Determine if the user can view any schedules.
     */
    public function viewAny(User $user): bool
    {
        // Only admins can view schedules list
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view the schedule.
     */
    public function view(User $user, Schedule $schedule): bool
    {
        // Only admins can view schedule details
        return $user->isAdmin();
    }

    /**
     * Determine if the user can create schedules.
     */
    public function create(User $user): bool
    {
        // Only admins can create schedules (closures/overrides)
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the schedule.
     */
    public function update(User $user, Schedule $schedule): bool
    {
        // Only admins can update schedules
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the schedule.
     */
    public function delete(User $user, Schedule $schedule): bool
    {
        // Only admins can delete schedules
        return $user->isAdmin();
    }

    /**
     * Determine if the user can create department closures.
     */
    public function createClosure(User $user): bool
    {
        // Only admins can create closures
        return $user->isAdmin();
    }

    /**
     * Determine if the user can override allowed days.
     */
    public function overrideDays(User $user): bool
    {
        // Only admins can override allowed days
        return $user->isAdmin();
    }
}
