<?php

namespace App\Services;

use App\Models\ArchiveSubmission;
use App\Models\StageHistory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Single-student onboarding shared by the manual create flow and the CSV bulk import.
 *
 * Preserves the side-effects of SupervisorController::createStudent
 * (creates User + Student + initial StageHistory + initial ArchiveSubmission).
 */
class StudentOnboardingService
{
    /**
     * On-board a single student record.
     *
     * @param  array{
     *     university_id: int,
     *     supervisor_id?: int|null,
     *     full_name: string,
     *     lastname: string,
     *     email: string,
     *     matric_number: string,
     *     degree_level?: string,
     *     phone?: string|null,
     *     research_topic?: string|null,
     *     faculty?: string|null,
     *     department?: string|null,
     *     programme?: string|null,
     *     temporary_password?: string|null,
     *     stage_note?: string|null
     * }  $data
     * @return object{student: Student, user: User, temporary_password: string}
     */
    public function create(array $data): object
    {
        $universityId = (int) $data['university_id'];
        $temporaryPassword = (string) ($data['temporary_password'] ?? Str::random(48));

        return DB::transaction(function () use ($data, $universityId, $temporaryPassword) {
            $user = User::create([
                'university_id' => $universityId,
                'email' => $data['email'],
                'name' => $data['full_name'],
                'role' => 'student',
                'password' => Hash::make($temporaryPassword),
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'university_id' => $universityId,
                'supervisor_id' => $data['supervisor_id'] ?? null,
                'matric_number' => $data['matric_number'],
                'lastname' => $data['lastname'],
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'degree_level' => $data['degree_level'] ?? 'BSc',
                'phone' => $data['phone'] ?? null,
                'research_topic' => $data['research_topic'] ?? null,
                'faculty' => $data['faculty'] ?? null,
                'department' => $data['department'] ?? null,
                'programme' => $data['programme'] ?? null,
                'current_stage' => 1,
                'progress_percentage' => 0,
                'points_earned' => 0,
                'status' => 'active',
                'account_status' => 'active',
                'personal_drive_url' => null,
            ]);

            StageHistory::create([
                'university_id' => $universityId,
                'student_id' => $student->id,
                'stage_number' => 1,
                'stage_name' => config('research.stages')[0]['name'] ?? 'Topic / Subject / Interest Area',
                'action' => 'entered',
                'note' => $data['stage_note'] ?? 'Student onboarded by supervisor',
            ]);

            ArchiveSubmission::firstOrCreate(
                ['university_id' => $universityId, 'student_id' => $student->id],
                [
                    'degree_level' => $student->degree_level ?: 'BSc',
                    'project_type' => 'project',
                    'submission_status' => ArchiveSubmission::STATUS_DRAFT,
                    'visibility' => 'institution_only',
                ]
            );

            return (object) [
                'student' => $student,
                'user' => $user,
                'temporary_password' => $temporaryPassword,
            ];
        });
    }
}
