<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ImportStudentsFromCsv extends Command
{
    protected $signature = 'import:students
                            {--csv= : Path to the CSV file (default: database/seeders/students_data.csv)}
                            {--supervisor= : Supervisor email to assign students to (default: olaarowolo.ng@gmail.com)}
                            {--force : Run without confirmation in production}';

    protected $description = 'Import students from a CSV file, creating or updating User and Student records';

    public function handle(): int
    {
        if ($this->getOutput()->getVerbosity() >= 1) {
            // Production safety check
            if (app()->environment('production') && !$this->option('force')) {
                if (!$this->confirm('You are running in production. Are you sure you want to import students?')) {
                    $this->info('Aborted.');
                    return self::FAILURE;
                }
            }
        }

        $csvPath = $this->option('csv') ?: database_path('seeders/students_data.csv');
        $supervisorEmail = trim($this->option('supervisor') ?: 'olaarowolo.ng@gmail.com');

        if (!file_exists($csvPath)) {
            $this->error("CSV file not found: {$csvPath}");
            return self::FAILURE;
        }

        $supervisorUser = User::where('email', $supervisorEmail)->first();
        if (!$supervisorUser) {
            $this->error("Supervisor user not found with email: {$supervisorEmail}");
            return self::FAILURE;
        }

        $supervisor = Supervisor::where('user_id', $supervisorUser->id)->first();
        $defaultSupervisorId = $supervisor ? $supervisor->id : null;

        $file = fopen($csvPath, 'r');
        if (!$file) {
            $this->error("Unable to open CSV file: {$csvPath}");
            return self::FAILURE;
        }

        $headers = fgetcsv($file);
        $headers = array_map(fn ($h) => trim($h), $headers);

        $this->info("Importing students from: {$csvPath}");
        $this->line("Supervisor: {$supervisorEmail} (ID: {$defaultSupervisorId})");
        $this->line(str_repeat('-', 60));

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $rowNumber = 1;

        while (($row = fgetcsv($file)) !== false) {
            $rowNumber++;

            if (count($row) < count($headers)) {
                $this->warn("Row {$rowNumber}: insufficient columns, skipping.");
                $skipped++;
                continue;
            }

            $data = array_combine($headers, $row);

            $email = trim($data['email'] ?? '');
            $matricNumber = trim($data['matric_number'] ?? '');
            $fullName = trim($data['full_name'] ?? '');

            if (empty($email) || empty($matricNumber) || empty($fullName)) {
                $this->warn("Row {$rowNumber}: missing email, matric_number, or full_name, skipping.");
                $skipped++;
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
            $targetSupervisorId = $defaultSupervisorId;

            if ($rowSupervisorEmail !== $supervisorEmail) {
                $targetUser = User::where('email', $rowSupervisorEmail)->first();
                if ($targetUser) {
                    $targetSup = Supervisor::where('user_id', $targetUser->id)->first();
                    $targetSupervisorId = $targetSup ? $targetSup->id : null;
                }
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
                'supervisor_id' => $targetSupervisorId,
            ];

            $existingStudent = Student::where('matric_number', $matricNumber)->first();
            if ($existingStudent) {
                $existingStudent->update($studentData);
                $updated++;
                $this->line("  Updated: {$fullName} ({$matricNumber})");
            } else {
                Student::create($studentData);
                $created++;
                $this->line("  Created: {$fullName} ({$matricNumber})");
            }
        }

        fclose($file);

        $this->line(str_repeat('-', 60));
        $this->info("Import complete:");
        $this->line("  Created:  {$created}");
        $this->line("  Updated:  {$updated}");
        $this->line("  Skipped:  {$skipped}");

        return self::SUCCESS;
    }

    protected function extractLastname(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));
        return end($parts);
    }
}
