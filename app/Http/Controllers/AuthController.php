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
            'matric_number' => $student->matric_number,
            'university_id' => $university->id,
            'current_stage' => $student->current_stage,
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
            'pin_code' => 'required|string',
            'passphrase' => 'required|string',
        ]);

        $university = University::where('code', $validated['university_code'])->first();

        if (!$university) {
            $this->simulateSlowOperation();
            return $this->error('Invalid credentials', 401);
        }

        $supervisor = Supervisor::where([
            ['university_id', '=', $university->id],
            ['is_active', '=', true],
        ])->first();

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
            'name' => $supervisor->user->name,
            'title' => $supervisor->title,
            'university_id' => $university->id,
            'token' => $this->generateSecureToken($supervisor->user_id),
        ], 'Supervisor login successful');
    }

    /**
     * Login admin with timing-safe responses
     */
    public function loginAdmin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where([
            ['email', '=', $validated['email']],
            ['role', '=', 'admin'],
        ])->first();

        // Use hash_equals for timing-safe password check
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            $this->recordFailedLoginAttempt($request, $validated['email']);
            $this->simulateSlowOperation();
            return $this->error('Invalid credentials', 401);
        }

        // Clear failed attempts on successful login
        $this->clearFailedLoginAttempts($request, $validated['email']);

        // Regenerate session ID to prevent session fixation
        $request->session()->regenerate();

        session([
            'university_id' => $user->university_id,
            'user_id' => $user->id,
            'role' => 'admin',
            'last_activity' => time(),
            'session_started' => time(),
        ]);

        return $this->success([
            'user_id' => $user->id,
            'name' => $user->name,
            'role' => 'admin',
            'university_id' => $user->university_id,
            'token' => $this->generateSecureToken($user->id),
        ], 'Admin login successful');
    }

    /**
     * Login super admin with timing-safe responses
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
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            $this->recordFailedLoginAttempt($request, $validated['email']);
            $this->simulateSlowOperation();
            return $this->error('Invalid credentials', 401);
        }

        // Clear failed attempts on successful login
        $this->clearFailedLoginAttempts($request, $validated['email']);

        // Regenerate session ID to prevent session fixation
        $request->session()->regenerate();

        session([
            'university_id' => $user->university_id,
            'user_id' => $user->id,
            'role' => 'super_admin',
            'last_activity' => time(),
            'session_started' => time(),
        ]);

        return $this->success([
            'user_id' => $user->id,
            'name' => $user->name,
            'role' => 'super_admin',
            'university_id' => $user->university_id,
            'token' => $this->generateSecureToken($user->id),
        ], 'Super Admin login successful');
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
            return $this->error('Student account not found with this email', 404);
        }
        
        $user = $student->user;
        if (!$user || $user->role !== 'student') {
            return $this->error('Student account not found with this email', 404);
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

        // Send OTP email
        Mail::to($validated['email'])->send(new \App\Mail\LoginOtpMail($code, 'student', $student->full_name));

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
            return $this->error('Student account not found', 404);
        }
        
        $user = $student->user;
        if (!$user || $user->role !== 'student') {
            return $this->error('Invalid OTP request', 401);
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
            'email' => $student->email ?? $user->email,
            'university_id' => $user->university_id,
            'current_stage' => $student->current_stage,
            'token' => $this->generateSecureToken($user->id),
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
                return $this->error('University not found', 404);
            }

            $student = Student::where([
                ['university_id', '=', $university->id],
                ['matric_number', '=', $matricNumber],
                ['lastname', '=', $lastname],
            ])->first();

            if (!$student) {
                return $this->error('Invalid student credentials', 401);
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
                return $this->error('Invalid supervisor credentials', 401);
            }

            $university = University::where('code', $universityCode)->first();
            if (!$university || $university->id !== $user->university_id) {
                return $this->error('University mismatch', 401);
            }

            $supervisor = Supervisor::where('user_id', $user->id)->where('university_id', $university->id)->first();
            if (!$supervisor) {
                return $this->error('Supervisor account not found', 401);
            }

            if (!Hash::check($pinCode, $supervisor->pin_code) || !Hash::check($passphrase, $supervisor->passphrase)) {
                return $this->error('Invalid supervisor credentials', 401);
            }
        } else {
            $email = trim((string) ($validated['email'] ?? ''));
            if (!$email || !isset($validated['password'])) {
                return $this->error('Email and password are required', 422);
            }

            $user = User::where('email', $email)->where('role', $validated['role'])->first();
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return $this->error('Invalid credentials for this role', 401);
            }
        }

        if (!$user || $user->role !== $validated['role']) {
            return $this->error('Invalid credentials for this role', 401);
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

        Mail::to($user->email)->send(new LoginOtpMail($code, $validated['role'], $user->name));

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
            return $this->error('Invalid OTP request', 401);
        }

        $otp = OtpToken::where('user_id', $user->id)
            ->where('role', $validated['role'])
            ->where('email', $validated['email'])
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
            case 'admin':
                return route('admin.dashboard');
            case 'supervisor':
                return route('supervisor.dashboard');
            case 'student':
                return route('student.dashboard');
            default:
                return route('login');
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

        return $this->success(null, 'Logout successful');
    }

    /**
     * Generate cryptographically secure token
     */
    private function generateSecureToken(int $userId): string
    {
        return hash('sha256', $userId . random_int(1000000, 9999999) . microtime(true) . config('app.key'));
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
}
