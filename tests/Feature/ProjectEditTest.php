<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_manager_can_view_edit_screen_for_a_project(): void
    {
        //arrange
        $managerUser = User::factory()->manager()->create();
        $project = Project::factory()->create();
        $project->assignees()->attach([$managerUser->id]);

        //act
        $this->actingAs($managerUser)
            ->fromRoute('dashboard')
            ->get(route('projects.edit', $project))
            ->assertSuccessful()
            ->assertLocation(route('projects.edit', $project))
//            ->assertSee('foobar')
//            ->assertSee($this->$project['foobar']);
            ->assertSee($project['foobar']);

        //assert
    }

    public function test_a_manager_can_patch_an_updated_project(): void
    {
        $this->markTestSkipped();
    }

    public function test_updated_project_is_seen_on_dashboard(): void
    {
        $this->markTestSkipped();
    }

    public function test_a_regular_cannot_view_edit_screen_for_a_project(): void
    {
        $this->markTestSkipped();
    }
}
