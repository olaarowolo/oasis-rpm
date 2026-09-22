<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $userName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $token, string $userName)
    {
        $this->token = $token;
        $this->userName = $userName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $resetUrl = config('app.url') . "/reset-password?token={$this->token}";

        return $this->subject('Password Reset Request - TheOAsis Research Portal')
            ->text('emails.password-reset')
            ->with([
                'resetUrl' => $resetUrl,
                'userName' => $this->userName,
                'token' => $this->token,
                'expiresIn' => '1 hour',
            ]);
    }
}
