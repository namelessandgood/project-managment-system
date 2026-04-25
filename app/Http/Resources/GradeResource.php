<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'project_id' => $this->project_id,
      'supervisor_score' => $this->supervisor_score,
      'jury_average_score' => $this->jury_average_score,
      'final_score' => $this->final_score,
      'project' => new ProjectResource($this->whenLoaded('project')),
      'calculated_at' => $this->calculated_at,
      'recalculated_at' => $this->recalculated_at,
    ];
  }
}
