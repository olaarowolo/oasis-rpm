<?php

namespace Tests\Browser;

use App\Models\Student;
use App\Models\University;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RoleLoginTest extends DuskTestCase
{
    public function test_admin_super_admin_and_student_login_flows(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Research Supervision Portal')
                ->assertSee('Student')
                ->assertSee('Admin');
        });

        $this->assertStudentLoginFlow();
        $this->assertAdminLoginFlow();
        $this->assertSuperAdminLoginFlow();
    }

    protected function assertStudentLoginFlow(): void
    {
        $university = University::updateOrCreate(
            ['code' => 'LASU'],
            [
                'name' => 'Lagos State University',
                'email' => 'info@lasu.edu.ng',
                'department' => 'Research Office',
                'phone' => '08020000000',
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'olasunkanmiarowolo@gmail.com'],
            [
                'university_id' => $university->id,
                'password' => Hash::make('Student@2026'),
                'name' => 'Olasunkanmi Arowolo',
                'role' => 'student',
            ]
        );

        Student::updateOrCreate(
            ['user_id' => $user->id],
            [
                'university_id' => $university->id,
                'matric_number' => '100910031',
                'lastname' => 'AROWOLO',
                'full_name' => 'Olasunkanmi Arowolo',
                'email' => 'olasunkanmiarowolo@gmail.com',
                'degree_level' => 'BSc',
                'current_stage' => 1,
                'status' => 'active',
                'account_status' => 'active',
            ]
        );

        $response = Http::asForm()->post('http://localhost:8000/api/auth/login-student', [
            'university_code' => 'LASU',
            'matric_number' => '100910031',
            'lastname' => 'AROWOLO',
        ]);

        $this->assertTrue($response->successful(), 'Student login API should succeed.');
        $this->assertSame('/student/dashboard', $response->json('data.dashboard_url'));
        $this->assertSame('student', $response->json('data.role', null));
    }

    protected function assertAdminLoginFlow(): void
    {
        $university = University::updateOrCreate(
            ['code' => 'AFS'],
            [
                'name' => 'AfriScribe University',
                'email' => 'info@afriscribe.edu',
                'department' => 'Research Office',
                'phone' => '08011111111',
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'admin@afriscribe.org'],
            [
                'university_id' => $university->id,
                'password' => Hash::make('Admin@2026'),
                'name' => 'Admin User',
                'role' => 'admin',
            ]
        );

        $loginResponse = Http::asForm()->post('http://localhost:8000/api/auth/login-admin', [
            'email' => 'admin@afriscribe.org',
            'password' => 'Admin@2026',
            'university_code' => 'AFS',
        ]);

        $this->assertTrue($loginResponse->successful(), 'Admin login should request MFA challenge.');
        $challengeId = $loginResponse->json('data.challenge_id');
        $this->assertNotEmpty($challengeId);

        $code = Cache::get('admin_mfa:' . $challengeId)['code'] ?? null;
        $this->assertNotNull($code, 'Expected an MFA code to be stored in cache for the admin login challenge.');

        $mfaResponse = Http::asForm()->post('http://localhost:8000/api/auth/admin/verify-mfa', [
            'challenge_id' => $challengeId,
            'code' => (string) $code,
        ]);

        $this->assertTrue($mfaResponse->successful(), 'Admin MFA verification should succeed.');
        $this->assertSame('/admin/dashboard', $mfaResponse->json('data.dashboard_url'));
        $this->assertSame('admin', $mfaResponse->json('data.role'));
    }

    protected function assertSuperAdminLoginFlow(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@afriscribe.org'],
            [
                'university_id' => null,
                'password' => Hash::make('SuperAdmin@2026'),
                'name' => 'Platform Super Admin',
                'role' => 'super_admin',
            ]
        );

        $response = Http::asForm()->post('http://localhost:8000/api/auth/super-admin/login', [
            'email' => 'superadmin@afriscribe.org',
            'password' => 'SuperAdmin@2026',
        ]);

        $this->assertTrue($response->successful(), 'Super admin login should succeed.');
        $this->assertSame('/super-admin/dashboard', $response->json('data.dashboard_url'));
        $this->assertSame('super_admin', $response->json('data.role'));
    }
}
