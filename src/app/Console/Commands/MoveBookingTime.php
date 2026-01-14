<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MoveBookingTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:move-time
                            {booking_id : The ID of the booking to modify}
                            {new_time : The new time slot (format: HH:MM, e.g., 13:00)}
                            {--date= : Optionally change the date as well (format: YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move a booking to a different time (and optionally date)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookingId = $this->argument('booking_id');
        $newTime = $this->argument('new_time');
        $newDate = $this->option('date');

        // Find the booking
        $booking = Booking::find($bookingId);

        if (!$booking) {
            $this->error("Booking with ID {$bookingId} not found.");
            return Command::FAILURE;
        }

        // Display current booking details
        $this->info("Current Booking Details:");
        $this->table(
            ['Field', 'Value'],
            [
                ['Booking ID', $booking->id],
                ['Representative', $booking->user->name],
                ['Department', $booking->department->name],
                ['Current Date', $booking->booking_date],
                ['Current Time', $booking->time_slot],
                ['Status', $booking->status],
            ]
        );

        // Validate time format
        if (!preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $newTime)) {
            $this->error("Invalid time format. Please use HH:MM format (e.g., 13:00)");
            return Command::FAILURE;
        }

        // Validate date format if provided
        if ($newDate) {
            try {
                Carbon::createFromFormat('Y-m-d', $newDate);
            } catch (\Exception $e) {
                $this->error("Invalid date format. Please use YYYY-MM-DD format (e.g., 2026-01-15)");
                return Command::FAILURE;
            }

            // Check if date is in the past
            if (Carbon::parse($newDate)->isPast()) {
                $this->error("Cannot move booking to a past date.");
                return Command::FAILURE;
            }
        }

        // Prepare update data
        $oldTime = $booking->time_slot;
        $oldDate = $booking->booking_date;

        $booking->time_slot = $newTime;
        if ($newDate) {
            $booking->booking_date = $newDate;
        }

        // Confirm the change
        $this->newLine();
        $this->info("New Booking Details:");
        $this->line("Date: " . ($newDate ?? $booking->booking_date) . ($newDate ? " (changed from {$oldDate})" : " (unchanged)"));
        $this->line("Time: {$newTime} (changed from {$oldTime})");
        $this->newLine();

        if (!$this->confirm('Do you want to proceed with this change?', true)) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        // Save the booking
        try {
            $booking->save();

            $this->newLine();
            $this->info("✓ Booking #{$bookingId} has been successfully updated!");
            $this->line("  Representative: {$booking->user->name}");
            $this->line("  Department: {$booking->department->name}");
            $this->line("  New Date: {$booking->booking_date}");
            $this->line("  New Time: {$booking->time_slot}");

            // Log the change
            \App\Services\AuditLogService::log(
                $booking,
                'booking_time_moved',
                [
                    'booking_date' => $oldDate,
                    'time_slot' => $oldTime,
                ],
                [
                    'booking_date' => $booking->booking_date,
                    'time_slot' => $booking->time_slot,
                ],
                [
                    'changed_by' => 'CLI',
                    'command' => 'booking:move-time',
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to update booking: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
