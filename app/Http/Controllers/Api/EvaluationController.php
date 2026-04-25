<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvaluationRequest;
use App\Http\Resources\EvaluationResource;
use App\Http\Resources\EvaluationResourceCollection;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\Rubric;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
  /** @param Request $request */
  public function indexByProject(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $evaluations = Evaluation::query()
      ->with(['evaluator.role', 'rubric.criteria', 'details.criterion'])
      ->where('project_id', $id)
      ->paginate(20);

    return ApiResponse::success(new EvaluationResourceCollection($evaluations), 'Evaluations fetched');
  }

  /** @param Request $request */
  public function storeByProject(StoreEvaluationRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Evaluator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $existing = Evaluation::query()
      ->where('project_id', $id)
      ->where('evaluator_id', $request->user()->id)
      ->exists();

    if ($existing) {
      return ApiResponse::error('Evaluation already exists for this project and evaluator', 422);
    }

    $rubric = Rubric::query()->with('criteria')->findOrFail((int) $request->validated('rubric_id'));
    $criteriaWeights = $rubric->criteria->keyBy('id');

    $evaluation = DB::transaction(function () use ($request, $id, $rubric, $criteriaWeights): Evaluation {
      $evaluation = Evaluation::query()->create([
        'project_id' => $id,
        'evaluator_id' => $request->user()->id,
        'rubric_id' => $rubric->id,
        'total_score' => 0,
        'notes' => $request->validated('notes'),
      ]);

      $weightedTotal = 0.0;

      foreach ($request->validated('scores') as $scoreItem) {
        $criteriaId = (int) $scoreItem['criteria_id'];
        $score = (float) $scoreItem['score'];
        $criterion = $criteriaWeights->get($criteriaId);

        if (! $criterion) {
          throw new \InvalidArgumentException('Criteria does not belong to selected rubric.');
        }

        EvaluationDetail::query()->create([
          'evaluation_id' => $evaluation->id,
          'criteria_id' => $criteriaId,
          'score' => $score,
        ]);

        // Weighted aggregation based on rubric criterion percentages.
        $weightedTotal += ($score * (float) $criterion->weight_percentage) / 100;
      }

      $evaluation->total_score = $weightedTotal;
      $evaluation->save();

      return $evaluation;
    });

    return ApiResponse::success(
      new EvaluationResource($evaluation->load(['evaluator.role', 'rubric.criteria', 'details.criterion'])),
      'Evaluation created',
      201,
    );
  }

  /** @param Request $request */
  public function show(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin, RoleName::Evaluator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $evaluation = Evaluation::query()
      ->with(['project.group', 'evaluator.role', 'rubric.criteria', 'details.criterion'])
      ->findOrFail($id);

    return ApiResponse::success(new EvaluationResource($evaluation), 'Evaluation fetched');
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
