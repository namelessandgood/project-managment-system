<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\GroupResource;
use App\Http\Resources\MilestoneResource;
use App\Http\Resources\TopicResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'status' => $this->status?->value ?? $this->status,
      'abstract' => $this->abstract,
      'objectives' => $this->objectives,
      'tech_stack' => $this->tech_stack,
      'group' => new GroupResource($this->whenLoaded('group')),
      'topic' => new TopicResource($this->whenLoaded('topic')),
      'active_supervisor_assignment' => $this->whenLoaded('activeSupervisorAssignment', function () {
        return [
          'id' => $this->activeSupervisorAssignment?->id,
          'is_active' => $this->activeSupervisorAssignment?->is_active,
          'assigned_at' => $this->activeSupervisorAssignment?->assigned_at,
          'supervisor' => $this->activeSupervisorAssignment?->relationLoaded('supervisor')
            ? new UserResource($this->activeSupervisorAssignment->supervisor)
            : null,
        ];
      }),
      'milestones' => $this->whenLoaded('milestones', fn() => MilestoneResource::collection($this->milestones)),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
