<?php

namespace Database\Seeders;

use App\Models\GlobalSlotConfig;
use Illuminate\Database\Seeder;

class GlobalSlotConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing config if any
        GlobalSlotConfig::query()->delete();

        // Create default configuration
        GlobalSlotConfig::create([
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
            'updated_by' => null,
        ]);

        $this->command->info('✅ Global slot configuration created successfully!');
        $this->command->info('   - Non-pharmacy slots: 20/day (1:00 PM - 4:00 PM)');
        $this->command->info('   - Pharmacy slots: 10/day (1:00 PM - 2:40 PM)');
        $this->command->info('   - Allowed days: Tuesday, Thursday');
        $this->command->info('   - Cooldown period: 14 days');
    }
}
