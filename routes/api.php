<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\MeetingLogController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AIAssistantController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DefenseReadinessController;
use App\Http\Middleware\CheckUniversity;
use App\Http\Middleware\EnsureStudentLogin;
use App\Http\Middleware\EnsureSupervisorLogin;
use App\Http\Middleware\EnsureAdminLogin;
use App\Http\Middleware\RateLimitMiddleware;
use App\Http\Middleware\AuthGuard;

/*
|--------------------------------------------------------------------------
| API Routes - TheOAsis Research Supervision Portal
|--------------------------------------------------------------------------
| Complete API endpoints for 12-stage research lifecycle with multi-tenant support
|
*/

// ============================================================
// PUBLIC ROUTES - Rate Limited (No middleware)
// ============================================================
Route::post('/demo/request', [NotificationController::class, 'submitDemoRequest'])
    ->middleware('throttle:10,1');

// Universities - Search and list (for search-select)
Route::get('/universities/search', [AuthController::class, 'searchUniversities'])
    ->middleware('throttle:20,1');

// Departments - Structured department data for universities that support it
Route::get('/departments', [DepartmentController::class, 'index'])
    ->middleware('throttle:20,1');
Route::get('/universities/{code}/departments', [DepartmentController::class, 'byUniversity'])
    ->middleware('throttle:20,1');
    
// Rate limiting applied via Throttle middleware (Laravel built-in)
Route::post('/auth/login-student', [AuthController::class, 'loginStudent'])
    ->middleware('throttle:5,1');
Route::post('/auth/login-supervisor', [AuthController::class, 'loginSupervisor'])
    ->middleware('throttle:5,1');
Route::post('/auth/login-admin', [AuthController::class, 'loginAdmin'])
    ->middleware('throttle:5,1');
Route::post('/auth/login-super-admin', [AuthController::class, 'loginSuperAdmin'])
    ->middleware('throttle:5,1');
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp'])
    ->middleware('throttle:10,1');
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp'])
    ->middleware('throttle:10,1');
    
// Auth status and logout
Route::get('/auth/me', [AuthController::class, 'me']);
Route::post('/auth/logout', [AuthController::class, 'logout']);

