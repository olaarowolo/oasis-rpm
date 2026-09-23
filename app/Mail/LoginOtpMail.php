<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otpCode;
    public string $role;
    public string $name;

    public function __construct(string $otpCode, string $role, string $name)
    {
        $this->otpCode = $otpCode;
        $this->role = ucfirst(str_replace('_', ' ', $role));
        $this->name = $name;
    }

    public function build(): self
    {
        return $this
            ->subject('Your sign-in verification code')
            ->view('emails.login-otp', [
                'otpCode' => $this->otpCode,
                'role' => $this->role,
                'name' => $this->name,
            ])
            ->text('emails.login-otp-plain', [
                'otpCode' => $this->otpCode,
                'role' => $this->role,
                'name' => $this->name,
            ]);
    }
}
