<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignJuryRequest;
use App\Http\Requests\AssignSupervisorRequest;
use App\Models\JuryAssignment;
use App\Models\Project;
use App\Models\SupervisorAssignment;
use App\Models\SupervisorProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
  /** @param Request $request */
  public function showSupervisor(Request $request, int $id): JsonResponse
  {
    Project::query()->findOrFail($id);

    $assignment = SupervisorAssignment::query()
      ->with(['supervisor.role'])
      ->where('project_id', $id)
      ->where('is_active', true)
      ->first();

    return ApiResponse::success($assignment, 'Active supervisor fetched');
  }

  /** @param Request $request */
  public function assignSupervisor(AssignSupervisorRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    Project::query()->findOrFail($id);

    $supervisorId = (int) $request->validated('supervisor_id');
    $capacity = (int) (SupervisorProfile::query()->where('user_id', $supervisorId)->value('max_projects') ?? 0);
    $activeCount = SupervisorAssignment::query()
      ->where('supervisor_id', $supervisorId)
      ->where('is_active', true)
      ->count();

    if ($capacity > 0 && $activeCount >= $capacity) {
      return ApiResponse::error('Supervisor is at maximum capacity', 422);
    }

    $assignment = DB::transaction(function () use ($id, $supervisorId): SupervisorAssignment {
      // Keep assignment history while ensuring only one active row exists per project.
      SupervisorAssignment::query()
        ->where('project_id', $id)
        ->where('is_active', true)
        ->update(['is_active' => false]);

      return SupervisorAssignment::query()->create([
        'project_id' => $id,
        'supervisor_id' => $supervisorId,
        'is_active' => true,
      ]);
    });

    return ApiResponse::success($assignment->load('supervisor.role'), 'Supervisor assigned');
  }

  /** @param Request $request */
  public function supervisorHistory(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    Project::query()->findOrFail($id);

    $history = SupervisorAssignment::query()
      ->with('supervisor.role')
      ->where('project_id', $id)
      ->orderByDesc('assigned_at')
      ->paginate(20);

    return ApiResponse::success(JsonResource::collection($history), 'Supervisor assignment history fetched');
  }

  /** @param Request $request */
  public function juryIndex(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin, RoleName::Supervisor])) {
      return ApiResponse::error('Forbidden', 403);
    }

    Project::query()->findOrFail($id);

    $jury = JuryAssignment::query()
      ->with('evaluator.role')
      ->where('project_id', $id)
      ->paginate(20);

    return ApiResponse::success(JsonResource::collection($jury), 'Jury evaluators fetched');
  }

  /** @param Request $request */
  public function assignJury(AssignJuryRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    Project::query()->findOrFail($id);

    $assignment = JuryAssignment::query()->firstOrCreate([
      'project_id' => $id,
      'evaluator_id' => (int) $request->validated('evaluator_id'),
    ]);

    return ApiResponse::success($assignment->load('evaluator.role'), 'Jury evaluator assigned');
  }

  /** @param Request $request */
  public function removeJury(Request $request, int $id, int $evaluatorId): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    JuryAssignment::query()
      ->where('project_id', $id)
      ->where('evaluator_id', $evaluatorId)
      ->delete();

    return ApiResponse::success(null, 'Jury evaluator removed');
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
