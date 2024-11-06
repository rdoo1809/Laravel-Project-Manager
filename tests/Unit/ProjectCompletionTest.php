<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ProjectCompletionTest extends TestCase
{
    public function test_that_when_a_project_member_is_removed_they_are_also_removed_from_tasks(): void
    {
        $this->markTestSkipped();
        //arrange
        //manager
        //member who is on a project and has a task_user record

        //act - detach user from project


        //assert
        //they are not on the project - have no pivot records
        $this->assertTrue(true);
    }

    public function test_that_project_tasks_can_be_marked_as_completed(): void
    {
        $this->markTestSkipped();
        $this->assertTrue(true);
    }

    public function test_that_a_project_with_no_open_tasks_can_be_marked_completed_by_manager(): void
    {
        $this->markTestSkipped();
        $this->assertTrue(true);
    }
}
