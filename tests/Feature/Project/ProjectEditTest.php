<?php

namespace Feature\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectEditTest extends TestCase
{
    use RefreshDatabase;

    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->followingRedirects();
        $this->managerUser = User::factory()->manager()->create();
        $this->project = Project::factory()->create([
            'title' => 'old title',
            'description' => 'old description'
        ]);
    }

    public function test_a_regular_cannot_view_edit_screen_for_a_project(): void
    {
        $regularUser = User::factory()->regular()->create();
        $this->actingAs($regularUser)
            ->fromRoute('dashboard')
            ->get(route('projects.edit', $this->project))
            ->assertForbidden();
    }

    public function test_a_manager_can_view_edit_screen_for_a_project(): void
    {
        $this->actingAs($this->managerUser)
            ->fromRoute('dashboard')
            ->get(route('projects.edit', $this->project))
            ->assertSuccessful();
    }

    public function test_a_manager_can_patch_an_updated_project(): void
    {
        $newPayload = [
            'title' => 'new title',
            'description' => 'new description',
        ];

        $this->actingAs($this->managerUser)
            ->fromRoute('projects.edit', $this->project)
            ->patchJson(route('projects.update', $this->project), $newPayload)
            ->assertRedirect();

        $updatedProject = Project::query()->where('id', $this->project->id)->first();

        $this->assertNotEquals($updatedProject->title, $this->project->title);
        $this->assertNotEquals($updatedProject->description, $this->project->description);
    }

    public function test_members_can_be_removed_from_project(): void
    {
        $this->markTestIncomplete();
        //arrange
        $project = Project::factory()->create();
        $manager = User::factory()->manager()->create();
        $regular = User::factory()->regular()->create();
        $project->assignees()->attach([$manager->id, $regular->id]);

        $newPayload = [
            ...$project
        ];

        $this->actingAs($manager)
            ->fromRoute('projects.edit')
            ->post(route('projects.update', $project), $newPayload)
            ->assertSuccessful();


        $this->assertTrue($project->assignees->count() === 2);
    }

    public function test_members_can_be_added_to_project(): void
    {
        $this->project->assignees()->attach([$this->managerUser->id]);
        $regular = User::factory()->regular()->create();
        $newPayload = [
            'title' => $this->project->title,
            'description' => $this->project->description,
            'members' => [...$this->project->assignees->pluck('id'), $regular->id]
        ];

        $this->actingAs($this->managerUser)
            ->fromRoute('projects.edit', $this->project)
            ->patchJson(route('projects.update', $this->project), $newPayload)
            ->assertSuccessful();

        $this->assertEquals(2, $this->project->refresh()->assignees->count());
    }

    /**
     * @dataProvider ProvidesInvalidProjectPayloads
     */
    public function test_project_with_invalid_payload_returns_val_error(array $invalidProject): void
    {
        $this->actingAs($this->managerUser)
            ->fromRoute('projects.edit', $this->project)
            ->patchJson(route('projects.update', $this->project), $invalidProject)
            ->assertUnprocessable();
    }

    public static function ProvidesInvalidProjectPayloads(): array
    {
        return [
            'numeric title' => [
                [
                    'title' => 123,
                    'description' => 'description',
                    'members' => []
                ]
            ],
            'empty title' => [
                [
                    'title' => '',
                    'description' => 'description',
                    'members' => []
                ]
            ],
            'numeric description' => [
                [
                    'title' => 'title',
                    'description' => 123,
                    'members' => []
                ]
            ],
            'empty description' => [
                [
                    'title' => 'tile',
                    'description' => '',
                    'members' => []
                ]
            ]
        ];
    }

}
