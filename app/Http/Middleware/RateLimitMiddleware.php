<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RateLimitMiddleware
{
    /**
     * The rate limit window in seconds
     */
    protected $window = 60;

    /**
     * The maximum number of attempts
     */
    protected $maxAttempts = 5;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  $key  Optional custom key
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $key = null)
    {
        $identifier = $this->getRateLimitIdentifier($request, $key);
        $key = "rate_limit:{$identifier}";

        $attempts = Cache::get($key, 0);

        if ($attempts >= $this->maxAttempts) {
            Log::warning('Rate limit exceeded', [
                'identifier' => $identifier,
                'attempts' => $attempts,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'path' => $request->path(),
            ]);

            return $this->handleRateLimitExceeded($request, $identifier);
        }

        Cache::increment($key);
        Cache::put($key, $attempts + 1, $this->window);

        // Add rate limit headers to response
        $response = $next($request);
        $response->headers->set('X-RateLimit-Limit', $this->maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', $this->maxAttempts - ($attempts + 1));
        $response->headers->set('X-RateLimit-Reset', now()->addSeconds($this->window)->timestamp);

        return $response;
    }

    /**
     * Get the rate limit identifier
     */
    protected function getRateLimitIdentifier(Request $request, ?string $key = null): string
    {
        if ($key) {
            return $key;
        }

        return $request->ip();
    }

    /**
     * Handle rate limit exceeded
     */
    protected function handleRateLimitExceeded(Request $request, string $identifier)
    {
        $retryAfter = $this->getWindowRemaining($identifier);

        Log::alert('Rate limit exceeded - potential abuse', [
            'identifier' => $identifier,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'path' => $request->path(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Too many requests. Please try again later.',
            'error_code' => 'RATE_LIMIT_EXCEEDED',
            'retry_after' => $retryAfter,
        ], 429, [
            'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->timestamp,
        ]);
    }

    /**
     * Get remaining time in seconds
     */
    protected function getWindowRemaining(string $identifier): int
    {
        $key = "rate_limit:{$identifier}";
        $ttl = Cache::get($key, 0);

        if ($ttl === 0) {
            return 0;
        }

        return max(0, $this->window - (time() - Cache::get("{$key}_timestamp", time())));
    }

    /**
     * Clear rate limit for identifier
     */
    public function clearRateLimit(string $identifier): void
    {
        $key = "rate_limit:{$identifier}";
        Cache::forget($key);
    }
}
