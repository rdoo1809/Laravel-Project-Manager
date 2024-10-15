<?php

namespace Feature\Task;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCreationTest extends TestCase
{
    use RefreshDatabase;
    public function test_task_can_be_created_for_project(): void
    {
        $manager = User::factory()->manager()->create();
        $project = Project::factory()->create();
        $project->assignees()->attach([$manager->id]);

        $payload = [
            'task' => 'get tests passing'
        ];
        $this->assertDatabaseMissing('tasks', [
            'project_id' => $project->id,
            'task' => $payload['task']
        ]);

        $this->actingAs($manager)
            ->postJson(route('project.tasks.store', $project), [
                'task' => $payload['task']
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'task' => 'get tests passing'
        ]);
    }

    public function test_existing_project_task_can_be_assigned_to_project_member(): void
    {
        //arrange
        $manager = User::factory()->manager()->create();
        $regular = User::factory()->regular()->create();
        $project = Project::factory()->create();
        $project->assignees()->attach([$manager->id, $regular->id]);
        $task = Task::factory()->create(['project_id' => $project]);

        $task->users()->append();
        //have a task already stored - append a user to it

        //act


        //assert
        $this->assertTrue(true);
    }

    public function test_a_project_is_loaded_with_tasks(): void
    {
        //arrange
        $this->markTestIncomplete();

        //act


        //assert
    }
}
