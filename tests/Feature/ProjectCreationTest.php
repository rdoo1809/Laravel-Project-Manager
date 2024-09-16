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
        $validProject = Project::factory()->raw();
        $managerUser = User::factory()->manager()->create();
        $this->followingRedirects();

        $response = $this->actingAs($managerUser)
            ->fromRoute('dashboard')
            ->post(route('projects.store'), $validProject)
            ->assertSuccessful()
            ->assertSee($validProject['title']);

        $this->assertDatabaseHas('projects', $validProject);

        $newProject = Project::query()->where('title', $validProject['title'])->first();

        //assert only a single assignee
        $this->assertCount(1, $newProject->assignees);
        $this->assertTrue($newProject->assignees->first->is($managerUser));


        //pivot tables - singular form - in alpha order


        //confirm project has 1 member - who is manager


    }

}
