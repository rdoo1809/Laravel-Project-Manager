<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Database\Factories\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectCreationTest extends TestCase
{

    public function test_a_regular_user_cannot_create_a_project(): void
    {
        $validProject = Project::factory()->raw();
        $regularUser = User::factory()->regular()->create();

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
        $this->markTestIncomplete();
    }

}
