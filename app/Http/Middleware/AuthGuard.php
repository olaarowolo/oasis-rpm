<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\OtpToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Auth\AuthenticationException;

class AuthGuard
{
    const RATE_LIMIT_WINDOW = 60; // seconds
    const MAX_ATTEMPTS = 5;
    const LOCKOUT_DURATION = 900; // 15 minutes
    const SESSION_TTL = 1800; // 30 minutes
    const MFA_TTL = 300; // 5 minutes for MFA code
    const MAX_TOKEN_LIFE = 43200; // 12 hours for Sanctum tokens

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Check if user is authenticated
        if (!$this->checkAuthentication($request)) {
            $this->logFailedAuthentication($request, 'Invalid session');
            return $this->unauthorized($request);
        }

        // Verify session hasn't expired
        if ($this->isSessionExpired($request)) {
            $this->logFailedAuthentication($request, 'Session expired');
            return $this->sessionExpired($request);
        }

        // Update session last activity
        $this->updateSessionActivity($request);

        // Regenerate session periodically to prevent fixation attacks
        $this->regenerateSessionIfNeeded($request);

        // Apply MFA if required
        if ($this->requiresMfa($request)) {
            if (!$this->verifyMfa($request)) {
                return $this->requireMfa($request);
            }
        }

        // Check if user is using valid Sanctum token
        if (!$this->validateToken($request)) {
            $this->logFailedAuthentication($request, 'Invalid or expired token');
            return $this->tokenExpired($request);
        }

