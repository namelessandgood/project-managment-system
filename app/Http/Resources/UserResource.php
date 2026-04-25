<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'email' => $this->email,
      'is_active' => $this->is_active,
      'role' => $this->whenLoaded('role', function (): array {
        return [
          'id' => $this->role->id,
          'name' => $this->role->name?->value ?? $this->role->name,
        ];
      }),
      'departments' => $this->whenLoaded('departments', function () {
        return $this->departments->map(fn($department): array => [
          'id' => $department->id,
          'name' => $department->name,
        ]);
      }),
      'supervisor_profile' => $this->whenLoaded('supervisorProfile', function () {
        return [
          'id' => $this->supervisorProfile?->id,
          'max_projects' => $this->supervisorProfile?->max_projects,
        ];
      }),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
