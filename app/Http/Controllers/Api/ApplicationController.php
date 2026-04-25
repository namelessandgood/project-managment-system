<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\ApplicationStatus;
use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PatchApplicationStatusRequest;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\ApplicationResourceCollection;
use App\Models\ProjectApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
  /** @param Request $request */
  public function index(Request $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Supervisor, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $query = ProjectApplication::query()->with(['topic.creator.role', 'group.creator.role']);

    if ($request->filled('topic_id')) {
      $query->where('topic_id', (int) $request->input('topic_id'));
    }

    if ($request->filled('group_id')) {
      $query->where('group_id', (int) $request->input('group_id'));
    }

    if ($request->filled('status')) {
      $query->where('status', $request->string('status'));
    }

    $applications = $query->paginate(20);

    return ApiResponse::success(new ApplicationResourceCollection($applications), 'Applications fetched');
  }

  /** @param Request $request */
  public function store(StoreApplicationRequest $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Student])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $payload = $request->validated();
    $exists = ProjectApplication::query()
      ->where('topic_id', (int) $payload['topic_id'])
      ->where('group_id', (int) $payload['group_id'])
      ->exists();

    if ($exists) {
      return ApiResponse::error('This group has already applied to this topic', 422);
    }

    $application = ProjectApplication::query()->create([
      ...$payload,
      'status' => ApplicationStatus::Pending->value,
    ]);

    return ApiResponse::success(new ApplicationResource($application->load(['topic', 'group'])), 'Application submitted', 201);
  }

  /** @param Request $request */
  public function show(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Supervisor, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $application = ProjectApplication::query()->with(['topic', 'group.users.role'])->findOrFail($id);

    return ApiResponse::success(new ApplicationResource($application), 'Application fetched');
  }

  /** @param Request $request */
  public function patchStatus(PatchApplicationStatusRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $application = ProjectApplication::query()->findOrFail($id);
    $application->status = (string) $request->validated('status');
    $application->save();

    return ApiResponse::success(new ApplicationResource($application->load(['topic', 'group'])), 'Application status updated');
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
