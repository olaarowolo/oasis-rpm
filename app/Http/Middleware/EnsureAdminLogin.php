<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (!in_array(session('role'), ['admin', 'super_admin'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Admin access required',
            ], 403);
        }

        return $next($request);
    }
}
