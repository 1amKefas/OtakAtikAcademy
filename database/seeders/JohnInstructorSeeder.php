<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class JohnInstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete test user
        User::where('email', 'test.dob.1766134902@example.com')->delete();

        // Create John instructor
        User::firstOrCreate(
            ['email' => 'instructor@otakatik.com'],
            [
                'name' => 'John',
                'password' => bcrypt('12345678'),
                'is_instructor' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
