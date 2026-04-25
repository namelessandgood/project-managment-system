<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'user_id' => $this->user_id,
      'title' => $this->title,
      'message' => $this->message,
      'is_read' => $this->is_read,
      'user' => new UserResource($this->whenLoaded('user')),
      'created_at' => $this->created_at,
    ];
  }
}
