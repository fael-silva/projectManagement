<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\GeneratesToken;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase, GeneratesToken;

    /** @test */
    public function it_returns_correct_report_data()
    {
        $user = User::factory()->create();
        $token = $this->generateToken($user);

        $this->actingAs($user, 'api');

        Project::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'planejado',
        ]);

        $project = Project::factory()->create(['user_id' => $user->id, 'status' => 'em andamento']);
        Task::factory()->count(5)->create([
            'project_id' => $project->id,
            'status' => 'pendente',
        ]);

        $response = $this->getJson('/api/reports/projects', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_projects',
                'projects_by_status' => ['planejado', 'em andamento', 'concluído'],
                'total_tasks',
                'tasks_by_status' => ['pendente', 'em andamento', 'concluída'],
            ]);

        $response->assertJson([
            'total_projects' => 4,
            'projects_by_status' => [
                'planejado' => 3,
                'em andamento' => 1,
                'concluído' => 0,
            ],
            'total_tasks' => 5,
            'tasks_by_status' => [
                'pendente' => 5,
                'em andamento' => 0,
                'concluída' => 0,
            ],
        ]);
    }
}