// ============================================================
// PROTECTED ROUTES (University context required)
// ============================================================
Route::middleware([CheckUniversity::class])->group(function () {

    // ========================================================
    // STUDENT ROUTES (EnsureStudentLogin middleware)
    // ========================================================
    Route::middleware(['app.auth', EnsureStudentLogin::class])->prefix('/student')->group(function () {
        // Dashboard & Profile
        Route::get('/dashboard', [StudentController::class, 'dashboard']);
        Route::get('/roadmap', [StudentController::class, 'getRoadmap']);
        Route::get('/progress', [StudentController::class, 'getProgress']);
        Route::get('/profile', [StudentController::class, 'getProfile']);
        Route::put('/profile', [StudentController::class, 'updateProfile']);

        // Proposals
        Route::get('/proposals', [ProposalController::class, 'listStudentProposals']);
        Route::post('/proposals', [ProposalController::class, 'submitProposal']);
        Route::get('/proposals/{id}', [ProposalController::class, 'getProposal']);
        Route::put('/proposals/{id}', [ProposalController::class, 'updateProposal']);

        // Meeting Logs
        Route::get('/meetings', [MeetingLogController::class, 'listStudentMeetings']);
        Route::post('/meetings', [MeetingLogController::class, 'createMeetingLog']);
        Route::get('/meetings/{id}', [MeetingLogController::class, 'getMeetingLog']);
        Route::put('/meetings/{id}', [MeetingLogController::class, 'updateMeetingLog']);

        // Resources
        Route::get('/resources', [ResourceController::class, 'listStudentResources']);
        Route::get('/resources/{id}', [ResourceController::class, 'getResource']);
        Route::post('/resources/{id}/complete', [ResourceController::class, 'markResourceComplete']);
        Route::get('/resources/{id}/progress', [ResourceController::class, 'getResourceProgress']);

        // History
        Route::get('/stages/history', [StudentController::class, 'getStageHistory']);
        Route::get('/stages/current', [StudentController::class, 'getCurrentStage']);
        Route::get('/topics/history', [StudentController::class, 'getTopicHistory']);

        // Archive Submission
        Route::get('/archive', [StudentController::class, 'getArchiveSubmission']);
        Route::post('/archive', [StudentController::class, 'upsertArchiveSubmission']);
        Route::post('/archive/upload', [StudentController::class, 'uploadArchiveDocument']);
        Route::post('/archive/submit', [StudentController::class, 'submitArchiveSubmission']);

        // Defense Readiness Manuscript
        Route::get('/defense-readiness', [DefenseReadinessController::class, 'studentIndex']);
        Route::get('/defense-readiness/settings', [DefenseReadinessController::class, 'studentSettings']);
        Route::patch('/defense-readiness/settings', [DefenseReadinessController::class, 'studentUpdateSettings']);
        Route::put('/defense-readiness/sections/{section}/content', [DefenseReadinessController::class, 'studentAutosave']);
        Route::post('/defense-readiness/sections/{section}/submit', [DefenseReadinessController::class, 'studentSubmitSection']);
        Route::post('/defense-readiness/sections/{section}/acknowledge-conditions', [DefenseReadinessController::class, 'studentAcknowledgeConditions']);
        Route::post('/defense-readiness/sections', [DefenseReadinessController::class, 'studentAddCustomSection']);
        Route::delete('/defense-readiness/sections/{section}', [DefenseReadinessController::class, 'studentDeleteSection']);
    });

    // ========================================================
    // SUPERVISOR ROUTES (EnsureSupervisorLogin middleware)
    // ========================================================
    Route::middleware(['app.auth', EnsureSupervisorLogin::class])->prefix('/supervisor')->group(function () {
        // Dashboard & Profile
        Route::get('/dashboard', [SupervisorController::class, 'dashboard']);
        Route::get('/profile', [SupervisorController::class, 'getProfile']);
        Route::put('/profile', [SupervisorController::class, 'updateProfile']);

        // Student Roster
        Route::get('/students', [SupervisorController::class, 'getRoster']);
        Route::get('/students/{id}', [SupervisorController::class, 'getStudent']);
        Route::put('/students/{id}/stage', [SupervisorController::class, 'setStudentStage']);
        Route::put('/students/{id}/status', [SupervisorController::class, 'updateStudentStatus']);

        // Proposals
        Route::get('/proposals', [ProposalController::class, 'listPendingProposals']);
        Route::get('/proposals/{id}', [ProposalController::class, 'getProposal']);
        Route::patch('/proposals/{id}/approve', [ProposalController::class, 'approveProposal']);
        Route::patch('/proposals/{id}/reject', [ProposalController::class, 'rejectProposal']);
        Route::patch('/proposals/{id}/request-revision', [ProposalController::class, 'requestRevision']);

        // Meeting Logs
        Route::get('/meetings', [MeetingLogController::class, 'listAllMeetings']);
        Route::get('/meetings/{id}', [MeetingLogController::class, 'getMeetingLog']);
        Route::patch('/meetings/{id}/approve', [MeetingLogController::class, 'approveMeetingLog']);
        Route::patch('/meetings/{id}/reject', [MeetingLogController::class, 'rejectMeetingLog']);
        Route::patch('/meetings/{id}/feedback', [MeetingLogController::class, 'provideFeedback']);

        // Resources
        Route::get('/resources/pending', [ResourceController::class, 'listPendingResources']);
        Route::get('/resources/submissions', [ResourceController::class, 'listResourceSubmissions']);
        Route::patch('/resources/{id}/approve', [ResourceController::class, 'approveResource']);
        Route::patch('/resources/{id}/reject', [ResourceController::class, 'rejectResource']);

        // Analytics
        Route::get('/analytics', [AnalyticsController::class, 'supervisorAnalytics']);
        Route::get('/analytics/student/{id}', [AnalyticsController::class, 'studentProgress']);
        Route::get('/analytics/export', [AnalyticsController::class, 'exportAnalytics']);

        // Stage Management
        Route::post('/stages/advance', [SupervisorController::class, 'advanceStudentStage']);
        Route::post('/stages/gate', [SupervisorController::class, 'gateStage']);

        // Meeting Scheduling
        Route::post('/meetings/schedule', [MeetingLogController::class, 'scheduleMeeting']);
        Route::get('/meetings/schedule', [MeetingLogController::class, 'getSchedule']);

        // Archive Reviews
        Route::get('/archive/submissions', [SupervisorController::class, 'listPendingArchiveSubmissions']);
        Route::get('/archive/submissions/{id}', [SupervisorController::class, 'getArchiveSubmission']);
        Route::patch('/archive/submissions/{id}/approve', [SupervisorController::class, 'approveArchiveSubmission']);
        Route::patch('/archive/submissions/{id}/reject', [SupervisorController::class, 'rejectArchiveSubmission']);

        // Defense Readiness Manuscripts
        Route::get('/manuscripts', [DefenseReadinessController::class, 'supervisorIndex']);
        Route::get('/manuscripts/{document}', [DefenseReadinessController::class, 'supervisorShow']);
        Route::post('/sections/{section}/decision', [DefenseReadinessController::class, 'supervisorDecision']);
    });

    // ========================================================
    // ADMIN ROUTES (EnsureAdminLogin middleware)
    // ========================================================
    Route::middleware(['app.auth', EnsureAdminLogin::class])->prefix('/admin')->group(function () {
        // Users
        Route::get('/users', [AdminController::class, 'listUsers']);
        Route::post('/users', [AdminController::class, 'createUser']);
        Route::get('/users/{id}', [AdminController::class, 'getUser']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);

        // Configuration
        Route::get('/config', [AdminController::class, 'getConfig']);
        Route::post('/config', [AdminController::class, 'updateConfig']);
        Route::get('/config/{key}', [AdminController::class, 'getConfigValue']);

        // System Status
        Route::get('/system/status', [AdminController::class, 'getSystemStatus']);
        Route::get('/system/health', [AdminController::class, 'healthCheck']);

        // Audit Logs
        Route::get('/audit-logs', [AdminController::class, 'getAuditLogs']);
        Route::get('/audit-logs/{id}', [AdminController::class, 'getAuditLog']);

        // Resources Management
        Route::get('/resources', [AdminController::class, 'listAllResources']);
        Route::post('/resources', [AdminController::class, 'createResource']);
        Route::put('/resources/{id}', [AdminController::class, 'updateResource']);
        Route::delete('/resources/{id}', [AdminController::class, 'deleteResource']);

        // Archive Management
        Route::get('/archive/submissions', [AdminController::class, 'listArchiveSubmissions']);
    });

    Route::middleware(['app.auth', 'super_admin'])->prefix('/admin')->group(function () {
        // Universities
        Route::get('/universities', [AdminController::class, 'listUniversities']);
        Route::post('/universities', [AdminController::class, 'createUniversity']);
        Route::get('/universities/{id}', [AdminController::class, 'getUniversity']);
        Route::put('/universities/{id}', [AdminController::class, 'updateUniversity']);
        Route::delete('/universities/{id}', [AdminController::class, 'deleteUniversity']);
    });

    // ========================================================
    // SHARED ROUTES (All authenticated users)
    // ========================================================
    Route::post('/ai-assistant/query', [AIAssistantController::class, 'query']);
    Route::get('/ai-assistant/models', [AIAssistantController::class, 'listModels']);
    Route::get('/ai-assistant/history', [AIAssistantController::class, 'getQueryHistory']);

    Route::post('/notifications/email/resend', [NotificationController::class, 'resendEmail']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'getUnreadNotifications']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});

// ============================================================
// HEALTH CHECK (Public - No authentication)
// ============================================================
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});