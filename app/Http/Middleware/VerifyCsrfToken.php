<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/api/*',  // API routes typically use token-based auth instead of session-based CSRF
        '/health',
        '/test/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        // For API routes, we use Sanctum tokens instead of CSRF tokens
        if ($this->isApiRequest($request)) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }

    /**
     * Determine if the request is an API request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isApiRequest($request)
    {
        return $request->is('api/*') || $request->expectsJson();
    }
}
