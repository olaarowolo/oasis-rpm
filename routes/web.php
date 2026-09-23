<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\Web\AdminResourceWebController;
use App\Http\Controllers\Web\AuditLogWebController;
use App\Http\Controllers\Web\SupervisorMeetingWebController;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\SystemConfig;
use App\Models\Proposal;
use App\Models\MeetingLog;
use App\Models\University;
use App\Models\User;

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
Route::get('/login', function (Request $request) {
    if ($request->session()->has('user_id') && $request->session()->has('role')) {
        return redirect()->route('home');
    }

    return view('index');
})->name('login');

// ================= AUTHENTICATION ROUTES =================
Route::prefix('api/auth')->group(function () {
    // Student login
    Route::post('/student/login', [AuthController::class, 'loginStudent'])->name('auth.student.login');
    // Student OTP flow
    Route::post('/student/send-otp', [AuthController::class, 'sendStudentOtp'])->name('auth.student.send-otp');
    Route::post('/student/verify-otp', [AuthController::class, 'verifyStudentOtp'])->name('auth.student.verify-otp');
    // Supervisor email verification (before credentials)
    Route::post('/supervisor/send-otp', [AuthController::class, 'sendSupervisorOtp'])->name('auth.supervisor.send-otp');
    Route::post('/supervisor/verify-otp', [AuthController::class, 'verifySupervisorOtp'])->name('auth.supervisor.verify-otp');
    // Supervisor login
    Route::post('/supervisor/login', [AuthController::class, 'loginSupervisor'])->name('auth.supervisor.login');
    // Admin email verification (before credentials)
    Route::post('/admin/send-otp', [AuthController::class, 'sendAdminOtp'])->name('auth.admin.send-otp');
    Route::post('/admin/verify-otp', [AuthController::class, 'verifyAdminOtp'])->name('auth.admin.verify-otp');
    // Admin login
    Route::post('/admin/login', [AuthController::class, 'loginAdmin'])->name('auth.admin.login');
    Route::post('/admin/verify-mfa', [AuthController::class, 'verifyAdminMfa'])->name('auth.admin.verify-mfa');
    // Super Admin login
    Route::post('/super-admin/login', [AuthController::class, 'loginSuperAdmin'])->name('auth.super-admin.login');
});

// ================= BACKWARD COMPATIBILITY - Direct API routes =================
Route::post('/super-admin/login', [AuthController::class, 'loginSuperAdmin']);
Route::post('/admin/login', [AuthController::class, 'loginAdmin']);
Route::post('/admin/verify-mfa', [AuthController::class, 'verifyAdminMfa']);
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
})->middleware('app.auth')->name('home');