        return $next($request);
    }

    /**
     * Check if user is authenticated
     */
    protected function checkAuthentication(Request $request): bool
    {
        $userId = $request->session()->get('user_id');
        $role = $request->session()->get('role');

        if (!$userId || !$role) {
            return false;
        }

        // Verify user exists
        $user = User::find($userId);
        if (!$user) {
            $request->session()->flush();
            return false;
        }

        // Ensure session role is still aligned with persisted user role.
        if ((string) $user->role !== (string) $role) {
            Log::warning('Session role mismatch detected', [
                'user_id' => $userId,
                'session_role' => $role,
                'database_role' => $user->role,
                'ip' => $request->ip(),
            ]);
            $request->session()->flush();
            return false;
        }

        // Check if user is active
        if ($role === 'student') {
            $student = \App\Models\Student::where('user_id', $userId)->first();
            if ($student && $student->status !== 'active' && $student->status !== 'graduated') {
                return false;
            }
        }

        if ($role === 'supervisor') {
            $supervisorId = $request->session()->get('supervisor_id');
            if (!$supervisorId) {
                return false;
            }
            $supervisor = \App\Models\Supervisor::where('id', $supervisorId)->where('user_id', $userId)->first();
            if (!$supervisor) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if session has expired
     */
    protected function isSessionExpired(Request $request): bool
    {
        $lastActivity = $request->session()->get('last_activity');
        $now = time();

        if (!$lastActivity) {
            return false;
        }

        if (($now - $lastActivity) > self::SESSION_TTL) {
            return true;
        }

        return false;
    }

    /**
     * Update session last activity timestamp
     */
    protected function updateSessionActivity(Request $request): void
    {
        $request->session()->put('last_activity', time());
        $request->session()->put('session_started', $request->session()->get('session_started') ?? time());
    }

    /**
     * Regenerate session ID periodically to prevent session fixation
     */
    protected function regenerateSessionIfNeeded(Request $request): void
    {
        $sessionStarted = $request->session()->get('session_started');
        $lastRegeneration = $request->session()->get('last_regeneration');
        
        // Regenerate every 30 minutes or if more than 1 hour since start
        if (!$lastRegeneration || (time() - $lastRegeneration) > 1800 || (time() - $sessionStarted) > 3600) {
            $request->session()->regenerate();
            $request->session()->put('last_regeneration', time());
        }
    }

    /**
     * Check if user requires MFA
     */
    protected function requiresMfa(Request $request): bool
    {
        $role = $request->session()->get('role');

        // Require MFA for admin roles once the login has been initiated and the session is established.
        return in_array($role, ['admin', 'super_admin'], true) && !$request->session()->has('mfa_verified');
    }

    /**
     * Verify MFA code
     */
    protected function verifyMfa(Request $request): bool
    {
        $mfaCode = $request->session()->get('mfa_verified');
        return $mfaCode === true;
    }

    /**
     * Require MFA response
     */
    protected function requireMfa(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Multi-factor authentication required',
            'requires_mfa' => true,
            'user_id' => $request->session()->get('user_id'),
            'role' => $request->session()->get('role'),
        ], 403);
    }

    /**
     * Handle unauthorized access with improved security
     */
    protected function unauthorized(Request $request)
    {
        // Clear any sensitive data
        $request->session()->forget(['user_id', 'role', 'university_id', 'student_id', 'supervisor_id']);

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Please authenticate.',
            'redirect' => route('login'),
        ], 401);
    }

    /**
     * Handle session expired with improved security
     */
    protected function sessionExpired(Request $request)
    {
        $request->session()->flush();

        return response()->json([
            'success' => false,
            'message' => 'Session expired. Please login again.',
            'redirect' => route('login'),
        ], 401);
    }

    /**
     * Handle token expired with improved security
     */
    protected function tokenExpired(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Token has expired. Please login again.',
            'redirect' => route('login'),
        ], 401);
    }

    /**
     * Validate Sanctum token
     */
    protected function validateToken(Request $request): bool
    {
        $token = $request->bearerToken();
        if (!$token) {
            return true; // Session-based auth is valid
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return false;
        }

        // Check if token has expired
        if ($accessToken->tokenable && $accessToken->tokenable->expires_at && $accessToken->tokenable->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Record failed login attempt with detailed logging
     */
    public function recordFailedAttempt(Request $request): void
    {
        $ip = $request->ip();
        $email = $request->input('email');
        $matric = $request->input('matric_number');
        $identifier = $email ?? $matric ?? $ip;

        // Rate limiting using Redis if available, fallback to cache
        $key = "login_attempts:{$identifier}";
        $attempts = Cache::increment($key);
        Cache::put($key, $attempts, self::RATE_LIMIT_WINDOW);

        Log::warning('Failed login attempt', [
            'identifier' => $identifier,
            'attempts' => $attempts,
            'ip' => $ip,
            'user_agent' => $request->userAgent(),
            'path' => $request->path(),
        ]);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $lockoutKey = "login_lockout:{$identifier}";
            Cache::put($lockoutKey, time(), self::LOCKOUT_DURATION);

            Log::alert('Account temporarily locked', [
                'identifier' => $identifier,
                'attempts' => $attempts,
                'ip' => $ip,
            ]);

            // Send lockout notification email
            $this->sendLockoutNotification($request, $identifier, $ip);
        }
    }

    /**
     * Send lockout notification email to user
     */
    protected function sendLockoutNotification(Request $request, string $identifier, string $ip): void
    {
        try {
            // Try to find user by email
            $user = User::where('email', $identifier)->first();
            
            if ($user) {
                $subject = 'Security Alert: Your Account Has Been Locked';
                $message = "Your account has been temporarily locked due to multiple failed login attempts.\n\n"
                    . "IP Address: {$ip}\n"
                    . "Time: " . now()->toDateTimeString() . "\n"
                    . "This lockout will expire in " . (self::LOCKOUT_DURATION / 60) . " minutes.\n\n"
                    . "If you did not attempt to log in, please contact your system administrator immediately.";

                // Send email notification
                Mail::raw($message, function ($mail) use ($user, $subject) {
                    $mail->to($user->email)
                        ->subject($subject);
                });

                Log::info('Lockout notification sent', ['user_id' => $user->id, 'email' => $user->email]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send lockout notification', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log failed authentication attempt
     */
    public function logFailedAuthentication(Request $request, string $reason): void
    {
        $ip = $request->ip();
        $email = $request->input('email');
        $matric = $request->input('matric_number');
        $identifier = $email ?? $matric ?? $ip;

        Log::warning('Authentication failed', [
            'reason' => $reason,
            'identifier' => $identifier,
            'ip' => $ip,
            'user_agent' => $request->userAgent(),
            'path' => $request->path(),
        ]);
    }

    /**
     * Check if user is locked out
     */
    public function isLockedOut(Request $request): bool
    {
        $ip = $request->ip();
        $email = $request->input('email');
        $matric = $request->input('matric_number');
        $identifier = $email ?? $matric ?? $ip;

        $lockoutKey = "login_lockout:{$identifier}";
        $lockedAt = Cache::get($lockoutKey);
        
        if (!$lockedAt) {
            return false;
        }

        $lockoutTime = (int) $lockedAt;
        $elapsed = time() - $lockoutTime;

        return $elapsed < self::LOCKOUT_DURATION;
    }

    /**
     * Get remaining lockout time
     */
    public function getLockoutTime(Request $request): int
    {
        $ip = $request->ip();
        $email = $request->input('email');
        $matric = $request->input('matric_number');
        $identifier = $email ?? $matric ?? $ip;

        $lockoutKey = "login_lockout:{$identifier}";
        $lockedAt = Cache::get($lockoutKey);
        
        if (!$lockedAt) {
            return 0;
        }

        $lockoutTime = (int) $lockedAt;
        $elapsed = time() - $lockoutTime;
        $remaining = self::LOCKOUT_DURATION - $elapsed;

        return max(0, $remaining);
    }

    /**
     * Clear failed attempts after successful login
     */
    public function clearFailedAttempts(Request $request): void
    {
        $ip = $request->ip();
        $email = $request->input('email');
        $matric = $request->input('matric_number');
        $identifier = $email ?? $matric ?? $ip;

        $key = "login_attempts:{$identifier}";
        Cache::forget($key);

        $lockoutKey = "login_lockout:{$identifier}";
        Cache::forget($lockoutKey);
    }

    /**
     * Generate and store MFA token
     */
    public function generateMfaToken(int $userId, string $role): string
    {
        $token = Str::random(32);
        $expiresAt = now()->addMinutes(self::MFA_TTL);

        Cache::put("mfa:{$userId}:{$role}", [
            'token' => $token,
            'expires_at' => $expiresAt,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], self::MFA_TTL * 60);

        return $token;
    }

    /**
     * Verify MFA token with additional security checks
     */
    public function verifyMfaToken(int $userId, string $role, string $token): bool
    {
        $data = Cache::get("mfa:{$userId}:{$role}");

        if (!$data || $data['token'] !== $token) {
            return false;
        }

        if ($data['expires_at'] < now()) {
            Cache::forget("mfa:{$userId}:{$role}");
            return false;
        }

        // Verify IP and user agent match
        if ($data['ip'] !== request()->ip()) {
            Log::warning('MFA token used from different IP', [
                'expected_ip' => $data['ip'],
                'actual_ip' => request()->ip(),
                'user_id' => $userId,
            ]);
            return false;
        }

        Cache::forget("mfa:{$userId}:{$role}");
        return true;
    }

    /**
     * Validate password complexity with enhanced requirements
     */
    public function validatePassword(string $password): array
    {
        $errors = [];

        if (strlen($password) < 12) {
            $errors[] = 'Password must be at least 12 characters long';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }

        // Check if password is in common passwords list
        $commonPasswords = [
            'password', '123456', '12345678', 'qwerty', 'abc123', 'password123',
            'admin', 'letmein', 'welcome', 'monkey', 'dragon', 'master',
            '123456789', '1234567890', 'password1', 'iloveyou',
        ];

        if (in_array(strtolower($password), $commonPasswords)) {
            $errors[] = 'Password is too common. Please choose a stronger password';
        }

        // Check if password matches email or part of name
        $userEmail = request()->input('email');
        if ($userEmail && strpos(strtolower($password), strtolower(explode('@', $userEmail)[0])) !== false) {
            $errors[] = 'Password should not contain your email username';
        }

        return $errors;
    }

    /**
     * Hash password with strong algorithm (Argon2id)
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3,
        ]);
    }

    /**
     * Verify password hash with timing-safe comparison
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Hash sensitive data
     */
    public function hashSensitiveData(string $data): string
    {
        return hash('sha256', $data . config('app.key'));
    }

    /**
     * Sanitize user input
     */
    public function sanitizeInput(string $input): string
    {
        $input = trim($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        $input = stripslashes($input);
        return $input;
    }
}
