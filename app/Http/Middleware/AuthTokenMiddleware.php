<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthenticationException;

class AuthTokenMiddleware
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
        $token = $request->bearerToken();

        if (!$token) {
            return $this->handleMissingToken($request);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            Log::warning('Invalid token access attempt', [
                'token' => substr($token, 0, 10) . '...',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'path' => $request->path(),
            ]);
            
            return $this->handleInvalidToken($request);
        }

        // Check if token has expired
        if ($accessToken->tokenable && $accessToken->tokenable->expires_at && $accessToken->tokenable->expires_at->isPast()) {
            Log::warning('Expired token access attempt', [
                'user_id' => $accessToken->tokenable_id,
                'token_last_four' => substr($token, -4),
                'ip' => $request->ip(),
                'path' => $request->path(),
            ]);
            
            return $this->handleExpiredToken($request);
        }

        // Store token info in request for later use
        $request->merge([
            'token' => $accessToken,
            'authenticated_token' => true,
        ]);

        return $next($request);
    }

    /**
     * Handle missing token
     */
    protected function handleMissingToken(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Token not provided. Authentication required.',
            'error_code' => 'TOKEN_MISSING',
        ], 401);
    }

    /**
     * Handle invalid token
     */
    protected function handleInvalidToken(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Invalid token. Please authenticate again.',
            'error_code' => 'TOKEN_INVALID',
        ], 401);
    }

    /**
     * Handle expired token
     */
    protected function handleExpiredToken(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Token has expired. Please login again.',
            'error_code' => 'TOKEN_EXPIRED',
            'redirect' => route('login'),
        ], 401);
    }
}
