<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        Permission::firstOrCreate(['name' => 'view all projects']);
        Permission::firstOrCreate(['name' => 'view own projects']);

        $roleUser = Role::firstOrCreate(['name' => 'user']);
        $roleUser->givePermissionTo(['view own projects']);

        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->syncPermissions(Permission::all());
    }
}
