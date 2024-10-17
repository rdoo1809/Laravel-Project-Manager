<?php

namespace Feature\Task;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TaskCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = User::factory()->manager()->create();
        $this->project = Project::factory()->create();
        $this->project->assignees()->attach([$this->manager->id]);
    }

    //Todo - rn we only add members after task creation - can support both? do we have the tech
    public function test_task_can_be_created_for_project(): void
    {
        $rawTask = Task::factory()->raw([
            'project_id' => $this->project->id
        ]);

        $this->assertDatabaseMissing('tasks', $rawTask);

        $this->actingAs($this->manager)
            ->postJson(route('project.tasks.store', $this->project), $rawTask)
            ->assertSuccessful();

        $this->assertDatabaseHas('tasks', $rawTask);
    }

    public function test_existing_project_task_can_be_assigned_to_project_members(): void
    {
        $manager = User::factory()->manager()->create();
        $regular = User::factory()->regular()->create();
        $project = Project::factory()->create();
        $project->assignees()->attach([$manager->id, $regular->id]);
        $task = Task::factory()->create(['project_id' => $project->id]);

        $pivotPayload = [
            'assignees' => [$regular->id]
        ];

        $this->actingAs($manager)
            ->fromRoute('projects.edit', $project)
            ->postJson(route('project.tasks.assign', $task), $pivotPayload)
            ->assertSuccessful()
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has('task')
                    ->has('assignees', 1)
            );
        $this->assertDatabaseCount('task_user', 1);
    }

    public function test_non_project_members_cannot_be_assigned_to_task(): void
    {
        $this->markTestIncomplete();
    }

    public function test_a_project_is_loaded_with_tasks(): void
    {
        //arrange
        $this->markTestIncomplete();

        //act


        //assert
    }
}
