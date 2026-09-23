<?php

namespace App\Http\Controllers;

use App\Mail\PortalEmail;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends BaseController
{
    public function submitDemoRequest(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'university_code' => 'required|string|max:50',
            'department' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);

        $recipient = env('DEMO_REQUEST_EMAIL', env('MAIL_USERNAME', 'support@afriscribe.org'));
        $universityCode = strtoupper(trim($validated['university_code']));
        $subject = 'Demo Request - ' . $universityCode . ' Research Supervision Portal';
        
        $body = implode(PHP_EOL, [
            'Hello Team,',
            '',
            'I would like to request a demo for the multi-tenant Research Supervision Portal.',
            '',
            'Name: ' . $validated['name'],
            'Email: ' . $validated['email'],
            'University Code: ' . $universityCode,
            'Department: ' . ($validated['department'] ?? 'N/A'),
            'Notes: ' . ($validated['notes'] ?? 'N/A'),
            '',
            'Thank you.',
        ]);

        try {
            $mail = new PortalEmail('demo-request', [
                'title' => $validated['name'],
                'message' => $validated['email'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'universityName' => $universityCode,
                'universityCode' => $universityCode,
                'department' => $validated['department'] ?? 'N/A',
                'notes' => $validated['notes'] ?? '',
                'portalName' => 'TheOAsis Research Supervision Portal',
                'portalBrand' => 'TheOAsis',
            ]);
            $mail->subject($subject);
            $mail->from(config('mail.from.address'), config('mail.from.name'));
            $mail->replyTo($validated['email'], $validated['name']);

            Mail::to($recipient)->send($mail);

            return $this->success(null, 'Demo request submitted successfully. We will contact you shortly.');
        } catch (\Throwable $e) {
            \Log::error('Demo request email failed: ' . $e->getMessage(), [
                'request' => $validated,
                'error' => $e->getMessage(),
            ]);

            return $this->error('We could not submit your demo request right now. Please try again later or email support@afriscribe.org.', 500);
        }
    }

    public function resendEmail(Request $request)
    {
        $validated = $request->validate([
            'email_type' => 'required|string',
            'recipient_email' => 'required|email',
        ]);

        return $this->success(null, 'Email resent successfully');
    }

    public function getUnreadNotifications(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        $notifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return $this->success($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return $this->error('Notification not found', 404);
        }

        $user = $request->user();
        if ($notification->user_id !== $user->id) {
            return $this->error('Unauthorized', 403);
        }

        $notification->markAsRead();

        return $this->success($notification);
    }
}
