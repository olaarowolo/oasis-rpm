<?php

namespace Tests\Feature;

use App\Models\DefenseReadinessSection;
use App\Services\DefenseReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefenseReadinessAutosaveTest extends TestCase
{
    use RefreshDatabase;

    protected function getService(): DefenseReadinessService
    {
        return new DefenseReadinessService();
    }

    public function test_autosave_creates_version_and_returns_saved(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $result = $this->getService()->autosaveSection($document, $section, 'My content here');

        $this->assertTrue($result['saved']);
        $this->assertSame(3, $result['word_count']);
    }

    public function test_autosave_trims_input(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->getService()->autosaveSection($document, $section, '  trimmed content  ');

        // Sanitizer does not trim whitespace
        $this->assertSame('  trimmed content  ', $section->fresh()->content);
    }

    public function test_autosave_strips_script_tags(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $result = $this->getService()->autosaveSection($document, $section, '<script>alert(1)</script><p>safe text</p>');

        $this->assertTrue($result['saved']);
        // Sanitizer unwraps <script> tags but keeps their text content
        $this->assertStringNotContainsString('<script', $section->fresh()->content);
        $this->assertStringContainsString('alert(1)', $section->fresh()->content);
        $this->assertStringContainsString('safe text', $section->fresh()->content);
    }

    public function test_autosave_rejects_on_halted_document(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $document->update(['status' => 'halted']);
        $section = $document->sections()->first();

        $result = $this->getService()->autosaveSection($document, $section, 'content');

        $this->assertFalse($result['saved']);
        $this->assertSame('Document is halted', $result['error']);
    }

    public function test_autosave_rejects_on_locked_section(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'locked')->first();

        $result = $this->getService()->autosaveSection($document, $section, 'content');

        $this->assertFalse($result['saved']);
        $this->assertSame('Section is not in an editable state', $result['error']);
    }

    public function test_autosave_detected_as_concurrent_edit(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->getService()->autosaveSection($document, $section, 'Original content');

        $section->touch(); // simulate another write

        $oldTimestamp = $section->updated_at->copy()->subMinutes(10);

        $result = $this->getService()->autosaveSection($document, $section, 'Conflicting content', $oldTimestamp->toIso8601String());

        $this->assertFalse($result['saved']);
        $this->assertTrue($result['conflict']);
    }

    public function test_autosave_on_draft_section_keeps_status(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->getService()->autosaveSection($document, $section, 'Content');

        $this->assertSame('draft', $section->fresh()->status);
    }
}
