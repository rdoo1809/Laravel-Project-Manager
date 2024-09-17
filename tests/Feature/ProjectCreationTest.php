<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Tests\TestCase;

class ProjectCreationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->validProject = Project::factory()->raw();
        $this->managerUser = User::factory()->manager()->create();
        $this->regularUser = User::factory()->regular()->create();
        $this->followingRedirects();
    }

    public function test_a_regular_user_cannot_create_a_project(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->fromRoute('projects.create')
            ->post(route('projects.store'), $this->validProject);

        $response->assertForbidden();
        $this->assertDatabaseMissing('projects', $this->validProject);
    }

    public function test_a_manager_can_create_a_project(): void
    {
        $this->actingAs($this->managerUser)
            ->fromRoute('projects.create')
            ->post(route('projects.store'), $this->validProject)
            ->assertSee($this->validProject['title'])
            ->assertSuccessful();
        $this->assertDatabaseHas('projects', $this->validProject);
    }

    public function test_a_fresh_project_is_seen_on_dashboard(): void
    {
        $this->validProject = Project::factory()->raw();
        $this->managerUser = User::factory()->manager()->create();

        $this->actingAs($this->managerUser)
            ->fromRoute('dashboard')
            ->post(route('projects.store'), $this->validProject)
            ->assertSuccessful()
            ->assertSee($this->validProject['title']);
        $this->assertDatabaseHas('projects', $this->validProject);
    }

    public function test_a_fresh_project_only_has_manager_as_member(): void
    {
        $this->actingAs($this->managerUser)
            ->fromRoute('dashboard')
            ->post(route('projects.store'), $this->validProject)
            ->assertSuccessful()
            ->assertSee($this->validProject['title']);
        $this->assertDatabaseHas('projects', $this->validProject);
        $newProject = Project::query()->where('title', $this->validProject['title'])->first();

        $this->assertCount(1, $newProject->assignees);
        $this->assertTrue($newProject->assignees->first()->is($this->managerUser));
    }

    public function test_manager_can_add_regular_assignees_to_new_project(): void
    {
        $this->markTestSkipped();

        $this->actingAs($this->managerUser)
            ->fromRoute('dashboard')
            ->post(route('projects.store'), $this->validProject)
            ->assertSuccessful();
        $this->assertDatabaseHas('projects', $this->validProject);

//        $newProject = Project::query()->where('title', $this->validProject['title'])->first();
//        $this->assertCount(2, $newProject->assignees);
//        $this->assertTrue($newProject->assignees->second()->is($regularUser));
    }
}
