<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_regular_cannot_view_edit_screen_for_a_project(): void
    {
        $regularUser = User::factory()->regular()->create();
        $project = Project::factory()->create();

        $this->actingAs($regularUser)
            ->fromRoute('dashboard')
            ->get(route('projects.edit', $project))
            ->assertForbidden();
    }

    public function test_a_manager_can_view_edit_screen_for_a_project(): void
    {
        $managerUser = User::factory()->manager()->create();
        $project = Project::factory()->create();
        $project->assignees()->attach([$managerUser->id]);

        $this->actingAs($managerUser)
            ->fromRoute('dashboard')
            ->get(route('projects.edit', $project))
            ->assertSuccessful();
    }

    public function test_a_manager_can_patch_an_updated_project(): void
    {
        $managerUser = User::factory()->manager()->create();
        $originalProject = Project::factory()->create([
            'title' => 'old title',
            'description' => 'old description'
        ]);
        $newPayload = [
            'title' => 'new title',
            'description' => 'new description',
        ];

        $this->actingAs($managerUser)
            ->fromRoute('dashboard')
            ->patchJson(route('projects.update', $originalProject), $newPayload)
            ->assertRedirect();

        $updatedProject = Project::query()->where('id', $originalProject->id)->first();

        $this->assertNotEquals($updatedProject->title, $originalProject->title);
        $this->assertNotEquals($updatedProject->description, $originalProject->description);
    }

    /**
     * @dataProvider ProvidesInvalidProjectPayloads
     */
    public function test_project_with_invalid_payload_returns_val_error(array $invalidProject): void
    {
        $managerUser = User::factory()->manager()->create();
        $project = Project::factory()->create();

        $this->actingAs($managerUser)
            ->fromRoute('dashboard')
            ->patchJson(route('projects.update', $project), $invalidProject)
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
