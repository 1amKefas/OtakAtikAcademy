<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Buat admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@otakatik.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('12345678'),
                'is_admin' => true,
                'is_instructor' => false,
                'age_range' => '35-44',
                'education_level' => 'Bachelor',
                'location' => 'Jakarta',
                'phone' => '+62 812-3456-7890',
                'date_of_birth' => '1985-01-01',
                'bio' => 'System Administrator',
                'expertise' => 'System Management',
                'email_verified_at' => now(),
            ]
        );

        // Buat instructor user
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@otakatik.com'],
            [
                'name' => 'John Instructor',
                'password' => Hash::make('12345678'),
                'is_admin' => false,
                'is_instructor' => true,
                'age_range' => '35-44',
                'education_level' => 'Master',
                'location' => 'Bandung',
                'phone' => '+62 811-2233-4455',
                'date_of_birth' => '1988-05-15',
                'bio' => 'Experienced web development instructor.',
                'expertise' => 'Web Development, JavaScript, PHP',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Seeder berhasil!');
        $this->command->info('');
        $this->command->info('📧 Admin:');
        $this->command->info('   Email: admin@otakatik.com');
        $this->command->info('   Password: 12345678');
        $this->command->info('');
        $this->command->info('👨‍🏫 Instructor:');
        $this->command->info('   Email: instructor@otakatik.com');
        $this->command->info('   Password: 12345678');
        $this->command->info('');
        $this->command->info('---');
        $this->command->info('Siap untuk login! 🚀');
    }
}