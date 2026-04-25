<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'description' => $this->description,
      'creator' => new UserResource($this->whenLoaded('creator')),
      'created_at' => $this->created_at,
    ];
  }
}
