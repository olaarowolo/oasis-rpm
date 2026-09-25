<?php

namespace Tests\Feature;

use App\Mail\LoginOtpMail;
use App\Mail\PortalEmail;
use App\Models\AuditLog;
use App\Models\OtpToken;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\University;
use App\Models\User;
use Database\Seeders\SupervisorSeeder;
use Database\Seeders\UniversitySeeder;
use Database\Seeders\UserSeeder;
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

    public function test_protected_student_route_requires_authentication(): void
    {
        $university = University::create([
            'name' => 'Demo University',
            'code' => 'DEMO',
            'email' => 'info@demo.edu',
            'department' => 'Research Office',
            'phone' => '08044444444',
        ]);

        $response = $this->withHeaders([
            'X-University-ID' => $university->id,
        ])->getJson('/api/student/dashboard');

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_admin_login_requires_mfa_before_accessing_protected_routes(): void
    {
        $university = University::create([
            'name' => 'AfriScribe University',
            'code' => 'AFS',
            'email' => 'info@afriscribe.edu',
            'department' => 'Research Office',
            'phone' => '08033333333',
        ]);

        User::create([
            'university_id' => $university->id,
            'email' => 'admin-mfa@afriscribe.org',
            'password' => 'Admin@2026',
            'name' => 'Admin MFA',
            'role' => 'admin',
        ]);

        $loginResponse = $this->postJson('/api/auth/login-admin', [
            'email' => 'admin-mfa@afriscribe.org',
            'password' => 'Admin@2026',
            'university_code' => 'AFS',
        ]);

        $loginResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.requires_mfa', true);

        $response = $this->getJson('/api/admin/universities');

        $response->assertStatus(403)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Multi-factor authentication required');
    }

    public function test_password_reset_does_not_leak_account_existence(): void
    {
        $university = University::create([
            'name' => 'Reset University',
            'code' => 'RST',
            'email' => 'info@reset.edu',
            'department' => 'Research Office',
            'phone' => '08055555555',
        ]);

        User::create([
            'university_id' => $university->id,
            'email' => 'reset-user@example.com',
            'password' => 'Reset@2026',
            'name' => 'Reset User',
            'role' => 'student',
        ]);

        $response = $this->postJson('/password/reset-link', [
            'email' => 'missing-user@example.com',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'If email exists, reset link will be sent');

        $knownUserResponse = $this->postJson('/password/reset-link', [
            'email' => 'reset-user@example.com',
        ]);

        $knownUserResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'If email exists, reset link will be sent');
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

    public function test_student_resource_endpoint_returns_all_university_resources(): void
    {
        $university = University::create([
            'name' => 'Lagos State University',
            'code' => 'LASU',
            'email' => 'info@lasu.edu.ng',
            'department' => 'Research Office',
            'phone' => '08011111111',
        ]);

        $user = User::create([
            'university_id' => $university->id,
            'email' => 'resource.student@lasu.edu.ng',
            'password' => 'Student@2026',
            'name' => 'Resource Student',
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'university_id' => $university->id,
            'matric_number' => '2022001',
            'lastname' => 'Student',
            'full_name' => 'Resource Student',
            'email' => 'resource.student@lasu.edu.ng',
            'degree_level' => 'BSc',
            'current_stage' => 1,
            'status' => 'active',
            'account_status' => 'active',
        ]);

        $resources = [
            ['section' => 'Current Assignment', 'type' => 'video', 'title' => 'Stage 1 Resource', 'url' => 'https://example.com/s1', 'description' => 'First stage resource', 'sort_order' => 1, 'is_mandatory' => true, 'stage' => 1, 'points' => 10],
            ['section' => 'Required Worksheets', 'type' => 'document', 'title' => 'Stage 2 Resource', 'url' => 'https://example.com/s2', 'description' => 'Second stage resource', 'sort_order' => 1, 'is_mandatory' => true, 'stage' => 2, 'points' => 10],
            ['section' => 'Prerequisite Videos', 'type' => 'video', 'title' => 'Stage 3 Resource', 'url' => 'https://example.com/s3', 'description' => 'Third stage resource', 'sort_order' => 1, 'is_mandatory' => false, 'stage' => 3, 'points' => 5],
        ];

        foreach ($resources as $resource) {
            \App\Models\Resource::create([
                'university_id' => $university->id,
                'section' => $resource['section'],
                'type' => $resource['type'],
                'title' => $resource['title'],
                'url' => $resource['url'],
                'description' => $resource['description'],
                'sort_order' => $resource['sort_order'],
                'is_mandatory' => $resource['is_mandatory'],
                'stage' => $resource['stage'],
                'points' => $resource['points'],
            ]);
        }

        $response = $this->withSession([
            'user_id' => $user->id,
            'role' => 'student',
            'student_id' => $student->id,
            'university_id' => $university->id,
        ])->withHeader('X-University-ID', (string) $university->id)
          ->getJson('/api/student/resources');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');

        $stages = collect($response->json('data'))->pluck('stage')->all();
        $this->assertEquals([1, 2, 3], $stages);
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
            ->assertJsonPath('data.dashboard_url', '/super-admin/dashboard')
            ->assertJsonPath('data.token', fn ($token) => is_string($token) && $token !== '');
    }

    public function test_lasu_supervisor_seed_uses_olaarowolo_ng_email_and_affiliation_data(): void
    {
        $this->seed([UniversitySeeder::class, UserSeeder::class, SupervisorSeeder::class]);

        $user = User::where('email', 'olaarowolo.ng@gmail.com')->firstOrFail();
        $this->assertSame('supervisor', $user->role);
        $this->assertSame('Olasunkanmi Arowolo, PhD', $user->name);

        $supervisor = Supervisor::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('Lecturer & Research Supervisor', $supervisor->title);
        $this->assertSame('Dept. of Journalism & Media Studies', $supervisor->department);
        $this->assertStringContainsString('Journalism', $supervisor->research_areas);
        $this->assertNotNull($supervisor->booking_url);
        $this->assertStringContainsString('calendar.app.google', $supervisor->booking_url);
    }

    public function test_supervisor_login_returns_canonical_email_even_when_legacy_alias_is_typed(): void
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
            'email' => 'olaarowolo.ng@gmail.com',
            'password' => 'Supervisor@2026',
            'name' => 'Olasunkanmi Arowolo, PhD',
            'role' => 'supervisor',
        ]);

        $supervisor = Supervisor::create([
            'user_id' => $user->id,
            'university_id' => $university->id,
            'title' => 'Lecturer & Research Supervisor',
            'department' => 'Dept. of Journalism & Media Studies',
            'research_areas' => 'Journalism & Media Studies, Digital Media, Communication Studies',
            'booking_url' => 'https://calendar.app.google/7hf8cS6m4yFj9f9y6',
            'pin_code' => bcrypt('2026'),
            'passphrase' => bcrypt('LASU-Arowolo-2026'),
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login-supervisor', [
            'university_code' => 'LASU',
            'pin_code' => '2026',
            'passphrase' => 'LASU-Arowolo-2026',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'olaarowolo.ng@gmail.com')
            ->assertJsonPath('data.name', 'Olasunkanmi Arowolo, PhD');

        $this->assertSame('olaarowolo.ng@gmail.com', $supervisor->fresh()->user->email);
    }

    public function test_supervisor_sees_only_its_own_students_and_students_link_to_their_supervisor(): void
    {
        $university = University::create([
            'name' => 'Supervisor Scope University',
            'code' => 'SSU',
            'email' => 'info@ssu.edu',
            'department' => 'Research Office',
            'phone' => '08070000001',
        ]);

        $ownerUser = User::create([
            'university_id' => $university->id,
            'email' => 'owner.supervisor@ssu.edu',
            'password' => 'Supervisor@2026',
            'name' => 'Owner Supervisor',
            'role' => 'supervisor',
        ]);

        $otherUser = User::create([
            'university_id' => $university->id,
            'email' => 'other.supervisor@ssu.edu',
            'password' => 'Supervisor@2026',
            'name' => 'Other Supervisor',
            'role' => 'supervisor',
        ]);

        $ownerSupervisor = Supervisor::create([
            'user_id' => $ownerUser->id,
            'university_id' => $university->id,
            'title' => 'Lead Supervisor',
            'department' => 'Computer Science',
            'research_areas' => 'AI',
            'pin_code' => bcrypt('2026'),
            'passphrase' => bcrypt('SUPPASS2026'),
            'is_active' => true,
        ]);

        $otherSupervisor = Supervisor::create([
            'user_id' => $otherUser->id,
            'university_id' => $university->id,
            'title' => 'Assistant Supervisor',
            'department' => 'Computer Science',
            'research_areas' => 'ML',
            'pin_code' => bcrypt('2027'),
            'passphrase' => bcrypt('SUPPASS2027'),
            'is_active' => true,
        ]);

        $ownerStudent = Student::create([
            'user_id' => User::create([
                'university_id' => $university->id,
                'email' => 'owner.student@ssu.edu',
                'password' => 'Student@2026',
                'name' => 'Owner Student',
                'role' => 'student',
            ])->id,
            'university_id' => $university->id,
            'supervisor_id' => $ownerSupervisor->id,
            'matric_number' => 'SSU-001',
            'lastname' => 'Student',
            'full_name' => 'Owner Student',
            'email' => 'owner.student@ssu.edu',
            'degree_level' => 'BSc',
            'current_stage' => 1,
            'status' => 'active',
            'account_status' => 'active',
        ]);

        $otherStudent = Student::create([
            'user_id' => User::create([
                'university_id' => $university->id,
                'email' => 'other.student@ssu.edu',
                'password' => 'Student@2026',
                'name' => 'Other Student',
                'role' => 'student',
            ])->id,
            'university_id' => $university->id,
            'supervisor_id' => $otherSupervisor->id,
            'matric_number' => 'SSU-002',
            'lastname' => 'Student',
            'full_name' => 'Other Student',
            'email' => 'other.student@ssu.edu',
            'degree_level' => 'BSc',
            'current_stage' => 1,
            'status' => 'active',
            'account_status' => 'active',
        ]);

        $rosterResponse = $this->withSession([
            'user_id' => $ownerUser->id,
            'role' => 'supervisor',
            'supervisor_id' => $ownerSupervisor->id,
            'university_id' => $university->id,
            'last_activity' => time(),
            'session_started' => time(),
        ])->getJson('/api/supervisor/students');

        $rosterResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownerStudent->id);

        $this->assertSame($ownerSupervisor->id, $ownerStudent->fresh()->supervisor_id);
        $this->assertSame($otherSupervisor->id, $otherStudent->fresh()->supervisor_id);
    }

    public function test_role_specific_login_responses_include_dashboard_redirects(): void
    {
        $studentUniversity = University::create([
            'name' => 'Student University',
            'code' => 'STU',
            'email' => 'info@student.edu',
            'department' => 'Research Office',
            'phone' => '08055500001',
        ]);

        $studentUser = User::create([
            'university_id' => $studentUniversity->id,
            'email' => 'student.login@test.edu',
            'password' => 'Student@2026',
            'name' => 'Student Redirect',
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'university_id' => $studentUniversity->id,
            'matric_number' => 'STU2026',
            'lastname' => 'Redirect',
            'full_name' => 'Student Redirect',
            'email' => 'student.login@test.edu',
            'degree_level' => 'BSc',
            'current_stage' => 1,
            'status' => 'active',
            'account_status' => 'active',
        ]);

        $studentLogin = $this->postJson('/api/auth/login-student', [
            'university_code' => 'STU',
            'matric_number' => 'STU2026',
            'lastname' => 'Redirect',
        ]);

        $studentLogin->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.dashboard_url', '/student/dashboard');

        $this->withSession(['_last_session_regenerate' => time() - 1801]);
        $home = $this->get('/home');

        $home->assertRedirect('/student/dashboard');
        $this->get('/student/dashboard')->assertOk();

        $supervisorUniversity = University::create([
            'name' => 'Supervisor University',
            'code' => 'SUP',
            'email' => 'info@supervisor.edu',
            'department' => 'Research Office',
            'phone' => '08055500002',
        ]);

        $supervisorUser = User::create([
            'university_id' => $supervisorUniversity->id,
            'email' => 'supervisor.login@test.edu',
            'password' => 'Supervisor@2026',
            'name' => 'Supervisor Redirect',
            'role' => 'supervisor',
        ]);

        Supervisor::create([
            'user_id' => $supervisorUser->id,
            'university_id' => $supervisorUniversity->id,
            'title' => 'Senior Supervisor',
            'department' => 'Computer Science',
            'research_areas' => 'AI',
            'pin_code' => bcrypt('2026'),
            'passphrase' => bcrypt('SUPPASS2026'),
            'is_active' => true,
        ]);

        $supervisorLogin = $this->postJson('/api/auth/login-supervisor', [
            'university_code' => 'SUP',
            'pin_code' => '2026',
            'passphrase' => 'SUPPASS2026',
        ]);

        $supervisorLogin->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.dashboard_url', '/supervisor/dashboard');
    }

    public function test_supervisor_students_page_renders_for_authenticated_supervisor(): void
    {
        $university = University::create([
            'name' => 'Supervisor Web University',
            'code' => 'SWU',
            'email' => 'info@swu.edu',
            'department' => 'Research Office',
            'phone' => '08066666666',
        ]);

        $supervisorUser = User::create([
            'university_id' => $university->id,
            'email' => 'web.supervisor@swu.edu',
            'password' => 'Supervisor@2026',
            'name' => 'Web Supervisor',
            'role' => 'supervisor',
        ]);

        $supervisor = Supervisor::create([
            'user_id' => $supervisorUser->id,
            'university_id' => $university->id,
            'title' => 'Dr.',
            'department' => 'Computer Science',
            'research_areas' => 'Software Engineering',
            'pin_code' => bcrypt('2026'),
            'passphrase' => bcrypt('SWU-Supervisor-2026'),
            'is_active' => true,
        ]);

        $studentUser = User::create([
            'university_id' => $university->id,
            'email' => 'student.web@swu.edu',
            'password' => 'Student@2026',
            'name' => 'Student Web',
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $studentUser->id,
            'university_id' => $university->id,
            'supervisor_id' => $supervisor->id,
            'matric_number' => 'SWU-001',
            'lastname' => 'Web',
            'full_name' => 'Student Web',
            'email' => 'student.web@swu.edu',
            'degree_level' => 'BSc',
            'current_stage' => 2,
            'progress_percentage' => 40,
            'status' => 'suspended',
            'account_status' => 'active',
        ]);

        $response = $this->withSession([
            'user_id' => $supervisorUser->id,
            'role' => 'supervisor',
            'supervisor_id' => $supervisor->id,
            'university_id' => $university->id,
            'last_activity' => time(),
            'session_started' => time(),
        ])->get('/supervisor/students');

        $response->assertOk()
            ->assertSee('Student Roster')
            ->assertSee('Student Web')
            ->assertSee('SWU-001')
            ->assertSee('Suspended')
            ->assertSee('/api/supervisor/students/');
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

    public function test_super_admin_can_link_student_to_supervisor_and_notify_both_parties(): void
    {
        Mail::fake();

        $university = University::create([
            'name' => 'Link State University',
            'code' => 'LSU',
            'email' => 'info@lsu.edu',
            'department' => 'Research Office',
            'phone' => '08077777777',
        ]);

        $superAdmin = User::create([
            'university_id' => $university->id,
            'email' => 'super-admin@lsu.edu',
            'password' => 'SuperAdmin@2026',
            'name' => 'Link Super Admin',
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $supervisorUser = User::create([
            'university_id' => $university->id,
            'email' => 'supervisor.link@lsu.edu',
            'password' => 'Supervisor@2026',
            'name' => 'Supervisor Link',
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        $supervisor = Supervisor::create([
            'user_id' => $supervisorUser->id,
            'university_id' => $university->id,
            'title' => 'Dr.',
            'department' => 'Computer Science',
            'pin_code' => bcrypt('1234'),
            'passphrase' => bcrypt('super-secret-passphrase'),
            'is_active' => true,
        ]);

        $studentUser = User::create([
            'university_id' => $university->id,
            'email' => 'student.link@lsu.edu',
            'password' => 'Student@2026',
            'name' => 'Student Link',
            'role' => 'student',
            'is_active' => true,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'university_id' => $university->id,
            'supervisor_id' => null,
            'matric_number' => 'LSU-001',
            'lastname' => 'Link',
            'full_name' => 'Student Link',
            'email' => 'student.link@lsu.edu',
            'degree_level' => 'BSc',
            'current_stage' => 1,
            'progress_percentage' => 0,
            'status' => 'active',
            'account_status' => 'active',
        ]);

        $response = $this->withSession([
            'user_id' => $superAdmin->id,
            'role' => 'super_admin',
            'university_id' => $university->id,
            'mfa_verified' => true,
            'last_activity' => time(),
            'session_started' => time(),
        ])->post('/super-admin/relationships/assign', [
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame($supervisor->id, $student->fresh()->supervisor_id);

        Mail::assertSent(PortalEmail::class, function ($mail) use ($student) {
            return $mail->viewName === 'supervision-linked' && $mail->hasTo($student->email);
        });

        Mail::assertSent(PortalEmail::class, function ($mail) use ($supervisorUser) {
            return $mail->viewName === 'supervision-linked' && $mail->hasTo($supervisorUser->email);
        });

        $auditLog = AuditLog::query()
            ->where('model_type', 'Student')
            ->where('model_id', $student->id)
            ->latest()
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertSame('sent', data_get($auditLog->new_values, 'notifications.student.status'));
        $this->assertSame('sent', data_get($auditLog->new_values, 'notifications.supervisor.status'));
    }

    public function test_super_admin_dashboard_prioritizes_matching_supervisors_for_selected_student(): void
    {
        $university = University::create([
            'name' => 'Recommendation University',
            'code' => 'RCU',
            'email' => 'info@rcu.edu',
            'department' => 'Research Office',
            'phone' => '08012345678',
        ]);

        $superAdmin = User::create([
            'university_id' => $university->id,
            'email' => 'superadmin@rcu.edu',
            'password' => 'SuperAdmin@2026',
            'name' => 'Recommendation Super Admin',
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $matchingSupervisorUser = User::create([
            'university_id' => $university->id,
            'email' => 'fit-supervisor@rcu.edu',
            'password' => 'Supervisor@2026',
            'name' => 'Fit Supervisor',
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        Supervisor::create([
            'user_id' => $matchingSupervisorUser->id,
            'university_id' => $university->id,
            'title' => 'Dr.',
            'department' => 'Department of Journalism and Media Studies',
            'research_areas' => 'editorial workflows, newsroom operations',
            'pin_code' => bcrypt('1234'),
            'passphrase' => bcrypt('fit-passphrase'),
            'is_active' => true,
        ]);

        $mismatchSupervisorUser = User::create([
            'university_id' => $university->id,
            'email' => 'remote-supervisor@rcu.edu',
            'password' => 'Supervisor@2026',
            'name' => 'Remote Supervisor',
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        Supervisor::create([
            'user_id' => $mismatchSupervisorUser->id,
            'university_id' => $university->id,
            'title' => 'Dr.',
            'department' => 'Physics',
            'research_areas' => 'astrophysics',
            'pin_code' => bcrypt('1234'),
            'passphrase' => bcrypt('remote-passphrase'),
            'is_active' => true,
        ]);

        $studentUser = User::create([
            'university_id' => $university->id,
            'email' => 'topic-student@rcu.edu',
            'password' => 'Student@2026',
            'name' => 'Topic Student',
            'role' => 'student',
            'is_active' => true,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'university_id' => $university->id,
            'supervisor_id' => null,
            'matric_number' => 'RCU-001',
            'lastname' => 'Student',
            'full_name' => 'Topic Student',
            'email' => 'topic-student@rcu.edu',
            'degree_level' => 'BSc',
            'research_topic' => 'digital journalism practice',
            'current_stage' => 1,
            'status' => 'active',
            'account_status' => 'active',
        ]);

        $response = $this->withSession([
            'user_id' => $superAdmin->id,
            'role' => 'super_admin',
            'university_id' => $university->id,
            'mfa_verified' => true,
            'last_activity' => time(),
            'session_started' => time(),
        ])->get('/super-admin/dashboard?student_id=' . $student->id);

        $response->assertOk();

        $recommendedSupervisors = $response->viewData('recommendedSupervisors');
        $recommendationStudent = $response->viewData('recommendationStudent');

        $this->assertNotNull($recommendationStudent);
        $this->assertSame('Topic Student', $recommendationStudent->full_name);
        $this->assertSame(
            ['Fit Supervisor', 'Remote Supervisor'],
            $recommendedSupervisors->take(2)->pluck('user.name')->values()->all()
        );
        $this->assertStringNotContainsString('Department fit: of.', (string) $recommendedSupervisors->first()->recommendation_reason);
    }

    public function test_student_otp_request_does_not_leak_account_existence(): void
    {
        $response = $this->postJson('/api/auth/student/send-otp', [
            'email' => 'ghost@example.com',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'If an account matches this email, a verification code will be sent');
    }

    private function makeRecoveryStudent(array $overrides = []): array
    {
        $university = University::create([
            'name' => 'Recovery University',
            'code' => 'RCV',
            'email' => 'info@rcv.edu',
            'department' => 'Research Office',
            'phone' => '08010000000',
        ]);

        $user = User::create([
            'university_id' => $university->id,
            'email' => $overrides['email'] ?? 'recovery.student@rcv.edu',
            'password' => 'Student@2026',
            'name' => $overrides['full_name'] ?? 'Recovery Student',
            'role' => 'student',
        ]);

        $student = Student::create(array_merge([
            'user_id' => $user->id,
            'university_id' => $university->id,
            'matric_number' => 'RCV-001',
            'lastname' => 'Student',
            'full_name' => 'Recovery Student',
            'email' => 'recovery.student@rcv.edu',
            'phone' => '08031234567',
            'degree_level' => 'BSc',
            'faculty' => 'Science',
            'department' => 'Computer Science',
            'programme' => 'Computer Science',
            'current_stage' => 1,
            'status' => 'active',
            'account_status' => 'active',
        ], $overrides));

        return [$university, $user, $student];
    }

    private function recoveryExpectedValue(Student $student, string $field): string
    {
        switch ($field) {
            case 'phone':
                return (string) $student->phone;
            case 'full_name':
                return (string) $student->full_name;
            case 'degree_level':
                return (string) $student->degree_level;
            case 'faculty':
                return (string) $student->faculty;
            case 'department':
                return (string) $student->department;
            case 'programme':
                return (string) $student->programme;
            case 'supervisor_name':
            case 'supervisor_department':
                return '';
            default:
                return '';
        }
    }

    public function test_student_recovery_start_does_not_leak_account_existence(): void
    {
        $university = University::create([
            'name' => 'Recovery University',
            'code' => 'RCV',
            'email' => 'info@rcv.edu',
            'department' => 'Research Office',
            'phone' => '08010000000',
        ]);

        $response = $this->postJson('/api/auth/student/recovery/start', [
            'university_code' => 'RCV',
            'matric_number' => 'GHOST-999',
            'lastname' => 'Nobody',
            'email' => 'nobody@rcv.edu',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', null)
            ->assertJsonPath('message', 'If the provided details match an account, a verification code will be sent to the email on file');
    }

    public function test_student_recovery_start_returns_masked_challenge_without_revealing_email(): void
    {
        [, , $student] = $this->makeRecoveryStudent();

        $response = $this->postJson('/api/auth/student/recovery/start', [
            'university_code' => 'RCV',
            'matric_number' => 'RCV-001',
            'lastname' => 'Student',
            'email' => 'recovery.student@rcv.edu',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['challenge_id', 'field', 'label', 'hint', 'email_hint']]);

        $data = $response->json('data');
        $this->assertNotNull($data['challenge_id']);
        $this->assertContains($data['field'], ['phone', 'full_name', 'degree_level', 'faculty', 'department', 'programme']);

        // The full email must never be revealed at the start step.
        $this->assertArrayNotHasKey('email', $data);
        $this->assertNotSame($student->email, $data['email_hint']);
        $this->assertStringContainsString('*', $data['email_hint']);
    }

    public function test_student_recovery_confirm_with_valid_detail_sends_otp_to_onfile_email(): void
    {
        Mail::fake();

        [, $user, $student] = $this->makeRecoveryStudent();

        $start = $this->postJson('/api/auth/student/recovery/start', [
            'university_code' => 'RCV',
            'matric_number' => 'RCV-001',
            'lastname' => 'Student',
            'email' => 'recovery.student@rcv.edu',
        ]);

        $field = $start->json('data.field');
        $expected = $this->recoveryExpectedValue($student, $field);

        $confirm = $this->postJson('/api/auth/student/recovery/confirm', [
            'challenge_id' => $start->json('data.challenge_id'),
            'field' => $field,
            'value' => $expected,
        ]);

        $confirm->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', $student->email);

        Mail::assertSent(LoginOtpMail::class, function ($mail) use ($student) {
            return $mail->hasTo($student->email) && $mail->role === 'Student';
        });

        $otp = OtpToken::where('user_id', $user->id)->where('role', 'student')->latest()->first();
        $this->assertNotNull($otp);
    }

    public function test_student_recovery_confirm_with_invalid_detail_fails_and_rate_limits(): void
    {
        Mail::fake();

        [, , $student] = $this->makeRecoveryStudent();

            $start = $this->postJson('/api/auth/student/recovery/start', [
            'university_code' => 'RCV',
            'matric_number' => 'RCV-001',
            'lastname' => 'Student',
            'email' => 'recovery.student@rcv.edu',
        ]);

        $field = $start->json('data.field');

        // Four failures return 401; the fifth triggers the 15-minute lockout lock.
        for ($i = 0; $i < 4; $i++) {
            $this->postJson('/api/auth/student/recovery/confirm', [
                'challenge_id' => $start->json('data.challenge_id'),
                'field' => $field,
                'value' => 'definitely-not-correct',
            ])->assertStatus(401);
        }

        $locked = $this->postJson('/api/auth/student/recovery/confirm', [
            'challenge_id' => $start->json('data.challenge_id'),
            'field' => $field,
            'value' => 'definitely-not-correct',
        ]);

        $locked->assertStatus(429);
    }

    public function test_student_recovery_full_flow_logs_in_via_existing_otp_verify(): void
    {
        Mail::fake();

        [, $user, $student] = $this->makeRecoveryStudent();

        $start = $this->postJson('/api/auth/student/recovery/start', [
            'university_code' => 'RCV',
            'matric_number' => 'RCV-001',
            'lastname' => 'Student',
            'email' => 'recovery.student@rcv.edu',
        ]);

        $field = $start->json('data.field');
        $confirm = $this->postJson('/api/auth/student/recovery/confirm', [
            'challenge_id' => $start->json('data.challenge_id'),
            'field' => $field,
            'value' => $this->recoveryExpectedValue($student, $field),
        ]);

        $otp = OtpToken::where('user_id', $user->id)->where('role', 'student')->latest()->first();
        $this->assertNotNull($otp);

        $verify = $this->postJson('/api/auth/student/verify-otp', [
            'email' => $student->email,
            'otp' => $otp->code,
        ]);

        $verify->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.role', 'student')
            ->assertJsonPath('data.dashboard_url', '/student/dashboard')
            ->assertJsonPath('data.token', fn ($token) => is_string($token) && $token !== '');

        $this->assertEquals($user->id, session('user_id'));
        $this->assertEquals($student->id, session('student_id'));
        $this->assertEquals('student', session('role'));

        $this->get('/home')->assertRedirect('/student/dashboard');
        $this->get('/student/dashboard')->assertOk();
    }

    public function test_student_recovery_rejects_suspended_student(): void
    {
        [, , $student] = $this->makeRecoveryStudent(['status' => 'suspended']);

        $response = $this->postJson('/api/auth/student/recovery/start', [
            'university_code' => 'RCV',
            'matric_number' => 'RCV-001',
            'lastname' => 'Student',
            'email' => 'recovery.student@rcv.edu',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', null);

        $this->assertNull(OtpToken::where('user_id', $student->user_id)->first());
    }

    public function test_student_recovery_reconciles_stale_email_via_matric_based_address(): void
    {
        Mail::fake();

        // Stored email is a stale personal address that does NOT embed the matric.
        [, $user, $student] = $this->makeRecoveryStudent([
            'email' => 'stale.personal@gmail.com',
        ]);
        $user->email = 'stale.personal@gmail.com';
        $user->save();

        $reconciledEmail = 'student.RCV-001@rcv.edu';

        $start = $this->postJson('/api/auth/student/recovery/start', [
            'university_code' => 'RCV',
            'matric_number' => 'RCV-001',
            'lastname' => 'Student',
            'email' => $reconciledEmail,
        ]);

        $start->assertOk()->assertJsonPath('success', true);
        $this->assertNotNull($start->json('data.challenge_id'));

        $field = $start->json('data.field');
        $expected = $this->recoveryExpectedValue($student, $field);

        $confirm = $this->postJson('/api/auth/student/recovery/confirm', [
            'challenge_id' => $start->json('data.challenge_id'),
            'field' => $field,
            'value' => $expected,
        ]);

        $confirm->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', $reconciledEmail);

        // The stale stored email must have been reconciled to the matric-based email.
        $this->assertEquals($reconciledEmail, $student->fresh()->email);
        $this->assertEquals($reconciledEmail, $user->fresh()->email);

        // OTP must be dispatched only to the reconciled email, never the stale one.
        Mail::assertSent(LoginOtpMail::class, function ($mail) use ($reconciledEmail) {
            return $mail->hasTo($reconciledEmail) && $mail->role === 'Student';
        });
        Mail::assertNotSent(LoginOtpMail::class, function ($mail) {
            return $mail->hasTo('stale.personal@gmail.com');
        });

        $otp = OtpToken::where('user_id', $user->id)->where('role', 'student')->latest()->first();
        $this->assertNotNull($otp);
        $this->assertEquals($reconciledEmail, $otp->email);
    }
}
