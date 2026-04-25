<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PatchProjectStatusRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectResourceCollection;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
  /** @param Request $request */
  public function index(Request $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin, RoleName::Supervisor])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $projects = Project::query()
      ->with(['group.creator.role', 'group.users.role', 'topic', 'activeSupervisorAssignment.supervisor.role'])
      ->paginate(20);

    return ApiResponse::success(new ProjectResourceCollection($projects), 'Projects fetched');
  }

  /** @param Request $request */
  public function show(Request $request, int $id): JsonResponse
  {
    $project = Project::query()
      ->with([
        'group.creator.role',
        'group.users.role',
        'topic',
        'activeSupervisorAssignment.supervisor.role',
        'milestones',
      ])
      ->findOrFail($id);

    return ApiResponse::success(new ProjectResource($project), 'Project fetched');
  }

  /** @param Request $request */
  public function store(StoreProjectRequest $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Student, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $project = Project::query()->create($request->validated());

    return ApiResponse::success(new ProjectResource($project->load(['group', 'topic'])), 'Project created', 201);
  }

  /** @param Request $request */
  public function update(UpdateProjectRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Student, RoleName::Supervisor])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $project = Project::query()->findOrFail($id);
    $project->fill($request->validated());
    $project->save();

    return ApiResponse::success(new ProjectResource($project->load(['group', 'topic'])), 'Project updated');
  }

  /** @param Request $request */
  public function patchStatus(PatchProjectStatusRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $project = Project::query()->findOrFail($id);
    $project->status = (string) $request->validated('status');
    $project->save();

    return ApiResponse::success(new ProjectResource($project->load(['group', 'topic'])), 'Project status updated');
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
