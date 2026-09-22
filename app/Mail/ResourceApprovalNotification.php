<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResourceApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $resourceTitle;
    public $status;
    public $pointsEarned;
    public $comment;

    /**
     * Create a new message instance.
     */
    public function __construct($resourceTitle, $status, $pointsEarned = null, $comment = null)
    {
        $this->resourceTitle = $resourceTitle;
        $this->status = $status;
        $this->pointsEarned = $pointsEarned;
        $this->comment = $comment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved' 
            ? 'Resource Approved - TheOAsis Portal'
            : 'Resource Rejected - TheOAsis Portal';
            
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.resource-approval',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
