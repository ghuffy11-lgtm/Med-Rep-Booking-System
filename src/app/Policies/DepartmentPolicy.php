<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    /**
     * Determine if the user can view any departments.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view departments (for booking dropdown)
        return true;
    }

    /**
     * Determine if the user can view the department.
     */
    public function view(User $user, Department $department): bool
    {
        // All authenticated users can view department details
        return true;
    }

    /**
     * Determine if the user can create departments.
     */
    public function create(User $user): bool
    {
        // Only admins can create departments
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the department.
     */
    public function update(User $user, Department $department): bool
    {
        // Only admins can update departments
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the department.
     */
    public function delete(User $user, Department $department): bool
    {
        // Only admins can delete departments
        return $user->isAdmin();
    }

    /**
     * Determine if the user can manage department schedules.
     */
    public function manageSchedules(User $user, Department $department): bool
    {
        // Only admins can manage schedules
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view department statistics.
     */
    public function viewStatistics(User $user, Department $department): bool
    {
        // Only admins can view detailed statistics
        return $user->isAdmin();
    }
}
