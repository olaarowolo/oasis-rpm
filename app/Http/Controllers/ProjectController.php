<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index()
    {
        $projects = Project::with('members', 'tasks')->paginate(15);
        return response()->json($projects);
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,paused,completed',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'budget' => 'nullable|numeric|min:0',
        ]);

        $project = Project::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return response()->json($project, 201);
    }

    /**
     * Display the specified project
     */
    public function show(Project $project)
    {
        return response()->json($project->load('members', 'tasks', 'resources'));
    }

    /**
     * Update the specified project
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:planning,active,paused,completed',
            'end_date' => 'nullable|date|after:start_date',
            'budget' => 'nullable|numeric|min:0',
        ]);

        $project->update($validated);

        return response()->json($project);
    }

    /**
     * Delete the specified project
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully']);
    }
}
