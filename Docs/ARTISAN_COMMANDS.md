# Artisan Commands Reference

This document lists all custom artisan commands available in the Med-Rep-Booking-System.

## Table of Contents
1. [Booking Commands](#booking-commands)
2. [Usage Examples](#usage-examples)
3. [Tips and Best Practices](#tips-and-best-practices)

---

## Booking Commands

### booking:move-time

Move a booking to a different time (and optionally a different date).

**Signature:**
```bash
php artisan booking:move-time {booking_id} {new_time} [--date=]
```

**Arguments:**
- `booking_id` (required): The ID of the booking to modify
- `new_time` (required): The new time slot in HH:MM format (24-hour format)

**Options:**
- `--date=` (optional): Optionally change the date as well (format: YYYY-MM-DD)

**Description:**
This command allows administrators to move a booking to a different time slot. It provides a safe interactive way to update booking times with confirmation prompts.

**Features:**
- Displays current booking details before making changes
- Validates time and date formats
- Prevents moving bookings to past dates
- Requires confirmation before saving
- Logs the change in audit logs
- Shows success message with updated details

---

## Usage Examples

### Example 1: Move booking time only (same day)

Move booking #45 from 12:00 PM to 1:00 PM (13:00):

```bash
docker exec -it pharmacy_php php artisan booking:move-time 45 13:00
```

**Output:**
```
Current Booking Details:
+---------------+----------------------------+
| Field         | Value                      |
+---------------+----------------------------+
| Booking ID    | 45                         |
| Representative| John Doe                   |
| Department    | Cardiology                 |
| Current Date  | 2026-01-15                 |
| Current Time  | 12:00:00                   |
| Status        | approved                   |
+---------------+----------------------------+

New Booking Details:
Date: 2026-01-15 (unchanged)
Time: 13:00 (changed from 12:00:00)

Do you want to proceed with this change? (yes/no) [yes]:
> yes

✓ Booking #45 has been successfully updated!
  Representative: John Doe
  Department: Cardiology
  New Date: 2026-01-15
  New Time: 13:00
```

### Example 2: Move booking to different date and time

Move booking #52 to January 20, 2026 at 10:30 AM:

```bash
docker exec -it pharmacy_php php artisan booking:move-time 52 10:30 --date=2026-01-20
```

**Output:**
```
Current Booking Details:
+---------------+----------------------------+
| Field         | Value                      |
+---------------+----------------------------+
| Booking ID    | 52                         |
| Representative| Jane Smith                 |
| Department    | Dermatology                |
| Current Date  | 2026-01-15                 |
| Current Time  | 14:00:00                   |
| Status        | pending                    |
+---------------+----------------------------+

New Booking Details:
Date: 2026-01-20 (changed from 2026-01-15)
Time: 10:30 (changed from 14:00:00)

Do you want to proceed with this change? (yes/no) [yes]:
> yes

✓ Booking #52 has been successfully updated!
  Representative: Jane Smith
  Department: Dermatology
  New Date: 2026-01-20
  New Time: 10:30
```

### Example 3: List all bookings (using tinker)

To find booking IDs, you can use Laravel Tinker:

```bash
docker exec -it pharmacy_php php artisan tinker
```

Then run:
```php
// Get today's bookings
Booking::whereDate('booking_date', today())->get(['id', 'user_id', 'booking_date', 'time_slot', 'status']);

// Get specific user's bookings
Booking::where('user_id', 5)->get(['id', 'booking_date', 'time_slot', 'status']);

// Get bookings by date
Booking::whereDate('booking_date', '2026-01-15')->get(['id', 'user_id', 'booking_date', 'time_slot']);

// Exit tinker
exit
```

### Example 4: Cancel operation

If you change your mind:

```bash
docker exec -it pharmacy_php php artisan booking:move-time 45 13:00
```

When prompted:
```
Do you want to proceed with this change? (yes/no) [yes]:
> no

Operation cancelled.
```

---

## Tips and Best Practices

### Time Format
- Always use 24-hour format (HH:MM)
- Examples:
  - 9:00 AM → `09:00`
  - 12:00 PM → `12:00`
  - 1:00 PM → `13:00`
  - 5:30 PM → `17:30`

### Date Format
- Use ISO format: YYYY-MM-DD
- Examples:
  - January 15, 2026 → `2026-01-15`
  - December 31, 2026 → `2026-12-31`

### Validation Rules
- Time must be in HH:MM format (00:00 to 23:59)
- Date (if provided) must be in YYYY-MM-DD format
- Cannot move bookings to past dates
- Booking ID must exist in the database

### Before Moving a Booking

1. **Check the booking exists:**
   ```bash
   docker exec -it pharmacy_php php artisan tinker
   Booking::find(45)
   ```

2. **Verify the representative's schedule** to ensure they are available at the new time

3. **Check department availability** at the new time slot

4. **Consider notifying the representative** about the time change via email

### Audit Logging

All booking time changes are automatically logged in the `audit_logs` table with:
- Action: `booking_time_moved`
- Old date and time
- New date and time
- Timestamp
- IP address (CLI if run from command line)

View recent changes:
```bash
docker exec -it pharmacy_php php artisan tinker
```
```php
\App\Models\AuditLog::where('action', 'booking_time_moved')->latest()->take(10)->get();
```

### Common Errors

**Error:** "Booking with ID X not found."
- **Solution:** Verify the booking ID exists using tinker or the admin dashboard

**Error:** "Invalid time format. Please use HH:MM format"
- **Solution:** Use 24-hour format with leading zeros (e.g., 09:00, not 9:00)

**Error:** "Invalid date format. Please use YYYY-MM-DD format"
- **Solution:** Ensure year is 4 digits and use hyphens (e.g., 2026-01-15)

**Error:** "Cannot move booking to a past date."
- **Solution:** Only future dates are allowed for bookings

---

## Alternative: Using Laravel Tinker

For more complex operations or bulk updates, you can use Laravel Tinker:

```bash
docker exec -it pharmacy_php php artisan tinker
```

### Example: Move multiple bookings

```php
// Find bookings to move
$bookings = Booking::whereDate('booking_date', '2026-01-15')
    ->where('time_slot', '12:00')
    ->get();

// Update each booking
foreach ($bookings as $booking) {
    $booking->time_slot = '13:00';
    $booking->save();
    echo "Moved booking #{$booking->id}\n";
}
```

### Example: Move booking with full control

```php
$booking = Booking::find(45);
$booking->booking_date = '2026-01-20';
$booking->time_slot = '14:30';
$booking->save();

echo "Booking #{$booking->id} moved to {$booking->booking_date} at {$booking->time_slot}";
```

---

**Version**: 1.0
**Last Updated**: January 2026
**Document ID**: ARTISAN-CMD-001

For questions: tech-support@example.com
