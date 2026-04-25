<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'project_id' => $this->project_id,
      'rubric_id' => $this->rubric_id,
      'total_score' => $this->total_score,
      'notes' => $this->notes,
      'project' => new ProjectResource($this->whenLoaded('project')),
      'evaluator' => new UserResource($this->whenLoaded('evaluator')),
      'rubric' => $this->whenLoaded('rubric', function () {
        return [
          'id' => $this->rubric?->id,
          'name' => $this->rubric?->name,
          'criteria' => $this->rubric?->relationLoaded('criteria')
            ? $this->rubric->criteria->map(fn($criterion): array => [
              'id' => $criterion->id,
              'title' => $criterion->title,
              'weight_percentage' => $criterion->weight_percentage,
            ])
            : null,
        ];
      }),
      'details' => $this->whenLoaded('details', function () {
        return $this->details->map(fn($detail): array => [
          'id' => $detail->id,
          'criteria_id' => $detail->criteria_id,
          'score' => $detail->score,
          'criterion' => $detail->relationLoaded('criterion') ? [
            'title' => $detail->criterion?->title,
            'weight_percentage' => $detail->criterion?->weight_percentage,
          ] : null,
        ]);
      }),
      'created_at' => $this->created_at,
    ];
  }
}
