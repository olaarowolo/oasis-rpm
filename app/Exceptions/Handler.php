<?php

namespace App\Exceptions;

use App\Services\ErrorAlertService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'new_password',
        'token',
        'otp',
        'api_key',
        'passphrase',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            app(ErrorAlertService::class)->handle($e);
        });
    }

    /**
     * Extra context for failures that happen outside an HTTP request.
     *
     * @return array<string, mixed>
     */
    protected function context(): array
    {
        $context = parent::context();

        if (! app()->runningInConsole()) {
            return $context;
        }

        $arguments = $this->consoleArguments();

        return array_filter([
            'command' => $arguments[0] ?? null,
            'arguments' => $this->redactConsoleInput($arguments),
        ], fn ($value) => $value !== null && $value !== []);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthenticationException && ($request->expectsJson() || $request->is('api/*'))) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please authenticate.',
                'redirect' => route('login'),
            ], 401);
        }

        // Framework owned responses keep their existing behaviour: validation
        // failures must still redirect back with errors, and a session timeout
        // must still send the user to the login screen.
        if ($exception instanceof ValidationException
            || $exception instanceof HttpResponseException
            || $exception instanceof AuthenticationException) {
            return parent::render($request, $exception);
        }

        if ($this->expectsJson($request)) {
            return $this->renderJson($request, $exception);
        }

        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        return $this->renderFriendlyPage($request, $this->prepareException($exception));
    }

    /**
     * A branded, customer focused error page. It never exposes an exception
     * class, file path or stack trace, only a reference code that also appears
     * in the alert email sent to the support mailbox.
     */
    protected function renderFriendlyPage(Request $request, Throwable $exception)
    {
        $status = $this->statusFor($exception);

        $service = app(ErrorAlertService::class);
        $reference = $service->referenceCode($exception);
        $homeUrl = $this->homeUrl();
        $target = $service->resolveReturnUrl($request, $homeUrl);

        return response()->view('errors.friendly', [
            'status' => $status,
            'reference' => $reference,
            'targetUrl' => $target['url'],
            'homeUrl' => $homeUrl,
            'countdownSeconds' => (int) config('error_reporting.page.countdown_seconds', 10),
            'pageUrl' => $request->fullUrl(),
            'supportEmail' => (string) config('error_reporting.page.support_email', 'tech@olaarowolo.com'),
        ], $status);
    }

    /**
     * @return array<string, mixed>
     */
    protected function renderJson(Request $request, Throwable $exception)
    {
        $status = $this->statusFor($exception);
        $message = $status === 404 ? 'The requested resource was not found.' : $this->publicMessage($status);

        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if (config('app.debug')) {
            $payload['exception'] = $exception::class;
            $payload['reference'] = app(ErrorAlertService::class)->referenceCode($exception);
        }

        return response()->json($payload, $status);
    }

    protected function statusFor(Throwable $exception): int
    {
        $status = match (true) {
            $exception instanceof ValidationException => 422,
            $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
            default => 500,
        };

        return ($status >= 400 && $status < 600) ? $status : 500;
    }

    protected function publicMessage(int $status): string
    {
        return match ($status) {
            401 => 'Unauthorized. Please authenticate.',
            403 => 'You do not have permission to perform this action.',
            404 => 'The requested resource was not found.',
            419 => 'Your session expired. Please refresh the page and try again.',
            429 => 'Too many requests. Please slow down and try again shortly.',
            default => 'Something went wrong on our side. Our support team has been notified.',
        };
    }

    protected function expectsJson(Request $request): bool
    {
        return $request->expectsJson() || $request->is('api/*');
    }

    protected function homeUrl(): string
    {
        $role = session('role');

        $route = match ($role) {
            'student' => 'student.dashboard',
            'supervisor' => 'supervisor.dashboard',
            'admin' => 'admin.dashboard',
            'super_admin' => 'super-admin.dashboard',
            default => null,
        };

        if ($route && app('router')->has($route)) {
            return route($route);
        }

        return session('role') ? route('home') : route('landing');
    }

    /**
     * @return array<int, string>
     */
    protected function consoleArguments(): array
    {
        $argv = $_SERVER['argv'] ?? [];

        if (! is_array($argv)) {
            return [];
        }

        // The first entry is the running script itself, not a command, and any
        // leading flags belong to the runner rather than to the command.
        $arguments = array_values(array_filter(
            array_map('strval', array_slice($argv, 1)),
            fn (string $argument) => $argument !== '' && ! str_starts_with($argument, '-')
        ));

        return $arguments;
    }

    /**
     * @param  array<int|string, mixed>  $values
     * @return array<int|string, mixed>
     */
    protected function redactConsoleInput(array $values): array
    {
        return app(ErrorAlertService::class)->redact($values);
    }
}
