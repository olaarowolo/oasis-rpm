<?php

namespace Tests\Feature;

use App\Models\DefenseReadinessDocument;
use App\Models\DefenseReadinessSection;
use App\Mail\PortalEmail;
use App\Services\DefenseReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DefenseReadinessApiTest extends TestCase
{
    use RefreshDatabase;

    protected function getService(): DefenseReadinessService
    {
        return new DefenseReadinessService();
    }

    public function test_student_document_endpoint_returns_sections_and_progress(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $this->getService()->documentForStudent($ctx['student']);

        $response = $this->withSession($this->defenseStudentSession($ctx['student']))
            ->get('/api/student/defense-readiness');

        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'status',
                'study_approach',
                'sections',
            ]);
    }

    public function test_student_save_endpoint_creates_content(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $response = $this->withSession($this->defenseStudentSession($ctx['student']))
            ->putJson("/api/student/defense-readiness/sections/{$section->id}/content", [
                'content' => 'Draft content for the section',
            ]);

        $response->assertOk();
        $this->assertSame('Draft content for the section', $section->fresh()->content);
    }

    public function test_student_submit_endpoint_succeeds(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->getService()->autosaveSection($document, $section, str_repeat('word ', 600));

        $response = $this->withSession($this->defenseStudentSession($ctx['student']))
            ->postJson("/api/student/defense-readiness/sections/{$section->id}/submit");

        $response->assertOk();
        $this->assertSame('submitted', $section->fresh()->status);
    }

    public function test_supervisor_documents_endpoint_returns_list(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $this->getService()->documentForStudent($ctx['student']);

        $response = $this->withSession($this->defenseSupervisorSession($ctx['supervisor']))
            ->get('/api/supervisor/manuscripts');

        $response->assertOk()
            ->assertJsonStructure(['documents', 'total']);

        $this->assertSame(1, $response->json('total'));
    }

    public function test_supervisor_section_endpoint_returns_reviews(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->getService()->autosaveSection($document, $section, str_repeat('word ', 600));
        $this->getService()->submitSection($document, $section, $ctx['studentUser']);

        $response = $this->withSession($this->defenseSupervisorSession($ctx['supervisor']))
            ->get("/api/supervisor/manuscripts/{$document->id}");

        $response->assertOk();
        $sections = $response->json('sections');
        $this->assertGreaterThan(0, count($sections));
    }

    public function test_supervisor_decision_endpoint_accepts_section(): void
    {
        Mail::fake();

        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->getService()->autosaveSection($document, $section, str_repeat('word ', 600));
        $this->getService()->submitSection($document, $section, $ctx['studentUser']);

        $response = $this->withSession($this->defenseSupervisorSession($ctx['supervisor']))
            ->postJson("/api/supervisor/sections/{$section->id}/decision", [
                'action' => 'accepted',
                'comment' => 'Good work',
            ]);

        $response->assertOk();
        $this->assertSame('accepted', $section->fresh()->status);
    }

    public function test_supervisor_decision_endpoint_rejects_section(): void
    {
        Mail::fake();

        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->getService()->autosaveSection($document, $section, str_repeat('word ', 600));
        $this->getService()->submitSection($document, $section, $ctx['studentUser']);

        $response = $this->withSession($this->defenseSupervisorSession($ctx['supervisor']))
            ->postJson("/api/supervisor/sections/{$section->id}/decision", [
                'action' => 'rejected',
                'comment' => 'Does not meet standards',
            ]);

        $response->assertOk();
        $this->assertSame('rejected', $section->fresh()->status);
    }

    public function test_student_settings_endpoint_returns_current_settings(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $this->getService()->documentForStudent($ctx['student']);

        $response = $this->withSession($this->defenseStudentSession($ctx['student']))
            ->get('/api/student/defense-readiness/settings');

        $response->assertOk()
            ->assertJsonStructure(['study_approach', 'primary_data_collection']);
    }

    public function test_student_can_update_settings_while_in_draft(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);

        $response = $this->withSession($this->defenseStudentSession($ctx['student']))
            ->patchJson('/api/student/defense-readiness/settings', [
                'study_approach' => 'qualitative',
                'primary_data_collection' => true,
            ]);

        $response->assertOk();
        $this->assertSame('qualitative', $document->fresh()->study_approach);
        $this->assertTrue($document->fresh()->primary_data_collection);
    }

    public function test_student_cannot_update_settings_after_submitting_section(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->getService()->autosaveSection($document, $section, str_repeat('word ', 600));
        $this->getService()->submitSection($document, $section, $ctx['studentUser']);

        $response = $this->withSession($this->defenseStudentSession($ctx['student']))
            ->patchJson('/api/student/defense-readiness/settings', [
                'study_approach' => 'qualitative',
                'primary_data_collection' => true,
            ]);

        $response->assertStatus(422);
    }

    public function test_student_can_add_custom_section(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $this->getService()->documentForStudent($ctx['student']);

        $response = $this->withSession($this->defenseStudentSession($ctx['student']))
            ->postJson('/api/student/defense-readiness/sections', [
                'title' => 'Supplemental Analysis',
                'guidance' => 'Optional additional analysis',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('defense_readiness_sections', [
            'title' => 'Supplemental Analysis',
            'kind' => 'custom',
        ]);
    }
}
