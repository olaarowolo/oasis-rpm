<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentCsvSeeder extends Seeder
{
    protected string $defaultCsvPath = __DIR__ . '/students_data.csv';

    protected string $defaultSupervisorEmail = 'olaarowolo.ng@gmail.com';

    public function run(?string $csvPath = null, ?string $supervisorEmail = null): void
    {
        $csvPath = $csvPath ?? $this->defaultCsvPath;
        $supervisorEmail = $supervisorEmail ?? $this->defaultSupervisorEmail;

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found: {$csvPath}");
            return;
        }

        $supervisorUser = User::where('email', $supervisorEmail)->first();
        if (!$supervisorUser) {
            $this->command->error("Supervisor user not found with email: {$supervisorEmail}");
            return;
        }

        $supervisor = Supervisor::where('user_id', $supervisorUser->id)->first();
        $supervisorId = $supervisor ? $supervisor->id : null;

        $file = fopen($csvPath, 'r');
        if (!$file) {
            $this->command->error("Unable to open CSV file: {$csvPath}");
            return;
        }

        $headers = fgetcsv($file);
        $headers = array_map(fn ($h) => trim($h), $headers);

        $created = 0;
        $updated = 0;

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < count($headers)) {
                continue;
            }

            $data = array_combine($headers, $row);

            $email = trim($data['email'] ?? '');
            $matricNumber = trim($data['matric_number'] ?? '');
            $fullName = trim($data['full_name'] ?? '');

            if (empty($email) || empty($matricNumber) || empty($fullName)) {
                $this->command->warn("Skipping row: empty email, matric_number, or full_name for {$fullName}");
                continue;
            }

            $lastname = trim($data['lastname'] ?? '') ?: $this->extractLastname($fullName);
            $phone = trim($data['phone'] ?? '') ?: null;
            $degreeLevel = trim($data['degree_level'] ?? '') ?: 'BSc';
            $researchTopic = trim($data['research_topic'] ?? '') ?: null;
            $currentStage = (int) ($data['current_stage'] ?? 1);
            $progressPercentage = (int) ($data['progress_percentage'] ?? 0);
            $rowSupervisorEmail = trim($data['supervisor_email'] ?? '') ?: $supervisorEmail;

            $targetSupervisor = $supervisor;
            if ($rowSupervisorEmail !== $supervisorEmail) {
                $targetUser = User::where('email', $rowSupervisorEmail)->first();
                $targetSupervisor = $targetUser
                    ? Supervisor::where('user_id', $targetUser->id)->first()
                    : null;
            }

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'university_id' => 1,
                    'password' => Hash::make('Student@2026'),
                    'name' => $fullName,
                    'role' => 'student',
                    'is_active' => 1,
                ]
            );

            $studentData = [
                'user_id' => $user->id,
                'university_id' => 1,
                'matric_number' => $matricNumber,
                'lastname' => $lastname,
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'degree_level' => $degreeLevel,
                'research_topic' => $researchTopic,
                'research_topic_approved_date' => $researchTopic
                    ? now()->subDays(random_int(15, 60))
                    : null,
                'current_stage' => $currentStage,
                'progress_percentage' => $progressPercentage,
                'points_earned' => $progressPercentage * 2,
                'status' => 'active',
                'account_status' => 'active',
                'supervisor_id' => $targetSupervisor ? $targetSupervisor->id : null,
            ];

            $existingStudent = Student::where('matric_number', $matricNumber)->first();
            if ($existingStudent) {
                $existingStudent->update($studentData);
                $updated++;
            } else {
                Student::create($studentData);
                $created++;
            }
        }

        fclose($file);
        $this->command->info("Import complete: {$created} students created, {$updated} students updated.");
    }

    protected function extractLastname(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));
        return end($parts);
    }
}
