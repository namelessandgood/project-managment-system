<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\GroupResource;
use App\Http\Resources\TopicResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'status' => $this->status?->value ?? $this->status,
      'topic' => new TopicResource($this->whenLoaded('topic')),
      'group' => new GroupResource($this->whenLoaded('group')),
      'applied_at' => $this->applied_at,
    ];
  }
}
