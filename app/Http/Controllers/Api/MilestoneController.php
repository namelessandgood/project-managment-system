<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMilestoneRequest;
use App\Http\Requests\UpdateMilestoneRequest;
use App\Http\Resources\MilestoneResourceCollection;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
  /** @param Request $request */
  public function indexByProject(Request $request, int $id): JsonResponse
  {
    Project::query()->findOrFail($id);

    $query = Milestone::query()
      ->with(['creator.role'])
      ->where('project_id', $id)
      ->orderBy('due_date');

    if (filter_var($request->input('upcoming', false), FILTER_VALIDATE_BOOLEAN)) {
      $query->where('due_date', '>=', now());
    }

    $milestones = $query->paginate(20);

    return ApiResponse::success(new MilestoneResourceCollection($milestones), 'Milestones fetched');
  }

  /** @param Request $request */
  public function storeByProject(StoreMilestoneRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Supervisor, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    Project::query()->findOrFail($id);

    $milestone = Milestone::query()->create([
      'project_id' => $id,
      'title' => (string) $request->validated('title'),
      'description' => $request->validated('description'),
      'due_date' => $request->validated('due_date'),
      'created_by' => $request->user()->id,
    ]);

    return ApiResponse::success($milestone->load('creator.role'), 'Milestone created', 201);
  }

  /** @param Request $request */
  public function update(UpdateMilestoneRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Supervisor, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $milestone = Milestone::query()->findOrFail($id);
    $milestone->fill($request->validated());
    $milestone->save();

    return ApiResponse::success($milestone, 'Milestone updated');
  }

  /** @param Request $request */
  public function destroy(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $milestone = Milestone::query()->findOrFail($id);
    $milestone->delete();

    return ApiResponse::success(null, 'Milestone deleted');
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
