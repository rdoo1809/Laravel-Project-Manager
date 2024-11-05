<?php

namespace Feature\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProjectViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_manager_can_view_all_projects_on_dashboard(): void
    {
        $manager = User::factory()->manager()->create();
        $projects = Project::factory()->count(3)->create();

        $this->actingAs($manager)
            ->get(route('dashboard'))
            ->assertSuccessful()
            ->assertInertia(fn($page) => $page->component('Dashboard')
                ->has('projects', fn(AssertableJson $json) => $json
                    ->has('data', 3))
            );
    }

    public function test_a_regular_can_only_view_projects_they_are_assigned_to_on_dashboard(): void
    {
        $regular = User::factory()->regular()->create();
        $myProject = Project::factory()->create();
        $myProject->assignees()->attach([$regular->id]);
        $notMyproject = Project::factory()->create();

        $response = $this->actingAs($regular)
            ->get(route('dashboard'))
            ->assertSuccessful();

        $response->assertInertia(fn($page) => $page->component('Dashboard')
            ->has('projects', 1)
            ->where('projects.data.0.project_id', $myProject->id)
        );
    }
}
