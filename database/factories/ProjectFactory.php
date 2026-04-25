<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\GroupStatus;
use App\Enums\ProjectStatus;
use App\Enums\RoleName;
use App\Models\Group;
use App\Models\ProjectTopic;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
  public function definition(): array
  {
    $creatorId = $this->resolveCreatorUserId();

    $group = Group::query()->create([
      'name' => fake()->unique()->words(2, true),
      'created_by' => $creatorId,
      'status' => GroupStatus::Proposed->value,
    ]);

    $topic = ProjectTopic::query()->create([
      'title' => fake()->sentence(6),
      'description' => fake()->paragraph(),
      'created_by' => $creatorId,
    ]);

    return [
      'group_id' => $group->id,
      'topic_id' => $topic->id,
      'title' => fake()->sentence(8),
      'abstract' => fake()->paragraph(),
      'objectives' => fake()->paragraph(),
      'tech_stack' => fake()->randomElement(['Laravel', 'Vue', 'PostgreSQL', 'Docker']),
      'status' => ProjectStatus::Pending->value,
    ];
  }

  private function resolveCreatorUserId(): int
  {
    $role = Role::query()->firstOrCreate([
      'name' => RoleName::Student->value,
    ]);

    $user = User::query()->firstOrCreate(
      ['email' => 'factory.student@example.com'],
      [
        'name' => 'Factory Student',
        'password' => Hash::make('password'),
        'role_id' => $role->id,
        'is_active' => true,
      ]
    );

    return $user->id;
  }
}
