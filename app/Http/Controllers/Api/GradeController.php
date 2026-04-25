<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\GradeResource;
use App\Models\Evaluation;
use App\Models\FinalGrade;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
{
  /** @param Request $request */
  public function showByProject(Request $request, int $id): JsonResponse
  {
    Project::query()->findOrFail($id);

    $grade = FinalGrade::query()->with('project.group')->where('project_id', $id)->first();

    return ApiResponse::success($grade ? new GradeResource($grade) : null, 'Final grade fetched');
  }

  /** @param Request $request */
  public function calculate(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    Project::query()->findOrFail($id);

    $evaluations = Evaluation::query()
      ->with(['evaluator.role'])
      ->where('project_id', $id)
      ->get();

    // Supervisor score comes from evaluator role = supervisor.
    $supervisorEvaluation = $evaluations->first(
      fn(Evaluation $evaluation): bool => $evaluation->evaluator?->role?->name === RoleName::Supervisor,
    );

    $juryEvaluations = $evaluations->filter(
      fn(Evaluation $evaluation): bool => $evaluation->evaluator?->role?->name !== RoleName::Supervisor,
    );

    $supervisorScore = (float) ($supervisorEvaluation?->total_score ?? 0);
    $juryAverageScore = (float) ($juryEvaluations->avg('total_score') ?? 0);
    $finalScore = ($supervisorScore * 0.4) + ($juryAverageScore * 0.6);

    $grade = FinalGrade::query()->where('project_id', $id)->first();

    if ($grade) {
      $grade->supervisor_score = $supervisorScore;
      $grade->jury_average_score = $juryAverageScore;
      $grade->final_score = $finalScore;
      $grade->recalculated_at = now();
      $grade->save();
    } else {
      $grade = new FinalGrade();
      $grade->project_id = $id;
      $grade->supervisor_score = $supervisorScore;
      $grade->jury_average_score = $juryAverageScore;
      $grade->final_score = $finalScore;
      $grade->save();
    }

    return ApiResponse::success(new GradeResource($grade->load('project.group')), 'Final grade calculated');
  }

  private function hasAnyRole(Request $request, array $roles): bool
  {
    $user = $request->user()?->loadMissing('role');

    if (! $user) {
      return false;
    }

    foreach ($roles as $role) {
      if ($user->role?->name === $role) {
        return true;
      }
    }

    return false;
  }
}