// ================= SUPER ADMIN ROUTES (Platform-level access) =================
Route::middleware(['app.auth', 'role:super_admin'])->prefix('/super-admin')->group(function () {
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
Route::middleware(['app.auth', 'role:admin,super_admin'])->prefix('/admin')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $selectedUniversityId = session('university_id');
        $currentUser = User::with('university')->find(session('user_id'));

        $userQuery = User::query();
        $studentQuery = Student::query();
        $supervisorQuery = Supervisor::query();

        if ($selectedUniversityId) {
            $userQuery->where('university_id', $selectedUniversityId);
            $studentQuery->where('university_id', $selectedUniversityId);
            $supervisorQuery->where('university_id', $selectedUniversityId);
        }

        $stats = [
            'total_universities' => University::count(),
            'active_users' => (clone $userQuery)->where('is_active', true)->count(),
            'supervisors' => (clone $supervisorQuery)->where('is_active', true)->count(),
            'students' => (clone $studentQuery)->whereIn('status', ['active', 'completed', 'graduated'])->count(),
            'pending_approvals' => (clone $studentQuery)->where('status', 'suspended')->count() + (clone $supervisorQuery)->where('is_active', false)->count(),
        ];

        $recentUsers = (clone $userQuery)
            ->with(['university', 'student', 'supervisor'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('admin-dashboard', compact('currentUser', 'stats', 'recentUsers', 'selectedUniversityId'));
    })->name('admin.dashboard');

    Route::get('/universities', function () {
        return view('admin.universities');
    })->name('admin.universities');

    Route::get('/users', function (Request $request) {
        $selectedUniversityId = $request->query('university_id', session('university_id'));
        $selectedRole = $request->query('role');

        $universities = University::orderBy('name')->get();
        $userQuery = User::with(['university', 'student', 'supervisor'])->orderBy('name');

        if ($selectedUniversityId) {
            $userQuery->where('university_id', $selectedUniversityId);
        }

        if ($selectedRole) {
            $userQuery->where('role', $selectedRole);
        }

        $users = $userQuery->get();

        $stats = [
            'total' => $users->count(),
            'active' => $users->where('is_active', true)->count(),
            'admins' => $users->whereIn('role', ['admin', 'super_admin'])->count(),
            'supervisors' => $users->where('role', 'supervisor')->count(),
            'students' => $users->where('role', 'student')->count(),
        ];

        $roles = [
            'student' => 'Student',
            'supervisor' => 'Supervisor',
            'admin' => 'Admin',
            'super_admin' => 'Super Admin',
        ];

        return view('admin.users', compact('users', 'universities', 'stats', 'roles', 'selectedUniversityId', 'selectedRole'));
    })->name('admin.users');

    Route::get('/config', function (Request $request) {
        $selectedUniversityId = $request->query('university_id', session('university_id'));
        $universities = University::orderBy('name')->get();
        $currentUniversity = $selectedUniversityId ? University::find($selectedUniversityId) : null;

        $configEntries = SystemConfig::where('university_id', $selectedUniversityId)
            ->orderBy('config_key')
            ->get();

        $configMap = $configEntries->mapWithKeys(function (SystemConfig $config) {
            return [$config->config_key => $config->getTypedValue()];
        })->all();

        $configCount = $configEntries->count();

        return view('admin.config', compact('universities', 'currentUniversity', 'selectedUniversityId', 'configEntries', 'configMap', 'configCount'));
    })->name('admin.config');

    // ---- Audit logs ----
    Route::get('/audit-logs', [AuditLogWebController::class, 'index'])->name('admin.audit-logs');
    // Alias used by the filter form in the audit-logs view.
    Route::get('/audit-logs/list', [AuditLogWebController::class, 'index'])->name('admin.audit-logs.index');
    Route::get('/audit-logs/export', [AuditLogWebController::class, 'export'])->name('admin.audit-logs.export');

    // ---- Learning resources CRUD ----
    Route::get('/resources', [AdminResourceWebController::class, 'index'])->name('admin.resources');
    Route::get('/resources/create', [AdminResourceWebController::class, 'create'])->name('admin.resources.create');
    Route::post('/resources', [AdminResourceWebController::class, 'store'])->name('admin.resources.store');
    Route::get('/resources/{id}/edit', [AdminResourceWebController::class, 'edit'])->name('admin.resources.edit');
    Route::put('/resources/{id}', [AdminResourceWebController::class, 'update'])->name('admin.resources.update');
    Route::delete('/resources/{id}', [AdminResourceWebController::class, 'destroy'])->name('admin.resources.destroy');
});

