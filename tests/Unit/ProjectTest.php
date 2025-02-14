<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_project_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $project->user);
        $this->assertEquals($user->id, $project->user->id);
    }

    /** @test */
    public function a_project_has_many_tasks()
    {
        $project = Project::factory()->create();
        $task1 = Task::factory()->create(['project_id' => $project->id]);
        $task2 = Task::factory()->create(['project_id' => $project->id]);

        $this->assertTrue($project->tasks->contains($task1));
        $this->assertTrue($project->tasks->contains($task2));
        $this->assertEquals(2, $project->tasks->count());
    }
}

