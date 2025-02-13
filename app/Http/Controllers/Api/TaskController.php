<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function index($projectId)
    {
        $project = Project::findOrFail($projectId);

        return response()->json($project->tasks);
    }

    public function store(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pendente,em andamento,concluída',
            'completed_at' => 'nullable|date',
        ]);

        $task = $project->tasks()->create($validated);

        return response()->json($task, 201);
    }

    public function show($projectId, $taskId)
    {
        $project = Project::findOrFail($projectId);

        $task = $project->tasks()->findOrFail($taskId);

        return response()->json($task);
    }

    public function update(Request $request, $projectId, $taskId)
    {
        $project = Project::findOrFail($projectId);

        $task = $project->tasks()->findOrFail($taskId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pendente,em andamento,concluída',
            'completed_at' => 'nullable|date',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function destroy($projectId, $taskId)
    {
        $project = Project::findOrFail($projectId);

        $task = $project->tasks()->findOrFail($taskId);

        $task->delete();

        return response()->json(null, 204);
    }
}

