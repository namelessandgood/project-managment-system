<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\FeedbackResource;
use App\Http\Resources\MilestoneResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'project_id' => $this->project_id,
      'milestone_id' => $this->milestone_id,
      'version_number' => $this->version_number,
      'file_path' => $this->file_path,
      'file_type' => $this->file_type?->value ?? $this->file_type,
      'link_url' => $this->link_url,
      'delivery_type' => $this->delivery_type,
      'project' => new ProjectResource($this->whenLoaded('project')),
      'milestone' => new MilestoneResource($this->whenLoaded('milestone')),
      'submitter' => new UserResource($this->whenLoaded('submitter')),
      'feedback' => $this->whenLoaded('feedbackEntries', fn() => FeedbackResource::collection($this->feedbackEntries)),
      'created_at' => $this->created_at,
    ];
  }
}
