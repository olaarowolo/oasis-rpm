<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSupervisorLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (session('role') !== 'supervisor') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Supervisor access required',
            ], 403);
        }

        return $next($request);
    }
}
