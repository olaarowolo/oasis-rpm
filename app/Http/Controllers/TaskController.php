<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display tasks for a project
     */
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tasks = $query->paginate(20);
        return response()->json($tasks);
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,completed,blocked',
            'priority' => 'required|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        $task = Task::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return response()->json($task, 201);
    }

    /**
     * Display the specified task
     */
    public function show(Task $task)
    {
        return response()->json($task->load('assignee', 'comments', 'attachments'));
    }

    /**
     * Update the specified task
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:todo,in_progress,review,completed,blocked',
            'priority' => 'in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    /**
     * Delete the specified task
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
