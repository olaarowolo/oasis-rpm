<?php

namespace App\Services;

use App\Mail\ErrorAlertMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class ErrorAlertService
{
    /**
     * Guards against an exception raised while an alert is being sent
     * being turned into another alert.
     */
    private static bool $sending = false;

    public function handle(Throwable $e, ?Request $request = null): void
    {
        try {
            if (! $this->shouldAlert($e)) {
                return;
            }

            $context = $this->buildContext($e, $request);
            $this->storeContext($context);
            $this->send($context);
        } catch (Throwable $inner) {
            Log::warning('Error alert could not be prepared: '.$inner->getMessage());
        }
    }

    public function shouldAlert(Throwable $e): bool
    {
        if (! config('error_reporting.enabled')) {
            return false;
        }

        $environments = (array) config('error_reporting.environments', []);
        if ($environments !== [] && ! in_array(app()->environment(), $environments, true)) {
            return false;
        }

        foreach ((array) config('error_reporting.ignore_exceptions', []) as $ignored) {
            if ($e instanceof $ignored) {
                return false;
            }
        }

        return ! $this->isThrottled($this->fingerprint($e));
    }

    public function fingerprint(Throwable $e): string
    {
        $origin = $e->getFile().':'.$e->getLine();
        $frame = $e->getTrace()[0] ?? [];

        return md5($e::class.'|'.$origin.'|'.($frame['file'] ?? '').':'.($frame['line'] ?? ''));
    }

    /**
     * A short, deterministic code derived from the fingerprint. The same
     * failure always produces the same code, so the page the user sees and
     * the alert email can be matched without any storage.
     */
    public function referenceCode(Throwable $e): string
    {
        return 'SUP-'.strtoupper(substr(base_convert((string) crc32($this->fingerprint($e)), 10, 36), 0, 6));
    }

    public function isThrottled(string $fingerprint): bool
    {
        $window = (int) config('error_reporting.throttle_minutes', 10);
        $ceiling = (int) config('error_reporting.max_per_hour', 10);

        if ($window > 0 && ! Cache::add($this->throttleKey($fingerprint), true, now()->addMinutes($window))) {
            return true;
        }

        if ($ceiling > 0) {
            $store = Cache::store(config('error_reporting.cache_store'));
            $key = 'error_alert:hourly';
            $used = (int) $store->increment($key);
            if ($used === 1) {
                $store->put($key, 1, now()->addHour());
            }

            if ($used > $ceiling) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    public function buildContext(Throwable $e, ?Request $request = null): array
    {
        $request ??= request();

        return [
            'source' => 'automatic',
            'reference_code' => $this->referenceCode($e),
            'environment' => app()->environment(),
            'level' => $this->level($e),
            'exception' => $e::class,
            'message' => $this->truncate($e->getMessage(), 1000),
            'origin' => str_replace(base_path(), '', $e->getFile()).':'.$e->getLine(),
            'occurred_at' => now()->toIso8601String(),
            'fingerprint' => $this->fingerprint($e),
            'trace' => $this->trace($e),
            'request' => config('error_reporting.include_request') && $request instanceof Request
                ? $this->requestContext($request)
                : null,
            'identity' => $this->identity(),
            'user_note' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function send(array $context): void
    {
        if (self::$sending) {
            return;
        }

        $recipients = $this->recipients();
        if ($recipients === []) {
            return;
        }

        self::$sending = true;

        try {
            Mail::to($recipients)->send(new ErrorAlertMail(
                $context,
                (string) config('error_reporting.from_address')
            ));
        } catch (Throwable $e) {
            Log::warning('Error alert email failed to send: '.$e->getMessage());
        } finally {
            self::$sending = false;
        }
    }

    /**
     * Keeps the built context for a short window so a user report posted
     * from the error page can attach the real diagnostic details instead of
     * trusting whatever the browser sends.
     *
     * @param  array<string, mixed>  $context
     */
    public function storeContext(array $context): void
    {
        $code = $context['reference_code'] ?? null;
        $ttl = (int) config('error_reporting.page.context_cache_seconds', 600);

        if (! $code || $ttl <= 0) {
            return;
        }

        try {
            Cache::put('error_alert:context:'.$code, $context, now()->addSeconds($ttl));
        } catch (Throwable $e) {
            Log::warning('Error alert context could not be cached: '.$e->getMessage());
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function storedContext(string $referenceCode): ?array
    {
        try {
            $context = Cache::get('error_alert:context:'.strtoupper(trim($referenceCode)));
        } catch (Throwable $e) {
            return null;
        }

        return is_array($context) ? $context : null;
    }

    /**
     * Resolves where the user should be returned to after an error.
     *
     * @return array{url: string, source: string}
     */
    public function resolveReturnUrl(Request $request, ?string $fallback = null): array
    {
        $candidates = [];

        $referer = $request->headers->get('referer');
        if ($referer && $this->isSameHost($referer)) {
            $candidates[] = ['url' => $referer, 'source' => 'referer'];
        }

        $remembered = $request->hasSession() ? $request->session()->get('error_return_url') : null;
        if (is_string($remembered) && $remembered !== '') {
            $candidates[] = ['url' => $remembered, 'source' => 'session'];
        }

        $failingUrl = $this->currentUrl($request);

        foreach ($candidates as $candidate) {
            if ($this->isLoopRisk($request, $candidate['url'], $failingUrl)) {
                continue;
            }

            return $candidate;
        }

        return ['url' => $fallback ?: url('/'), 'source' => 'home'];
    }

    /**
     * A failing page must never bounce the user back into itself.
     */
    public function isLoopRisk(Request $request, string $target, ?string $failingUrl = null): bool
    {
        $targetPath = $this->normalisePath($target);

        if ($targetPath !== '' && $targetPath === $this->normalisePath($failingUrl ?? $this->currentUrl($request))) {
            return true;
        }

        if (! $request->hasSession()) {
            return false;
        }

        $guardWindow = (int) config('error_reporting.page.loop_guard_seconds', 60);
        if ($guardWindow <= 0) {
            return false;
        }

        $recent = (array) $request->session()->get('error_return_guard', []);

        if (isset($recent[$targetPath]) && (time() - (int) $recent[$targetPath]) < $guardWindow) {
            return true;
        }

        $recent = array_filter(
            $recent,
            fn ($timestamp) => (time() - (int) $timestamp) < $guardWindow
        );
        $recent[$targetPath] = time();
        $request->session()->put('error_return_guard', $recent);

        return false;
    }

    /**
     * Remembers the last page a user viewed successfully so the error page
     * has a return target even without a referer.
     */
    public function rememberReturnUrl(Request $request): void
    {
        if (! $request->isMethod('GET') || ! $request->hasSession() || $request->ajax()) {
            return;
        }

        if ($this->isErrorPath($this->normalisePath($this->currentUrl($request)))) {
            return;
        }

        $request->session()->put('error_return_url', $this->currentUrl($request));
    }

    /**
     * @return array<int, string>
     */
    public function recipients(): array
    {
        $configured = (array) config('error_reporting.to', []);

        $recipients = is_array($configured)
            ? $configured
            : explode(',', (string) $configured);

        $recipients = array_values(array_filter(array_map(
            fn ($address) => trim((string) $address),
            $recipients
        )));

        if ($recipients === []) {
            Log::warning('Error alert skipped: no recipient configured (ERROR_ALERT_TO).');
        }

        return $recipients;
    }

    protected function throttleKey(string $fingerprint): string
    {
        return 'error_alert:throttle:'.$fingerprint;
    }

    /**
     * @return array<int, array{file: string, line: int, call: string}>
     */
    protected function trace(Throwable $e): array
    {
        $limit = (int) config('error_reporting.max_trace_lines', 25);

        $frames = [];
        foreach (array_slice($e->getTrace(), 0, max(1, $limit)) as $frame) {
            $frames[] = [
                'file' => str_replace(base_path(), '', (string) ($frame['file'] ?? '[internal]')),
                'line' => (int) ($frame['line'] ?? 0),
                'call' => $this->describeCall($frame),
            ];
        }

        return $frames;
    }

    /**
     * @param  array<string, mixed>  $frame
     */
    protected function describeCall(array $frame): string
    {
        $class = $frame['class'] ?? '';
        $type = $frame['type'] ?? '';

        return trim(($class ? $class.$type : '').($frame['function'] ?? '[closure]'), '->');
    }

    /**
     * @return array<string, mixed>
     */
    protected function requestContext(Request $request): array
    {
        return [
            'method' => $request->getMethod(),
            'url' => $this->currentUrl($request),
            'route' => optional($request->route())->getName() ?: null,
            'ip' => $request->ip(),
            'headers' => $this->headers($request),
            'input' => $this->redact($request->all()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function identity(): array
    {
        if (! app()->bound('session') || ! request()->hasSession()) {
            return [];
        }

        $session = request()->session();

        return array_filter([
            'user_id' => $session->get('user_id'),
            'role' => $session->get('role'),
            'university_id' => $session->get('university_id'),
            'student_id' => $session->get('student_id'),
            'supervisor_id' => $session->get('supervisor_id'),
        ], fn ($value) => $value !== null && $value !== '');
    }

    /**
     * @return array<string, string>
     */
    protected function headers(Request $request): array
    {
        $headers = [];

        foreach ((array) config('error_reporting.include_headers', []) as $header) {
            $value = $request->headers->get($header);
            if (is_string($value) && $value !== '') {
                $headers[$header] = $this->truncate($value, 300);
            }
        }

        return $headers;
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function redact(array $input): array
    {
        $keys = $this->redactKeys();
        $max = (int) config('error_reporting.max_value_length', 500);

        $redacted = [];
        foreach ($input as $key => $value) {
            $name = (string) $key;

            if ($this->isSensitiveKey($name, $keys)) {
                $redacted[$name] = '[redacted]';

                continue;
            }

            $redacted[$name] = is_array($value)
                ? $this->redact($value)
                : $this->truncate($this->stringify($value), $max);
        }

        return $redacted;
    }

    /**
     * @param  array<int, string>  $keys
     */
    protected function isSensitiveKey(string $key, array $keys): bool
    {
        $key = Str::lower($key);

        foreach ($keys as $needle) {
            $needle = Str::lower((string) $needle);
            if ($needle !== '' && str_contains($key, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    protected function redactKeys(): array
    {
        return (array) config('error_reporting.redact_keys', []);
    }

    protected function stringify(mixed $value): string
    {
        if (is_null($value)) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if ($value instanceof \Stringable) {
            return (string) $value;
        }

        return json_encode($value) ?: '['.get_debug_type($value).']';
    }

    protected function truncate(string $value, int $limit): string
    {
        return Str::limit($value, max(1, $limit), '…');
    }

    protected function level(Throwable $e): string
    {
        if ($e instanceof \Error || $e instanceof \ErrorException) {
            return 'CRITICAL';
        }

        return 'ERROR';
    }

    protected function currentUrl(Request $request): string
    {
        return $request->fullUrl();
    }

    protected function normalisePath(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path = is_string($path) ? $path : '/';

        return '/'.ltrim($path, '/');
    }

    protected function isSameHost(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return false;
        }

        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);

        if (is_string($appHost) && $appHost !== '' && Str::lower($host) === Str::lower($appHost)) {
            return true;
        }

        $requestHost = request()->getHost();

        return $requestHost !== '' && Str::lower($host) === Str::lower($requestHost);
    }

    protected function isErrorPath(string $path): bool
    {
        return Str::contains($path, 'support/error-report')
            || Str::contains($path, 'errors/')
            || Str::startsWith($path, '/_');
    }
}
