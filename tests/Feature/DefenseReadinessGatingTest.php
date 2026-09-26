<?php

namespace Tests\Feature;

use App\Models\DefenseReadinessDocument;
use App\Models\DefenseReadinessSection;
use App\Services\DefenseReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefenseReadinessGatingTest extends TestCase
{
    use RefreshDatabase;

    protected function getService(): DefenseReadinessService
    {
        return new DefenseReadinessService();
    }

    public function test_first_top_level_section_is_unlocked_by_default(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();
        $document = $service->documentForStudent($ctx['student']);

        $firstSection = $document->sections()->whereNull('parent_id')->orderBy('position')->first();
        $this->assertTrue($service->isUnlocked($document, $firstSection));
    }

    public function test_second_section_is_locked_until_first_is_accepted(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();
        $document = $service->documentForStudent($ctx['student']);

        $sections = $document->sections()->whereNull('parent_id')->where('kind', 'fixed')->orderBy('position')->get();
        $secondSection = $sections[1] ?? null;
        $this->assertNotNull($secondSection);

        $this->assertFalse($service->isUnlocked($document, $secondSection));

        $firstSection = $sections[0];
        $service->autosaveSection($document, $firstSection, str_repeat('word ', 600));
        $service->submitSection($document, $firstSection, $ctx['studentUser']);

        $service->decide($document, $firstSection, $ctx['supervisorUser'], 'accepted', 'Good work', null);

        $document->refresh();
        $secondSection = $secondSection->fresh();
        $this->assertTrue($service->isUnlocked($document, $secondSection));
    }

    public function test_partial_completion_does_not_unlock_next_section(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();
        $document = $service->documentForStudent($ctx['student']);

        $sections = $document->sections()->whereNull('parent_id')->where('kind', 'fixed')->orderBy('position')->get();
        $secondSection = $sections[1] ?? null;

        $firstSection = $sections[0];
        $service->autosaveSection($document, $firstSection, str_repeat('word ', 600));
        $service->submitSection($document, $firstSection, $ctx['studentUser']);

        $service->decide($document, $firstSection, $ctx['supervisorUser'], 'revision_requested', 'Revise this', null);

        $document->refresh();
        $this->assertFalse($service->isUnlocked($document, $secondSection));
    }

    public function test_settings_editable_only_in_draft_with_no_submissions(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();
        $document = $service->documentForStudent($ctx['student']);

        $this->assertTrue($service->canUpdateSettings($document));

        $firstSection = $document->sections()->where('status', 'draft')->first();
        $service->autosaveSection($document, $firstSection, str_repeat('word ', 600));
        $service->submitSection($document, $firstSection, $ctx['studentUser']);

        $this->assertFalse($service->canUpdateSettings($document->fresh()));
    }

    public function test_can_update_settings_with_valid_approach(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();
        $document = $service->documentForStudent($ctx['student']);

        $result = $service->updateSettings($document, 'qualitative', true);

        $this->assertTrue($result);
        $this->assertSame('qualitative', $document->fresh()->study_approach);
        $this->assertTrue($document->fresh()->primary_data_collection);
    }

    public function test_cannot_update_settings_after_submission(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();
        $document = $service->documentForStudent($ctx['student']);

        $firstSection = $document->sections()->where('status', 'draft')->first();
        $service->autosaveSection($document, $firstSection, str_repeat('word ', 600));
        $service->submitSection($document, $firstSection, $ctx['studentUser']);

        $result = $service->updateSettings($document->fresh(), 'qualitative', true);
        $this->assertFalse($result);
    }
}
