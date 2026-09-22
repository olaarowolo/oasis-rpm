<?php

namespace Tests\Feature;

use App\Mail\LoginOtpMail;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_returns_ok_status(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk()
            ->assertJsonPath('status', 'ok');
    }

    public function test_student_login_returns_token_for_valid_credentials(): void
    {
        $university = University::create([
            'name' => 'Lagos State University',
            'code' => 'LASU',
            'email' => 'info@lasu.edu.ng',
            'department' => 'Research Office',
            'phone' => '08000000000',
        ]);

        $user = User::create([
            'university_id' => $university->id,
            'email' => 'student1@lasu.edu.ng',
            'password' => 'secret123',
            'name' => 'Ada Okafor',
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $user->id,
            'university_id' => $university->id,
            'matric_number' => '2021001',
            'lastname' => 'Okafor',
            'full_name' => 'Ada Okafor',
            'email' => 'student1@lasu.edu.ng',
            'degree_level' => 'BSc',
            'current_stage' => 1,
            'status' => 'active',
            'account_status' => 'active',
        ]);

        $response = $this->postJson('/api/auth/login-student', [
            'university_code' => 'LASU',
            'matric_number' => '2021001',
            'lastname' => 'Okafor',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.matric_number', '2021001')
            ->assertJsonPath('data.university_id', $university->id)
            ->assertJsonPath('data.token', fn ($token) => is_string($token) && $token !== '');
    }

    public function test_super_admin_login_returns_token_for_valid_credentials(): void
    {
        $university = University::create([
            'name' => 'AfriScribe University',
            'code' => 'AFS',
            'email' => 'info@afriscribe.edu',
            'department' => 'Research Office',
            'phone' => '08011111111',
        ]);

        $user = User::create([
            'university_id' => $university->id,
            'email' => 'superadmin@afriscribe.org',
            'password' => 'SuperAdmin@2026',
            'name' => 'Platform Super Admin',
            'role' => 'super_admin',
        ]);

        $response = $this->postJson('/api/auth/login-super-admin', [
            'email' => 'superadmin@afriscribe.org',
            'password' => 'SuperAdmin@2026',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.role', 'super_admin')
            ->assertJsonPath('data.token', fn ($token) => is_string($token) && $token !== '');
    }

    public function test_admin_can_request_and_verify_otp_for_login(): void
    {
        Mail::fake();

        $university = University::create([
            'name' => 'AfriScribe University',
            'code' => 'AFS',
            'email' => 'info@afriscribe.edu',
            'department' => 'Research Office',
            'phone' => '08022222222',
        ]);

        $user = User::create([
            'university_id' => $university->id,
            'email' => 'admin2@afriscribe.org',
            'password' => 'Admin@2026',
            'name' => 'Admin Plus',
            'role' => 'admin',
        ]);

        $requestResponse = $this->postJson('/api/auth/send-otp', [
            'role' => 'admin',
            'email' => 'admin2@afriscribe.org',
            'password' => 'Admin@2026',
        ]);

        $requestResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.role', 'admin');

        Mail::assertSent(LoginOtpMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $otpToken = \App\Models\OtpToken::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($otpToken);

        $verifyResponse = $this->postJson('/api/auth/verify-otp', [
            'role' => 'admin',
            'email' => 'admin2@afriscribe.org',
            'otp' => $otpToken->code,
        ]);

        $verifyResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.role', 'admin')
            ->assertJsonPath('data.user_id', $user->id);
    }
}
