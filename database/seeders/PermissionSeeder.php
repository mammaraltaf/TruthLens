<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view all articles',
            'review reports',
            'manage sources',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name);
        }

        Role::findByName('moderator')->syncPermissions($permissions);
        Role::findByName('admin')->syncPermissions(Permission::all());
    }
}
