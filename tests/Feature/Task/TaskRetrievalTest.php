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

    protected function setUp(): void
    {
        parent::setUp();

    }
}
