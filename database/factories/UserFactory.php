<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role; // Importar a classe Role, caso esteja usando roles
use Spatie\Permission\Models\Permission; // Importar Permission para permissões diretas

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function withRole(string $roleName = 'user'): static
    {
        return $this->afterCreating(function ($user) use ($roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $user->assignRole($role);
        });
    }

    public function withPermissions(array $permissions): static
    {
        return $this->afterCreating(function ($user) use ($permissions) {
            foreach ($permissions as $permissionName) {
                $permission = Permission::firstOrCreate(['name' => $permissionName]);
                $user->givePermissionTo($permission);
            }
        });
    }
}
