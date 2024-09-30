<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCreationTest extends TestCase
{
    use RefreshDatabase;

    protected array $validProject = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->validProject = Project::factory()->raw();
        $this->managerUser = User::factory()->manager()->create();
        $this->regularUser = User::factory()->regular()->create();
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

    public function test_a_fresh_project_only_has_manager_as_member_by_default(): void
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
        $regularUserIds = User::factory()->count(5)->create()
            ->pluck('id')->toArray();

        $newProjectPayload = [
            ...$this->validProject,
            'members' => $regularUserIds
        ];

        $this->actingAs($this->managerUser)
            ->fromRoute('dashboard')
            ->post(route('projects.store'), $newProjectPayload)
            ->assertSuccessful();
        $this->assertDatabaseHas('projects', $this->validProject);

        $newProject = Project::query()->where('title', $this->validProject['title'])->first();

        $this->assertTrue(
            !!$newProject->assignees()->where('user_id', '=', $this->managerUser->id)->first(),
            "The manager has not been set as an assignee");

        $this->assertEquals(
            count($regularUserIds),
            $newProject
                ->assignees()
                ->whereIn('users.id', $regularUserIds)
                ->count(),
            "At least 1 regular user has not been assigned to the project."
        );

        $this->assertCount(count($regularUserIds) + 1, $newProject->assignees, "The number of assignees does not match the requirements.");
    }


    //refactor? dataprovider for invalid payload combos to test val
    public function test_project_with_invalid_title_returns_val_error(): void
    {
        $invalidTitleProject = [
            'title' => 123,
            'description' => $this->validProject['description'],
            'members' => []
        ];

        $this->actingAs($this->managerUser)
            ->fromRoute('dashboard')
            ->postJson(route('projects.store'), $invalidTitleProject)
            ->assertUnprocessable();
    }

    public function test_project_with_invalid_description_returns_val_error(): void
    {
        $invalidDescriptionProject = [
            'title' => $this->validProject['title'],
            'description' => 123,
            'members' => []
        ];

        $this->actingAs($this->managerUser)
            ->fromRoute('dashboard')
            ->postJson(route('projects.store'), $invalidDescriptionProject)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('description');
    }

    public function test_project_with_invalid_ids_returns_val_error(): void
    {
        $invalidProjectMembers = [
            ...$this->validProject,
            'members' => ['string']
        ];

        $response = $this->actingAs($this->managerUser)
            ->fromRoute('dashboard')
            ->postJson(route('projects.store'), $invalidProjectMembers);

        $response->assertUnprocessable();
    }
}
