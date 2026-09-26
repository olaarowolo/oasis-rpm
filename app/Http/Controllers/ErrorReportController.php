<?php

namespace App\Http\Controllers;

use App\Services\ErrorAlertService;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ErrorReportController extends Controller
{
    use ValidatesRequests;

    public function __construct(private readonly ErrorAlertService $errorAlerts) {}

    /**
     * Receives the optional note a user adds on the error page and emails it,
     * with the stored diagnostic context, to the support mailbox.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reference' => ['required', 'string', 'max:24', 'regex:/^SUP-[A-Z0-9]{4,12}$/'],
            'user_note' => ['nullable', 'string', 'max:1000'],
            'page_url' => ['nullable', 'string', 'max:500'],
        ], [
            'reference.regex' => 'That reference code is not valid.',
        ]);

        if ($this->isHoneypotFilled($request) || ! $this->pageIsTrusted($validated['page_url'] ?? null)) {
            return $this->failure('We could not send that report.');
        }

        $reference = strtoupper(trim((string) $validated['reference']));
        $context = $this->contextFor($reference, $request, $validated);

        try {
            $this->errorAlerts->send($context);
        } catch (Throwable $e) {
            Log::warning('User error report could not be sent: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you. Report '.$reference.' is now with our support team.',
        ]);
    }

    public function supportEmail(): string
    {
        return (string) config('error_reporting.page.support_email', 'tech@olaarowolo.com');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function contextFor(string $reference, Request $request, array $validated): array
    {
        $stored = $this->errorAlerts->storedContext($reference) ?? [];

        $note = trim((string) ($validated['user_note'] ?? ''));

        $context = array_merge([
            'source' => 'user-report',
            'reference_code' => $reference,
            'environment' => app()->environment(),
            'level' => 'ERROR',
            'exception' => 'User reported error',
            'message' => 'A user submitted a report from the error page.',
            'origin' => null,
            'occurred_at' => now()->toIso8601String(),
            'trace' => [],
            'identity' => [],
            'request' => null,
        ], $stored, [
            'source' => 'user-report',
            'reference_code' => $reference,
            'user_note' => $note === '' ? null : $note,
        ]);

        if (filled($validated['page_url'] ?? null)) {
            $context['reported_page_url'] = $validated['page_url'];
        }

        if (! $request->hasSession()) {
            return $context;
        }

        $context['identity'] = array_filter(array_merge($context['identity'] ?? [], [
            'user_id' => $request->session()->get('user_id'),
            'role' => $request->session()->get('role'),
            'university_id' => $request->session()->get('university_id'),
        ]), fn ($value) => $value !== null && $value !== '');

        return $context;
    }

    protected function failure(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 422);
    }

    protected function isHoneypotFilled(Request $request): bool
    {
        return filled($request->input('website')) || filled($request->input('url'));
    }

    protected function pageIsTrusted(?string $url): bool
    {
        if (blank($url)) {
            return true;
        }

        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return false;
        }

        $allowedHosts = array_filter([
            parse_url((string) config('app.url'), PHP_URL_HOST),
            request()->getHost(),
        ]);

        foreach ($allowedHosts as $allowed) {
            if (Str::lower((string) $allowed) === Str::lower($host)) {
                return true;
            }
        }

        return false;
    }
}
