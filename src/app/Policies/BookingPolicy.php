<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Determine if the user can view any bookings.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view bookings
        return true;
    }

    /**
     * Determine if the user can view the booking.
     */
    public function view(User $user, Booking $booking): bool
    {
        // Representatives can only view their own bookings
        if ($user->role === 'representative') {
            return $user->id === $booking->user_id;
        }

        // Admins can view all bookings
        return $user->isAdmin();
    }

    /**
     * Determine if the user can create bookings.
     */
    public function create(User $user): bool
    {
        // Only representatives can create bookings
        return $user->role === 'representative' && $user->is_active;
    }

    /**
     * Determine if the user can update the booking.
     */
    public function update(User $user, Booking $booking): bool
    {
        // Representatives cannot update bookings after creation
        // Only admins can update (approve/reject)
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete/cancel the booking.
     */
    public function delete(User $user, Booking $booking): bool
    {
        // Representatives can only cancel their own PENDING bookings
        if ($user->role === 'representative') {
            return $user->id === $booking->user_id 
                && $booking->status === 'pending';
        }

        // Admins can cancel pending or approved bookings
        if ($user->isAdmin()) {
            return in_array($booking->status, ['pending', 'approved']);
        }

        return false;
    }

    /**
     * Determine if the user can approve the booking.
     */
    public function approve(User $user, Booking $booking): bool
    {
        // Only admins can approve
        if (!$user->isAdmin()) {
            return false;
        }

        // Can only approve pending bookings
        return $booking->status === 'pending';
    }

    /**
     * Determine if the user can reject the booking.
     */
    public function reject(User $user, Booking $booking): bool
    {
        // Only admins can reject
        if (!$user->isAdmin()) {
            return false;
        }

        // Can only reject pending bookings
        return $booking->status === 'pending';
    }

    /**
     * Determine if the user can cancel an approved booking.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        // Only admins can cancel approved bookings
        if (!$user->isAdmin()) {
            return false;
        }

        // Can cancel pending or approved bookings
        return in_array($booking->status, ['pending', 'approved']);
    }

    /**
     * Determine if the user can view booking history.
     */
    public function viewHistory(User $user): bool
    {
        // Representatives can view their own history
        // Admins can view all history
        return true;
    }

    /**
     * Determine if the user can view the approval queue.
     */
    public function viewApprovalQueue(User $user): bool
    {
        // Only admins can view approval queue
        return $user->isAdmin();
    }
}
