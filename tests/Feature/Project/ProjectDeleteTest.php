<?php

namespace Feature\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectDeleteTest extends TestCase
{
    use RefreshDatabase;

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

    protected function setUp(): void
    {
        parent::setUp();
        $this->validProject = Project::factory()->create();
    }
}
