<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Receptionist;
use App\Models\Patient;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users with different roles
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@clinic.com',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
            'status' => 'active'
        ]);

        $doctorUser = User::factory()->create([
            'name' => 'Dr. John Smith',
            'email' => 'doctor@clinic.com',
            'role' => 'doctor',
            'password' => bcrypt('doctor123'),
            'status' => 'active',
            'employee_id' => 'DOC001',
            'license_number' => 'MD123456',
            'specialization' => 'General Medicine',
            'department' => 'Internal Medicine',
            'hire_date' => now()->subYears(5)
        ]);

        $staffUser = User::factory()->create([
            'name' => 'Staff Member',
            'email' => 'staff@clinic.com',
            'role' => 'staff',
            'password' => bcrypt('staff123'),
            'status' => 'active',
            'employee_id' => 'STAFF001',
            'department' => 'Administration',
            'hire_date' => now()->subYears(2)
        ]);

        $patientUser = User::factory()->create([
            'name' => 'Patient User',
            'email' => 'patient@clinic.com',
            'role' => 'patient',
            'password' => bcrypt('patient123'),
            'status' => 'active'
        ]);

        // Create staff records for the dedicated staff tables
        
        // Create Admin records
        Admin::factory()->create([
            'staffName' => 'System Administrator',
            'staffEmail' => 'sysadmin@clinic.com',
            'staffPhoneNumber' => '+1-555-0100',
            'dateHired' => now()->subYears(3),
            'role' => 'admin',
            'password' => bcrypt('sysadmin123'),
            'authorityLevel' => 3
        ]);

        Admin::factory()->create([
            'staffName' => 'Medical Administrator',
            'staffEmail' => 'medadmin@clinic.com',
            'staffPhoneNumber' => '+1-555-0101',
            'dateHired' => now()->subYears(2),
            'role' => 'admin',
            'password' => bcrypt('medadmin123'),
            'authorityLevel' => 2
        ]);

        // Create Doctor records
        Doctor::factory()->create([
            'staffName' => 'Dr. Sarah Johnson',
            'staffEmail' => 'sarah.johnson@clinic.com',
            'staffPhoneNumber' => '+1-555-0200',
            'dateHired' => now()->subYears(8),
            'role' => 'doctor',
            'password' => bcrypt('doctor123'),
            'specialization' => 'Cardiology'
        ]);

        Doctor::factory()->create([
            'staffName' => 'Dr. Michael Chen',
            'staffEmail' => 'michael.chen@clinic.com',
            'staffPhoneNumber' => '+1-555-0201',
            'dateHired' => now()->subYears(6),
            'role' => 'doctor',
            'password' => bcrypt('doctor123'),
            'specialization' => 'Pediatrics'
        ]);

        Doctor::factory()->create([
            'staffName' => 'Dr. Emily Rodriguez',
            'staffEmail' => 'emily.rodriguez@clinic.com',
            'staffPhoneNumber' => '+1-555-0202',
            'dateHired' => now()->subYears(4),
            'role' => 'doctor',
            'password' => bcrypt('doctor123'),
            'specialization' => 'Neurology'
        ]);

        // Create Receptionist records
        Receptionist::factory()->create([
            'staffName' => 'Alice Williams',
            'staffEmail' => 'alice.williams@clinic.com',
            'staffPhoneNumber' => '+1-555-0300',
            'dateHired' => now()->subYears(3),
            'role' => 'receptionist',
            'password' => bcrypt('receptionist123'),
            'status' => 'active',
            'shift' => 'morning'
        ]);

        Receptionist::factory()->create([
            'staffName' => 'Robert Brown',
            'staffEmail' => 'robert.brown@clinic.com',
            'staffPhoneNumber' => '+1-555-0301',
            'dateHired' => now()->subYears(2),
            'role' => 'receptionist',
            'password' => bcrypt('receptionist123'),
            'status' => 'active',
            'shift' => 'afternoon'
        ]);

        Receptionist::factory()->create([
            'staffName' => 'Lisa Davis',
            'staffEmail' => 'lisa.davis@clinic.com',
            'staffPhoneNumber' => '+1-555-0302',
            'dateHired' => now()->subYears(1),
            'role' => 'receptionist',
            'password' => bcrypt('receptionist123'),
            'status' => 'active',
            'shift' => 'evening'
        ]);

        // Create additional random staff using factories
        Admin::factory(2)->create();
        Doctor::factory(5)->create();
        Receptionist::factory(3)->create();

        // Create some sample patients
        Patient::factory(10)->create();

        // Create sample products for inventory
        Product::factory(20)->create();
        
        // Run additional seeders for stock management
        $this->call([
            StockSeeder::class,
            ReorderSeeder::class,
        ]);
        
        $this->command->info('Staff tables seeded successfully!');
        $this->command->info('Created users:');
        $this->command->info('- Admin: admin@clinic.com / admin123');
        $this->command->info('- Doctor: doctor@clinic.com / doctor123');
        $this->command->info('- Staff: staff@clinic.com / staff123');
        $this->command->info('- Patient: patient@clinic.com / patient123');
        $this->command->info('- System Admin: sysadmin@clinic.com / sysadmin123');
        $this->command->info('- Medical Admin: medadmin@clinic.com / medadmin123');
        $this->command->info('- Receptionists: alice.williams@clinic.com, robert.brown@clinic.com, lisa.davis@clinic.com / receptionist123');
        $this->command->info('- Stock alerts and reorder requests created for inventory management demo');
    }
}
