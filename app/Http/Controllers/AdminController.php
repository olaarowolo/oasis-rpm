<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\Models\User;
use App\Models\SystemConfig;
use App\Models\AuditLog;
use App\Models\ArchiveSubmission;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends BaseController
{
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
            'department' => 'required|string',
            'phone' => 'sometimes|string|nullable',
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
        if (!$university) {
            return $this->error('University not found', 404);
        }
        return $this->success($university, 'University retrieved successfully');
    }

    public function updateUniversity(Request $request, $id)
    {
        $university = University::find($id);
        if (!$university) {
            return $this->error('University not found', 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|unique:universities,name,' . $id,
            'email' => 'sometimes|email',
            'department' => 'sometimes|string',
            'phone' => 'sometimes|string|nullable',
            'branding_color' => 'sometimes|string',
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
        if (!$university) {
            return $this->error('University not found', 404);
        }
        $university->delete();
        return $this->success(null, 'University deleted successfully');
    }

    public function listUsers(Request $request)
    {
        $role = $request->query('role');
        $universityId = $request->query('university_id', session('university_id'));
        
        $query = User::with(['student', 'supervisor', 'university']);
        
        if ($role) {
            $query->where('role', $role);
        }
        
        if ($universityId) {
            $query->where('university_id', $universityId);
        }
        
        $users = $query->get();
        return $this->success($users, 'Users retrieved successfully');
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'email' => 'required|email|unique:users',
            'name' => 'required|string',
            'password' => 'required|string|min:8',
            'role' => 'required|in:student,supervisor,admin,super_admin',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        return $this->success($user, 'User created successfully', 201);
    }

    public function getUser(Request $request, $id)
    {
        $user = User::with('student', 'supervisor')->find($id);
        if (!$user) {
            return $this->error('User not found', 404);
        }
        return $this->success($user, 'User retrieved successfully');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->error('User not found', 404);
        }

        $validated = $request->validate([
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'name' => 'sometimes|string',
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:student,supervisor,admin,super_admin',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return $this->success($user, 'User updated successfully');
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->error('User not found', 404);
        }
        $user->delete();
        return $this->success(null, 'User deleted successfully');
    }

    public function getConfig(Request $request)
    {
        $universityId = $request->query('university_id', session('university_id'));
        $configs = SystemConfig::where('university_id', $universityId)->get();
        return $this->success($configs->pluck('config_value', 'config_key'), 'Configuration retrieved');
    }

    public function getConfigValue(Request $request, $key)
    {
        $universityId = $request->query('university_id', session('university_id'));
        $config = SystemConfig::where([
            ['university_id', '=', $universityId],
            ['config_key', '=', $key],
        ])->first();

        if (!$config) {
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

        $universityId = $validated['university_id'] ?? session('university_id');
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
        $universityId = $request->query('university_id', session('university_id'));
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
        if (!$log) {
            return $this->error('Audit log not found', 404);
        }
        return $this->success($log, 'Audit log retrieved successfully');
    }

    public function listAllResources(Request $request)
    {
        $universityId = $request->query('university_id', session('university_id'));
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

        $resource = Resource::create($validated);
        return $this->success($resource, 'Resource created successfully', 201);
    }

    public function updateResource(Request $request, $id)
    {
        $resource = Resource::find($id);
        if (!$resource) {
            return $this->error('Resource not found', 404);
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
        if (!$resource) {
            return $this->error('Resource not found', 404);
        }
        $resource->delete();
        return $this->success(null, 'Resource deleted successfully');
    }

    public function listArchiveSubmissions(Request $request)
    {
        $universityId = $request->query('university_id', session('university_id'));
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
}
