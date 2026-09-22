<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureStudentLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (session('role') !== 'student') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Student access required',
            ], 403);
        }

        return $next($request);
    }
}
