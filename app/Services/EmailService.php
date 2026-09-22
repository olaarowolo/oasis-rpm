<?php

namespace App\Services;

use App\Models\University;

class EmailService
{
    protected University $university;

    public function __construct(University $university)
    {
        $this->university = $university;
    }

    public function sendWelcomeStudent($studentEmail, $studentName): bool
    {
        return $this->sendEmail(
            $studentEmail,
            'Welcome to Research Supervision Portal',
            "Welcome {$studentName}! You have been enrolled in the Research Supervision Portal."
        );
    }

    public function sendWelcomeSupervisor($supervisorEmail, $supervisorName): bool
    {
        return $this->sendEmail(
            $supervisorEmail,
            'Welcome to Research Supervision Portal',
            "Welcome {$supervisorName}! You are now set up as a supervisor in the Research Supervision Portal."
        );
    }

    public function sendProposalSubmitted($studentEmail, $proposalTitle): bool
    {
        return $this->sendEmail(
            $studentEmail,
            'Research Proposal Submitted - Awaiting Review',
            "Your proposal '{$proposalTitle}' has been submitted and is awaiting supervisor review."
        );
    }

    public function sendProposalApproved($studentEmail, $proposalTitle, $supervisorComment = null): bool
    {
        $message = "Congratulations! Your research proposal '{$proposalTitle}' has been approved!";
        if ($supervisorComment) {
            $message .= "\n\nSupervisor's comment: {$supervisorComment}";
        }
        return $this->sendEmail($studentEmail, 'Research Proposal Approved!', $message);
    }

    public function sendProposalRevisionRequired($studentEmail, $proposalTitle, $feedback): bool
    {
        $message = "Your research proposal '{$proposalTitle}' requires revision.\n\nFeedback:\n{$feedback}";
        return $this->sendEmail($studentEmail, 'Research Proposal - Revision Required', $message);
    }

    public function sendMeetingScheduled($studentEmail, $meetingDate, $mode): bool
    {
        return $this->sendEmail(
            $studentEmail,
            'Supervision Meeting Scheduled',
            "A supervision meeting has been scheduled for {$meetingDate} via {$mode}."
        );
    }

    public function sendMeetingReminder($studentEmail, $meetingDate, $time): bool
    {
        return $this->sendEmail(
            $studentEmail,
            'Reminder: Supervision Meeting Tomorrow',
            "Reminder: You have a supervision meeting tomorrow at {$time}."
        );
    }

    public function sendStageAdvanced($studentEmail, $stageName, $stageNumber): bool
    {
        return $this->sendEmail(
            $studentEmail,
            "Research Progress: Stage {$stageNumber} - {$stageName}",
            "Congratulations! You have advanced to Stage {$stageNumber}: {$stageName}"
        );
    }

    public function sendResourceAssigned($studentEmail, $resourceTitle, $section): bool
    {
        return $this->sendEmail(
            $studentEmail,
            'New Learning Resource Available',
            "A new learning resource '{$resourceTitle}' has been assigned to you in {$section}."
        );
    }

    public function sendAIResponse($studentEmail, $query, $response): bool
    {
        $message = "Your AI Assistant Query:\n{$query}\n\nResponse:\n{$response}";
        return $this->sendEmail($studentEmail, 'AI Assistant Response to Your Query', $message);
    }

    protected function sendEmail(string $to, string $subject, string $html): bool
    {
        try {
            $emailConfig = $this->university->email_config ?? [];
            $fromAddress = $emailConfig['from_address'] ?? config('mail.from.address');
            $fromName = $emailConfig['from_name'] ?? config('mail.from.name');

            // In production, use Mail::to($to)->send() with mailable classes
            // For now, log the email
            \Log::info("Email sent to {$to}: {$subject}");
            return true;
        } catch (\Exception $e) {
            \Log::error("Email send failed: " . $e->getMessage());
            return false;
        }
    }
}
