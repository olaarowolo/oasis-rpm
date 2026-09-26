<?php

namespace Tests;

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createDefenseReadinessContext(array $overrides = []): array
    {
        $university = University::create([
            'name' => 'Federal University of Technology',
            'code' => 'FUT',
            'email' => 'info@fut.edu',
            'department' => 'School of Science and Technology',
            'phone' => '08012345678',
        ]);

        $supervisorUser = User::create([
            'university_id' => $university->id,
            'email' => 'supervisor@fut.edu',
            'password' => bcrypt('Supervisor@2026'),
            'name' => 'Dr. Ada Supervisor',
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        $supervisor = Supervisor::create([
            'user_id' => $supervisorUser->id,
            'university_id' => $university->id,
            'title' => 'Dr.',
            'department' => 'Computer Science',
            'pin_code' => bcrypt('1234'),
            'passphrase' => bcrypt('fut-supervisor-pass'),
            'is_active' => true,
        ]);

        $studentUser = User::create([
            'university_id' => $university->id,
            'email' => 'student@fut.edu',
            'password' => bcrypt('Student@2026'),
            'name' => 'Ada Okafor',
            'role' => 'student',
            'is_active' => true,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'university_id' => $university->id,
            'supervisor_id' => $supervisor->id,
            'matric_number' => 'FUT-001',
            'lastname' => 'Okafor',
            'full_name' => 'Ada Okafor',
            'email' => 'student@fut.edu',
            'degree_level' => 'BSc',
            'current_stage' => 3,
            'progress_percentage' => 0,
            'status' => 'active',
            'account_status' => 'active',
        ]);

        return array_merge([
            'university' => $university,
            'supervisorUser' => $supervisorUser,
            'supervisor' => $supervisor,
            'studentUser' => $studentUser,
            'student' => $student,
        ], $overrides);
    }

    protected function defenseStudentSession(Student $student, $user = null): array
    {
        return [
            'user_id' => $user ? $user->id : $student->user_id,
            'role' => 'student',
            'university_id' => $student->university_id,
            'student_id' => $student->id,
            'last_activity' => time(),
            'session_started' => time(),
        ];
    }

    protected function defenseSupervisorSession(Supervisor $supervisor, $user = null): array
    {
        return [
            'user_id' => $user ? $user->id : $supervisor->user_id,
            'role' => 'supervisor',
            'university_id' => $supervisor->university_id,
            'supervisor_id' => $supervisor->id,
            'last_activity' => time(),
            'session_started' => time(),
        ];
    }
}
