<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
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
        $assignees = [...$request->input('assignees', [])];

        foreach ($assignees as $assignee) {
            if ($taskProject->assignees->contains($assignee)) {
                $task->assignees()->attach($assignee);
            }
        }

        return response()->json([
            'task' => $task->id,
            'assignees' => [...$task->assignees]
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
