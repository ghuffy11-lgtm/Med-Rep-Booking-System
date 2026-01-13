<?php

namespace App\Services;

use App\Models\Department;
use App\Models\GlobalSlotConfig;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class ValidationRuleService
{
    /**
     * Get validation rules for booking creation
     */
    public static function bookingStoreRules(): array
    {
        return [
            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
                function ($attribute, $value, $fail) {
                    $department = Department::find($value);
                    if ($department && !$department->is_active) {
                        $fail('The selected department is currently inactive.');
                    }
                },
            ],
            'booking_date' => [
                'required',
                'date',
                'after:' . now()->format('Y-m-d'),
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value);
                    $config = GlobalSlotConfig::current();
                    
                    if (!$config->isDateAllowed($date)) {
                        $allowedDays = implode(', ', $config->getAllowedDays());
                        $fail("Bookings are only allowed on: {$allowedDays}.");
                    }
                },
            ],
            'time_slot' => [
                'required',
                'date_format:H:i:s',
                function ($attribute, $value, $fail) {
                    $request = request();
                    if (!$request->has('department_id')) {
                        return;
                    }

                    $department = Department::find($request->department_id);
                    if (!$department) {
                        return;
                    }

                    $config = GlobalSlotConfig::current();
                    $isPharmacy = $department->is_pharmacy_department;
                    
                    $validSlots = $isPharmacy 
                        ? $config->getPharmacyTimeSlots() 
                        : $config->getNonPharmacyTimeSlots();
                    
                    $validTimes = array_column($validSlots, 'time');
                    
                    if (!in_array($value, $validTimes)) {
                        $fail('The selected time slot is not valid for this department.');
                    }
                },
            ],
        ];
    }

    /**
     * Get validation rules for department creation/update
     */
    public static function departmentRules(?int $departmentId = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments')->ignore($departmentId),
            ],
            'description' => 'nullable|string|max:1000',
            'is_pharmacy_department' => 'required|boolean',
            'is_active' => 'required|boolean',
        ];
    }

    /**
     * Get validation rules for schedule creation/update
     */
    public static function scheduleRules(): array
    {
        return [
            'department_id' => 'required|integer|exists:departments,id',
            'start_date' => [
                'required',
                'date',
                'after_or_equal:' . now()->format('Y-m-d'),
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],
            'is_closed' => 'required|boolean',
            'override_days' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $validDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        foreach ($value as $day) {
                            if (!in_array($day, $validDays)) {
                                $fail("Invalid day: {$day}. Must be one of: " . implode(', ', $validDays));
                            }
                        }
                    }
                },
            ],
            'override_days.*' => 'string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            // Add these two lines after 'override_days.*' rule:
            'override_start_time' => 'nullable|date_format:H:i|required_with:override_end_time',
            'override_end_time' => 'nullable|date_format:H:i|required_with:override_start_time|after:override_start_time',

            'notes' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ];
    }

    /**
     * Get validation rules for user creation/update
     */
    public static function userRules(?int $userId = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => $userId
                ? 'nullable|string|min:8|confirmed'
                : 'required|string|min:8|confirmed',
            'role' => [
                'required',
                Rule::in(['super_admin', 'pharmacy_admin', 'representative']),
            ],
            'company' => 'required_if:role,representative|nullable|string|max:255',
            'civil_id' => [
                'required_if:role,representative',
                'nullable',
                'string',
                'size:12',
                Rule::unique('users')->ignore($userId),
            ],
            'mobile_number' => [
                'required_if:role,representative',
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->ignore($userId),
            ],
            'is_active' => 'required|boolean',
        ];
    }

    /**
     * Get validation rules for global slot config update
     */
