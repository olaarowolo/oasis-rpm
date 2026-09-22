<?php

namespace App\Http\Middleware;

use App\Models\University;
use Closure;
use Illuminate\Http\Request;

class CheckUniversity
{
    public function handle(Request $request, Closure $next)
    {
        $universityId = $request->header('X-University-ID') ?? session('university_id');

        if ($universityId) {
            $university = University::find($universityId);
            if ($university) {
                $request->attributes->set('university', $university);
                return $next($request);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'University not found or not provided',
        ], 400);
    }
}
