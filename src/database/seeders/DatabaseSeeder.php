<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->info('');

        // Run seeders in order
        $this->call([
            GlobalSlotConfigSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✨ Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Global configuration: ✓');
        $this->command->info('   - Departments: 8 total (5 non-pharmacy, 3 pharmacy)');
        $this->command->info('   - Users: 9 total (1 super admin, 2 pharmacy admins, 6 representatives)');
        $this->command->info('');
        $this->command->info('🚀 You can now:');
        $this->command->info('   1. Login with any of the test accounts');
        $this->command->info('   2. Test booking creation as representatives');
        $this->command->info('   3. Test approval workflow as admins');
        $this->command->info('   4. Test cooldown periods and validations');
        $this->command->info('');
        $this->command->info('🔑 All passwords are: password123');
    }
}
