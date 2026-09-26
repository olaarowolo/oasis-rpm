<?php

namespace Tests\Feature;

use App\Models\DefenseReadinessDocument;
use App\Models\DefenseReadinessReview;
use App\Mail\PortalEmail;
use App\Services\DefenseReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DefenseReadinessSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function getService(): DefenseReadinessService
    {
        return new DefenseReadinessService();
    }

    protected function prepareSection(DefenseReadinessDocument $document, $section, $ctx): void
    {
        $service = $this->getService();
        $service->autosaveSection($document, $section, str_repeat('word ', 600));
        $service->submitSection($document, $section, $ctx['studentUser']);
    }

    public function test_submit_section_with_insufficient_word_count_still_submits(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $version = $this->getService()->submitSection($document, $section, $ctx['studentUser']);

        $this->assertNotNull($version);
        $this->assertSame(1, $version->version_number);
        $this->assertSame('submitted', $section->fresh()->status);
    }

    public function test_submission_creates_notification_for_supervisor(): void
    {
        Mail::fake();

        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->prepareSection($document, $section, $ctx);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $ctx['supervisorUser']->id,
            'type' => 'defense_section_submitted',
        ]);

        Mail::assertSent(PortalEmail::class, function ($mail) use ($ctx, $section) {
            return $mail->viewName === 'defense-section-submitted'
                && $mail->hasTo($ctx['supervisorUser']->email);
        });
    }

    public function test_supervisor_can_accept_submitted_section(): void
    {
        Mail::fake();

        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->prepareSection($document, $section, $ctx);

        $this->getService()->decide($document, $section, $ctx['supervisorUser'], 'accepted', 'Looks good', null);

        $this->assertSame('accepted', $section->fresh()->status);
        $review = DefenseReadinessReview::where('section_id', $section->id)->latest()->first();
        $this->assertSame(DefenseReadinessReview::ACTION_ACCEPTED, $review->action);
        $this->assertSame($ctx['supervisor']->id, $review->reviewer_user_id);
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
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->prepareSection($document, $section, $ctx);

        $this->getService()->decide($document, $section, $ctx['supervisorUser'], 'revision_requested', 'Needs more detail', null);

        $this->assertSame('revision_requested', $section->fresh()->status);
        $review = DefenseReadinessReview::where('section_id', $section->id)->latest()->first();
        $this->assertSame(DefenseReadinessReview::ACTION_REVISION_REQUESTED, $review->action);

        Mail::assertSent(PortalEmail::class, function ($mail) use ($document) {
            return $mail->viewName === 'defense-section-revision'
                && $mail->hasTo($document->student->email);
        });
    }

    public function test_supervisor_can_reject_section(): void
    {
        Mail::fake();

        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->prepareSection($document, $section, $ctx);

        $this->getService()->decide($document, $section, $ctx['supervisorUser'], 'rejected', 'Off-topic', null);

        $this->assertSame('rejected', $section->fresh()->status);
        $document->refresh();
        $this->assertSame('halted', $document->status);
        $this->assertSame($section->id, $document->halted_section_id);

        Mail::assertSent(PortalEmail::class, function ($mail) use ($document) {
            return $mail->viewName === 'defense-section-rejected'
                && $mail->hasTo($document->student->email);
        });
    }

    public function test_supervisor_can_conditionally_approve(): void
    {
        Mail::fake();

        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->prepareSection($document, $section, $ctx);

        $conditionsString = json_encode(['Finalize your research gap', 'Add methodology details']);
        $this->getService()->decide($document, $section, $ctx['supervisorUser'], 'conditional', 'Address these conditions', $conditionsString);

        $this->assertSame('conditional', $section->fresh()->status);
        $this->assertNotNull($section->fresh()->conditions_acknowledged_at === null);

        Mail::assertSent(PortalEmail::class, function ($mail) use ($document) {
            return $mail->viewName === 'defense-section-conditional'
                && $mail->hasTo($document->student->email);
        });
    }

    public function test_release_halt_unlocks_document(): void
    {
        Mail::fake();

        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->prepareSection($document, $section, $ctx);

        $this->getService()->decide($document, $section, $ctx['supervisorUser'], 'rejected', 'Not good', null);
        $document->refresh();
        $this->assertSame('halted', $document->status);
        $this->assertNotNull($document->halted_section_id);

        $document->sections()->where('id', $section->id)->update(['status' => 'draft']);
        $this->getService()->releaseHalt($document);

        $document->refresh();
        $this->assertNull($document->halted_section_id);
        $this->assertNotSame('halted', $document->status);
    }

    public function test_decide_requires_comment_for_accepted(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->prepareSection($document, $section, $ctx);

        $this->expectException(\InvalidArgumentException::class);
        $this->getService()->decide($document, $section, $ctx['supervisorUser'], 'accepted', null, null);
    }

    public function test_decide_with_invalid_action_throws(): void
    {
        $ctx = $this->createDefenseReadinessContext();
        $document = $this->getService()->documentForStudent($ctx['student']);
        $section = $document->sections()->where('status', 'draft')->first();

        $this->prepareSection($document, $section, $ctx);

        $this->expectException(\InvalidArgumentException::class);
        $this->getService()->decide($document, $section, $ctx['supervisorUser'], 'halted', 'test', null);
    }
}
