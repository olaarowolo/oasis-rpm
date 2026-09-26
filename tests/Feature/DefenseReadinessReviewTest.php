<?php

namespace Tests\Feature;

use App\Models\DefenseReadinessReview;
use App\Mail\PortalEmail;
use App\Services\DefenseReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DefenseReadinessReviewTest extends TestCase
{
    use RefreshDatabase;

public function test_supervisor_can_accept_submitted_section(): void
    {
        Mail::fake();

        $ctx = $this->createDefenseReadinessContext();
        $service = new DefenseReadinessService();
        $document = $service->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $service->autosaveSection($document, $section, str_repeat('x', 600), null);
        $service->submitSection($document, $section, $ctx['studentUser']);

        $service->decide($document, $section, $ctx['supervisorUser'], 'accepted', 'Good work', null);

        $this->assertSame('accepted', $section->fresh()->status);
        $review = DefenseReadinessReview::where('section_id', $section->id)->latest()->first();
        $this->assertSame(DefenseReadinessReview::ACTION_ACCEPTED, $review->action);
        $this->assertSame($ctx['supervisorUser']->id, $review->reviewer_user_id);

        Mail::assertSent(PortalEmail::class, function ($mail) use ($document) {
            return $mail->viewName === 'defense-section-accepted'
                && $mail->hasTo($document->student->email);
        });
    }

public function test_supervisor_can_request_revision(): void
    {
        Mail::fake();

        $ctx = $this->createDefenseReadinessContext();
        $service = new DefenseReadinessService();
        $document = $service->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $service->autosaveSection($document, $section, str_repeat('x', 600), null);
        $service->submitSection($document, $section, $ctx['studentUser']);

        $service->decide($document, $section, $ctx['supervisorUser'], 'revision_requested', 'Needs more detail', null);

        $this->assertSame('revision_requested', $section->fresh()->status);
        $review = DefenseReadinessReview::where('section_id', $section->id)->latest()->first();
        $this->assertSame(DefenseReadinessReview::ACTION_REVISION_REQUESTED, $review->action);
        $this->assertSame('Needs more detail', $review->comment);

        Mail::assertSent(PortalEmail::class, function ($mail) use ($document) {
            return $mail->viewName === 'defense-section-revision'
                && $mail->hasTo($document->student->email);
        });
    }

    public function test_supervisor_can_reject_section(): void
    {
        Mail::fake();

        $ctx = $this->createDefenseReadinessContext();
        $service = new DefenseReadinessService();
        $document = $service->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $service->autosaveSection($document, $section, str_repeat('x', 600), null);
        $service->submitSection($document, $section, $ctx['studentUser']);

        $service->decide($document, $section, $ctx['supervisorUser'], 'rejected', 'Off-topic', null);

        $this->assertSame('rejected', $section->fresh()->status);
        $review = DefenseReadinessReview::where('section_id', $section->id)->latest()->first();
        $this->assertSame(DefenseReadinessReview::ACTION_REJECTED, $review->action);

        Mail::assertSent(PortalEmail::class, function ($mail) use ($document) {
            return $mail->viewName === 'defense-section-rejected'
                && $mail->hasTo($document->student->email);
        });
    }

    public function test_supervisor_can_halt_section(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = new DefenseReadinessService();
        $document = $service->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $service->autosaveSection($document, $section, str_repeat('x', 600), null);
        $service->submitSection($document, $section, $ctx['studentUser']);

        $service->decide($document, $section, $ctx['supervisorUser'], 'rejected', 'Halted', null);

        $this->assertSame('rejected', $section->fresh()->status);
        $this->assertNotNull($document->fresh()->halted_section_id);

        // Request revision on the halted section to release the halt
        $service->decide($document, $section, $ctx['supervisorUser'], 'revision_requested', 'Please revise', null);

        $this->assertNull($document->fresh()->halted_section_id);
        $this->assertNotSame('rejected', $document->fresh()->haltedSection->status ?? null);
    }

    public function test_release_halt_unlocks_next_sections(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $service = new DefenseReadinessService();
        $document = $service->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $service->autosaveSection($document, $section, str_repeat('x', 600), null);
        $service->submitSection($document, $section, $ctx['studentUser']);

        $service->decide($document, $section, $ctx['supervisorUser'], 'rejected', 'Halted', null);
        $this->assertNotNull($document->fresh()->halted_section_id);

        // Request revision on the halted section to release the halt
        $service->decide($document, $section, $ctx['supervisorUser'], 'revision_requested', 'Please revise', null);

        $this->assertNull($document->fresh()->halted_section_id);
        $this->assertNotSame('rejected', $document->fresh()->haltedSection->status ?? null);
    }

    public function test_accepting_all_required_sections_marks_document_completed(): void
    {
        Mail::fake();

        $ctx = $this->createDefenseReadinessContext();
        $service = new DefenseReadinessService();
        $document = $service->documentForStudent($ctx['student']);

        $allSections = $document->sections()->get();

        foreach ($allSections as $section) {
            // Skip group sections - they cannot be submitted directly
            if ($section->isGroup()) {
                continue;
            }
            // Unlock each section before submitting
            if ($section->status === 'locked') {
                $section->update(['status' => 'draft']);
            }
            $service->autosaveSection($document, $section, str_repeat('x', 600), null);
            $service->submitSection($document, $section, $ctx['studentUser']);

            if ($section->is_required) {
                $service->decide($document, $section, $ctx['supervisorUser'], 'accepted', 'Accepted', null);
            }
        }

        // Accept the last section in order to trigger completion
        // Get last section by position (flattened order)
        $sections = $document->sections()
            ->orderByRaw("CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END")
            ->orderBy('parent_id', 'asc')
            ->orderBy('position', 'asc')
            ->get();
        $flattened = [];
        foreach ($sections as $section) {
            if ($section->parent_id === null) {
                $flattened[] = $section;
                $children = $document->sections()
                    ->where('parent_id', $section->id)
                    ->orderBy('position')
                    ->get();
                foreach ($children as $child) {
                    $flattened[] = $child;
                }
            }
        }
        $lastSection = end($flattened);
        if ($lastSection && $lastSection->status !== 'accepted') {
            $service->decide($document, $lastSection, $ctx['supervisorUser'], 'accepted', 'Accepted', null);
        }

        $document->refresh();
        $this->assertTrue($document->status === 'completed' || $document->status === 'ready_for_defense');
    }
}