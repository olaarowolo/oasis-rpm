<?php

namespace Tests\Feature;

use App\Mail\ErrorAlertMail;
use App\Mail\PortalEmail;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class EmailViewsTest extends TestCase
{
    public function test_all_portal_email_views_render(): void
    {
        $views = [
            'test',
            'topic-submitted',
            'topic-approved',
            'topic-revision',
            'topic-conditionally-approved',
            'meeting-status',
            'stage-advanced',
            'digest',
            'resource-submitted',
            'resource-approved',
            'resource-rejected',
            'supervision-linked',
            'defense-section-submitted',
            'defense-section-accepted',
            'defense-section-conditional',
            'defense-section-revision',
            'defense-section-rejected',
            'defense-comment',
            'defense-completed',
        ];

        foreach ($views as $view) {            $mail = new PortalEmail($view, [
                'studentName' => 'Ada Okafor',
                'topic' => 'AI for Student Mentoring',
                'resourceTitle' => 'Project Planning Guide',
                'meetingNumber' => 'M-001',
                'logId' => 'LOG-001',
                'stageName' => 'Proposal Defense Readiness',
                'stage' => 3,
                'total' => 11,
                'pendingProposals' => 2,
                'pendingLogs' => 3,
                'pendingResources' => 1,
                'totalStudents' => 10,
                'points' => 10,
                'approvedDate' => '2026-09-21',
                'reviewedDate' => '2026-09-21',
                'feedback' => 'Please revise and resubmit this section.',
                'comment' => 'Keep the scope focused.',
                'conditions' => "- Finalize your research gap\n- Add a method section",
                'reviewerName' => 'Dr. Bola Adeyemi',
                'statusLabel' => 'ACCEPTED',
                'recipientName' => 'Ada Okafor',
                'introText' => 'You have been linked to a supervisor in the portal.',
                'counterpartName' => 'Dr. Bola Adeyemi',
                'counterpartRole' => 'Assigned supervisor',
                'counterpartMeta' => 'Mass Communication',
                'relationshipNote' => 'Review the dashboard for your updated supervision workflow.',
                'ctaLabel' => 'Open the portal',
                'url' => 'https://example.com/portal',
            ]);

            $html = $mail->renderHtml();

            $this->assertIsString($html);
            $this->assertStringContainsString('TheOAsis', $html);
        }
    }

    public function test_error_alert_view_renders_for_both_sources(): void
    {
        $context = [
            'source' => 'automatic',
            'reference_code' => 'SUP-AB12CD',
            'environment' => 'production',
            'level' => 'CRITICAL',
            'exception' => RuntimeException::class,
            'message' => 'Call to a member function on null',
            'origin' => '/app/Http/Controllers/ProposalController.php:118',
            'occurred_at' => '2026-09-26T14:00:00+00:00',
            'trace' => [
                ['file' => '/app/Http/Controllers/ProposalController.php', 'line' => 118, 'call' => 'App\Http\Controllers\ProposalController->store'],
            ],
            'request' => [
                'method' => 'POST',
                'url' => 'https://supervise.afriscribe.org/api/student/proposals',
                'route' => 'student.proposals.store',
                'ip' => '203.0.113.9',
                'headers' => ['user-agent' => 'Mozilla/5.0'],
                'input' => ['topic' => 'AI and supervision', 'password' => '[redacted]'],
            ],
            'identity' => ['user_id' => 12, 'role' => 'student'],
            'user_note' => null,
        ];

        $automatic = (new ErrorAlertMail($context))->renderHtml();
        $this->assertStringContainsString('SUP-AB12CD', $automatic);
        $this->assertStringContainsString('AI and supervision', $automatic);
        $this->assertStringContainsString('Call to a member function on null', $automatic);
        $this->assertStringNotContainsString('What the user was doing', $automatic);

        $userReport = array_merge($context, [
            'source' => 'user-report',
            'user_note' => 'I was saving my proposal when the page stopped responding.',
        ]);

        $report = (new ErrorAlertMail($userReport))->renderHtml();
        $this->assertStringContainsString('What the user was doing', $report);
        $this->assertStringContainsString('stopped responding', $report);
    }

    public function test_demo_request_submission_sends_email_and_returns_success_message(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/demo/request', [
            'name' => 'Ada Okafor',
            'email' => 'ada@example.com',
            'university_code' => 'LASU',
            'department' => 'Computer Science',
            'notes' => 'We want to review the student portal.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Demo request submitted successfully. We will contact you shortly.');

        Mail::assertSentCount(1);
    }
}
