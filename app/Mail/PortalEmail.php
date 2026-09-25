<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PortalEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $viewName;

    public array $data;

    public function __construct(string $viewName, array $data = [])
    {
        $this->viewName = $viewName;
        $this->data = $data;
    }

    public function build(): self
    {
        $payload = $this->resolveViewPayload();

        return $this
            ->subject($payload['subject'])
            ->view('emails.portal.'.$this->viewName, $payload['data']);
    }

    public function renderHtml(): string
    {
        $payload = $this->resolveViewPayload();

        return view('emails.portal.'.$this->viewName, $payload['data'])->render();
    }

    protected function resolveViewPayload(): array
    {
        $defaultUniversityCode = strtoupper((string) (config('universities.default', 'LASU')));
        $universityCode = strtoupper((string) ($this->data['universityCode'] ?? $defaultUniversityCode));
        $universityConfig = config('universities.presets.'.$universityCode)
            ?: config('universities.presets.'.$defaultUniversityCode);

        $context = [
            'universityCode' => $universityCode,
            'universityName' => $universityConfig['name'] ?? $universityCode,
            'department' => $universityConfig['department'] ?? 'Research Affairs',
            'portalName' => $this->data['portalName'] ?? 'Research Supervision Portal',
            'portalBrand' => $this->data['portalBrand'] ?? 'TheOAsis',
            'supervisorName' => $this->data['supervisorName'] ?? 'Supervisor',
            'brandDark' => $this->data['brandDark'] ?? '#002744',
            'brandBlue' => $this->data['brandBlue'] ?? '#035388',
            'brandGold' => $this->data['brandGold'] ?? '#f59e0b',
            'ink' => $this->data['ink'] ?? '#1f2937',
            'muted' => $this->data['muted'] ?? '#6b7280',
            'line' => $this->data['line'] ?? '#e5e7eb',
            'bg' => $this->data['bg'] ?? '#f0f4f8',
        ];

        $viewMap = [
            'test' => [
                'subject' => $universityCode.' Portal — test email',
                'data' => [
                    'title' => 'Test email',
                    'bodyText' => 'This confirms the Research Supervision Portal can send email successfully.',
                ],
            ],
            'demo-request' => [
                'subject' => 'Demo Request - '.$universityCode.' Research Supervision Portal',
                'data' => [
                    'title' => 'Demo Request',
                    'message' => 'You have received a new demo request.',
                    'name' => $this->data['name'] ?? 'N/A',
                    'email' => $this->data['email'] ?? 'N/A',
                    'universityName' => $universityCode,
                    'universityCode' => $universityCode,
                    'department' => $this->data['department'] ?? 'N/A',
                    'notes' => $this->data['notes'] ?? '',
                    'portalName' => $this->data['portalName'] ?? 'Research Supervision Portal',
                    'portalBrand' => $this->data['portalBrand'] ?? 'TheOAsis',
                ],
            ],
            'account-invite' => [
                'subject' => 'Complete your account setup for '.($this->data['universityName'] ?? $universityCode),
                'data' => [
                    'title' => 'Complete your account setup',
                    'name' => $this->data['name'] ?? 'User',
                    'roleLabel' => $this->data['roleLabel'] ?? 'User',
                    'completionUrl' => $this->data['completionUrl'] ?? '',
                    'expiresAt' => $this->data['expiresAt'] ?? '',
                ],
            ],
            'student-welcome' => [
                'subject' => 'Welcome to the '.($this->data['universityName'] ?? $universityCode).' Research Supervision Portal',
                'data' => [
                    'title' => 'Welcome to the Research Supervision Portal',
                    'name' => $this->data['name'] ?? 'Student',
                    'roleLabel' => $this->data['roleLabel'] ?? 'Student',
                    'matric' => $this->data['matric'] ?? '',
                    'universityCode' => $universityCode,
                    'supervisorName' => $this->data['supervisorName'] ?? null,
                    'loginUrl' => $this->data['loginUrl'] ?? '',
                    'loginHint' => $this->data['loginHint'] ?? '',
                ],
            ],
            'topic-submitted' => [
                'subject' => 'New topic proposal from '.($this->data['studentName'] ?? 'a student'),
                'data' => [
                    'title' => 'New topic proposal',
                    'studentName' => $this->data['studentName'] ?? 'Student',
                    'topic' => $this->data['topic'] ?? '',
                    'matric' => $this->data['matric'] ?? '',
                    'proposalId' => $this->data['proposalId'] ?? '',
                    'abstract' => $this->data['abstract'] ?? '',
                    'url' => $this->data['url'] ?? '',
                ],
            ],
            'topic-approved' => [
                'subject' => 'Your research topic has been APPROVED',
                'data' => [
                    'title' => 'Topic approved',
                    'studentName' => $this->data['studentName'] ?? 'Student',
                    'topic' => $this->data['topic'] ?? '',
                    'comment' => $this->data['comment'] ?? '',
                    'approvedDate' => $this->data['approvedDate'] ?? '',
                    'url' => $this->data['url'] ?? '',
                ],
            ],
            'topic-revision' => [
                'subject' => 'Revision requested on your topic proposal',
                'data' => [
                    'title' => 'Revision requested',
                    'studentName' => $this->data['studentName'] ?? 'Student',
                    'topic' => $this->data['topic'] ?? '',
                    'comment' => $this->data['comment'] ?? '',
                    'url' => $this->data['url'] ?? '',
                ],
            ],
            'topic-conditionally-approved' => [
                'subject' => 'Your topic proposal has been CONDITIONALLY APPROVED',
                'data' => [
                    'title' => 'Conditionally approved',
                    'studentName' => $this->data['studentName'] ?? 'Student',
                    'topic' => $this->data['topic'] ?? '',
                    'conditions' => $this->data['conditions'] ?? '',
                    'comment' => $this->data['comment'] ?? '',
                    'approvedDate' => $this->data['approvedDate'] ?? '',
                    'url' => $this->data['url'] ?? '',
                ],
            ],
            'meeting-status' => [
                'subject' => 'Meeting log '.($this->data['meetingNumber'] ?? '').' — '.($this->data['status'] ?? 'PENDING'),
                'data' => [
                    'title' => 'Meeting log update',
                    'studentName' => $this->data['studentName'] ?? 'Student',
                    'meetingNumber' => $this->data['meetingNumber'] ?? '',
                    'logId' => $this->data['logId'] ?? '',
                    'feedback' => $this->data['feedback'] ?? '',
                    'status' => $this->data['status'] ?? 'PENDING',
                    'url' => $this->data['url'] ?? '',
                    'isSupervisorNote' => ($this->data['event'] ?? '') === 'SUBMITTED',
                ],
            ],
            'stage-advanced' => [
                'subject' => 'Research stage updated: '.($this->data['stageName'] ?? ''),
                'data' => [
                    'title' => 'Research stage updated',
                    'studentName' => $this->data['studentName'] ?? 'Student',
                    'stage' => $this->data['stage'] ?? '',
                    'total' => $this->data['total'] ?? '',
                    'stageName' => $this->data['stageName'] ?? '',
                    'note' => $this->data['note'] ?? '',
                    'url' => $this->data['url'] ?? '',
                ],
            ],
            'digest' => [
                'subject' => 'Daily supervision digest — '.($this->data['pendingProposals'] ?? 0).' proposal(s), '.($this->data['pendingLogs'] ?? 0).' log(s) pending'.(($this->data['pendingResources'] ?? 0) > 0 ? ', '.($this->data['pendingResources'] ?? 0).' resource(s) awaiting review' : ''),
                'data' => [
                    'title' => 'Daily supervision digest',
                    'pendingProposals' => $this->data['pendingProposals'] ?? 0,
                    'pendingLogs' => $this->data['pendingLogs'] ?? 0,
                    'pendingResources' => $this->data['pendingResources'] ?? 0,
                    'totalStudents' => $this->data['totalStudents'] ?? 0,
                    'url' => $this->data['url'] ?? '',
                ],
            ],
            'resource-submitted' => [
                'subject' => 'Resource completion submitted by '.($this->data['studentName'] ?? 'a student'),
                'data' => [
                    'title' => 'Resource completion submitted',
                    'studentName' => $this->data['studentName'] ?? 'Student',
                    'matric' => $this->data['matric'] ?? '',
                    'resourceTitle' => $this->data['resourceTitle'] ?? '',
                    'points' => $this->data['points'] ?? 0,
                    'submittedDate' => $this->data['submittedDate'] ?? '',
                    'url' => $this->data['url'] ?? '',
                ],
            ],
            'resource-approved' => [
                'subject' => 'Your resource completion has been APPROVED',
                'data' => [
                    'title' => 'Resource approved',
                    'studentName' => $this->data['studentName'] ?? 'Student',
                    'resourceTitle' => $this->data['resourceTitle'] ?? '',
                    'points' => $this->data['points'] ?? 0,
                    'feedback' => $this->data['feedback'] ?? '',
                    'reviewedDate' => $this->data['reviewedDate'] ?? '',
                    'url' => $this->data['url'] ?? '',
                ],
            ],
            'resource-rejected' => [
                'subject' => 'Revision requested on your resource completion',
                'data' => [
                    'title' => 'Resource revision requested',
                    'studentName' => $this->data['studentName'] ?? 'Student',
                    'resourceTitle' => $this->data['resourceTitle'] ?? '',
                    'feedback' => $this->data['feedback'] ?? '',
                    'url' => $this->data['url'] ?? '',
                ],
            ],
            'supervision-linked' => [
                'subject' => $this->data['subject'] ?? 'Supervision relationship updated',
                'data' => [
                    'title' => $this->data['title'] ?? 'Supervision relationship updated',
                    'recipientName' => $this->data['recipientName'] ?? 'Portal user',
                    'introText' => $this->data['introText'] ?? 'A supervision relationship has been updated in the portal.',
                    'counterpartName' => $this->data['counterpartName'] ?? 'Assigned contact',
                    'counterpartRole' => $this->data['counterpartRole'] ?? 'Relationship contact',
                    'counterpartMeta' => $this->data['counterpartMeta'] ?? '',
                    'relationshipNote' => $this->data['relationshipNote'] ?? '',
                    'ctaLabel' => $this->data['ctaLabel'] ?? 'Open the portal',
                    'url' => $this->data['url'] ?? '',
                ],
            ],
        ];

        $view = $viewMap[$this->viewName] ?? [
            'subject' => $universityCode.' Portal notification',
            'data' => ['title' => 'Notification', 'message' => $this->data['message'] ?? 'You have a new notification.'],
        ];

        $view['data'] = array_merge($context, $view['data']);

        return $view;
    }
}
