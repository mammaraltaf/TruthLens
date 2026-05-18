        <?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(BadgeSeeder::class);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@truthlens.local'],
            [
                'name' => 'TruthLens Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'user@truthlens.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ])->assignRole('user');

        $this->call(ArticleFeedSeeder::class);
    }
}
