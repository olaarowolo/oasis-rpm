<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ErrorAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(public array $context, public ?string $fromAddress = null) {}

    public function build(): self
    {
        return $this
            ->subject($this->resolveSubject())
            ->from($this->fromAddress ?: config('mail.from.address'), config('mail.from.name'))
            ->view('emails.portal.error-alert', ['context' => $this->context]);
    }

    public function renderHtml(): string
    {
        return view('emails.portal.error-alert', ['context' => $this->context])->render();
    }

    public function isUserReport(): bool
    {
        return ($this->context['source'] ?? 'automatic') === 'user-report';
    }

    public function resolveSubject(): string
    {
        $reference = (string) ($this->context['reference_code'] ?? 'SUP-UNKNOWN');
        $exception = class_basename((string) ($this->context['exception'] ?? 'Error'));
        $level = (string) ($this->context['level'] ?? 'ERROR');
        $message = trim(preg_replace('/\s+/', ' ', (string) ($this->context['message'] ?? '')) ?? '');

        if ($this->isUserReport()) {
            $page = (string) (data_get($this->context, 'request.url') ?? data_get($this->context, 'page_url') ?? 'the portal');

            return "[supervise] {$reference} USER REPORT — {$page}";
        }

        return "[supervise] {$reference} {$level} {$exception} — ".Str::limit($message, 120);
    }
}
