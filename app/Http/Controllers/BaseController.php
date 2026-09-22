<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelController;

class BaseController extends LaravelController
{
    use AuthorizesRequests, ValidatesRequests;

    protected ?University $university = null;
    protected ?object $user = null;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $universityId = $request->header('X-University-ID') ?? session('university_id');
            if ($universityId) {
                try {
                    $this->university = University::findOrFail($universityId);
                    $request->attributes->set('university', $this->university);
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    // University not found, continue without it
                }
            }
            return $next($request);
        });
    }

    protected function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(string $message = 'Error', int $code = 400, $data = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
