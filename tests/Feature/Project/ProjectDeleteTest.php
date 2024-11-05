<?php

namespace Feature\Project;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validProject = Project::factory()->create();
    }

    public function test_a_regular_cannot_delete_a_project()
    {
        $regularUser = User::factory()->regular()->create();

        $this->actingAs($regularUser)
            ->fromRoute('dashboard')
            ->deleteJson(route('projects.destroy', $this->validProject->id))
            ->assertForbidden();

        $this->assertDatabaseCount('projects', 1);
    }

    public function test_a_manager_can_delete_a_project()
    {
        $managerUser = User::factory()->manager()->create();

        $this->actingAs($managerUser)
            ->fromRoute('dashboard')
            ->deleteJson(route('projects.destroy', $this->validProject->id))
            ->assertSuccessful();

        $this->assertDatabaseCount('projects', 0);
    }


    public function test_a_project_with_relations_can_be_deleted(): void
    {
        //arrange
        //      project - user - task
        $this->assertDatabaseCount('projects', 1);
        $managerUser = User::factory()->manager()->create();
        $this->validProject->assignees()->attach([$managerUser->id]);

        // todo not able to delete when there is a task rel?
        $projectTask = Task::factory()->create([
            'project_id' => $this->validProject->id
        ]);

        //act
        // accesss trash route
        $this->actingAs($managerUser)
            ->fromRoute('dashboard')
            ->deleteJson(route('projects.destroy', $this->validProject->id))
            ->assertSuccessful();

        //assert db missing
        $this->assertDatabaseCount('projects', 0);
        $this->assertTrue(true);
    }
}
