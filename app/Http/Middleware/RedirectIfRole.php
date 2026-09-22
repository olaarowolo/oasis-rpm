<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RedirectIfRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $userRole = session('role');
        $currentRoute = Route::currentRouteName();

        // If user is authenticated and trying to access login page, redirect to their dashboard
        if ($currentRoute === 'login' && $userRole) {
            return $this->redirectBasedOnRole($userRole);
        }

        // Check if user's role matches allowed roles for this route
        if ($userRole && !in_array($userRole, $roles)) {
            return $this->redirectBasedOnRole($userRole);
        }

        return $next($request);
    }

    /**
     * Redirect user to their dashboard based on role
     */
    private function redirectBasedOnRole(string $role): \Illuminate\Http\RedirectResponse
    {
        switch ($role) {
            case 'super_admin':
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'supervisor':
                return redirect()->route('supervisor.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            default:
                return redirect()->route('login');
        }
    }
}