// ================= SUPERVISOR ROUTES =================
Route::middleware(['app.auth', 'role:supervisor'])->prefix('/supervisor')->group(function () {
    Route::get('/dashboard', function () {
        $students = Student::where('university_id', session('university_id'))
            ->where('supervisor_id', session('supervisor_id'))
            ->with(['proposals', 'meetingLogs'])
            ->get();

        // Calculate pending and approved proposals
        $pendingProposals = 0;
        $approvedProposals = 0;
        foreach ($students as $student) {
            $pendingProposals += $student->proposals->where('status', 'pending')->count();
            $pendingProposals += $student->proposals->where('status', 'revision_required')->count();
            $approvedProposals += $student->proposals->where('status', 'approved')->count();
        }

        // Count meetings this month
        $monthlyMeetings = MeetingLog::whereIn('student_id', $students->pluck('id'))
            ->whereYear('meeting_date', now()->year)
            ->whereMonth('meeting_date', now()->month)
            ->count();

        // Total meetings
        $totalMeetings = MeetingLog::whereIn('student_id', $students->pluck('id'))->count();

        // Students by stage
        $studentsByStage = [
            1 => $students->where('current_stage', 1)->count(),
            2 => $students->where('current_stage', 2)->count(),
            3 => $students->where('current_stage', 3)->count(),
            4 => $students->where('current_stage', 4)->count(),
            5 => $students->where('current_stage', 5)->count(),
            6 => $students->where('current_stage', 6)->count(),
        ];

        return view('supervisor-dashboard', [
            'totalStudents' => $students->count(),
            'pendingProposals' => $pendingProposals,
            'approvedProposals' => $approvedProposals,
            'totalMeetings' => $totalMeetings,
            'monthlyMeetings' => $monthlyMeetings,
            'studentsByStage' => $studentsByStage,
            'students' => $students,
        ]);
    })->name('supervisor.dashboard');

    Route::get('/students', function () {
        $students = Student::where('university_id', session('university_id'))
            ->where('supervisor_id', session('supervisor_id'))
            ->orderBy('full_name')
            ->get();

        return view('supervisor.students', [
            'students' => $students,
            'totalStudents' => $students->count(),
            'activeStudents' => $students->where('status', 'active')->count(),
            'graduatedStudents' => $students->where('status', 'graduated')->count(),
        ]);
    })->name('supervisor.students');

    Route::post('/students/create', [SupervisorController::class, 'createStudent'])->name('supervisor.students.create');
    Route::get('/proposals', function () {
        return view('supervisor.proposals');
    })->name('supervisor.proposals');

    Route::get('/meetings', [SupervisorMeetingWebController::class, 'index'])->name('supervisor.meetings');
    Route::get('/meetings/create', [SupervisorMeetingWebController::class, 'create'])->name('supervisor.meetings.create');
    Route::post('/meetings', [SupervisorMeetingWebController::class, 'store'])->name('supervisor.meetings.store');
    Route::get('/meetings/{id}', [SupervisorMeetingWebController::class, 'show'])->name('supervisor.meetings.view');

    Route::get('/analytics', function () {
        return view('supervisor.analytics');
    })->name('supervisor.analytics');

    Route::get('/resources/pending', function () {
        return view('supervisor.resources');
    })->name('supervisor.resources.pending');
});

// ================= STUDENT ROUTES =================
Route::middleware(['app.auth', 'role:student'])->prefix('/student')->group(function () {
    Route::get('/dashboard', function () {
        return view('student-dashboard');
    })->name('student.dashboard');

    Route::get('/meetings', function () {
        // Data is fetched client-side from GET /api/student/meetings.
        return view('student.meetings');
    })->name('student.meetings');

    Route::get('/resources', function () {
        return view('student.resources');
    })->name('student.resources');

    Route::get('/proposals', function () {
        $proposals = \App\Models\Proposal::where('student_id', session('student_id'))
            ->orderByDesc('date_submitted')
            ->get();
        return view('student.proposals', compact('proposals'));
    })->name('student.proposals');

    Route::get('/defense-readiness', function () {
        $student = \App\Models\Student::with('archiveSubmission')->find(session('student_id'));

        $hasApprovedTopic = (bool) ($student?->research_topic_approved_date);
        $progressPercentage = (int) ($student?->progress_percentage ?? 0);
        $hasFinalDocument = (bool) ($student?->archiveSubmission);

        $completedMeetings = \App\Models\MeetingLog::where('student_id', session('student_id'))
            ->where('status', 'approved')->count();
        $requiredMeetings = 6;

        $completedResources = \App\Models\ResourceProgress::where('student_id', session('student_id'))
            ->where('status', 'approved')->count();
        $totalResources = \App\Models\ResourceProgress::where('student_id', session('student_id'))->count();

        // Weighted readiness score across the five checklist items.
        $resourceRatio = $totalResources > 0 ? ($completedResources / $totalResources) : 0;
        $meetingRatio = $requiredMeetings > 0 ? min($completedMeetings / $requiredMeetings, 1) : 0;
        $defenseScore = (int) round(
            ($hasApprovedTopic ? 20 : 0)
            + (min($progressPercentage, 100) / 100 * 30)
            + ($hasFinalDocument ? 20 : 0)
            + ($meetingRatio * 15)
            + ($resourceRatio * 15)
        );

        return view('student.defense-readiness', compact(
            'hasApprovedTopic', 'progressPercentage', 'hasFinalDocument',
            'completedMeetings', 'requiredMeetings', 'completedResources',
            'totalResources', 'defenseScore'
        ));
    })->name('student.defense-readiness');
});
