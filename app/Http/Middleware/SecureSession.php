<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecureSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasSession() && $request->session()->isStarted()) {
            $lastRegenerated = (int) $request->session()->get('_last_session_regenerate', 0);

            if (time() - $lastRegenerated > 1800) {
                $request->session()->regenerate(true);
                $request->session()->put('_last_session_regenerate', time());
            }

            if (!$request->session()->has('_user_agent')) {
                $request->session()->put('_user_agent', $request->userAgent());
            }

            if (!$request->session()->has('_ip_address')) {
                $request->session()->put('_ip_address', $request->ip());
            }
        }

        $response = $next($request);
        $this->addSecurityHeaders($response);

        return $response;
    }

    /**
     * Add security headers to response.
     */
    protected function addSecurityHeaders($response): void
    {
        if (method_exists($response, 'header')) {
            $response->header('X-Frame-Options', 'DENY');
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; frame-ancestors 'none'; base-uri 'self'; form-action 'self'";
            $response->header('Content-Security-Policy', $csp);
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        }

        if (method_exists($response, 'headers')) {
            $headers = $response->headers;
            $headers->set('X-Frame-Options', 'DENY');
            $headers->set('X-Content-Type-Options', 'nosniff');
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; frame-ancestors 'none'; base-uri 'self'; form-action 'self'";
            $headers->set('Content-Security-Policy', $csp);
            $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        }
    }

    /**
     * Regenerate session ID for logout.
     */
    public function secureLogout(Request $request): void
    {
        $request->session()->regenerate(true);
        $request->session()->flush();
        $request->session()->regenerate(true);
        $request->session()->save();
    }

    /**
     * Validate session integrity.
     */
    public function validateSession(Request $request): bool
    {
        $userAgent = $request->session()->get('_user_agent');
        $ip = $request->session()->get('_ip_address');
        $currentAgent = $request->userAgent();
        $currentIp = $request->ip();

        if ($userAgent && $userAgent !== $currentAgent) {
            Log::warning('Session hijacking detected', [
                'expected_agent' => $userAgent,
                'actual_agent' => $currentAgent,
                'ip' => $currentIp,
            ]);
            return false;
        }

        if ($ip && $ip !== $currentIp) {
            Log::info('IP address changed during session', [
                'original_ip' => $ip,
                'current_ip' => $currentIp,
            ]);
        }

        return true;
    }
}
