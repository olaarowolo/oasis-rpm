<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SecureSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Regenerate session ID on every request to prevent session fixation
        if (!$request->hasSession() || !$request->session()->isStarted()) {
            $request->setLaravelSession($this->createSession());
        }

        // Regenerate session ID periodically (every 30 minutes)
        $lastRegenerated = $request->session()->get('_last_session_regenerate', 0);
        $sessionLifetime = config('session.lifetime', 120) * 60;

        if (time() - $lastRegenerated > 1800) {
            $request->session()->regenerate(true);
            $request->session()->put('_last_session_regenerate', time());
        }

        // Set secure session cookie parameters
        $this->configureSecureSession($request);

        // Add session security headers
        $response = $next($request);
        $this->addSecurityHeaders($response);

        return $response;
    }

    /**
     * Create a new secure session
     */
    protected function createSession(): \Illuminate\Session\Store
    {
        $config = config('session');
        $driver = $config['driver'];

        $session = new \Illuminate\Session\Store(
            $config['cookie'],
            new \Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler($config['files'] ?? storage_path('framework/sessions'))
        );

        return $session;
    }

    /**
     * Configure secure session parameters
     */
    protected function configureSecureSession(Request $request): void
    {
        $config = config('session');

        // Session cookie parameters
        $secure = $config['secure'] ?? false;
        $httpOnly = $config['http_only'] ?? true;
        $sameSite = $config['same_site'] ?? 'lax';

        session()->config([
            'secure' => true, // Force HTTPS in production
            'http_only' => true,
            'same_site' => 'strict',
            'expire_on_close' => true,
        ]);
    }

    /**
     * Add security headers to response
     */
    protected function addSecurityHeaders($response): void
    {
        // Prevent iframe embedding
        $response->header('X-Frame-Options', 'DENY');

        // Prevent MIME type sniffing
        $response->header('X-Content-Type-Options', 'nosniff');

        // Strict Transport Security (HSTS)
        $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // Content Security Policy
        $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; frame-ancestors 'none'; base-uri 'self'; form-action 'self'";
        $response->header('Content-Security-Policy', $csp);

        // Referrer Policy
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy
        $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    }

    /**
     * Regenerate session ID for logout
     */
    public function secureLogout(Request $request): void
    {
        // Regenerate session before destroying
        $request->session()->regenerate(true);

        // Flush all session data
        $request->session()->flush();

        // Regenerate again after flush
        $request->session()->regenerate(true);

        // Clear session cookie
        $request->session()->save();
    }

    /**
     * Validate session integrity
     */
    public function validateSession(Request $request): bool
    {
        $userAgent = $request->session()->get('_user_agent');
        $ip = $request->session()->get('_ip_address');
        $currentAgent = $request->userAgent();
        $currentIp = $request->ip();

        // Check if user agent has changed (session hijacking indicator)
        if ($userAgent && $userAgent !== $currentAgent) {
            Log::warning('Session hijacking detected', [
                'expected_agent' => $userAgent,
                'actual_agent' => $currentAgent,
                'ip' => $currentIp,
            ]);
            return false;
        }

        // Optional: IP check (may break legitimate IP changes)
        if ($ip && $ip !== $currentIp) {
            // Don't fail immediately, just log
            Log::info('IP address changed during session', [
                'original_ip' => $ip,
                'current_ip' => $currentIp,
            ]);
        }

        return true;
    }
}
