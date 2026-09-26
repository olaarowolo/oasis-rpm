<?php

namespace Tests\Feature;

use App\Models\DefenseReadinessDocument;
use App\Services\DefenseReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefenseReadinessSeedingTest extends TestCase
{
    use RefreshDatabase;

    protected function getService(): DefenseReadinessService
    {
        return new DefenseReadinessService();
    }

    public function test_seed_sections_creates_correct_sections_from_config(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();

        $document = $service->documentForStudent($ctx['student']);

        $expectedSections = config('defense_readiness.sections');
        $sectionKeys = $document->sections()->pluck('key')->toArray();

        foreach ($expectedSections as $section) {
            $this->assertContains($section['key'], $sectionKeys, "Section key {$section['key']} should be seeded");
        }

        $this->assertSame($ctx['student']->id, $document->student_id);
        $this->assertSame($ctx['student']->university_id, $document->university_id);
        $this->assertSame('draft', $document->status);
    }

    public function test_first_top_level_section_is_unlocked(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();
        $document = $service->documentForStudent($ctx['student']);

        $firstSection = $document->sections()->whereNull('parent_id')->orderBy('position')->first();

        $this->assertSame('draft', $firstSection->status);
        $this->assertTrue($service->isUnlocked($document, $firstSection));
    }

    public function test_other_sections_are_locked_initially(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();
        $document = $service->documentForStudent($ctx['student']);

        $lockedSections = $document->sections()->where('status', 'locked')->get();
        $this->assertGreaterThan(0, $lockedSections->count());
    }

    public function test_document_for_student_is_idempotent(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();

        $document1 = $service->documentForStudent($ctx['student']);
        $document2 = $service->documentForStudent($ctx['student']);

        $this->assertSame($document1->id, $document2->id);
    }

    public function test_seed_sections_use_config_titles(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();
        $document = $service->documentForStudent($ctx['student']);

        $firstSection = $document->sections()->whereNull('parent_id')->orderBy('position')->first();

        $expectedFirst = config('defense_readiness.sections')[0];
        $this->assertSame($expectedFirst['title'], $firstSection->title);
    }

    public function test_methodology_children_are_seeded(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();
        $document = $service->documentForStudent($ctx['student']);

        $methodology = $document->sections()->where('key', 'methodology')->first();
        $this->assertNotNull($methodology);
        $this->assertSame('group', $methodology->kind);
        $this->assertGreaterThan(0, $methodology->children()->count());
    }

    public function test_default_study_approach_is_quantitative(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = $this->getService();
        $document = $service->documentForStudent($ctx['student']);

        $this->assertNull($document->study_approach);
    }
}
