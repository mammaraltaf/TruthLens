<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            BadgeSeeder::class,
        ]);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@truthlens.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        $moderator = User::query()->updateOrCreate(
            ['email' => 'moderator@truthlens.local'],
            [
                'name' => 'Moderator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $moderator->syncRoles(['moderator']);

        User::query()->updateOrCreate(
            ['email' => 'user@truthlens.local'],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        )->assignRole('user');
    }
}