public static function globalConfigRules(): array
{
    return [
        'non_pharmacy_start_time' => 'required',
        'non_pharmacy_end_time' => [
            'required',
            'after:non_pharmacy_start_time',
        ],
        'pharmacy_start_time' => 'required',
        'pharmacy_end_time' => [
            'required',
            'after:pharmacy_start_time',
        ],
        'slot_duration_minutes' => 'required|integer|min:5|max:60',
        'allowed_days' => [
            'required',
            'array',
            'min:1',
        ],
        'allowed_days.*' => 'string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
        'non_pharmacy_daily_limit' => 'required|integer|min:1|max:100',
        'pharmacy_daily_limit' => 'required|integer|min:1|max:100',
        'cooldown_days' => 'required|integer|min:1|max:90',
        'booking_advance_days' => 'required|integer|min:1|max:90',  // ADD THIS LINE

    ];
}
    /**
     * Get validation rules for booking approval
     */
    public static function bookingApprovalRules(): array
    {
        return [
            'booking_id' => 'required|integer|exists:bookings,id',
        ];
    }

    /**
     * Get validation rules for booking rejection
     */
    public static function bookingRejectionRules(): array
    {
        return [
            'booking_id' => 'required|integer|exists:bookings,id',
            'rejection_reason' => 'required|string|max:1000',
        ];
    }

    /**
     * Get validation rules for booking cancellation
     */
    public static function bookingCancellationRules(): array
    {
        return [
            'booking_id' => 'required|integer|exists:bookings,id',
            'cancellation_reason' => 'required|string|max:1000',
        ];
    }

    /**
     * Get validation rules for civil ID format
     */
    public static function civilIdRules(): string
    {
        return 'required|string|size:12|regex:/^[0-9]{12}$/';
    }

    /**
     * Custom validation: Check if date is a valid booking day
     */
    public static function isValidBookingDay(Carbon $date): bool
    {
        $config = GlobalSlotConfig::current();
        return $config->isDateAllowed($date);
    }

    /**
     * Custom validation: Check if time slot is within allowed range
     */
    public static function isValidTimeSlot(string $timeSlot, bool $isPharmacy): bool
    {
        $config = GlobalSlotConfig::current();
        $validSlots = $isPharmacy 
            ? $config->getPharmacyTimeSlots() 
            : $config->getNonPharmacyTimeSlots();
        
        $validTimes = array_column($validSlots, 'time');
        return in_array($timeSlot, $validTimes);
    }

    /**
     * Get custom error messages
     */
    public static function customMessages(): array
    {
        return [
            'department_id.required' => 'Please select a department.',
            'department_id.exists' => 'The selected department does not exist.',
            'booking_date.required' => 'Please select a booking date.',
            'booking_date.after_or_equal' => 'Booking date must be today or in the future.',
            'time_slot.required' => 'Please select a time slot.',
            'time_slot.date_format' => 'Invalid time slot format.',
            'civil_id.size' => 'Civil ID must be exactly 12 digits.',
            'civil_id.regex' => 'Civil ID must contain only numbers.',
            'civil_id.unique' => 'This Civil ID is already registered.',
            'mobile_number.required_if' => 'Mobile number is required for representatives.',
            'mobile_number.max' => 'Mobile number cannot exceed 20 characters.',
            'mobile_number.unique' => 'This mobile number is already registered.',
            'email.unique' => 'This email address is already registered.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    /**
     * Get custom attribute names for better error messages
     */
    public static function customAttributes(): array
    {
        return [
            'department_id' => 'department',
            'booking_date' => 'appointment date',
            'time_slot' => 'time slot',
            'civil_id' => 'Civil ID',
            'is_pharmacy_department' => 'pharmacy department status',
            'is_active' => 'active status',
            'is_closed' => 'closure status',
            'override_days' => 'custom days',
            'non_pharmacy_start_time' => 'non-pharmacy start time',
            'non_pharmacy_end_time' => 'non-pharmacy end time',
            'pharmacy_start_time' => 'pharmacy start time',
            'pharmacy_end_time' => 'pharmacy end time',
            'slot_duration_minutes' => 'slot duration',
            'allowed_days' => 'allowed booking days',
            'non_pharmacy_daily_limit' => 'non-pharmacy daily limit',
            'pharmacy_daily_limit' => 'pharmacy daily limit',
            'cooldown_days' => 'cooldown period',
        ];
    }
}
