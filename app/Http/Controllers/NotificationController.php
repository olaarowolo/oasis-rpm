<?php

namespace App\Http\Controllers;

use App\Mail\PortalEmail;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationController extends BaseController
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        $limit = max(1, min((int) $request->integer('limit', 12), 25));
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return $this->success([
            'unread_count' => $unreadCount,
            'items' => $notifications->map(function (Notification $notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'time' => optional($notification->created_at)->diffForHumans(),
                    'read' => $notification->is_read,
                    'action_url' => data_get($notification->metadata, 'action_url'),
                ];
            })->values(),
        ]);
    }

    public function submitDemoRequest(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'university_code' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);

        $recipient = env('DEMO_REQUEST_EMAIL', env('MAIL_USERNAME', 'support@afriscribe.org'));
        $universityCode = strtoupper(trim((string) ($validated['university_code'] ?? '')));
        $subject = ($universityCode !== '' ? 'Demo Request - ' . $universityCode : 'Demo Request')
            . ' Research Supervision Portal';

        $body = implode(PHP_EOL, [
            'Hello Team,',
            '',
            'I would like to request a demo for the multi-tenant Research Supervision Portal.',
            '',
            'Name: ' . $validated['name'],
            'Email: ' . $validated['email'],
            'University Code: ' . ($universityCode !== '' ? $universityCode : 'N/A'),
            'Department: ' . ($validated['department'] ?? 'N/A'),
            'Context: ' . ($validated['notes'] ?? 'N/A'),
            '',
            'Thank you.',
        ]);

        try {
            $mail = new PortalEmail('demo-request', [
                'title' => $validated['name'],
                'message' => $validated['email'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'universityName' => $universityCode !== '' ? $universityCode : 'Unspecified Institution',
                'universityCode' => $universityCode !== '' ? $universityCode : 'N/A',
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
            Log::error('Demo request email failed: ' . $e->getMessage(), [
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

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return $this->success([
            'unread_count' => 0,
        ], 'Notifications marked as read');
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
