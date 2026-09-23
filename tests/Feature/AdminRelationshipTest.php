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
            ->assertViewIs('admin.users');

        $this->assertSame(
            ['Render Student'],
            $usersResponse->viewData('relationshipStudents')->pluck('full_name')->all()
        );
        $this->assertSame(
            ['Render Supervisor'],
            $usersResponse->viewData('recommendedSupervisors')->pluck('user.name')->all()
        );
        $this->assertCount(0, $usersResponse->viewData('relationshipHistory'));
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

    public function test_admin_recommendations_prioritize_student_topic_fit_over_raw_load(): void
    {
        $university = University::create([
            'name' => 'Heuristic State University',
            'code' => 'HSU',
            'email' => 'info@hsu.edu',
            'department' => 'Research Office',
            'phone' => '08077770000',
        ]);

        $admin = User::create([
            'university_id' => $university->id,
            'email' => 'heuristic-admin@hsu.edu',
            'password' => 'Admin@2026',
            'name' => 'Heuristic Admin',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $matchingSupervisorUser = User::create([
            'university_id' => $university->id,
            'email' => 'match-supervisor@hsu.edu',
            'password' => 'Supervisor@2026',
            'name' => 'Match Mentor',
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        $matchingSupervisor = Supervisor::create([
            'user_id' => $matchingSupervisorUser->id,
            'university_id' => $university->id,
            'title' => 'Dr.',
            'department' => 'Mass Communication',
            'research_areas' => 'journalism, digital media, communication studies',
            'pin_code' => bcrypt('1234'),
            'passphrase' => bcrypt('match-passphrase'),
            'is_active' => true,
        ]);

        $mismatchSupervisorUser = User::create([
            'university_id' => $university->id,
            'email' => 'mismatch-supervisor@hsu.edu',
            'password' => 'Supervisor@2026',
            'name' => 'Mismatch Mentor',
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        Supervisor::create([
            'user_id' => $mismatchSupervisorUser->id,
            'university_id' => $university->id,
            'title' => 'Dr.',
            'department' => 'Physics',
            'research_areas' => 'quantum mechanics, optics',
            'pin_code' => bcrypt('1234'),
            'passphrase' => bcrypt('mismatch-passphrase'),
            'is_active' => true,
        ]);

        $focusedStudentUser = User::create([
            'university_id' => $university->id,
            'email' => 'focus-student@hsu.edu',
            'password' => 'Student@2026',
            'name' => 'Focus Student',
            'role' => 'student',
            'is_active' => true,
        ]);

        $focusedStudent = Student::create([
            'user_id' => $focusedStudentUser->id,
            'university_id' => $university->id,
            'supervisor_id' => null,
            'matric_number' => 'HSU-001',
            'lastname' => 'Student',
            'full_name' => 'Focus Student',
            'email' => 'focus-student@hsu.edu',
            'degree_level' => 'BSc',
            'research_topic' => 'digital journalism in higher education',
            'current_stage' => 1,
            'progress_percentage' => 0,
            'status' => 'active',
            'account_status' => 'active',
        ]);

        $assignedStudentUser = User::create([
            'university_id' => $university->id,
            'email' => 'assigned-student@hsu.edu',
            'password' => 'Student@2026',
            'name' => 'Assigned Student',
            'role' => 'student',
            'is_active' => true,
        ]);

        Student::create([
            'user_id' => $assignedStudentUser->id,
            'university_id' => $university->id,
            'supervisor_id' => $matchingSupervisor->id,
            'matric_number' => 'HSU-002',
            'lastname' => 'Assigned',
            'full_name' => 'Assigned Student',
            'email' => 'assigned-student@hsu.edu',
            'degree_level' => 'BSc',
            'current_stage' => 2,
            'progress_percentage' => 20,
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
        ])->get('/admin/users?student_id=' . $focusedStudent->id);

        $response->assertOk();

        $recommendedSupervisors = $response->viewData('recommendedSupervisors');
        $recommendationStudent = $response->viewData('recommendationStudent');

        $this->assertNotNull($recommendationStudent);
        $this->assertSame('Focus Student', $recommendationStudent->full_name);
        $this->assertSame(
            ['Match Mentor', 'Mismatch Mentor'],
            $recommendedSupervisors->take(2)->pluck('user.name')->values()->all()
        );
    }
}