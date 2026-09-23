<?php

namespace Tests\Feature;

use App\Mail\PortalEmail;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_and_users_pages_render_relationship_operations(): void
    {
        $university = University::create([
            'name' => 'Render State University',
            'code' => 'RSU',
            'email' => 'info@rsu.edu',
            'department' => 'Research Office',
            'phone' => '08099999999',
        ]);

        $admin = User::create([
            'university_id' => $university->id,
            'email' => 'render-admin@rsu.edu',
            'password' => 'Admin@2026',
            'name' => 'Render Admin',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $supervisorUser = User::create([
            'university_id' => $university->id,
            'email' => 'render-supervisor@rsu.edu',
            'password' => 'Supervisor@2026',
            'name' => 'Render Supervisor',
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        $supervisor = Supervisor::create([
            'user_id' => $supervisorUser->id,
            'university_id' => $university->id,
            'title' => 'Dr.',
            'department' => 'Sciences',
            'pin_code' => bcrypt('1234'),
            'passphrase' => bcrypt('render-secret-passphrase'),
            'is_active' => true,
        ]);

        $studentUser = User::create([
            'university_id' => $university->id,
            'email' => 'render-student@rsu.edu',
            'password' => 'Student@2026',
            'name' => 'Render Student',
            'role' => 'student',
            'is_active' => true,
        ]);

        Student::create([
            'user_id' => $studentUser->id,
            'university_id' => $university->id,
            'supervisor_id' => null,
            'matric_number' => 'RSU-001',
            'lastname' => 'Render',
            'full_name' => 'Render Student',
            'email' => 'render-student@rsu.edu',
            'degree_level' => 'BSc',
            'current_stage' => 1,
            'progress_percentage' => 0,
            'status' => 'active',
            'account_status' => 'active',
        ]);

        $session = [
            'user_id' => $admin->id,
            'role' => 'admin',
            'university_id' => $university->id,
            'mfa_verified' => true,
            'last_activity' => time(),
            'session_started' => time(),
        ];

        $dashboardResponse = $this->withSession($session)->get('/admin/dashboard');
        $dashboardResponse->assertOk()
            ->assertSee('Supervision Link Desk')
            ->assertSee('Recommended Supervisors')
            ->assertSee('Recent Relationship Changes')
            ->assertSee('Unassigned Students')
            ->assertSee('Link and notify');

        $usersResponse = $this->withSession($session)->get('/admin/users');
        $usersResponse->assertOk()
            ->assertSee('Relationship Operations')
            ->assertSee('Recommended Supervisors')
            ->assertSee('Recent Relationship Changes')
            ->assertSee('Unassigned Students')
            ->assertSee('Render Student')
            ->assertSee('Render Supervisor');
    }

    public function test_admin_can_link_student_to_supervisor_and_notify_both_parties_within_scope(): void
    {
        Mail::fake();

        $university = University::create([
            'name' => 'Admin Link University',
            'code' => 'ALU',
            'email' => 'info@alu.edu',
            'department' => 'Research Office',
            'phone' => '08088888888',
        ]);

        $admin = User::create([
            'university_id' => $university->id,
            'email' => 'admin@alu.edu',
            'password' => 'Admin@2026',
            'name' => 'Scoped Admin',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $supervisorUser = User::create([
            'university_id' => $university->id,
            'email' => 'supervisor.admin@alu.edu',
            'password' => 'Supervisor@2026',
            'name' => 'Supervisor Admin',
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        $supervisor = Supervisor::create([
            'user_id' => $supervisorUser->id,
            'university_id' => $university->id,
            'title' => 'Dr.',
            'department' => 'Engineering',
            'pin_code' => bcrypt('1234'),
            'passphrase' => bcrypt('super-secret-passphrase'),
            'is_active' => true,
        ]);

        $studentUser = User::create([
            'university_id' => $university->id,
            'email' => 'student.admin@alu.edu',
            'password' => 'Student@2026',
            'name' => 'Student Admin',
            'role' => 'student',
            'is_active' => true,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'university_id' => $university->id,
            'supervisor_id' => null,
            'matric_number' => 'ALU-001',
            'lastname' => 'Admin',
            'full_name' => 'Student Admin',
            'email' => 'student.admin@alu.edu',
            'degree_level' => 'BSc',
            'current_stage' => 1,
            'progress_percentage' => 0,
            'status' => 'active',
            'account_status' => 'active',
        ]);

        $response = $this->withSession([
            'user_id' => $admin->id,
            'role' => 'admin',
            'university_id' => $university->id,
            'mfa_verified' => true,
            'last_activity' => time(),
            'session_started' => time(),
        ])->post('/admin/relationships/assign', [
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'scope_university_id' => $university->id,
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
}