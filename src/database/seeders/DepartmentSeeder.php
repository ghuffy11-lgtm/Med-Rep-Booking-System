<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing departments
        Department::query()->delete();

        // Non-Pharmacy Departments
        $nonPharmacyDepartments = [
            [
                'name' => 'Cardiology',
                'description' => 'Heart and cardiovascular system specialists',
                'is_pharmacy_department' => false,
            ],
            [
                'name' => 'Neurology',
                'description' => 'Brain and nervous system specialists',
                'is_pharmacy_department' => false,
            ],
            [
                'name' => 'Orthopedics',
                'description' => 'Bone, joint, and muscle specialists',
                'is_pharmacy_department' => false,
            ],
            [
                'name' => 'Pediatrics',
                'description' => 'Children\'s health specialists',
                'is_pharmacy_department' => false,
            ],
            [
                'name' => 'Dermatology',
                'description' => 'Skin health specialists',
                'is_pharmacy_department' => false,
            ],
        ];

        // Pharmacy Departments
        $pharmacyDepartments = [
            [
                'name' => 'Central Pharmacy',
                'description' => 'Main hospital pharmacy for general medications',
                'is_pharmacy_department' => true,
            ],
            [
                'name' => 'Oncology Pharmacy',
                'description' => 'Specialized pharmacy for cancer medications',
                'is_pharmacy_department' => true,
            ],
            [
                'name' => 'Pediatric Pharmacy',
                'description' => 'Specialized pharmacy for children\'s medications',
                'is_pharmacy_department' => true,
            ],
        ];

        // Create non-pharmacy departments
        foreach ($nonPharmacyDepartments as $dept) {
            Department::create($dept + ['is_active' => true]);
        }

        // Create pharmacy departments
        foreach ($pharmacyDepartments as $dept) {
            Department::create($dept + ['is_active' => true]);
        }

        $this->command->info('✅ Departments created successfully!');
        $totalNonPharmacy = count($nonPharmacyDepartments);
        $totalPharmacy = count($pharmacyDepartments);
        $this->command->info("   - Non-pharmacy departments: {$totalNonPharmacy}");
        $this->command->info("   - Pharmacy departments: {$totalPharmacy}");
    }
}
