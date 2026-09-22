<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public landing page
Route::get('/', function () {
    return view('landing');
});

// Login page - redirects authenticated users to their dashboard
Route::get('/login', function () {
    return view('index');
})->name('login')->middleware('guest');

// ================= AUTHENTICATION ROUTES =================
Route::prefix('api/auth')->group(function () {
    // Student login
    Route::post('/student/login', [AuthController::class, 'loginStudent'])->name('auth.student.login');
    // Student OTP flow
    Route::post('/student/send-otp', [AuthController::class, 'sendStudentOtp'])->name('auth.student.send-otp');
    Route::post('/student/verify-otp', [AuthController::class, 'verifyStudentOtp'])->name('auth.student.verify-otp');
    // Supervisor login
    Route::post('/supervisor/login', [AuthController::class, 'loginSupervisor'])->name('auth.supervisor.login');
    // Admin login
    Route::post('/admin/login', [AuthController::class, 'loginAdmin'])->name('auth.admin.login');
    // Super Admin login
    Route::post('/super-admin/login', [AuthController::class, 'loginSuperAdmin'])->name('auth.super-admin.login');
});

// ================= BACKWARD COMPATIBILITY - Direct API routes =================
Route::post('/super-admin/login', [AuthController::class, 'loginSuperAdmin']);
Route::post('/admin/login', [AuthController::class, 'loginAdmin']);
Route::post('/supervisor/login', [AuthController::class, 'loginSupervisor']);
Route::post('/student/login', [AuthController::class, 'loginStudent']);
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::get('/me', [AuthController::class, 'me']);
Route::post('/logout', [AuthController::class, 'logout']);

// ================= PASSWORD RESET ROUTES =================
Route::post('/password/reset-link', [PasswordResetController::class, 'sendResetLink'])->name('password.reset-link');
Route::post('/password/verify-token', [PasswordResetController::class, 'verifyToken'])->name('password.verify-token');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
Route::post('/password/change', [PasswordResetController::class, 'changePassword'])->name('password.change');

// ================= DASHBOARD VIEWS - Role-Based =================
// Default redirect based on role
Route::get('/home', function () {
    $role = session('role');
    switch ($role) {
        case 'super_admin':
            return redirect()->route('super-admin.dashboard');
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'supervisor':
            return redirect()->route('supervisor.dashboard');
        case 'student':
            return redirect()->route('student.dashboard');
        default:
            return redirect()->route('login');
    }
})->name('home');

// ================= SUPER ADMIN ROUTES (Platform-level access) =================
Route::middleware(['auth', 'role:super_admin'])->prefix('/super-admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('super-admin-dashboard');
    })->name('super-admin.dashboard');

    Route::get('/universities', function () {
        return view('super-admin.universities');
    })->name('super-admin.universities');

    Route::get('/users', function () {
        return view('super-admin.users');
    })->name('super-admin.users');

    Route::get('/config', function () {
        return view('super-admin.config');
    })->name('super-admin.config');

    Route::get('/audit-logs', function () {
        return view('super-admin.audit-logs');
    })->name('super-admin.audit-logs');

    Route::get('/resources', function () {
        return view('super-admin.resources');
    })->name('super-admin.resources');

    Route::get('/system-status', function () {
        return view('super-admin.system-status');
    })->name('super-admin.system-status');
});

// ================= ADMIN ROUTES (University-level access) =================
Route::middleware(['auth', 'role:admin'])->prefix('/admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin-dashboard');
    })->name('admin.dashboard');

    Route::get('/universities', function () {
        return view('admin.universities');
    })->name('admin.universities');

    Route::get('/users', function () {
        return view('admin.users');
    })->name('admin.users');

    Route::get('/config', function () {
        return view('admin.config');
    })->name('admin.config');

    Route::get('/audit-logs', function () {
        return view('admin.audit-logs');
    })->name('admin.audit-logs');

    Route::get('/resources', function () {
        return view('admin.resources');
    })->name('admin.resources');
});

// ================= SUPERVISOR ROUTES =================
Route::middleware(['auth', 'role:supervisor'])->prefix('/supervisor')->group(function () {
    Route::get('/dashboard', function () {
        return view('supervisor-dashboard');
    })->name('supervisor.dashboard');

    Route::get('/students', function () {
        return view('supervisor.students');
    })->name('supervisor.students');

    Route::post('/students/create', [SupervisorController::class, 'createStudent'])->name('supervisor.students.create');
    Route::get('/proposals', function () {
        return view('supervisor.proposals');
    })->name('supervisor.proposals');

    Route::get('/meetings', function () {
        return view('supervisor.meetings');
    })->name('supervisor.meetings');

    Route::get('/analytics', function () {
        return view('supervisor.analytics');
    })->name('supervisor.analytics');
});

// ================= STUDENT ROUTES =================
Route::middleware(['auth', 'role:student'])->prefix('/student')->group(function () {
    Route::get('/dashboard', function () {
        return view('student-dashboard');
    })->name('student.dashboard');

    Route::get('/meetings', function () {
        return view('student.meetings');
    })->name('student.meetings');

    Route::get('/resources', function () {
        return view('student.resources');
    })->name('student.resources');

    Route::get('/proposals', function () {
        return view('student.proposals');
    })->name('student.proposals');

    Route::get('/defense-readiness', function () {
        return view('student.defense-readiness');
    })->name('student.defense-readiness');
});
