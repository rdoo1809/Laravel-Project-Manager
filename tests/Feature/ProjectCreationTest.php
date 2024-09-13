<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProjectCreationTest extends TestCase
{

    public function test_a_regular_user_cannot_create_a_project(): void
    {
        $validProject = Project::factory()->raw();
        $regularUser = User::factory([
            'is_manager' => 0
        ])->regular()->create();

        $response = $this->actingAs($regularUser)
            ->fromRoute('projects.create')
            ->post(route('projects.store'), $validProject);

        $response->assertForbidden();
        $this->assertDatabaseMissing('projects', $validProject);
    }

    public function test_a_manager_can_create_a_project(): void
    {
        $validProject = Project::factory()->raw();
        $managerUser = User::factory()->manager()->create();

        $this->actingAs($managerUser)
            ->fromRoute('projects.create')
            ->post(route('projects.store'), $validProject)
            ->assertRedirect();
//            ->assertOk();
        $this->assertDatabaseHas('projects', $validProject);

    }

    public function test_a_fresh_project_only_has_manager_as_member(): void
    {
        //arrange
        //valid project
        //valid manager
        $validProject = Project::factory()->raw();
        $managerUser = User::factory()->manager()->create();

        //post to the store route
        $response = $this->actingAs($managerUser)
            ->fromRoute('dashboard')
            ->post(route('projects.store'), $validProject)
            ->assertJsonCount(1)
//            ->assertJsonFragment([
//                'title' => $validProject['title']
//            ]);
            ->assertJson([
                'newProject' => [
                    'title' => $validProject['title'],
                    'description' => $validProject['description'],
                    'assignees' => []
                ]
            ]);
//                ->assertRedirect();


        $this->assertDatabaseHas('projects', $validProject);

        //assert on json
        //there is a key with employees assigned - only manager present
    }

}
