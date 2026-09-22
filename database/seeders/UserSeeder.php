<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $seedUsers = [
            [
                'university_id' => 1,
                'email' => 'superadmin@afriscribe.org',
                'password' => 'SuperAdmin@2026',
                'name' => 'Platform Super Admin',
                'role' => 'super_admin',
            ],
            [
                'university_id' => 1,
                'email' => 'admin@afriscribe.org',
                'password' => 'Admin@2026',
                'name' => 'Dr. Olasunkanmi Arowolo',
                'role' => 'admin',
            ],
            [
                'university_id' => 1,
                'email' => 'olaarowolo.ng@gmail.com',
                'password' => 'Admin@2026',
                'name' => 'Dr. Olasunkanmi Arowolo',
                'role' => 'admin',
            ],
            [
                'university_id' => 1,
                'email' => 'supervisor@lasu.edu.ng',
                'password' => 'Supervisor@2026',
                'name' => 'Dr. Arowolo',
                'role' => 'supervisor',
            ],
            [
                'university_id' => 1,
                'email' => 'olaarowolo.uk@gmail.com',
                'password' => 'Supervisor@2026',
                'name' => 'Dr. Olasunkanmi Arowolo',
                'role' => 'supervisor',
            ],
            [
                'university_id' => 1,
                'email' => 'olasunkanmiarowolo@gmail.com',
                'password' => 'Student@2026',
                'name' => 'Olasunkanmi Arowolo',
                'role' => 'student',
            ],
            [
                'university_id' => 2,
                'email' => 'admin@ui.edu.ng',
                'password' => 'Admin@2026',
                'name' => 'Prof. Doe',
                'role' => 'admin',
            ],
            [
                'university_id' => 2,
                'email' => 'supervisor@ui.edu.ng',
                'password' => 'Supervisor@2026',
                'name' => 'Prof. Doe',
                'role' => 'supervisor',
            ],
        ];

        foreach ($seedUsers as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'university_id' => $user['university_id'],
                    'password' => Hash::make($user['password']),
                    'name' => $user['name'],
                    'role' => $user['role'],
                ]
            );
        }

        for ($i = 1; $i <= 5; $i++) {
            $email = "student$i@lasu.edu.ng";
            User::updateOrCreate(
                ['email' => $email],
                [
                    'university_id' => 1,
                    'password' => Hash::make('Student@2026'),
                    'name' => "Student $i LASU",
                    'role' => 'student',
                ]
            );
        }

        for ($i = 1; $i <= 5; $i++) {
            $email = "student$i@ui.edu.ng";
            User::updateOrCreate(
                ['email' => $email],
                [
                    'university_id' => 2,
                    'password' => Hash::make('Student@2026'),
                    'name' => "Student $i UI",
                    'role' => 'student',
                ]
            );
        }
    }
}
