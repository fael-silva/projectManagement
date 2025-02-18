<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    }
    /** @test */
    public function it_creates_a_new_project_with_tasks()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $payload = [
            'name' => 'Novo Projeto',
            'description' => 'Descrição do projeto',
            'cep' => '01001000',
            'tasks' => [
                ['title' => 'Tarefa 1', 'description' => 'Descrição da Tarefa 1'],
                ['title' => 'Tarefa 2', 'description' => 'Descrição da Tarefa 2'],
            ],
        ];

        $response = $this->postJson('/api/projects', $payload, [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'start_date',
                'status',
                'address' => ['cep', 'logradouro', 'bairro', 'localidade', 'uf'],
                'tasks' => [
                    ['id', 'title', 'description', 'status'],
                ],
            ]);

        $this->assertDatabaseHas('projects', [
            'name' => 'Novo Projeto',
            'description' => 'Descrição do projeto',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarefa 1',
            'description' => 'Descrição da Tarefa 1',
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarefa 2',
            'description' => 'Descrição da Tarefa 2',
        ]);
    }

    /** @test */
    public function it_returns_all_projects_of_authenticated_user()
    {
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'api']);
        $permission = Permission::firstOrCreate(['name' => 'view own projects', 'guard_name' => 'api']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create([
            'name' => 'Normal User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole($role);
        $user->givePermissionTo($permission);

        $this->actingAs($user, 'api');

        $projects = Project::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $otherUser = User::factory()->create();
        Project::factory()->count(10)->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/projects?per_page=50', [
            'Authorization' => "Bearer " . JWTAuth::fromUser($user),
        ]);

        $response->assertStatus(200);

        $response->assertJsonCount(3, 'data');

        foreach ($response->json('data') as $project) {
            $this->assertEquals($user->id, $project['user_id']);
        }
    }


    /** @test */
    public function it_returns_project_details_for_authenticated_user()
    {
        Project::truncate();
        Task::truncate();

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/projects/{$project->id}", [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
            ]);
    }

    /** @test */
    public function it_returns_404_if_project_does_not_belong_to_user()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $project = Project::factory()->create();

        $response = $this->getJson("/api/projects/{$project->id}", [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_updates_an_existing_project()
    {
        Project::truncate();
        Task::truncate();

        $user = User::factory()
            ->withRole('user')
            ->create();

        $token = JWTAuth::fromUser($user);

        $project = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Projeto Original',
            'description' => 'Descrição Original',
        ]);


        $updatedPayload = [
            'name' => 'Projeto Alterado',
            'description' => 'Descrição Alterada',
            'status' => 'em andamento',
        ];

        $response = $this->putJson("/api/projects/{$project->id}", $updatedPayload, [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $project->id,
                'name' => 'Projeto Alterado',
                'description' => 'Descrição Alterada',
                'status' => 'em andamento',
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Projeto Alterado',
            'description' => 'Descrição Alterada',
            'status' => 'em andamento',
        ]);
    }

    /** @test */
    public function it_updates_a_project_and_its_tasks()
    {
        $this->artisan('migrate:fresh');

        $user = User::factory()
            ->withRole('user')
            ->create();

        $token = JWTAuth::fromUser($user);

        $project = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Projeto Original',
            'description' => 'Descrição Original',
            'status' => 'planejado',
        ]);

        $task1 = Task::factory()->create([
            'project_id' => $project->id,
            'title' => 'Tarefa Original 1',
            'description' => 'Descrição Original 1',
        ]);

        $task2 = Task::factory()->create([
            'project_id' => $project->id,
            'title' => 'Tarefa Original 2',
            'description' => 'Descrição Original 2',
        ]);

        $updatePayload = [
            'name' => 'Projeto Atualizado',
            'description' => 'Descrição Atualizada',
            'status' => 'em andamento',
            'tasks' => [
                [
                    'id' => $task1->id,
                    'title' => 'Tarefa Atualizada 1',
                    'description' => 'Descrição Atualizada 1',
                    'status' => 'concluída',
                ],
                [
                    'title' => 'Nova Tarefa',
                    'description' => 'Descrição Nova',
                ],
            ],
            'tasks_to_remove' => [$task2->id],
        ];

        $response = $this->putJson("/api/projects/{$project->id}", $updatePayload, [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $project->id,
                'name' => 'Projeto Atualizado',
                'description' => 'Descrição Atualizada',
                'status' => 'em andamento',
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Projeto Atualizado',
            'description' => 'Descrição Atualizada',
            'status' => 'em andamento',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task1->id,
            'title' => 'Tarefa Atualizada 1',
            'description' => 'Descrição Atualizada 1',
            'status' => 'concluída',
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Nova Tarefa',
            'description' => 'Descrição Nova',
        ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task2->id,
        ]);
    }

    /** @test */
    public function it_returns_403_if_user_attempts_to_update_a_project_not_owned()
    {
        $this->artisan('migrate:fresh');

        $user = User::factory()
            ->withRole('user')
            ->create();

        $token = JWTAuth::fromUser($user);

        $project = Project::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $response = $this->putJson("/api/projects/{$project->id}", [
            'name' => 'Tentativa de Alteração',
        ], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_deletes_a_project_and_its_tasks()
    {
        $this->artisan('migrate:fresh');

        $user = User::factory()
            ->withRole('user')
            ->create();

        $token = JWTAuth::fromUser($user);

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $task1 = Task::factory()->create([
            'project_id' => $project->id,
        ]);

        $task2 = Task::factory()->create([
            'project_id' => $project->id,
        ]);

        $response = $this->deleteJson("/api/projects/{$project->id}", [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Projeto e tarefas deletados com sucesso!',
            ]);

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task1->id,
        ]);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task2->id,
        ]);
    }

    /** @test */
    public function it_returns_404_when_trying_to_delete_a_project_that_does_not_belong_to_user()
    {
        $this->artisan('migrate:fresh');

        $user = User::factory()
            ->withRole('user')
            ->create();

        $token = JWTAuth::fromUser($user);

        $project = Project::factory()->create();

        $response = $this->deleteJson("/api/projects/{$project->id}", [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(404);
    }


}
