<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$pharmDept = \App\Models\Department::where('is_pharmacy_department', true)->first();
$nonPharmDept = \App\Models\Department::where('is_pharmacy_department', false)->first();
$adminUser = \App\Models\User::whereIn('role', ['pharmacy_admin', 'super_admin'])->first();
$testDate = '2026-01-22'; // Thursday

echo "Creating 10 UNIQUE pharmacy bookings...\n";
$times = ['12:00:00', '12:10:00', '12:20:00', '12:30:00', '12:40:00', '12:50:00', '13:00:00', '13:10:00', '13:20:00', '13:30:00'];
foreach ($times as $time) {
    \App\Models\Booking::create([
        'user_id' => 17,
        'department_id' => $pharmDept->id,
        'booking_date' => $testDate,
        'time_slot' => $time,
        'status' => 'approved',
        'approved_by' => $adminUser->id,
        'approved_at' => now()
    ]);
}
echo "Created 10 pharmacy bookings\n";

echo "Creating 20 UNIQUE non-pharmacy bookings...\n";
$times2 = ['12:00:00', '12:10:00', '12:20:00', '12:30:00', '12:40:00', '12:50:00', '13:00:00', '13:10:00', '13:20:00', '13:30:00', '13:40:00', '13:50:00', '14:00:00', '14:10:00', '14:20:00', '14:30:00', '14:40:00', '14:50:00', '15:00:00', '15:10:00'];
foreach ($times2 as $time) {
    \App\Models\Booking::create([
        'user_id' => 17,
        'department_id' => $nonPharmDept->id,
        'booking_date' => $testDate,
        'time_slot' => $time,
        'status' => 'approved',
        'approved_by' => $adminUser->id,
        'approved_at' => now()
    ]);
}
echo "Created 20 non-pharmacy bookings\n";
