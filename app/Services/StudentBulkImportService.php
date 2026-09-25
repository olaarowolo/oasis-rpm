<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

/**
 * Bulk-import students from a CSV file, assigning each one to a supervisor,
 * faculty, department and programme.
 *
 * Intended for supervisor-scoped uploads. Reuses StudentOnboardingService so
 * the per-student side-effects (User + Student + StageHistory + ArchiveSubmission)
 * are identical to the manual create flow.
 */
class StudentBulkImportService
{
    /** Required CSV columns (lower-cased, trimmed). */
    public const REQUIRED_COLUMNS = ['full_name', 'lastname', 'matric_number', 'email', 'degree_level'];

    /** Canonical degree-level values. */
    public const DEGREE_LEVELS = ['BSc', 'MSc', 'PhD'];

    /** Maximum uploaded file size in bytes (5 MB). */
    public const MAX_FILE_BYTES = 5 * 1024 * 1024;

    /**
     * Run the import.
     *
     * @param  array{university_id: int, university_code: string, default_supervisor_id: int|null}  $context
     * @return object{total: int, created: int, skipped: int, duplicates: int, errors: array<int, array{row: int, identifier: string|null, reason: string}>, created_matrics: array<int, string>}
     */
    public function import(array $context, $csvFile): object
    {
        $universityId = (int) $context['university_id'];
        $universityCode = strtoupper(trim((string) ($context['university_code'] ?? '')));
        $defaultSupervisorId = $context['default_supervisor_id'] ?? null;

        $structured = (bool) config("universities.presets.{$universityCode}.has_structured_departments");
        $facultyDepartmentMap = $structured ? $this->facultyDepartmentMap() : [];

        $results = (object) [
            'total' => 0,
            'created' => 0,
            'skipped' => 0,
            'duplicates' => 0,
            'errors' => [],
            'created_matrics' => [],
        ];

        $validatedFile = $this->resolveFile($csvFile);
        if (is_string($validatedFile)) {
            $results->errors[] = ['row' => 0, 'identifier' => null, 'reason' => $validatedFile];

            return $results;
        }

        $handle = fopen($validatedFile->getRealPath(), 'r');
        if ($handle === false) {
            $results->errors[] = ['row' => 0, 'identifier' => null, 'reason' => 'Could not open the uploaded CSV file.'];

            return $results;
        }

        $header = fgetcsv($handle, 0, ',', '"');
        if ($header === false) {
            fclose($handle);
            $results->errors[] = ['row' => 0, 'identifier' => null, 'reason' => 'The CSV file is empty.'];

            return $results;
        }

        $header = $this->normalizeHeader($header);
        $missing = array_diff(self::REQUIRED_COLUMNS, array_keys($header));
        if (! empty($missing)) {
            fclose($handle);
            $results->errors[] = [
                'row' => 0,
                'identifier' => null,
                'reason' => 'Missing required column(s): '.implode(', ', $missing).'. Header must include these names (case-insensitive).',
            ];

            return $results;
        }

        $seenMatric = [];
        $seenEmail = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle, 0, ',', '"')) !== false) {
            $rowNumber++;
            $results->total++;

            $record = array_combine(
                array_keys($header),
                array_pad(array_slice($row, 0, count($header)), count($header), '')
            );
            $record = $this->mapAndNormalise($record);

            $identifier = trim((string) ($record['matric_number'] ?? '')) ?: trim((string) ($record['email'] ?? ''));

            $error = $this->validateRow($record, $header, $facultyDepartmentMap, $structured);
            if ($error !== null) {
                $results->errors[] = ['row' => $rowNumber, 'identifier' => $identifier ?: null, 'reason' => $error];
                $results->skipped++;

                continue;
            }

            $matric = $record['matric_number'];
            $email = $record['email'];

            if (in_array($matric, $seenMatric, true)) {
                $results->errors[] = ['row' => $rowNumber, 'identifier' => $matric, 'reason' => 'Duplicate matric number within this file.'];
                $results->duplicates++;
                $results->skipped++;

                continue;
            }
            if (in_array($email, $seenEmail, true)) {
                $results->errors[] = ['row' => $rowNumber, 'identifier' => $email, 'reason' => 'Duplicate email within this file.'];
                $results->duplicates++;
                $results->skipped++;

                continue;
            }

            if (Student::where('university_id', $universityId)->where('matric_number', $matric)->exists()) {
                $results->errors[] = ['row' => $rowNumber, 'identifier' => $matric, 'reason' => 'Matric number already exists at this university.'];
                $results->duplicates++;
                $results->skipped++;

                continue;
            }
            if (User::where('university_id', $universityId)->where('email', $email)->exists()) {
                $results->errors[] = ['row' => $rowNumber, 'identifier' => $email, 'reason' => 'Email already exists at this university.'];
                $results->duplicates++;
                $results->skipped++;

                continue;
            }

            $supervisorId = $defaultSupervisorId;
            if (! empty($record['supervisor_email'])) {
                $resolved = $this->resolveSupervisor($universityId, $record['supervisor_email']);
                if ($resolved === null) {
                    $results->errors[] = [
                        'row' => $rowNumber,
                        'identifier' => $email,
                        'reason' => 'supervisor_email could not be resolved to an active supervisor at this university.',
                    ];
                    $results->skipped++;

                    continue;
                }
                $supervisorId = $resolved;
            }

            if ($supervisorId === null) {
                $results->errors[] = ['row' => $rowNumber, 'identifier' => $matric, 'reason' => 'No supervisor is assigned (no default supervisor and supervisor_email was not resolvable).'];
                $results->skipped++;

                continue;
            }

            $record['supervisor_email'] = null; // not stored

            try {
                $outcome = app(StudentOnboardingService::class)->create(array_merge($record, [
                    'university_id' => $universityId,
                    'supervisor_id' => $supervisorId,
                    'temporary_password' => null, // generated inside the service; not returned for bulk
                ]));
                $results->created++;
                $results->created_matrics[] = $matric;
                $seenMatric[] = $matric;
                $seenEmail[] = $email;

                // Deliver credentials via the portal mailer (UserInvitationService).
                // Failures are logged internally and never roll back the student.
                try {
                    app(UserInvitationService::class)
                        ->sendStudentCredentials($outcome->student);
                } catch (\Throwable $e) {
                    // No-op: StudentOnboardingService already committed the student.
                }
            } catch (\Throwable $e) {
                $results->errors[] = ['row' => $rowNumber, 'identifier' => $matric, 'reason' => 'Failed to create student: '.$e->getMessage()];
                $results->skipped++;
            }
        }

        fclose($handle);

        return $results;
    }

    /** Build [faculty_or_school_code => [department names]] from LASU config. */
    protected function facultyDepartmentMap(): array
    {
        $config = config('lasu_departments', []);
        $map = [];

        foreach (['faculties', 'schools_and_directorates'] as $key) {
            if (! empty($config[$key]) && is_array($config[$key])) {
                foreach ($config[$key] as $unit => $unitData) {
                    $departments = array_values($unitData['departments'] ?? []);
                    $map[$unit] = $departments;
                }
            }
        }

        return $map;
    }

    protected function resolveFile($csvFile): object|string
    {
        if (! $csvFile instanceof UploadedFile) {
            return 'No CSV file was provided.';
        }

        if (! $csvFile->isValid()) {
            return 'The uploaded file is not valid.';
        }

        if ($csvFile->getSize() > self::MAX_FILE_BYTES) {
            return 'Uploaded CSV file exceeds the 5 MB size limit.';
        }

        $extension = strtolower($csvFile->getClientOriginalExtension());
        if (! in_array($extension, ['csv', 'txt'], true)) {
            return 'Unsupported file type. Only .csv or .txt files are allowed.';
        }

        return $csvFile;
    }

    protected function normalizeHeader(array $header): array
    {
        $normalized = [];
        foreach ($header as $value) {
            $clean = strtolower(trim((string) $value));
            $clean = preg_replace('/^\xEF\xBB\xBF/', '', $clean);
            $normalized[$clean] = $clean;
        }

        return $normalized;
    }

    /**
     * Map/normalise a raw CSV record to canonical keys and values.
     */
    protected function mapAndNormalise(array $record): array
    {
        $map = [
            'surname' => 'lastname',
            'last_name' => 'lastname',
            'first name' => 'full_name',
            'firstname' => 'full_name',
            'matric' => 'matric_number',
            'matric no' => 'matric_number',
            'matric_number' => 'matric_number',
            'degree' => 'degree_level',
            'degree_level' => 'degree_level',
            'level' => 'degree_level',
            'mobile' => 'phone',
            'phone number' => 'phone',
            'supervisor_email' => 'supervisor_email',
            'supervisor' => 'supervisor_email',
            'faculty' => 'faculty',
            'school' => 'faculty',
            'department' => 'department',
            'programme' => 'programme',
            'program' => 'programme',
            'course' => 'programme',
            'research_topic' => 'research_topic',
            'topic' => 'research_topic',
        ];

        $normalised = [];
        foreach ($record as $key => $value) {
            $canonical = $map[$key] ?? $key;
            $normalised[$canonical] = $value;
        }

        // Degree-level canonicalisation (BSc/MSc/PhD).
        $level = isset($normalised['degree_level']) ? strtolower(trim((string) $normalised['degree_level'])) : '';
        $normalised['degree_level'] = match ($level) {
            'bsc', 'bachelors', 'bachelor', 'undergraduate' => 'BSc',
            'msc', 'masters', 'master', 'postgraduate' => 'MSc',
            'phd', 'doctorate' => 'PhD',
            '' => '',
            default => $normalised['degree_level'],
        };

        // Trim string columns.
        foreach (['full_name', 'lastname', 'matric_number', 'email', 'phone', 'supervisor_email', 'faculty', 'department', 'programme', 'research_topic'] as $field) {
            if (array_key_exists($field, $normalised)) {
                $normalised[$field] = trim((string) $normalised[$field]);
            }
        }

        return $normalised;
    }

    /** Validate a single row's field-level rules + structured-department consistency. */
    protected function validateRow(array $record, array $header, array $facultyDepartmentMap, bool $structured): ?string
    {
        $validator = Validator::make(
            $record,
            [
                'full_name' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'matric_number' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'degree_level' => 'required|in:BSc,MSc,PhD',
                'phone' => 'nullable|string|max:20',
                'supervisor_email' => 'nullable|email|max:255',
                'faculty' => 'nullable|string|max:100',
                'department' => 'nullable|string|max:150',
                'programme' => 'nullable|string|max:255',
                'research_topic' => 'nullable|string',
            ],
            [
                'degree_level.in' => 'degree_level must be one of: BSc, MSc, PhD.',
            ]
        );

        if ($validator->fails()) {
            return $this->firstMessage($validator);
        }

        if ($structured) {
            $faculty = $record['faculty'] ?? '';
            $department = $record['department'] ?? '';

            if ($faculty === '') {
                return 'faculty is required for this university (structured department list).';
            }
            if (! array_key_exists($faculty, $facultyDepartmentMap)) {
                return "faculty [{$faculty}] is not recognised for this university.";
            }
            if ($department !== '' && ! in_array($department, $facultyDepartmentMap[$faculty], true)) {
                return "department [{$department}] does not belong to faculty [{$faculty}].";
            }
            if ($department === '') {
                return 'department is required for this university (structured department list).';
            }
        }

        return null;
    }

    protected function firstMessage($validator): string
    {
        $messages = $validator->errors()->all();

        return $messages ? (string) $messages[0] : 'Validation failed.';
    }

    protected function resolveSupervisor(int $universityId, string $email): ?int
    {
        return Supervisor::query()
            ->where('university_id', $universityId)
            ->where('is_active', true)
            ->whereHas('user', function ($q) use ($email) {
                $q->where('email', $email)->where('is_active', true);
            })
            ->value('id');
    }
}
