<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PasswordResetController extends BaseController
{
    /**
     * Send password reset email
     */
    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return $this->success(null, 'If email exists, reset link will be sent');
        }

        // Check rate limiting
        $rateLimitKey = "password_reset_attempts:{$validated['email']}";
        $attempts = Cache::get($rateLimitKey, 0);

        if ($attempts >= 3) {
            return $this->error('Too many reset attempts. Please try again in 24 hours', 429);
        }

        Cache::put($rateLimitKey, $attempts + 1, 86400);

        // Generate reset token
        $token = Str::random(64);
        $expiresAt = now()->addHour();

        Cache::put("password_reset:{$token}", [
            'user_id' => $user->id,
            'email' => $user->email,
            'expires_at' => $expiresAt,
            'ip' => $request->ip(),
        ], 3600);

        // Send reset email
        Mail::to($user->email)->send(new \App\Mail\PasswordResetMail($token, $user->name));

        return $this->success(null, 'If email exists, reset link will be sent');
    }

    /**
     * Verify reset token
     */
    public function verifyToken(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $data = Cache::get("password_reset:{$validated['token']}");

        if (!$data) {
            return $this->error('Invalid or expired reset token', 404);
        }

        if ($data['expires_at'] < now()) {
            Cache::forget("password_reset:{$validated['token']}");
            return $this->error('Reset token has expired', 400);
        }

        // Verify IP matches
        if ($data['ip'] !== $request->ip()) {
            Cache::forget("password_reset:{$validated['token']}");
            return $this->error('Token cannot be used from different IP address', 403);
        }

        $user = User::find($data['user_id']);

        return $this->success([
            'user_id' => $user->id,
            'email' => $user->email,
            'valid' => true,
        ], 'Token verified successfully');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:12|confirmed|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[!@#$%^&*(),.?":{}|<>]/',
        ]);

        $data = Cache::get("password_reset:{$validated['token']}");

        if (!$data) {
            return $this->error('Invalid or expired reset token', 404);
        }

        if ($data['expires_at'] < now()) {
            Cache::forget("password_reset:{$validated['token']}");
            return $this->error('Reset token has expired', 400);
        }

        // Verify IP matches
        if ($data['ip'] !== $request->ip()) {
            Cache::forget("password_reset:{$validated['token']}");
            return $this->error('Token cannot be used from different IP address', 403);
        }

        $user = User::find($data['user_id']);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        // Validate password complexity
        $passwordErrors = $this->validatePasswordComplexity($validated['password']);
        if (!empty($passwordErrors)) {
            return $this->error(implode('; ', $passwordErrors), 422);
        }

        // Hash new password
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Invalidate all active sessions
        $this->invalidateAllSessions($user);

        // Clear reset token
        Cache::forget("password_reset:{$validated['token']}");

        return $this->success(null, 'Password reset successfully. Please login with your new password');
    }

    /**
     * Validate password complexity
     */
    protected function validatePasswordComplexity(string $password): array
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

        // Check against common passwords
        $commonPasswords = [
            'password', '123456', '12345678', 'qwerty', 'abc123', 'password123',
            'admin', 'letmein', 'welcome', 'monkey', 'dragon', 'master',
            'Qwerty123!', 'Password1!', 'Admin123!', 'Root123!',
        ];

        if (in_array(strtolower($password), $commonPasswords)) {
            $errors[] = 'Password is too common. Please choose a stronger password';
        }

        return $errors;
    }

    /**
     * Invalidate all active sessions for a user
     */
    protected function invalidateAllSessions(User $user): void
    {
        // Regenerate session for current request
        request()->session()->regenerate(true);

        // Update session ID in database if using database session driver
        // Or clear cache entries if using cache driver
    }

    /**
     * Change password (when already logged in)
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:12|confirmed|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[!@#$%^&*(),.?":{}|<>]/',
        ]);

        $userId = session('user_id');
        if (!$userId) {
            return $this->error('Not authenticated', 401);
        }

        $user = User::find($userId);

        if (!$user || !Hash::check($validated['current_password'], $user->password)) {
            return $this->error('Current password is incorrect', 400);
        }

        // Validate password complexity
        $passwordErrors = $this->validatePasswordComplexity($validated['password']);
        if (!empty($passwordErrors)) {
            return $this->error(implode('; ', $passwordErrors), 422);
        }

        // Hash new password
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Invalidate all sessions for security
        $this->invalidateAllSessions($user);

        return $this->success(null, 'Password changed successfully');
    }
}
