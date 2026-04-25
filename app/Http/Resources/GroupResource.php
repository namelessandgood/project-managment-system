<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'status' => $this->status?->value ?? $this->status,
      'creator' => new UserResource($this->whenLoaded('creator')),
      'members' => $this->whenLoaded('users', fn() => UserResource::collection($this->users)),
      'project' => new ProjectResource($this->whenLoaded('project')),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
