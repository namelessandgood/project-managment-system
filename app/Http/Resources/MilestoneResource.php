<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MilestoneResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'project_id' => $this->project_id,
      'title' => $this->title,
      'description' => $this->description,
      'due_date' => $this->due_date,
      'creator' => new UserResource($this->whenLoaded('creator')),
      'created_at' => $this->created_at,
    ];
  }
}
