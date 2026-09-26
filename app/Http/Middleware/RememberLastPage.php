<?php

namespace App\Http\Middleware;

use App\Services\ErrorAlertService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Keeps a note of the last page a user loaded successfully so the error page
 * has somewhere sensible to return them to, even without a referer header.
 */
class RememberLastPage
{
    public function __construct(private readonly ErrorAlertService $errorAlerts) {}

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if ($response->getStatusCode() >= 400) {
            return;
        }

        try {
            $this->errorAlerts->rememberReturnUrl($request);
        } catch (Throwable $e) {
            // Never let bookkeeping interfere with a request.
        }
    }
}
