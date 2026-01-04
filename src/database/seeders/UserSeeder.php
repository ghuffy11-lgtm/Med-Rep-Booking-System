<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing users (except if you want to keep any)
        User::query()->delete();

        // Super Admin
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@pharmacy.local',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'company' => null,
            'civil_id' => null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Pharmacy Admins
        User::create([
            'name' => 'Pharmacy Admin 1',
            'email' => 'pharmacyadmin@pharmacy.local',
            'password' => Hash::make('password123'),
            'role' => 'pharmacy_admin',
            'company' => null,
            'civil_id' => null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Pharmacy Admin 2',
            'email' => 'admin2@pharmacy.local',
            'password' => Hash::make('password123'),
            'role' => 'pharmacy_admin',
            'company' => null,
            'civil_id' => null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Representatives (Active)
        User::create([
            'name' => 'Ahmed Al-Mansour',
            'email' => 'ahmed@pharmacompany.com',
            'password' => Hash::make('password123'),
            'role' => 'representative',
            'company' => 'PharmaCo Kuwait',
            'civil_id' => '285123456789',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Fatima Al-Sabah',
            'email' => 'fatima@medplus.com',
            'password' => Hash::make('password123'),
            'role' => 'representative',
            'company' => 'MedPlus International',
            'civil_id' => '286234567890',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Mohammed Al-Ahmad',
            'email' => 'mohammed@healthsolutions.com',
            'password' => Hash::make('password123'),
            'role' => 'representative',
            'company' => 'Health Solutions Ltd',
            'civil_id' => '287345678901',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Noura Al-Rashid',
            'email' => 'noura@globalmed.com',
            'password' => Hash::make('password123'),
            'role' => 'representative',
            'company' => 'Global Med Supplies',
            'civil_id' => '288456789012',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Khaled Al-Mutairi',
            'email' => 'khaled@pharmadistro.com',
            'password' => Hash::make('password123'),
            'role' => 'representative',
            'company' => 'Pharma Distribution Co',
            'civil_id' => '289567890123',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Inactive Representative (for testing inactive user handling)
        User::create([
            'name' => 'Sara Al-Hussein (Inactive)',
            'email' => 'sara@oldcompany.com',
            'password' => Hash::make('password123'),
            'role' => 'representative',
            'company' => 'Old Pharma Co',
            'civil_id' => '290678901234',
            'is_active' => false,
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Users created successfully!');
        $this->command->info('');
        $this->command->info('📧 Login Credentials (all passwords: password123):');
        $this->command->info('');
        $this->command->info('🔐 SUPER ADMIN:');
        $this->command->info('   Email: superadmin@pharmacy.local');
        $this->command->info('');
        $this->command->info('🔐 PHARMACY ADMINS:');
        $this->command->info('   Email: pharmacyadmin@pharmacy.local');
        $this->command->info('   Email: admin2@pharmacy.local');
        $this->command->info('');
        $this->command->info('🔐 REPRESENTATIVES (Active):');
        $this->command->info('   Email: ahmed@pharmacompany.com');
        $this->command->info('   Email: fatima@medplus.com');
        $this->command->info('   Email: mohammed@healthsolutions.com');
        $this->command->info('   Email: noura@globalmed.com');
        $this->command->info('   Email: khaled@pharmadistro.com');
        $this->command->info('');
        $this->command->info('❌ INACTIVE REPRESENTATIVE (cannot login):');
        $this->command->info('   Email: sara@oldcompany.com');
    }
}
