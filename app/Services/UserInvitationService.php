<?php

namespace App\Services;

use App\Mail\PortalEmail;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\University;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;

class UserInvitationService
{
    private const CACHE_PREFIX = 'user_invitation:';

    private const USER_CACHE_PREFIX = 'user_invitation_user:';

    private const INVITE_TTL_SECONDS = 604800;

    public function createInvitedUser(array $attributes): array
    {
        $assignedSupervisorId = $this->resolvePendingSupervisorId($attributes);

        return DB::transaction(function () use ($attributes, $assignedSupervisorId) {
            $user = User::create([
                'university_id' => $attributes['university_id'],
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'role' => $attributes['role'],
                'department' => $attributes['department'] ?? null,
                'phone' => $attributes['phone'] ?? null,
                'password' => Hash::make(Str::random(48)),
                'is_active' => false,
            ]);

            if (($attributes['role'] ?? null) === 'supervisor') {
                $placeholderPin = Str::random(20);
                $placeholderPassphrase = Str::random(40);

                Supervisor::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'university_id' => $user->university_id,
                        'title' => $attributes['supervisor_title'] ?? null,
                        'department' => $attributes['department'] ?? 'Pending supervisor setup',
                        'research_areas' => $attributes['research_areas'] ?? null,
                        'booking_url' => $attributes['booking_url'] ?? null,
                        'pin_code' => Hash::make($placeholderPin),
                        'passphrase' => Hash::make($placeholderPassphrase),
                        'is_active' => false,
                    ]
                );
            }

            return $this->issueInvitation($user->fresh('university'), [
                'supervisor_id' => $assignedSupervisorId,
            ]);
        });
    }

    public function resendInvitation(User $user): array
    {

        $pendingInvitation = $this->getInvitationForUser($user);
        $assignedSupervisorId = $pendingInvitation['supervisor_id']
            ?? $this->resolvePendingSupervisorId([
                'role' => $user->role,
                'university_id' => $user->university_id,
            ]);

        return $this->issueInvitation($user->fresh('university'), [
            'supervisor_id' => $assignedSupervisorId,
        ]);
    }

    public function findLeastLoadedSupervisorId(int $universityId): ?int
    {
        return Supervisor::query()
            ->where('university_id', $universityId)
            ->where('is_active', true)
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->withCount('students')
            ->orderBy('students_count')
            ->orderBy('id')
            ->value('id');
    }

    public function getInvitation(string $token): ?array
    {
        $invitation = Cache::get($this->cacheKey($token));

        if (! $invitation) {
            return null;
        }

        if (($invitation['expires_at'] ?? now()->subSecond()) < now()) {
            Cache::forget($this->cacheKey($token));

            return null;
        }

        $user = User::find($invitation['user_id'] ?? null);

        if (! $user || $user->email !== ($invitation['email'] ?? null)) {
            Cache::forget($this->cacheKey($token));

            return null;
        }

        return [
            'token' => $token,
            'user' => $user,
            'invitation' => $invitation,
        ];
    }

    public function completionRules(User $user): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
        ];

        // Check if university has structured departments
        $hasStructured = $this->hasStructuredDepartments($user->university_id);
        $allowedDepartments = $hasStructured ? $this->getAllowedDepartments($user->university_id) : [];

        $departmentRule = $hasStructured && $allowedDepartments
            ? 'required|string|max:255|in:'.implode(',', $allowedDepartments)
            : ($user->role === 'supervisor' ? 'required|string|max:255' : 'nullable|string|max:255');

        if (in_array($user->role, ['admin', 'super_admin'], true)) {
            return $rules + [
                'department' => $departmentRule,
                'password' => 'required|string|min:12|confirmed|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[!@#$%^&*(),.?":{}|<>]/',
            ];
        }

        if ($user->role === 'supervisor') {
            return $rules + [
                'department' => $departmentRule,
                'supervisor_title' => 'required|string|max:255',
                'research_areas' => 'nullable|string',
                'booking_url' => 'nullable|url|max:2048',
                'pin_code' => 'required|string|min:4|max:20',
                'passphrase' => 'required|string|min:8|max:255',
            ];
        }

        if ($user->role === 'student') {
            return $rules + [
                'lastname' => 'required|string|max:255',
                'matric_number' => 'required|string|max:255|unique:students,matric_number',
                'degree_level' => 'required|string|max:10',
                'research_topic' => 'nullable|string',
            ];
        }

        return $rules;
    }

    /**
     * Check if a university has structured departments enabled.
     */
    private function hasStructuredDepartments(int $universityId): bool
    {
        $university = University::find($universityId);
        if (! $university) {
            return false;
        }

        return config("universities.presets.{$university->code}.has_structured_departments", false);
    }

    /**
     * Get the list of allowed departments for a university.
     */
    private function getAllowedDepartments(int $universityId): array
    {
        $university = University::find($universityId);
        if (! $university) {
            return [];
        }

        $code = $university->code;
        if ($code !== 'LASU') {
            return [];
        }

        $config = config('lasu_departments', []);
        $all = [];
        foreach (['faculties', 'schools_and_directorates'] as $key) {
            if (! empty($config[$key])) {
                foreach ($config[$key] as $unit) {
                    $all = array_merge($all, array_values($unit['departments'] ?? []));
                }
            }
        }

        return array_values(array_unique($all));
    }

    public function completeInvitation(string $token, array $attributes): User
    {
        $payload = $this->getInvitation($token);

        if (! $payload) {
            throw new RuntimeException('Invitation link is invalid or has expired.');
        }

        return DB::transaction(function () use ($payload, $attributes) {
            /** @var User $user */
            $user = $payload['user'];
            $invitation = $payload['invitation'];

            $user->fill([
                'name' => $attributes['name'],
                'phone' => $attributes['phone'] ?? null,
                'department' => $attributes['department'] ?? ($user->role === 'supervisor' ? $attributes['department'] : $user->department),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            if (! empty($attributes['password'])) {
                $user->password = Hash::make($attributes['password']);
            }

            $user->save();

            if ($user->role === 'supervisor') {
                Supervisor::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'university_id' => $user->university_id,
                        'title' => $attributes['supervisor_title'],
                        'department' => $attributes['department'],
                        'research_areas' => $attributes['research_areas'] ?? null,
                        'booking_url' => $attributes['booking_url'] ?? null,
                        'pin_code' => Hash::make($attributes['pin_code']),
                        'passphrase' => Hash::make($attributes['passphrase']),
                        'is_active' => true,
                    ]
                );
            }

            if ($user->role === 'student') {
                $assignedSupervisorId = $invitation['supervisor_id'] ?? null;

                Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'university_id' => $user->university_id,
                        'supervisor_id' => $assignedSupervisorId,
                        'matric_number' => $attributes['matric_number'],
                        'lastname' => $attributes['lastname'],
                        'full_name' => $attributes['name'],
                        'email' => $user->email,
                        'degree_level' => $attributes['degree_level'],
                        'phone' => $attributes['phone'] ?? null,
                        'research_topic' => $attributes['research_topic'] ?? null,
                        'current_stage' => 1,
                        'progress_percentage' => 0,
                        'points_earned' => 0,
                        'status' => 'active',
                        'account_status' => 'active',
                    ]
                );
            }

            Cache::forget($this->cacheKey($payload['token']));
            Cache::forget($this->userCacheKey($user->id));

            return $user->fresh(['student', 'supervisor', 'university']);
        });
    }

    public function roleLabel(string $role): string
    {
        return Arr::get([
            'student' => 'Student',
            'supervisor' => 'Supervisor',
            'admin' => 'Admin',
            'super_admin' => 'Super Admin',
        ], $role, ucfirst(str_replace('_', ' ', $role)));
    }

    /**
     * Send a "credentials delivered" welcome email for a freshly created
     * student (e.g. from CSV bulk import). Students sign in using their
     * university code, matric number and surname, so the email conveys
     * those details rather than a (unused) password.
     *
     * Reuses the PortalEmail mailer so all user-facing emails route through
     * UserInvitationService. Failures are logged and swallowed so that a
     * single undeliverable email never rolls back a bulk import.
     */
    public function sendStudentCredentials(Student $student, ?string $loginUrl = null): bool
    {
        $student->loadMissing(['university', 'supervisor.user']);

        $universityCode = strtoupper((string) ($student->university?->code ?? config('universities.default', 'LASU')));
        $universityConfig = config('universities.presets.'.$universityCode)
            ?: config('universities.presets.'.config('universities.default', 'LASU'));

        try {
            Mail::to($student->email)->send(new PortalEmail('student-welcome', [
                'universityCode' => $universityCode,
                'universityName' => $student->university?->name ?? ($universityConfig['name'] ?? $universityCode),
                'name' => $student->full_name,
                'matric' => $student->matric_number,
                'roleLabel' => 'Student',
                'supervisorName' => optional($student->supervisor?->user)->name,
                'loginUrl' => $loginUrl ?? url('/login'),
                'loginHint' => sprintf('Sign in using your University code (%s), Matric number (%s) and surname.', $universityCode, $student->matric_number),
            ]));

            return true;
        } catch (\Throwable $e) {
            Log::warning('Failed to send student welcome credentials email', [
                'student_id' => $student->id,
                'matric' => $student->matric_number,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function cacheKey(string $token): string
    {
        return self::CACHE_PREFIX.$token;
    }

    private function issueInvitation(User $user, array $metadata = []): array
    {
        $expiresAt = now()->addDays(7);
        $token = Str::random(64);

        $previousToken = Cache::get($this->userCacheKey($user->id));
        if (is_string($previousToken) && $previousToken !== '') {
            Cache::forget($this->cacheKey($previousToken));
        }

        $payload = [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'expires_at' => $expiresAt,
            'supervisor_id' => $metadata['supervisor_id'] ?? null,
        ];

        Cache::put($this->cacheKey($token), $payload, self::INVITE_TTL_SECONDS);
        Cache::put($this->userCacheKey($user->id), $token, self::INVITE_TTL_SECONDS);

        Mail::to($user->email)->send(new PortalEmail('account-invite', [
            'name' => $user->name,
            'roleLabel' => $this->roleLabel($user->role),
            'completionUrl' => route('account-invitations.show', ['token' => $token]),
            'expiresAt' => $expiresAt->toDayDateTimeString(),
            'universityCode' => optional($user->university)->code,
            'universityName' => optional($user->university)->name,
        ]));

        return [
            'user' => $user,
            'token' => $token,
            'expires_at' => $expiresAt,
            'supervisor_id' => $payload['supervisor_id'],
        ];
    }

    private function getInvitationForUser(User $user): array
    {
        $token = Cache::get($this->userCacheKey($user->id));
        if (! is_string($token) || $token === '') {
            return [];
        }

        $payload = Cache::get($this->cacheKey($token));

        return is_array($payload) ? $payload : [];
    }

    private function resolvePendingSupervisorId(array $attributes): ?int
    {
        if (($attributes['role'] ?? null) !== 'student') {
            return null;
        }

        if (! empty($attributes['supervisor_id'])) {
            return (int) $attributes['supervisor_id'];
        }

        $universityId = isset($attributes['university_id']) ? (int) $attributes['university_id'] : 0;
        if ($universityId <= 0) {
            return null;
        }

        return $this->findLeastLoadedSupervisorId($universityId);
    }

    private function userCacheKey(int $userId): string
    {
        return self::USER_CACHE_PREFIX.$userId;
    }
}
