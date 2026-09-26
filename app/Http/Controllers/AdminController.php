<?php

namespace App\Http\Controllers;

use App\Models\ArchiveSubmission;
use App\Models\AuditLog;
use App\Models\Resource;
use App\Models\Supervisor;
use App\Models\SystemConfig;
use App\Models\University;
use App\Models\User;
use App\Services\UserInvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends BaseController
{
    public function __construct(private UserInvitationService $userInvitationService) {}

    public function listUniversities(Request $request)
    {
        $universities = University::withCount(['users', 'students', 'supervisors'])->get();

        return $this->success($universities, 'Universities retrieved successfully');
    }

    public function createUniversity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:universities',
            'code' => 'required|string|unique:universities',
            'email' => 'required|email',
            'department' => 'nullable|string',
            'phone' => 'sometimes|string|nullable',
            'has_structured_departments' => 'sometimes|boolean',
        ]);

        $university = University::create($validated);

        AuditLog::logAction(
            $university,
            User::find(session('user_id')),
            'University',
            $university->id,
            'created',
            null,
            $university->toArray()
        );

        return $this->success($university, 'University created successfully', 201);
    }

    public function getUniversity(Request $request, $id)
    {
        $university = University::find($id);
        if (! $university) {
            return $this->error('University not found', 404);
        }

        return $this->success($university, 'University retrieved successfully');
    }

    public function updateUniversity(Request $request, $id)
    {
        $university = University::find($id);
        if (! $university) {
            return $this->error('University not found', 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|unique:universities,name,'.$id,
            'email' => 'sometimes|email',
            'department' => 'sometimes|string|nullable',
            'phone' => 'sometimes|string|nullable',
            'branding_color' => 'sometimes|string',
            'has_structured_departments' => 'sometimes|boolean',
        ]);

        $oldValues = $university->toArray();
        $university->update($validated);

        AuditLog::logAction(
            $university,
            User::find(session('user_id')),
            'University',
            $university->id,
            'updated',
            $oldValues,
            $university->toArray()
        );

        return $this->success($university, 'University updated successfully');
    }

    public function deleteUniversity(Request $request, $id)
    {
        $university = University::find($id);
        if (! $university) {
            return $this->error('University not found', 404);
        }
        $university->delete();

        return $this->success(null, 'University deleted successfully');
    }

    public function listUsers(Request $request)
    {
        $role = $request->query('role');
        $universityId = $this->currentUserIsSuperAdmin()
            ? $request->query('university_id', session('university_id'))
            : $this->currentUniversityId();

        $query = User::with(['student', 'supervisor', 'university']);

        if ($role) {
            if (! $this->currentUserIsSuperAdmin() && $role === 'super_admin') {
                return $this->error('Unauthorized to view super admin accounts', 403);
            }

            $query->where('role', $role);
        } elseif (! $this->currentUserIsSuperAdmin()) {
            $query->where('role', '!=', 'super_admin');
        }

        if ($universityId) {
            $query->where('university_id', $universityId);
        }

        $users = $query->get();

        return $this->success($users, 'Users retrieved successfully');
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate(array_merge([
            'university_id' => 'required|exists:universities,id',
            'email' => 'required|email|unique:users',
            'name' => 'required|string',
            'role' => 'required|in:student,supervisor,admin,super_admin',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'require_supervisor_selection' => 'nullable|boolean',
            'supervisor_id' => [
                Rule::requiredIf(fn () => $request->input('role') === 'student' && $request->boolean('require_supervisor_selection')),
                'nullable',
                'exists:supervisors,id',
            ],
        ], $this->roleSpecificRules($request->input('role'))));

        $validated['require_supervisor_selection'] = $request->boolean('require_supervisor_selection');

        if (! $this->currentUserIsSuperAdmin()) {
            // An admin may only enrol supervisors and students. The privileged
            // roles are reserved to platform-level operators; attempting to
            // supply them is refused rather than silently downgraded, so the
            // policy is enforced here and in the web controller consistently.
            if (in_array((string) $validated['role'], ['admin', 'super_admin'], true)) {
                return $this->error('You are not authorised to create admin or super admin accounts.', 403);
            }

            $validated['university_id'] = $this->currentUniversityId();
        }

        if (($validated['role'] ?? null) === 'student' && ! empty($validated['supervisor_id'])) {
            $supervisor = Supervisor::find($validated['supervisor_id']);
            if (! $supervisor || (int) $supervisor->university_id !== (int) $validated['university_id']) {
                return $this->error('Selected supervisor must belong to the same university as the student.', 422);
            }
        }

        if (($validated['role'] ?? null) === 'student' && empty($validated['supervisor_id']) && ! $this->userInvitationService->findLeastLoadedSupervisorId((int) $validated['university_id'])) {
            return $this->error('No active supervisors are available for this university. Select a supervisor first or create an active supervisor account.', 422);
        }

        $invitation = $this->userInvitationService->createInvitedUser($validated);
        $user = $invitation['user'];

        return $this->success($user, 'User invite created successfully. An email has been sent to complete onboarding.', 201);
    }

    public function getUser(Request $request, $id)
    {
        $user = User::with('student', 'supervisor')->find($id);
        if (! $user) {
            return $this->error('User not found', 404);
        }

        if (! $this->canManageUser($user)) {
            return $this->error('Unauthorized to access this user', 403);
        }

        return $this->success($user, 'User retrieved successfully');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        if (! $user) {
            return $this->error('User not found', 404);
        }

        if (! $this->canManageUser($user)) {
            return $this->error('Unauthorized to update this user', 403);
        }

        $validated = $request->validate([
            'email' => 'sometimes|email|unique:users,email,'.$id,
            'name' => 'sometimes|string',
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:student,supervisor,admin,super_admin',
        ]);

        if (! $this->currentUserIsSuperAdmin()) {
            if (in_array((string) ($validated['role'] ?? $user->role), ['admin', 'super_admin'], true)) {
                return $this->error('You are not authorised to assign the admin or super admin role.', 403);
            }

            $validated['university_id'] = $this->currentUniversityId();
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return $this->success($user, 'User updated successfully');
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::find($id);
        if (! $user) {
            return $this->error('User not found', 404);
        }

        if (! $this->canManageUser($user)) {
            return $this->error('Unauthorized to delete this user', 403);
        }

        $user->delete();

        return $this->success(null, 'User deleted successfully');
    }

    public function getConfig(Request $request)
    {
        $universityId = $this->resolveManagedUniversityId(
            $request->query('university_id', session('university_id'))
        );
        $configs = SystemConfig::where('university_id', $universityId)->get();

        return $this->success($configs->pluck('config_value', 'config_key'), 'Configuration retrieved');
    }

    public function getConfigValue(Request $request, $key)
    {
        $universityId = $this->resolveManagedUniversityId(
            $request->query('university_id', session('university_id'))
        );
        $config = SystemConfig::where([
            ['university_id', '=', $universityId],
            ['config_key', '=', $key],
        ])->first();

        if (! $config) {
            return $this->error('Configuration key not found', 404);
        }

        return $this->success($config->getTypedValue(), 'Configuration value retrieved');
    }

    public function updateConfig(Request $request)
    {
        $validated = $request->validate([
            'university_id' => 'sometimes|exists:universities,id',
            'config_key' => 'required|string',
            'config_value' => 'required',
            'data_type' => 'sometimes|in:string,integer,boolean,json,array',
        ]);

        $universityId = $this->resolveManagedUniversityId(
            $validated['university_id'] ?? session('university_id')
        );
        $dataType = $validated['data_type'] ?? 'string';

        $config = SystemConfig::updateOrCreate(
            [
                'university_id' => $universityId,
                'config_key' => $validated['config_key'],
            ],
            [
                'config_value' => $validated['config_value'],
                'data_type' => $dataType,
            ]
        );

        return $this->success($config, 'Configuration updated successfully');
    }

    public function getSystemStatus(Request $request)
    {
        return $this->success([
            'status' => 'healthy',
            'database' => 'connected',
            'api_version' => '1.0',
            'timestamp' => now(),
        ], 'System status retrieved');
    }

    public function healthCheck(Request $request)
    {
        return $this->success(['status' => 'ok'], 'Health check passed');
    }

    public function getAuditLogs(Request $request)
    {
        $universityId = $this->resolveManagedUniversityId(
            $request->query('university_id', session('university_id'))
        );
        $limit = $request->query('limit', 100);

        $logs = AuditLog::where('university_id', $universityId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $this->success($logs, 'Audit logs retrieved successfully');
    }

    public function getAuditLog(Request $request, $id)
    {
        $log = AuditLog::find($id);
        if (! $log) {
            return $this->error('Audit log not found', 404);
        }

        if (! $this->currentUserIsSuperAdmin() && (int) $log->university_id !== $this->currentUniversityId()) {
            return $this->error('Unauthorized to access this audit log', 403);
        }

        return $this->success($log, 'Audit log retrieved successfully');
    }

    public function listAllResources(Request $request)
    {
        $universityId = $this->resolveManagedUniversityId(
            $request->query('university_id', session('university_id'))
        );
        $resources = Resource::where('university_id', $universityId)->get();

        return $this->success($resources, 'Resources retrieved successfully');
    }

    public function createResource(Request $request)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'section' => 'required|string',
            'type' => 'required|in:video,document,link,quiz,assignment',
            'title' => 'required|string',
            'url' => 'required|url',
            'description' => 'required|string',
            'stage' => 'required|integer|min:1|max:12',
            'points' => 'sometimes|integer|min:0',
            'is_mandatory' => 'sometimes|boolean',
        ]);

        if (! $this->currentUserIsSuperAdmin()) {
            $validated['university_id'] = $this->currentUniversityId();
        }

        $resource = Resource::create($validated);

        return $this->success($resource, 'Resource created successfully', 201);
    }

    public function updateResource(Request $request, $id)
    {
        $resource = Resource::find($id);
        if (! $resource) {
            return $this->error('Resource not found', 404);
        }

        if (! $this->canManageUniversityRecord((int) $resource->university_id)) {
            return $this->error('Unauthorized to update this resource', 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string',
            'url' => 'sometimes|url',
            'description' => 'sometimes|string',
            'stage' => 'sometimes|integer|min:1|max:12',
            'points' => 'sometimes|integer|min:0',
            'is_mandatory' => 'sometimes|boolean',
        ]);

        $resource->update($validated);

        return $this->success($resource, 'Resource updated successfully');
    }

    public function deleteResource(Request $request, $id)
    {
        $resource = Resource::find($id);
        if (! $resource) {
            return $this->error('Resource not found', 404);
        }

        if (! $this->canManageUniversityRecord((int) $resource->university_id)) {
            return $this->error('Unauthorized to delete this resource', 403);
        }

        $resource->delete();

        return $this->success(null, 'Resource deleted successfully');
    }

    public function listArchiveSubmissions(Request $request)
    {
        $universityId = $this->resolveManagedUniversityId(
            $request->query('university_id', session('university_id'))
        );
        $status = $request->query('status');
        $degreeLevel = $request->query('degree_level');

        $query = ArchiveSubmission::where('university_id', $universityId)
            ->with('student', 'reviewer')
            ->orderBy('updated_at', 'desc');

        if ($status) {
            $query->where('submission_status', $status);
        }

        if ($degreeLevel) {
            $query->where('degree_level', $degreeLevel);
        }

        return $this->success($query->get(), 'Archive submissions retrieved successfully');
    }

    protected function currentUserIsSuperAdmin(): bool
    {
        return session('role') === 'super_admin';
    }

    protected function currentUniversityId(): ?int
    {
        $universityId = session('university_id');

        return $universityId === null ? null : (int) $universityId;
    }

    protected function resolveManagedUniversityId($requestedUniversityId): ?int
    {
        if ($this->currentUserIsSuperAdmin()) {
            return $requestedUniversityId === null || $requestedUniversityId === ''
                ? null
                : (int) $requestedUniversityId;
        }

        return $this->currentUniversityId();
    }

    protected function canManageUniversityRecord(?int $universityId): bool
    {
        if ($this->currentUserIsSuperAdmin()) {
            return true;
        }

        return $universityId !== null && $universityId === $this->currentUniversityId();
    }

    protected function canManageUser(User $user): bool
    {
        if ($this->currentUserIsSuperAdmin()) {
            return true;
        }

        // Non-super-admin operators may not touch admin or super admin
        // records at all, so privileged accounts are invisible to them.
        return ! in_array($user->role, ['admin', 'super_admin'], true)
            && $this->canManageUniversityRecord($user->university_id === null ? null : (int) $user->university_id);
    }

    protected function roleSpecificRules(string $role): array
    {
        if ($role === 'supervisor') {
            return [
                'supervisor_title' => 'required|string|max:255',
                'research_areas' => 'nullable|string',
                'booking_url' => 'nullable|url|max:2048',
            ];
        }

        return [];
    }
}
