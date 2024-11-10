<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function show(Task $task, Project $project)
    {
        $projectTasks = Task::query()->where('project_id', $project->id)->get()->toArray();

        return response()->json([
            'project_tasks' => $projectTasks
        ]);
    }

    public function store(Project $project, Request $request)
    {
        $validated = $request->validate([
            'task' => 'required|string'
        ]);

        $project->tasks()->create($validated);

        return response()->json([
            'tasks' => Task::query()->get()
        ]);
    }

    public function assign(Task $task, Request $request)
    {
        $taskProject = Project::query()->where('id', $task->project_id)->firstOrFail();
        $assignees = [...$request->input('addTaskMembers', [])];

        foreach ($assignees as $assignee) {
            if ($taskProject->assignees->contains($assignee) && !$task->assignees->contains($assignee)) {
//                dd($assignee);
                $task->assignees()->attach([$assignee]);
            }
        }

        $task->load(['assignees']);
        return response()->json([
            'task' => $task,
//            'assignees' => [...$task->assignees]
        ]);
    }

    public function members(Project $project, Task $task)
    {
        $taskAssignees = $task->assignees->pluck('id');
        $nonTaskAssignees = $project->assignees()
            ->whereNotIn('users.id', $taskAssignees)
            ->where('is_manager', 0)->get();

        $task->load(['assignees']);
        return response()->json([
            'selectedTask' => $task,
            'nonAssignees' => [...$nonTaskAssignees]
        ]);
    }

    public function index()
    {
        //
    }

    public function edit(Task $task)
    {
        //
    }

    public function update(Request $request, Task $task)
    {
        //
    }

    public function destroy(Task $task)
    {
        //
    }
}
