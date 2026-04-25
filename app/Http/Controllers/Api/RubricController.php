<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRubricRequest;
use App\Http\Requests\UpdateRubricRequest;
use App\Models\Rubric;
use App\Models\RubricCriterion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class RubricController extends Controller
{
  /** @param Request $request */
  public function index(Request $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Evaluator, RoleName::Supervisor])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $rubrics = Rubric::query()
      ->with(['creator.role', 'criteria'])
      ->paginate(20);

    return ApiResponse::success(JsonResource::collection($rubrics), 'Rubrics fetched');
  }

  /** @param Request $request */
  public function store(StoreRubricRequest $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $rubric = DB::transaction(function () use ($request): Rubric {
      $rubric = Rubric::query()->create([
        'name' => (string) $request->validated('name'),
        'created_by' => $request->user()->id,
      ]);

      foreach ($request->validated('criteria') as $criterion) {
        RubricCriterion::query()->create([
          'rubric_id' => $rubric->id,
          'title' => $criterion['title'],
          'weight_percentage' => (float) $criterion['weight_percentage'],
        ]);
      }

      return $rubric;
    });

    return ApiResponse::success($rubric->load(['criteria', 'creator.role']), 'Rubric created', 201);
  }

  /** @param Request $request */
  public function update(UpdateRubricRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $rubric = Rubric::query()->findOrFail($id);

    DB::transaction(function () use ($request, $rubric): void {
      if ($request->has('name')) {
        $rubric->name = (string) $request->validated('name');
        $rubric->save();
      }

      if ($request->has('criteria')) {
        // Replace criterion set atomically to keep rubric weights consistent.
        RubricCriterion::query()->where('rubric_id', $rubric->id)->delete();

        foreach ($request->validated('criteria', []) as $criterion) {
          RubricCriterion::query()->create([
            'rubric_id' => $rubric->id,
            'title' => $criterion['title'],
            'weight_percentage' => (float) $criterion['weight_percentage'],
          ]);
        }
      }
    });

    return ApiResponse::success($rubric->load(['criteria', 'creator.role']), 'Rubric updated');
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
