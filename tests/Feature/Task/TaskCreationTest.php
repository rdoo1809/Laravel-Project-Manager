<?php

namespace Feature\Task;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TaskCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = User::factory()->manager()->create();
        $this->project = Project::factory()->create();
        $this->project->assignees()->attach([$this->manager->id]);
        $this->task = Task::factory()->create(['project_id' => $this->project->id]);
    }

    public function test_task_can_be_created_for_project(): void
    {
        $rawTask = Task::factory()->raw([
            'project_id' => $this->project->id
        ]);

        $this->assertDatabaseMissing('tasks', $rawTask);

        $this->actingAs($this->manager)
            ->postJson(route('projects.tasks.store', $this->project), $rawTask)
            ->assertSuccessful();

        $this->assertDatabaseHas('tasks', $rawTask);
    }

    public function test_existing_project_task_will_only_be_assigned_to_selected_project_members(): void
    {
        $regularUserIds = User::factory()->count(2)->regular()->create()->pluck('id')->toArray();
        $this->project->assignees()->attach($regularUserIds);

        $pivotPayload = [
            'addTaskMembers' => [$regularUserIds[0]]
        ];

        $response = $this->actingAs($this->manager)
            ->fromRoute('projects.edit', $this->project)
            ->postJson(route('projects.tasks.assign', $this->task), $pivotPayload)
            ->assertSuccessful();

        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->has('task', fn($json) => $json
                    ->has('assignees', fn($json) => $json
                        ->has('0', fn($json) => $json
                            ->where('id', $regularUserIds[0])->etc())->etc())->etc())
        );
        $this->assertDatabaseCount('task_user', 1);
    }

    public function test_existing_project_task_can_be_assigned_to_many_project_members(): void
    {
        $regularUserIds = User::factory()->count(3)->regular()->create()->pluck('id')->toArray();
        $this->project->assignees()->attach($regularUserIds);

        $pivotPayload = [
            'addTaskMembers' => $regularUserIds
        ];

        $this->actingAs($this->manager)
            ->fromRoute('projects.edit', $this->project)
            ->postJson(route('projects.tasks.assign', $this->task), $pivotPayload)
            ->assertSuccessful()
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has('task', fn($json) => $json
                        ->has('assignees', fn($json) => $json
                            ->has('0', fn($json) => $json
                                ->where('id', $regularUserIds[0])->etc())->etc())->etc())
            );
        $this->assertDatabaseCount('task_user', 3);
    }

    public function test_non_project_members_cannot_be_assigned_to_task(): void
    {
        $projectEmployee = User::factory()->regular()->create();
        $this->project->assignees()->attach([$projectEmployee->id]);
        $otherEmployee = User::factory()->regular()->create();

        $pivotPayload = [
            'addTaskMembers' => [$projectEmployee->id, $otherEmployee->id]
        ];

        $this->actingAs($this->manager)
            ->fromRoute('projects.edit', $this->project)
            ->postJson(route('projects.tasks.assign', $this->task), $pivotPayload)
            ->assertSuccessful()
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->has('task', fn($json) => $json
                        ->has('assignees', fn($json) => $json
                            ->has('0', fn($json) => $json
                                ->where('id', $projectEmployee->id)->etc()))->etc())
            );
        $this->assertDatabaseCount('task_user', 1);
    }

    public function test_existing_task_assignees_cannot_be_double_assigned(): void
    {
        $this->markTestIncomplete();
        //arrange
        //regular employee already attahced to task
        $taskMember = User::factory()->regular()->create();
        $this->task->assignees()->attach($taskMember->id);

        $taskAssigneeCount = $this->task->assignees->count();

        //act
        //try to assign same person to task
//        $this->actingAs($this->managerUser)
//            ->fromRoute('')
//            ->postJson(route(''))

        //assert
        //assert unsuccessful
        //same assignee count as before
        $this->assertDatabaseCount('task_user', '1');
    }
}
