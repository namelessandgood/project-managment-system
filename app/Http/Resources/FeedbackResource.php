<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'submission_id' => $this->submission_id,
      'comment' => $this->comment,
      'is_private' => $this->is_private,
      'author' => new UserResource($this->whenLoaded('user')),
      'created_at' => $this->created_at,
    ];
  }
}
