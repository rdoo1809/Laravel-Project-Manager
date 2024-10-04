<?php

namespace Tests\Feature;

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
            ->fromRoute('dashboard')
            ->patchJson(route('projects.update', $this->project), $newPayload)
            ->assertRedirect();

        $updatedProject = Project::query()->where('id', $this->project->id)->first();

        $this->assertNotEquals($updatedProject->title, $this->project->title);
        $this->assertNotEquals($updatedProject->description, $this->project->description);
    }

    /**
     * @dataProvider ProvidesInvalidProjectPayloads
     */
    public function test_project_with_invalid_payload_returns_val_error(array $invalidProject): void
    {
        $this->actingAs($this->managerUser)
            ->fromRoute('dashboard')
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
