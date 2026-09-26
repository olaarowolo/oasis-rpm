<?php

namespace App\Http\Controllers;

use App\Mail\LoginOtpMail;
use App\Models\OtpToken;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuthController extends BaseController
{
    protected $authGuard;

    private const ADMIN_MFA_TTL = 300;

    public function __construct()
    {
        parent::__construct();
        // AuthGuard is a middleware, not a controller dependency
        // It's applied via middleware groups in routes
    }

    /**
     * Login student with timing-safe responses to prevent enumeration
     */
    public function loginStudent(Request $request)
    {
        $validated = $request->validate([
            'university_code' => 'required|string',
            'matric_number' => 'required|string',
            'lastname' => 'required|string',
        ]);

        // Use timing-safe comparison to prevent enumeration
        $university = University::where('code', $validated['university_code'])->first();
        
        // Always perform slow operations even for invalid university to prevent timing attacks
        if (!$university) {
            // Simulate the work of checking student credentials to mask timing difference
            $this->simulateSlowOperation();
            return $this->error('Invalid credentials', 401);
        }

        $student = Student::where([
            ['university_id', '=', $university->id],
            ['matric_number', '=', $validated['matric_number']],
            ['lastname', '=', $validated['lastname']],
        ])->whereIn('status', ['active', 'graduated'])
            ->first();

        if (!$student) {
            // Simulate slow operation to prevent enumeration attacks
            $this->simulateSlowOperation();
            return $this->error('Invalid credentials', 401);
        }

        // Regenerate session ID to prevent session fixation
        $request->session()->regenerate();

        session([
            'university_id' => $university->id,
            'user_id' => $student->user_id,
            'student_id' => $student->id,
            'role' => 'student',
            'matric_number' => $student->matric_number,
            'last_activity' => time(),
            'session_started' => time(),
        ]);

        return $this->success([
            'user_id' => $student->user_id,
            'student_id' => $student->id,
            'name' => $student->full_name,
            'role' => 'student',
            'matric_number' => $student->matric_number,
            'university_id' => $university->id,
            'current_stage' => $student->current_stage,
            'dashboard_url' => $this->getDashboardUrl('student'),
            'token' => $this->generateSecureToken($student->user_id),
        ], 'Student login successful');
    }

    /**
     * Login supervisor with timing-safe responses
     */
    public function loginSupervisor(Request $request)
    {
        $validated = $request->validate([
            'university_code' => 'required|string',
            'email' => 'sometimes|email',
            'pin_code' => 'required|string',
            'passphrase' => 'required|string',
        ]);

        $university = University::where('code', $validated['university_code'])->first();

        if (!$university) {
            $this->simulateSlowOperation();
            return $this->error('Invalid credentials', 401);
        }

        // If email is provided, find the specific supervisor by email
        if (!empty($validated['email'])) {
            $supervisorUser = User::where('email', $validated['email'])
                ->where('role', 'supervisor')
                ->first();

            if (!$supervisorUser) {
                $this->simulateSlowOperation();
                return $this->error('Invalid credentials', 401);
            }

            $supervisor = Supervisor::where('user_id', $supervisorUser->id)
                ->where('university_id', $university->id)
                ->where('is_active', true)
                ->first();
        } else {
            // Fallback: find first active supervisor at university (legacy behavior)
            $supervisor = Supervisor::where([
                ['university_id', '=', $university->id],
                ['is_active', '=', true],
            ])->first();
        }

        if (!$supervisor) {
            $this->simulateSlowOperation();
            return $this->error('Invalid credentials', 401);
        }

        if (!Hash::check($validated['pin_code'], $supervisor->pin_code)) {
            $this->recordFailedLoginAttempt($request, $validated['university_code']);
            return $this->error('Invalid credentials', 401);
        }

        if (!Hash::check($validated['passphrase'], $supervisor->passphrase)) {
            $this->recordFailedLoginAttempt($request, $validated['university_code']);
            return $this->error('Invalid credentials', 401);
        }

        // Clear failed attempts on successful login
        $this->clearFailedLoginAttempts($request, $validated['university_code']);

        // Regenerate session ID to prevent session fixation
        $request->session()->regenerate();

        session([
            'university_id' => $university->id,
            'user_id' => $supervisor->user_id,
            'supervisor_id' => $supervisor->id,
            'role' => 'supervisor',
            'last_activity' => time(),
            'session_started' => time(),
        ]);

        return $this->success([
            'user_id' => $supervisor->user_id,
            'supervisor_id' => $supervisor->id,
            'email' => $supervisor->user->email,
            'name' => $supervisor->user->name,
            'title' => $supervisor->title,
            'university_id' => $university->id,
            'dashboard_url' => $this->getDashboardUrl('supervisor'),
            'token' => $this->generateSecureToken($supervisor->user_id),
        ], 'Supervisor login successful');
    }

    /**
     * Login super admin with timing-safe responses (platform-level, no university)
     */
    public function loginSuperAdmin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where([
            ['email', '=', $validated['email']],
            ['role', '=', 'super_admin'],
        ])->first();

        // Use hash_equals for timing-safe password check
        if (!$user || !$user->is_active || !Hash::check($validated['password'], $user->password)) {
            $this->recordFailedLoginAttempt($request, $validated['email']);
            $this->simulateSlowOperation();
            return $this->error('Invalid credentials', 401);
        }

        // Clear failed attempts on successful login
        $this->clearFailedLoginAttempts($request, $validated['email']);

        // Regenerate session ID to prevent session fixation
        $request->session()->regenerate();

        session([
            'user_id' => $user->id,
            'role' => 'super_admin',
            'last_activity' => time(),
            'session_started' => time(),
        ]);

        return $this->success([
            'user_id' => $user->id,
            'name' => $user->name,
            'role' => 'super_admin',
            'university_id' => null,
            'dashboard_url' => $this->getDashboardUrl('super_admin'),
            'token' => $this->generateSecureToken($user->id),
        ], 'Super Admin login successful');
    }

    /**
     * Send OTP to super admin email (platform-level, no university)
     */
    public function sendSuperAdminOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $genericMessage = 'If an account matches this email, a verification code will be sent';

        $user = User::where('email', $validated['email'])
            ->where('role', 'super_admin')
            ->first();

        if (!$user) {
            $this->logOtpLookupMiss('super_admin_precheck', 'user_not_found', [
                'email' => $validated['email'],
            ]);
            $this->simulateSlowOperation();
            return $this->success(null, $genericMessage);
        }

        if (! $this->isLoginEligible($user)) {
            $this->logOtpLookupMiss('super_admin_precheck', 'account_not_eligible', [
                'user_id' => $user->id,
                'user_is_active' => (bool) $user->is_active,
            ]);
            $this->simulateSlowOperation();
            return $this->success(null, $genericMessage);
        }

        // Generate a cryptographically secure OTP
        $code = $this->generateSecureOtp();
        $expiresAt = now()->addMinutes(5);

        // Remove any existing verification OTPs for this user/role
        OtpToken::where('user_id', $user->id)->where('role', 'super_admin')->delete();

        $otp = OtpToken::create([
            'user_id' => $user->id,
            'role' => 'super_admin',
            'email' => $validated['email'],
            'code' => $code,
            'expires_at' => $expiresAt,
        ]);

        try {
            $this->sendOtpMail($validated['email'], $code, $user->role, $user->name, $user->id);
        } catch (\Throwable $exception) {
            return $this->error('Unable to send verification code right now. Please try again later.', 500);
        }

        return $this->success([
            'email' => $validated['email'],
            'expires_at' => $otp->expires_at->toISOString(),
        ], 'Verification code sent to your email');
    }

    /**
     * Verify the super admin email OTP. On success the credentials step is revealed.
     * Does NOT log the user in.
     */
    public function verifySuperAdminOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', $validated['email'])
            ->where('role', 'super_admin')
            ->first();

        if (!$user) {
            $this->simulateSlowOperation();
            return $this->error('Verification code is invalid or has expired', 401);
        }

        $otp = OtpToken::where('user_id', $user->id)
            ->where('role', 'super_admin')
            ->whereIn('email', [$validated['email'], $user->email])
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otp || $otp->expires_at->isPast()) {
            return $this->error('Verification code has expired or is invalid', 401);
        }

        // Timing-safe comparison
        if (!hash_equals((string) $otp->code, (string) $validated['otp'])) {
            return $this->error('Invalid verification code', 401);
        }

        $otp->used_at = now();
        $otp->save();

        return $this->success([
            'email' => $validated['email'],
            'verified' => true,
            'is_super_admin' => true,
        ], 'OTP verified successfully');
    }

    /**
     * Login admin with timing-safe responses (NEW FLOW: university selection after OTP)
     */
    public function loginAdmin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'university_code' => 'required|string',
        ]);

        $university = University::where('code', strtoupper($validated['university_code']))->first();
        if (!$university) {
            $this->simulateSlowOperation();
            return $this->error('Invalid university', 401);
        }

        $user = User::where('email', $validated['email'])
            ->where('role', 'admin')
            ->where('university_id', $university->id)
            ->first();

        // Use hash_equals for timing-safe password check
        if (!$user || !$user->is_active || !Hash::check($validated['password'], $user->password)) {
            $this->recordFailedLoginAttempt($request, $validated['email']);
            $this->simulateSlowOperation();
            return $this->error('Invalid credentials', 401);
        }

        // Clear failed attempts on successful login
        $this->clearFailedLoginAttempts($request, $validated['email']);

        // Regenerate session ID to prevent session fixation
        $request->session()->regenerate();

        session([
            'university_id' => $university->id,
            'user_id' => $user->id,
            'role' => 'admin',
            'last_activity' => time(),
            'session_started' => time(),
        ]);

        return $this->success([
            'requires_mfa' => true,
            'challenge_id' => $this->createAdminMfaChallenge($request, $user),
            'user_id' => $user->id,
            'name' => $user->name,
            'role' => 'admin',
            'university_id' => $university->id,
            'dashboard_url' => $this->getDashboardUrl('admin'),
            'mfa_expires_in' => self::ADMIN_MFA_TTL,
        ], 'Admin credentials verified. MFA required');
    }

    /**
     * Verify admin MFA challenge and finalize login.
     */
    public function verifyAdminMfa(Request $request)
    {
        $validated = $request->validate([
            'challenge_id' => 'required|string',
            'code' => 'required|string|max:10',
        ]);

        $cacheKey = $this->getAdminMfaCacheKey($validated['challenge_id']);
        $challenge = Cache::get($cacheKey);

        if (!$challenge) {
            return $this->error('Invalid or expired verification code', 400);
        }

        if (($challenge['expires_at'] ?? now()->subMinute()) < now()) {
            Cache::forget($cacheKey);
            return $this->error('Verification code expired', 400);
        }

        if (($challenge['ip'] ?? null) !== $request->ip()) {
            return $this->error('Verification code cannot be used from a different IP address', 403);
        }

        if (($challenge['user_agent'] ?? null) !== $request->userAgent()) {
            return $this->error('Verification code cannot be used from a different device', 403);
        }

        if (!hash_equals((string) ($challenge['code'] ?? ''), trim($validated['code']))) {
            return $this->error('Invalid verification code', 401);
        }

        $user = User::find($challenge['user_id'] ?? null);
        if (!$user) {
            Cache::forget($cacheKey);
            return $this->error('User not found', 404);
        }

        Cache::forget($cacheKey);

        $request->session()->regenerate();
        session([
            'university_id' => $user->university_id,
            'user_id' => $user->id,
            'role' => $user->role,
            'last_activity' => time(),
            'session_started' => time(),
            'mfa_verified' => true,
            'mfa_verified_at' => now()->timestamp,
        ]);

        return $this->success([
            'user_id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'university_id' => $user->university_id,
            'dashboard_url' => $this->getDashboardUrl($user->role),
            'token' => $this->generateSecureToken($user->id),
        ], 'MFA verified successfully');
    }

    /**
     * Send OTP to student email
     */
    public function sendStudentOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        // Find student by email in either Student.email or User.email
        $student = Student::where('email', $validated['email'])->first();

        if (!$student) {
            // Try to find by User email instead
            $user = User::where('email', $validated['email'])->first();
            if ($user && $user->role === 'student') {
                $student = Student::where('user_id', $user->id)->first();
            }
        }

        if (!$student) {
            $this->logOtpLookupMiss('student_precheck', 'student_not_found', [
                'email' => $validated['email'],
            ]);
            return $this->success(null, 'If an account matches this email, a verification code will be sent');
        }

        $user = $student->user;
        if (!$user || $user->role !== 'student') {
            $this->logOtpLookupMiss('student_precheck', 'student_user_missing_or_role_mismatch', [
                'email' => $validated['email'],
                'student_id' => $student->id,
                'user_id' => $student->user_id,
            ]);
            return $this->success(null, 'If an account matches this email, a verification code will be sent');
        }

        if (! $this->isLoginEligible($user) || ! in_array($student->status, ['active', 'graduated'], true)) {
            $this->logOtpLookupMiss('student_precheck', 'account_not_eligible', [
                'student_id' => $student->id,
                'user_id' => $user->id,
                'user_is_active' => (bool) $user->is_active,
                'student_status' => $student->status,
            ]);
            $this->simulateSlowOperation();
            return $this->success(null, 'If an account matches this email, a verification code will be sent');
        }

        // Generate cryptographically secure OTP
        $code = $this->generateSecureOtp();
        $expiresAt = now()->addMinutes(5);

        // Delete any existing OTPs for this student
        OtpToken::where('user_id', $user->id)->where('role', 'student')->delete();

        // Create new OTP
        $otp = OtpToken::create([
            'user_id' => $user->id,
            'role' => 'student',
            'email' => $validated['email'],
            'code' => $code,
            'expires_at' => $expiresAt,
        ]);

        try {
            $this->sendOtpMail($validated['email'], $code, 'student', $student->full_name, $user->id);
        } catch (\Throwable $exception) {
            return $this->error('Unable to send verification code right now. Please try again later.', 500);
        }

        return $this->success([
            'user_id' => $user->id,
            'student_id' => $student->id,
            'email' => $validated['email'],
            'expires_at' => $otp->expires_at->toISOString(),
        ], 'Verification code sent to your email');
    }

    /**
     * Verify student OTP with timing-safe comparison
     */
    public function verifyStudentOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        // Find student by email in either Student.email or User.email
        $student = Student::where('email', $validated['email'])->first();

        if (!$student) {
            // Try to find by User email instead
            $user = User::where('email', $validated['email'])->first();
            if ($user && $user->role === 'student') {
                $student = Student::where('user_id', $user->id)->first();
            }
        }

        if (!$student) {
            return $this->error('Verification code is invalid or has expired', 401);
        }

        $user = $student->user;
        if (!$user || $user->role !== 'student') {
            return $this->error('Verification code is invalid or has expired', 401);
        }

        // Find the OTP (match against Student email or User email)
        $otp = OtpToken::where('user_id', $user->id)
            ->where('role', 'student')
            ->whereIn('email', [$validated['email'], $user->email])
            ->where('used_at', null)
            ->latest()
            ->first();

        if (!$otp || $otp->expires_at->isPast()) {
            return $this->error('Verification code has expired or is invalid', 401);
        }

        // Use hash_equals for timing-safe comparison
        if (!hash_equals((string) $otp->code, (string) $validated['otp'])) {
            return $this->error('Invalid verification code', 401);
        }

        $otp->used_at = now();
        $otp->save();

        // Regenerate session ID to prevent session fixation
        $request->session()->regenerate();

        session([
            'university_id' => $user->university_id,
            'user_id' => $user->id,
            'student_id' => $student->id,
            'role' => 'student',
            'matric_number' => $student->matric_number,
            'last_activity' => time(),
            'session_started' => time(),
        ]);

        return $this->success([
            'user_id' => $user->id,
            'student_id' => $student->id,
            'name' => $student->full_name,
            'role' => 'student',
            'email' => $student->email ?? $user->email,
            'university_id' => $user->university_id,
            'current_stage' => $student->current_stage,
            'dashboard_url' => $this->getDashboardUrl('student'),
            'token' => $this->generateSecureToken($user->id),
        ], 'OTP verified successfully');
    }

    /**
     * Student account recovery (knowledge-based): step 1.
     *
     * Identifies a student by university_code + matric_number + lastname and asks
     * for the email too. The email is reconciled against the stored email: a
     * university-issued email typically embeds the matric number, so if the
     * provided email embeds the matric and the stored email is missing/stale,
     * the stored email is corrected (after KBA succeeds) and the OTP is sent there.
     * On success, returns a single masked "confirmable" detail the student must
     * confirm before a login OTP is sent. Responses are identical whether or not
     * the student exists to prevent enumeration.
     */
    public function studentRecoveryStart(Request $request)
    {
        $validated = $request->validate([
            'university_code' => 'required|string',
            'matric_number' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
        ]);

        $genericMessage = 'If the provided details match an account, a verification code will be sent to the email on file';

        $university = University::where('code', strtoupper($validated['university_code']))->first();
        if (!$university) {
            $this->logOtpLookupMiss('student_recovery_start', 'university_not_found', [
                'university_code' => $validated['university_code'],
            ]);
            $this->simulateSlowOperation();
            return $this->success(null, $genericMessage);
        }

        $student = Student::where([
            ['university_id', '=', $university->id],
            ['matric_number', '=', $validated['matric_number']],
            ['lastname', '=', $validated['lastname']],
        ])->whereIn('status', ['active', 'graduated'])
          ->where('account_status', '!=', 'archived')
          ->first();

        if (!$student || !$student->user || $student->user->role !== 'student') {
            $this->logOtpLookupMiss('student_recovery_start', 'student_not_found_or_not_recoverable', [
                'matric_number' => $validated['matric_number'],
                'has_student' => (bool) $student,
            ]);
            $this->simulateSlowOperation();
            return $this->success(null, $genericMessage);
        }

        // Reconcile the email before doing any further work.
        $providedEmail = trim($validated['email'] ?? '');
        $targetEmail = $this->resolveRecoveryEmail($student, $providedEmail);

        if ($targetEmail === '') {
            $this->logOtpLookupMiss('student_recovery_start', 'no_usable_email', [
                'student_id' => $student->id,
                'provided_has_matric' => $this->emailContainsMatric($providedEmail, $student->matric_number),
            ]);
            $this->simulateSlowOperation();
            return $this->success(null, $genericMessage);
        }

        $lockKey = 'student_recovery_lock:' . $university->id . ':' . $validated['matric_number'];
        if (Cache::has($lockKey)) {
            return $this->error('Too many attempts. Please try again in 15 minutes.', 429);
        }

        $challenge = $this->getStudentRecoveryChallenge($student);
        if (!$challenge) {
            $this->simulateSlowOperation();
            return $this->success(null, $genericMessage);
        }

        $challengeId = Str::random(40);
        $cacheKey = 'student_recovery:' . $challengeId;
        Cache::put($cacheKey, [
            'user_id' => $student->user->id,
            'student_id' => $student->id,
            'university_id' => $university->id,
            'matric_number' => $student->matric_number,
            'email' => $targetEmail,
            'provided_email' => $providedEmail,
            'name' => $student->full_name ?? $student->user->name,
            'field' => $challenge['field'],
            'expected' => $challenge['expected'],
            'attempts' => 0,
            'ip' => $request->ip(),
        ], now()->addMinutes(10));

        return $this->success([
            'challenge_id' => $challengeId,
            'field' => $challenge['field'],
            'label' => $challenge['label'],
            'hint' => $challenge['hint'],
            'email_hint' => $this->maskEmail($targetEmail),
        ], 'Confirm a detail to receive a verification code');
    }

    /**
     * Student account recovery (knowledge-based): step 2.
     *
     * Verifies the student's confirmation of the challenge detail. On success, the
     * stored email is reconciled against the matric-based email supplied at start
     * (when the stored email is missing or stale), then a login OTP is dispatched
     * to the resolved email. The student then completes the existing student OTP
     * verification to be logged in.
     */
    public function studentRecoveryConfirm(Request $request)
    {
        $validated = $request->validate([
            'challenge_id' => 'required|string',
            'field' => 'required|string',
            'value' => 'required|string',
        ]);

        $genericFail = 'The provided detail does not match our records';

        $cacheKey = 'student_recovery:' . $validated['challenge_id'];
        $challenge = Cache::get($cacheKey);

        if (!$challenge) {
            return $this->error('This verification session has expired. Please try again.', 419);
        }

        if (($challenge['ip'] ?? null) !== $request->ip()) {
            Cache::forget($cacheKey);
            return $this->error('This verification session is invalid.', 403);
        }

        if (($validated['field'] ?? null) !== ($challenge['field'] ?? null)) {
            return $this->error($genericFail, 401);
        }

        $attempts = (int) ($challenge['attempts'] ?? 0) + 1;
        $challenge['attempts'] = $attempts;

        $match = hash_equals(
            $this->normalizeChallengeValue($challenge['field'], (string) ($challenge['expected'] ?? '')),
            $this->normalizeChallengeValue($challenge['field'], (string) $validated['value'])
        );

        if (!$match) {
            if ($attempts >= 5) {
                Cache::forget($cacheKey);
                $lockKey = 'student_recovery_lock:' . $challenge['university_id'] . ':' . $challenge['matric_number'];
                Cache::put($lockKey, true, now()->addMinutes(15));
                Log::warning('Student account recovery locked after repeated failures', [
                    'student_id' => $challenge['student_id'],
                    'field' => $challenge['field'],
                    'ip' => $request->ip(),
                ]);
                return $this->error($genericFail . '. Too many attempts; please try again in 15 minutes.', 429);
            }
            Cache::put($cacheKey, $challenge, now()->addMinutes(10));
            return $this->error($genericFail, 401);
        }

        Cache::forget($cacheKey);

        $user = User::find($challenge['user_id']);
        if (!$user) {
            return $this->error($genericFail, 401);
        }

        $student = Student::find($challenge['student_id']);
        if (!$student) {
            return $this->error($genericFail, 401);
        }

        $targetEmail = $this->resolveRecoveryEmail($student, (string) ($challenge['provided_email'] ?? ''));
        if ($targetEmail === '') {
            $targetEmail = (string) ($student->email ?? '');
        }

        // Reconcile a stale/missing stored email using the matric-based email the
        // student supplied at start. Only applied after KBA success so an
        // unverified attempt cannot mutate account data.
        $emailUpdated = false;
        if ($targetEmail !== '' && $targetEmail !== trim((string) $student->email)) {
            try {
                $this->reconcileStudentEmail($student, $user, $targetEmail);
                $emailUpdated = true;
            } catch (\Throwable $exception) {
                Log::warning('Student recovery email reconciliation failed', [
                    'student_id' => $student->id,
                    'user_id' => $user->id,
                    'email' => $targetEmail,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        $code = $this->generateSecureOtp();
        $expiresAt = now()->addMinutes(5);

        OtpToken::where('user_id', $user->id)->where('role', 'student')->delete();

        OtpToken::create([
            'user_id' => $user->id,
            'role' => 'student',
            'email' => $targetEmail,
            'code' => $code,
            'expires_at' => $expiresAt,
        ]);

        try {
            $this->sendOtpMail($targetEmail, $code, 'student', $challenge['name'], $user->id);
        } catch (\Throwable $exception) {
            return $this->error('Unable to send verification code right now. Please try again later.', 500);
        }

        Log::info('Student account recovery succeeded; login OTP dispatched', [
            'user_id' => $user->id,
            'student_id' => $student->id,
            'field' => $challenge['field'],
            'email_updated' => $emailUpdated,
            'email_hint' => $this->maskEmail($targetEmail),
            'ip' => $request->ip(),
        ]);

        return $this->success([
            'email' => $targetEmail,
            'email_hint' => $this->maskEmail($targetEmail),
        ], 'If your details match, a verification code has been sent to the email on file');
    }

    /**
     * Send an email-verification OTP to a supervisor before they enter credentials.
     * Mirrors the student pre-verification step. Does NOT log the user in.
     */
    public function sendSupervisorOtp(Request $request)
    {
        return $this->sendRoleVerificationOtp($request, 'supervisor');
    }

    /**
     * Verify the supervisor email OTP. On success the credentials step is revealed.
     * Does NOT log the user in.
     */
    public function verifySupervisorOtp(Request $request)
    {
        return $this->verifyRoleVerificationOtp($request, 'supervisor');
    }

    /**
     * Send an email-verification OTP to an admin/super_admin before they enter credentials.
     * Mirrors the student pre-verification step. Does NOT log the user in.
     */
    public function sendAdminOtp(Request $request)
    {
        return $this->sendRoleVerificationOtp($request, 'admin');
    }

    /**
     * Verify the admin/super_admin email OTP. On success the credentials step is revealed.
     * Does NOT log the user in.
     */
public function verifyAdminOtp(Request $request)
    {
        return $this->verifyRoleVerificationOtp($request, 'admin');
    }

    /**
     * Shared helper: send an email-verification OTP for a given login role.
     * On success the OTP is consumed but the user is NOT logged in; the
     * credential step (loginSupervisor / loginAdmin / loginSuperAdmin) still runs afterwards.
     */
    private function sendRoleVerificationOtp(Request $request, string $role)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $genericMessage = 'If an account matches this email, a verification code will be sent';

        // For "admin" the entered email may belong to an admin OR super_admin
        // (super admins use the same admin tab but skip university selection).
        $allowedRoles = $role === 'admin' ? ['admin', 'super_admin'] : [$role];

        $user = User::where('email', $validated['email'])
            ->whereIn('role', $allowedRoles)
            ->first();

        if (!$user) {
            $this->logOtpLookupMiss('role_precheck', 'user_not_found', [
                'role' => $role,
                'email' => $validated['email'],
            ]);
            $this->simulateSlowOperation();
            return $this->success(null, $genericMessage);
        }

        // A deactivated account must not be able to start a login. The generic
        // response is kept so the caller cannot probe which emails are active.
        if (! $this->isLoginEligible($user)) {
            $this->logOtpLookupMiss('role_precheck', 'account_not_eligible', [
                'role' => $role,
                'user_id' => $user->id,
                'user_is_active' => (bool) $user->is_active,
            ]);
            $this->simulateSlowOperation();
            return $this->success(null, $genericMessage);
        }

        // Generate a cryptographically secure OTP
        $code = $this->generateSecureOtp();
        $expiresAt = now()->addMinutes(5);

        // Remove any existing verification OTPs for this user/role
        OtpToken::where('user_id', $user->id)->where('role', $user->role)->delete();

        $otp = OtpToken::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'email' => $validated['email'],
            'code' => $code,
            'expires_at' => $expiresAt,
        ]);

        try {
            $this->sendOtpMail($validated['email'], $code, $user->role, $user->name, $user->id);
        } catch (\Throwable $exception) {
            return $this->error('Unable to send verification code right now. Please try again later.', 500);
        }

        return $this->success([
            'email' => $validated['email'],
            'expires_at' => $otp->expires_at->toISOString(),
        ], 'Verification code sent to your email');
    }

    /**
     * Shared helper: verify an email-verification OTP for a given login role.
     * On success the OTP is consumed but the user is NOT logged in; the
     * credential step (loginSupervisor / loginAdmin) still runs afterwards.
     */
    private function verifyRoleVerificationOtp(Request $request, string $role)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        // For "admin" the entered email may belong to an admin OR super_admin
        // (super admins use the same admin tab but skip university selection).
        $allowedRoles = $role === 'admin' ? ['admin', 'super_admin'] : [$role];

        $user = User::where('email', $validated['email'])
            ->whereIn('role', $allowedRoles)
            ->first();

        if (!$user) {
            $this->simulateSlowOperation();
            return $this->error('Verification code is invalid or has expired', 401);
        }

        $otp = OtpToken::where('user_id', $user->id)
            ->where('role', $user->role)
            ->whereIn('email', [$validated['email'], $user->email])
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otp || $otp->expires_at->isPast()) {
            return $this->error('Verification code has expired or is invalid', 401);
        }

        // Timing-safe comparison
        if (!hash_equals((string) $otp->code, (string) $validated['otp'])) {
            return $this->error('Invalid verification code', 401);
        }

        $otp->used_at = now();
        $otp->save();

        return $this->success([
            'email' => $validated['email'],
            'verified' => true,
            'is_super_admin' => $user->role === 'super_admin',
        ], 'OTP verified successfully');
    }

    /**
     * Send OTP to user based on role
     */
    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:student,supervisor,admin,super_admin',
            'email' => 'nullable|email',
            'password' => 'nullable|string',
            'university_code' => 'nullable|string',
            'matric_number' => 'nullable|string',
            'lastname' => 'nullable|string',
            'pin_code' => 'nullable|string',
            'passphrase' => 'nullable|string',
        ]);

        $user = null;

        if ($validated['role'] === 'student') {
            $universityCode = strtoupper((string) ($validated['university_code'] ?? ''));
            $matricNumber = trim((string) ($validated['matric_number'] ?? ''));
            $lastname = trim((string) ($validated['lastname'] ?? ''));

            if (!$universityCode || !$matricNumber || !$lastname) {
                return $this->error('University code, matric number, and surname are required', 422);
            }

            $university = University::where('code', $universityCode)->first();
            if (!$university) {
                $this->logOtpLookupMiss('generic_student_precheck', 'university_not_found', [
                    'university_code' => $universityCode,
                    'matric_number' => $matricNumber,
                ]);
                return $this->success(null, 'If the provided details match an account, a verification code will be sent');
            }

            $student = Student::where([
                ['university_id', '=', $university->id],
                ['matric_number', '=', $matricNumber],
                ['lastname', '=', $lastname],
            ])->first();

            if (!$student) {
                $this->logOtpLookupMiss('generic_student_precheck', 'student_not_found', [
                    'university_id' => $university->id,
                    'matric_number' => $matricNumber,
                    'lastname' => $lastname,
                ]);
                return $this->success(null, 'If the provided details match an account, a verification code will be sent');
            }

            $user = User::find($student->user_id);
        } elseif ($validated['role'] === 'supervisor') {
            $email = trim((string) ($validated['email'] ?? ''));
            $universityCode = strtoupper((string) ($validated['university_code'] ?? ''));
            $pinCode = (string) ($validated['pin_code'] ?? '');
            $passphrase = (string) ($validated['passphrase'] ?? '');

            if (!$email || !$universityCode || !$pinCode || !$passphrase) {
                return $this->error('Email, university code, PIN, and passphrase are required', 422);
            }

            $user = User::where('email', $email)->where('role', 'supervisor')->first();
            if (!$user) {
                $this->logOtpLookupMiss('generic_supervisor_precheck', 'user_not_found', [
                    'email' => $email,
                    'university_code' => $universityCode,
                ]);
                return $this->success(null, 'If the provided details match an account, a verification code will be sent');
            }

            $university = University::where('code', $universityCode)->first();
            if (!$university || $university->id !== $user->university_id) {
                $this->logOtpLookupMiss('generic_supervisor_precheck', 'university_mismatch', [
                    'email' => $email,
                    'university_code' => $universityCode,
                    'user_university_id' => $user->university_id,
                    'resolved_university_id' => $university?->id,
                ]);
                return $this->success(null, 'If the provided details match an account, a verification code will be sent');
            }

            $supervisor = Supervisor::where('user_id', $user->id)->where('university_id', $university->id)->first();
            if (!$supervisor) {
                $this->logOtpLookupMiss('generic_supervisor_precheck', 'supervisor_profile_missing', [
                    'email' => $email,
                    'user_id' => $user->id,
                    'university_id' => $university->id,
                ]);
                return $this->success(null, 'If the provided details match an account, a verification code will be sent');
            }

            if (!Hash::check($pinCode, $supervisor->pin_code) || !Hash::check($passphrase, $supervisor->passphrase)) {
                $this->logOtpLookupMiss('generic_supervisor_precheck', 'credential_mismatch', [
                    'email' => $email,
                    'user_id' => $user->id,
                ]);
                return $this->success(null, 'If the provided details match an account, a verification code will be sent');
            }
        } else {
            $email = trim((string) ($validated['email'] ?? ''));
            if (!$email || !isset($validated['password'])) {
                return $this->error('Email and password are required', 422);
            }

            $user = User::where('email', $email)->where('role', $validated['role'])->first();
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                $this->logOtpLookupMiss('generic_admin_precheck', 'user_not_found_or_password_mismatch', [
                    'email' => $email,
                    'role' => $validated['role'],
                    'user_found' => (bool) $user,
                ]);
                return $this->success(null, 'If the provided details match an account, a verification code will be sent');
            }
        }

        if (!$user || $user->role !== $validated['role']) {
            $this->logOtpLookupMiss('generic_precheck', 'role_mismatch_after_lookup', [
                'email' => $user?->email,
                'expected_role' => $validated['role'],
                'resolved_role' => $user?->role,
            ]);
            return $this->success(null, 'If the provided details match an account, a verification code will be sent');
        }

        if (!empty($validated['university_code'])) {
            $university = University::where('code', strtoupper($validated['university_code']))->first();
            if (!$university || $university->id !== $user->university_id) {
                return $this->error('University mismatch', 401);
            }
        }

        // Generate cryptographically secure OTP
        $code = $this->generateSecureOtp();
        $expiresAt = now()->addMinutes(5);

        OtpToken::where('user_id', $user->id)->where('role', $validated['role'])->delete();

        $otp = OtpToken::create([
            'user_id' => $user->id,
            'role' => $validated['role'],
            'email' => $user->email,
            'code' => $code,
            'expires_at' => $expiresAt,
        ]);

        try {
            $this->sendOtpMail($user->email, $code, $validated['role'], $user->name, $user->id);
        } catch (\Throwable $exception) {
            return $this->error('Unable to send verification code right now. Please try again later.', 500);
        }

        return $this->success([
            'user_id' => $user->id,
            'role' => $validated['role'],
            'email' => $user->email,
            'expires_at' => $otp->expires_at->toISOString(),
        ], 'Verification code sent to your email');
    }

    /**
     * Verify OTP with timing-safe comparison
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:student,supervisor,admin,super_admin',
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user || $user->role !== $validated['role']) {
            return $this->error('Verification code is invalid or has expired', 401);
        }

        $otp = OtpToken::where('user_id', $user->id)
            ->where('role', $validated['role'])
            ->where('email', $validated['email'])
            ->where('used_at', null)
            ->latest()
            ->first();

        if (!$otp || $otp->expires_at->isPast()) {
            return $this->error('Verification code is invalid or has expired', 401);
        }

        // Use hash_equals for timing-safe comparison
        if (!hash_equals((string) $otp->code, (string) $validated['otp'])) {
            return $this->error('Verification code is invalid or has expired', 401);
        }

        $otp->used_at = now();
        $otp->save();

        // Regenerate session ID to prevent session fixation
        $request->session()->regenerate();

        session([
            'university_id' => $user->university_id,
            'user_id' => $user->id,
            'role' => $validated['role'],
            'last_activity' => time(),
            'session_started' => time(),
        ]);

        return $this->success([
            'user_id' => $user->id,
            'name' => $user->name,
            'role' => $validated['role'],
            'email' => $user->email,
            'university_id' => $user->university_id,
            'dashboard_url' => $this->getDashboardUrl($validated['role']),
            'token' => $this->generateSecureToken($user->id),
        ], 'OTP verified successfully');
    }

    /**
     * Search universities for search-select (public endpoint)
     */
    public function searchUniversities(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        $universities = University::when($query, function($q) use ($query) {
            return $q->where(function($queryBuilder) use ($query) {
                $queryBuilder->where('code', 'LIKE', '%' . $query . '%')
                    ->orWhere('name', 'LIKE', '%' . $query . '%');
            });
        })->orderBy('code')->limit(20)->get();

        return $this->success([
            'universities' => $universities->map(function($uni) {
                return [
                    'id' => $uni->id,
                    'code' => $uni->code,
                    'name' => $uni->name,
                    'email' => $uni->email,
                    'logo_url' => $uni->logo_url,
                ];
            }),
        ], 'Universities retrieved successfully');
    }

    /**
     * Get current user info
     */
    public function me(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $role = $request->session()->get('role');
        $universityId = $request->session()->get('university_id');

        if (!$userId || !$role) {
            return $this->error('Not authenticated', 401);
        }

        $user = User::find($userId);
        if (!$user) {
            return $this->error('User not found', 404);
        }

        // Determine dashboard URL based on role
        $dashboardUrl = $this->getDashboardUrl($role);

        $data = [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $role,
            'university_id' => $universityId,
            'dashboard_url' => $dashboardUrl,
        ];

        if ($role === 'student' && $request->session()->get('student_id')) {
            $student = Student::find($request->session()->get('student_id'));
            $data['student'] = $student;
        } elseif ($role === 'supervisor' && $request->session()->get('supervisor_id')) {
            $supervisor = Supervisor::find($request->session()->get('supervisor_id'));
            $data['supervisor'] = $supervisor->makeHidden(['pin_code', 'passphrase']);
        }

        return $this->success($data, 'User fetched successfully');
    }

    /**
     * Get the dashboard URL based on user role
     */
    private function getDashboardUrl(string $role): string
    {
        switch ($role) {
            case 'super_admin':
                return route('super-admin.dashboard', [], false);
            case 'admin':
                return route('admin.dashboard', [], false);
            case 'supervisor':
                return route('supervisor.dashboard', [], false);
            case 'student':
                return route('student.dashboard', [], false);
            default:
                return route('login', [], false);
        }
    }

    /**
     * Logout user and invalidate token
     */
    public function logout(Request $request)
    {
        $userId = $request->session()->get('user_id');
        
        // Invalidate any active tokens for this user
        if ($userId) {
            // Clear session
            $request->session()->flush();
        } else {
            // Clear session anyway
            $request->session()->flush();
        }
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();

        if ($request->is('api/*') || $request->expectsJson() || $request->wantsJson()) {
            return $this->success(null, 'Logout successful');
        }

        return redirect()->route('login');
    }

    /**
     * Generate cryptographically secure token
     */
    private function generateSecureToken(int $userId): string
    {
        return hash('sha256', $userId . random_int(1000000, 9999999) . microtime(true) . config('app.key'));
    }

    /**
     * Create and store a pending MFA challenge for an admin login.
     */
    private function createAdminMfaChallenge(Request $request, User $user): string
    {
        $challengeId = Str::random(40);
        $code = (string) random_int(100000, 999999);

        Cache::put($this->getAdminMfaCacheKey($challengeId), [
            'user_id' => $user->id,
            'email' => $user->email,
            'code' => $code,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'expires_at' => now()->addSeconds(self::ADMIN_MFA_TTL),
        ], self::ADMIN_MFA_TTL);

        $this->sendOtpMail($user->email, $code, $user->role, $user->name, $user->id);

        return $challengeId;
    }

    /**
     * Build the cache key for admin MFA challenges.
     */
    private function getAdminMfaCacheKey(string $challengeId): string
    {
        return 'admin_mfa:' . $challengeId;
    }

    private function sendOtpMail(string $email, string $code, string $role, string $name, ?int $userId = null): void
    {
        try {
            Mail::to($email)->send(new LoginOtpMail($code, $role, $name));

            Log::info('OTP email dispatched successfully', [
                'email' => $email,
                'role' => $role,
                'user_id' => $userId,
                'mailer' => config('mail.default'),
            ]);
        } catch (\Throwable $exception) {
            Log::error('OTP email dispatch failed', [
                'email' => $email,
                'role' => $role,
                'user_id' => $userId,
                'mailer' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
                'mail_from' => config('mail.from.address'),
                'exception' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function logOtpLookupMiss(string $flow, string $reason, array $context = []): void
    {
        Log::warning('OTP request did not resolve to a sendable account', array_merge([
            'flow' => $flow,
            'reason' => $reason,
        ], $context));
    }

    /**
     * Whether an account may begin a login. Deactivated users are refused an
     * OTP up front rather than being allowed to reach the credential step and
     * fail there, which produced a confusing second error after the code was
     * already delivered.
     */
    private function isLoginEligible(User $user): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->role === 'supervisor') {
            $supervisor = Supervisor::where('user_id', $user->id)->first();

            if (! $supervisor || ! $supervisor->is_active) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generate cryptographically secure 6-digit OTP
     */
    private function generateSecureOtp(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Simulate slow operation to prevent timing attacks
     */
    private function simulateSlowOperation(): void
    {
        // Sleep a random amount of time between 100-300ms
        usleep(random_int(100000, 300000));
    }

    /**
     * Record failed login attempt for rate limiting
     */
    private function recordFailedLoginAttempt(Request $request, string $identifier): void
    {
        $ip = $request->ip();
        $rateLimitKey = "login_attempts:{$identifier}";
        
        $attempts = Cache::increment($rateLimitKey);
        Cache::put($rateLimitKey, $attempts, now()->addMinute());

        if ($attempts >= 5) {
            $lockoutKey = "login_lockout:{$identifier}";
            Cache::put($lockoutKey, true, now()->addMinutes(15));
            
            Log::warning('Account temporarily locked due to failed login attempts', [
                'identifier' => $identifier,
                'attempts' => $attempts,
                'ip' => $ip,
            ]);
        }
    }

    /**
     * Clear failed login attempts after successful login
     */
    private function clearFailedLoginAttempts(Request $request, string $identifier): void
    {
        $key = "login_attempts:{$identifier}";
        Cache::forget($key);

        $lockoutKey = "login_lockout:{$identifier}";
        Cache::forget($lockoutKey);
    }

    /**
     * Build a single random knowledge-based challenge from the student's populated
     * confirmable fields. Returns [field, label, hint, expected] or null.
     */
    private function getStudentRecoveryChallenge(Student $student): ?array
    {
        $fields = [];

        $this->addRecoveryField($fields, 'phone', 'Phone number', $student->phone, function ($v) {
            return $this->maskPhone($v);
        });

        $this->addRecoveryField($fields, 'full_name', 'Full name', $student->full_name, function ($v) {
            return $this->maskName($v);
        });

        $this->addRecoveryField($fields, 'degree_level', 'Degree level', $student->degree_level, function ($v) {
            return null;
        });

        $this->addRecoveryField($fields, 'faculty', 'Faculty', $student->faculty, function ($v) {
            return $this->maskShort($v);
        });

        $this->addRecoveryField($fields, 'department', 'Department', $student->department, function ($v) {
            return $this->maskShort($v);
        });

        $this->addRecoveryField($fields, 'programme', 'Programme', $student->programme, function ($v) {
            return $this->maskShort($v);
        });

        // Supervisor-derived fields are intentionally excluded from the challenge
        // set: students cannot reliably be expected to know their supervisor's
        // exact department or name (title suffixes, formatting), which would cause
        // false rejections. Only fields the student owns are offered.

        if (empty($fields)) {
            return null;
        }

        return collect($fields)->random();
    }

    private function addRecoveryField(array &$fields, string $field, string $label, ?string $value, callable $mask): void
    {
        if ($value === null || trim((string) $value) === '') {
            return;
        }

        $fields[] = [
            'field' => $field,
            'label' => $label,
            'hint' => $mask((string) $value),
            'expected' => (string) $value,
        ];
    }

    /**
     * Normalise a challenge value for case-insensitive, whitespace-insensitive comparison.
     * Phone numbers are reduced to digits so format differences don't cause failures.
     */
    private function normalizeChallengeValue(string $field, string $value): string
    {
        $value = trim($value);

        if ($field === 'phone') {
            return preg_replace('/\D/', '', $value);
        }

        return strtolower(preg_replace('/\s+/', ' ', $value));
    }

    private function maskEmail(string $email): string
    {
        $atPosition = strrpos($email, '@');
        if ($atPosition === false) {
            return $this->maskShort($email);
        }

        $local = substr($email, 0, $atPosition);
        $domain = substr($email, $atPosition + 1);

        $maskedLocal = strlen($local) > 2
            ? substr($local, 0, 2) . str_repeat('*', max(0, strlen($local) - 2))
            : str_repeat('*', strlen($local));

        return $maskedLocal . '@' . $domain;
    }

    private function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) <= 4) {
            return str_repeat('*', strlen($digits));
        }

        return str_repeat('*', strlen($digits) - 4) . substr($digits, -4);
    }

    private function maskName(string $name): string
    {
        $tokens = preg_split('/\s+/', trim($name)) ?: [];
        $masked = array_map(function ($token) {
            $length = strlen($token);
            if ($length <= 2) {
                return str_repeat('*', $length);
            }
            return substr($token, 0, 1) . str_repeat('*', $length - 2) . substr($token, -1);
        }, $tokens);

        return implode(' ', $masked);
    }

    private function maskShort(string $value): string
    {
        $length = strlen($value);
        if ($length <= 2) {
            return str_repeat('*', $length);
        }

        // First two characters only — do not disclose total length, which would
        // over-narrow the candidate set without helping the legitimate student.
        return substr($value, 0, 2) . '…';
    }

    /**
     * Resolve the email a recovery OTP should be sent to, optionally reconciling
     * a university-issued email that embeds the student's matric number.
     *
     * Many university templates issue emails of the form name+<matric>@domain.
     * If the email the student provides embeds their matric and the stored email
     * is missing or does not embed the matric, the provided email is treated as
     * authoritative. Otherwise the stored email is used.
     */
    private function resolveRecoveryEmail(Student $student, string $providedEmail): string
    {
        $provided = trim($providedEmail);
        $matric = (string) $student->matric_number;

        if ($provided !== '' && $matric !== '' && $this->emailContainsMatric($provided, $matric)) {
            $current = trim((string) ($student->email ?? ''));
            if ($current === '' || !$this->emailContainsMatric($current, $matric)) {
                return $provided;
            }
        }

        return trim((string) ($student->email ?? ''));
    }

    /**
     * True when the email's local part contains the matric as a whole token
     * (not as a substring of a longer digit run, e.g. 2021001 vs 20210010).
     */
    private function emailContainsMatric(string $email, string $matric): bool
    {
        if ($matric === '') {
            return false;
        }

        $local = strstr($email, '@', true) ?: '';
        if ($local === '') {
            $local = $email;
        }

        return preg_match('/(?<!\d)' . preg_quote($matric, '/') . '(?!\d)/', $local) === 1;
    }

    /**
     * Apply the reconciled email to both the Student and its linked User record.
     * The User update is guarded against uniqueness conflicts so a duplicate
     * email elsewhere will not corrupt another account.
     */
    private function reconcileStudentEmail(Student $student, User $user, string $email): void
    {
        if ($student->email !== $email) {
            $student->email = $email;
            $student->save();
        }

        if ($user->email !== $email && !User::where('email', $email)->where('id', '!=', $user->id)->exists()) {
            $user->email = $email;
            $user->save();
        }
    }
}
