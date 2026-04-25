<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Role;
use App\Models\SupervisorProfile;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        $roles = Role::query()->get();

        foreach ($roles as $role) {
            $email = sprintf('%s@example.com', $role->name->value);

            $user = User::factory()->create([
                'name' => sprintf('%s User', ucfirst($role->name->value)),
                'email' => $email,
                'role_id' => $role->id,
                'is_active' => true,
            ]);

            if ($role->name === RoleName::Supervisor) {
                SupervisorProfile::query()->firstOrCreate(
                    ['user_id' => $user->id],
                    ['max_projects' => 5]
                );
            }
        }
    }
}
