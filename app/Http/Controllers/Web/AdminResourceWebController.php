<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use App\Models\AuditLog;
use App\Models\Resource;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;

class AdminResourceWebController extends BaseController
{
    /**
     * List all resources within the current admin scope.
     */
    public function index(Request $request)
    {
        $selectedUniversityId = $request->query('university_id', session('university_id'));

        $query = Resource::query()->orderBy('stage')->orderBy('sort_order');
        if ($selectedUniversityId) {
            $query->where('university_id', $selectedUniversityId);
        }

        $resources = $query->get();

        return view('admin.resources', compact('resources', 'selectedUniversityId'));
    }

    /**
     * Show the create resource form.
     */
    public function create(Request $request)
    {
        $universities = University::orderBy('name')->get();
        $selectedUniversityId = $request->query('university_id', session('university_id'));

        return view('admin.resource-form', [
            'resource' => new Resource(['is_mandatory' => false, 'stage' => 1, 'points' => 0]),
            'universities' => $universities,
            'selectedUniversityId' => $selectedUniversityId,
            'mode' => 'create',
        ]);
    }

    /**
     * Persist a new resource.
     */
    public function store(Request $request)
    {
        $validated = $this->validateResource($request, true);

        $validated['is_mandatory'] = $request->boolean('is_mandatory');
        $resource = Resource::create($validated);

        $this->recordAudit($resource, 'created', null, $resource->toArray());

        return redirect()
            ->route('admin.resources', ['university_id' => $resource->university_id])
            ->with('status', 'Resource "' . $resource->title . '" created successfully.');
    }

    /**
     * Show the edit resource form.
     */
    public function edit($id)
    {
        $resource = Resource::findOrFail($id);
        $universities = University::orderBy('name')->get();

        return view('admin.resource-form', [
            'resource' => $resource,
            'universities' => $universities,
            'selectedUniversityId' => $resource->university_id,
            'mode' => 'edit',
        ]);
    }

    /**
     * Update an existing resource.
     */
    public function update(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);
        $oldValues = $resource->toArray();

        $validated = $this->validateResource($request, false);
        $validated['is_mandatory'] = $request->boolean('is_mandatory');

        $resource->update($validated);

        $this->recordAudit($resource, 'updated', $oldValues, $resource->toArray());

        return redirect()
            ->route('admin.resources', ['university_id' => $resource->university_id])
            ->with('status', 'Resource "' . $resource->title . '" updated successfully.');
    }

    /**
     * Delete a resource.
     */
    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);
        $universityId = $resource->university_id;
        $title = $resource->title;

        $this->recordAudit($resource, 'deleted', $resource->toArray(), null);
        $resource->delete();

        return redirect()
            ->route('admin.resources', ['university_id' => $universityId])
            ->with('status', 'Resource "' . $title . '" deleted successfully.');
    }

    /**
     * Shared validation rules. On create every field is required; on update
     * they may be partially provided.
     */
    private function validateResource(Request $request, bool $creating): array
    {
        $required = $creating ? 'required' : 'sometimes';

        return $request->validate([
            'university_id' => [$creating ? 'required' : 'sometimes', 'exists:universities,id'],
            'section' => [$required, 'string', 'max:255'],
            'type' => [$required, 'in:video,document,link,quiz,assignment'],
            'title' => [$required, 'string', 'max:255'],
            'url' => [$required, 'url', 'max:2048'],
            'description' => [$required, 'string'],
            'stage' => [$required, 'integer', 'min:1', 'max:12'],
            'points' => ['sometimes', 'integer', 'min:0'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);
    }

    private function recordAudit(Resource $resource, string $action, ?array $old, ?array $new): void
    {
        $university = University::find($resource->university_id);
        if (!$university) {
            return;
        }

        AuditLog::logAction(
            $university,
            User::find(session('user_id')),
            'Resource',
            $resource->id,
            $action,
            $old,
            $new
        );
    }
}
