<?php

namespace Tests\Feature;

use App\Models\DefenseReadinessDocument;
use App\Services\DefenseReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefenseReadinessTenancyTest extends TestCase
{
    use RefreshDatabase;

    protected function getService(): DefenseReadinessService
    {
        return new DefenseReadinessService();
    }

    public function test_student_can_access_own_document_via_api(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $this->getService()->documentForStudent($ctx['student']);

        $response = $this->withSession($this->defenseStudentSession($ctx['student']))
            ->get('/api/student/defense-readiness');

        $response->assertOk();
    }

    public function test_student_cannot_access_document_without_session(): void
    {
        $response = $this->get('/api/student/defense-readiness');

        $response->assertStatus(400);
    }

    public function test_student_can_access_sections_after_document_creation(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $this->getService()->documentForStudent($ctx['student']);

        $response = $this->withSession($this->defenseStudentSession($ctx['student']))
            ->get('/api/student/defense-readiness');

        $response->assertOk();
        $this->assertGreaterThan(0, count($response->json('sections')));
    }

    public function test_supervisor_can_see_manuscripts_in_their_university(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $this->getService()->documentForStudent($ctx['student']);

        $response = $this->withSession($this->defenseSupervisorSession($ctx['supervisor']))
            ->get('/api/supervisor/manuscripts');

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, $response->json('total'));
    }

    public function test_supervisor_cannot_see_other_university_manuscripts(): void
    {
        $ctx1 = $this->createDefenseReadinessContext();
        $this->getService()->documentForStudent($ctx1['student']);

        $otherUniversity = \App\Models\University::create([
            'name' => 'Other State University',
            'code' => 'OSU',
            'email' => 'info@osu.edu',
            'department' => 'Research',
            'phone' => '08099999999',
        ]);

        $otherSupervisorUser = \App\Models\User::create([
            'university_id' => $otherUniversity->id,
            'email' => 'other-super@osu.edu',
            'password' => bcrypt('Supervisor@2026'),
            'name' => 'Other Supervisor',
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        $otherSupervisor = \App\Models\Supervisor::create([
            'user_id' => $otherSupervisorUser->id,
            'university_id' => $otherUniversity->id,
            'title' => 'Dr.',
            'department' => 'Arts',
            'pin_code' => bcrypt('1234'),
            'passphrase' => bcrypt('other-pass'),
            'is_active' => true,
        ]);

        $response = $this->withSession([
            'user_id' => $otherSupervisorUser->id,
            'role' => 'supervisor',
            'university_id' => $otherUniversity->id,
            'supervisor_id' => $otherSupervisor->id,
            'last_activity' => time(),
            'session_started' => time(),
        ])->get('/api/supervisor/manuscripts');

        $response->assertOk();
        $this->assertSame(0, $response->json('total'));
    }

    public function test_student_cannot_access_supervisor_endpoints(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $this->getService()->documentForStudent($ctx['student']);

        $response = $this->withSession($this->defenseStudentSession($ctx['student']))
            ->get('/api/supervisor/manuscripts');

        $response->assertStatus(403);
    }

    public function test_supervisor_can_show_manuscript_detail(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);

        $response = $this->withSession($this->defenseSupervisorSession($ctx['supervisor']))
            ->get("/api/supervisor/manuscripts/{$document->id}");

        $response->assertOk()
            ->assertJsonStructure(['document', 'sections']);
    }
}
