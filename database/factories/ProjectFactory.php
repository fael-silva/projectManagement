<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'start_date' => now(),
            'end_date' => null,
            'status' => $this->faker->randomElement(['planejado', 'em andamento', 'concluído']),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
