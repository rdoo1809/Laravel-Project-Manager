<?php

namespace Feature\Task;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TaskRetrievalTest extends TestCase
{
    use RefreshDatabase;

    public function test_tasks_can_be_retrieved_by_project(): void
    {
        $managerUser = User::factory()->manager()->create();
        $project = Project::factory()->create();
        $tasks = Task::factory()->count(2)->create([
            'project_id' => $project->id
        ])->toArray();

        $this->actingAs($managerUser)
            ->fromRoute('dashboard')
            ->getJson(route('projects.tasks.show', $project))
            ->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('project_tasks')
                ->has('project_tasks.0', fn($json) => $json->where('id', $tasks[0]['id'])->etc())
                ->has('project_tasks.1', fn($json) => $json->where('id', $tasks[1]['id'])->etc()));
    }

    public function test_tasks_for_other_projects_are_not_retrieved(): void
    {
        $managerUser = User::factory()->manager()->create();
        $project = Project::factory()->create();
        $tasks = Task::factory()->count(2)->create([
            'project_id' => $project->id
        ])->toArray();
        $otherTask = Task::factory()->create();

        //act - access index route
        $this->actingAs($managerUser)
            ->fromRoute('dashboard')
            ->getJson(route('projects.tasks.show', $project))
            ->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('project_tasks', 2)
                ->has('project_tasks.0', fn($json) => $json->where('id', $tasks[0]['id'])->etc())
                ->has('project_tasks.1', fn($json) => $json->where('id', $tasks[1]['id'])->etc()));
    }

    public function test_tasks_are_retrieved_with_assignees_and_others(): void
    {
        $managerUser = User::factory()->manager()->create();
        $project = Project::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id
        ]);

        $taskMembers = User::factory()->count(2)->create()->pluck('id');
        $project->assignees()->attach($taskMembers);
        $taskMembers->each(function ($member) use ($task) {
            $task->assignees()->attach($member);
        });

        $response = $this->actingAs($managerUser)
            ->fromRoute('projects.edit', $project)
            ->getJson(route('projects.tasks.assignees', [$project, $task]))
            ->assertSuccessful();

        $response->assertJson(fn(AssertableJson $json) => $json
            ->has('selectedTask', fn($json) => $json
                ->has('assignees', fn($json) => $json
                    ->has(2)
                    ->has('0', fn($json) => $json->where('id', $taskMembers->first())
                        ->etc())->etc())->etc())
            ->has('nonAssignees')
        );
    }
}
