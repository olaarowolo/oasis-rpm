<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $userRole = session('role');

        // If no role is set, redirect to login
        if (!$userRole) {
            return redirect()->route('login')->with('error', 'Please log in to continue');
        }

        // Check if user's role is in the allowed roles
        if (!in_array($userRole, $roles)) {
            // Determine what role the user has vs what they need
            $requiredRoles = implode(', ', $roles);
            
            if ($userRole === 'super_admin') {
                // Super admins can access admin views but not supervisor or student views
                if (in_array('supervisor', $roles)) {
                    return redirect()->route('admin.dashboard')->with('error', 'You are a Super Admin. Please use the Admin Portal for system management.');
                }
                if (in_array('student', $roles)) {
                    return redirect()->route('admin.dashboard')->with('error', 'You are a Super Admin. Students cannot access the student view.');
                }
            }
            
            if ($userRole === 'admin') {
                if (in_array('supervisor', $roles)) {
                    return redirect()->route('admin.dashboard')->with('error', 'You are an Admin. Please use the Admin Portal for system management.');
                }
                if (in_array('student', $roles)) {
                    return redirect()->route('admin.dashboard')->with('error', 'You are an Admin. Students cannot access the student view.');
                }
            }
            
            if ($userRole === 'supervisor') {
                if (in_array('admin', $roles) || in_array('super_admin', $roles)) {
                    return redirect()->route('supervisor.dashboard')->with('error', 'You are a Supervisor. Admin features are not accessible from the Supervisor Portal.');
                }
            }
            
            if ($userRole === 'student') {
                if (in_array('admin', $roles) || in_array('super_admin', $roles)) {
                    return redirect()->route('student.dashboard')->with('error', 'You are a Student. Admin features are not accessible from the Student Portal.');
                }
                if (in_array('supervisor', $roles)) {
                    return redirect()->route('student.dashboard')->with('error', 'You are a Student. Supervisor features are not accessible from the Student Portal.');
                }
            }

            return redirect()->back()->with('error', 'You do not have permission to access this area. Your role: ' . $userRole);
        }

        return $next($request);
    }
}