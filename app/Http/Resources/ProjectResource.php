<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'project_id' => $this->id,
            'project_name' => $this->title,
            'project_description' => $this->description,
            'project_manager' => $this->assignees->where('is_manager', 1)->select('name', 'email'),
            'project_members' => $this->assignees->where('is_manager', 0)->map(function ($assignee) {
                return [
                    'member_name' => $assignee->name,
                    'member_email' => $assignee->email,
                    'member_tasks' => $assignee->tasks->map(function ($task) {
                        return [
                            'task_id' => $task->id,
                            'task' => $task->task,
                        ];
                    })
                ];
            }),
            'project_tasks' => $this->tasks->map(function ($task) {
                return [
                    'task_id' => $task->id,
                    'task' => $task->task,
                    'assignees' => $task->assignees->map(function ($assignee) {
                        return [
                            'assignee_name' => $assignee->name,
                            'assignee_email' => $assignee->email
                        ];
                    })
                ];
            })
        ];
    }
}

