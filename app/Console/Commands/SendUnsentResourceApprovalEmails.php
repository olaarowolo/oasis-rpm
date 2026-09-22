<?php

namespace App\Console\Commands;

use App\Mail\ResourceApprovalNotification;
use App\Models\Notification;
use App\Models\ResourceProgress;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendUnsentResourceApprovalEmails extends Command
{
    protected $signature = 'app:send-unsent-resource-approval-emails';

    protected $description = 'Send email notifications for all approved/rejected resources that don\'t have an email sent';

    public function handle()
    {
        $this->info('Finding unsent resource approval emails...');

        // Get all resource progress that are approved or rejected
        $resourceProgress = ResourceProgress::where('status', 'approved')
            ->orWhere('status', 'rejected')
            ->with('student', 'resource')
            ->get();

        $sentCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($resourceProgress as $progress) {
            // Check if notification exists for this progress
            $notification = Notification::where('user_id', $progress->student->user_id)
                ->where('metadata->resource_progress_id', $progress->id)
                ->first();

            if ($notification) {
                $this->info("Notification already exists for submission ID {$progress->id} - skipping");
                $skippedCount++;
                continue;
            }

            // Create notification
            $type = $progress->status === 'approved' ? Notification::TYPE_RESOURCE_APPROVED : Notification::TYPE_RESOURCE_REJECTED;
            $notification = Notification::create([
                'university_id' => $progress->student->university_id,
                'user_id' => $progress->student->user_id,
                'type' => $type,
                'title' => $progress->status === 'approved' ? 'Resource Approved' : 'Resource Rejected',
                'message' => 'Your resource submission "' . $progress->resource->title . '" has been ' . $progress->status . '.',
                'is_read' => false,
                'metadata' => [
                    'resource_id' => $progress->resource->id,
                    'resource_progress_id' => $progress->id,
                    'points_earned' => $progress->points_earned,
                ],
            ]);

            // Send email
            try {
                Mail::to($progress->student->email)->send(new ResourceApprovalNotification(
                    $progress->resource->title,
                    $progress->status,
                    $progress->status === 'approved' ? $progress->points_earned : null,
                    $progress->supervisor_comment ?? null
                ));

                $this->info("Email sent to {$progress->student->email} for resource: {$progress->resource->title}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("Failed to send email to {$progress->student->email}: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->info('');
        $this->info('=== Summary ===');
        $this->info("Total processed: " . $resourceProgress->count());
        $this->info("Emails sent: $sentCount");
        $this->info("Notifications created: $sentCount");
        $this->info("Skipped (already sent): $skippedCount");
        $this->info("Failed: $failedCount");

        return Command::SUCCESS;
    }
}
