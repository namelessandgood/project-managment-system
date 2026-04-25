<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PatchUserStatusRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateSupervisorProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourceCollection;
use App\Models\Department;
use App\Models\SupervisorProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserController extends Controller
{
  /** @param Request $request */
  public function index(Request $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $query = User::query()->with(['role', 'departments', 'supervisorProfile']);

    if ($request->filled('role')) {
      $query->whereHas('role', function ($roleQuery) use ($request): void {
        $roleQuery->where('name', $request->string('role'));
      });
    }

    if ($request->filled('department')) {
      $departmentId = (int) $request->input('department');
      $query->whereHas('departments', function ($departmentQuery) use ($departmentId): void {
        $departmentQuery->where('departments.id', $departmentId);
      });
    }

    if ($request->filled('is_active')) {
      $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
    }

    $users = $query->paginate(20);

    return ApiResponse::success(new UserResourceCollection($users), 'Users fetched');
  }

  /** @param Request $request */
  public function store(StoreUserRequest $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $payload = $request->validated();
    $payload['is_active'] = $payload['is_active'] ?? true;

    $user = User::query()->create($payload);
    $user->load(['role', 'departments', 'supervisorProfile']);

    return ApiResponse::success(new UserResource($user), 'User created', 201);
  }

  /** @param Request $request */
  public function show(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $user = User::query()->with(['role', 'departments', 'supervisorProfile'])->findOrFail($id);

    return ApiResponse::success(new UserResource($user), 'User fetched');
  }

  /** @param Request $request */
  public function update(UpdateUserRequest $request, int $id): JsonResponse
  {
    $actor = $request->user();
    if (! $actor || (! $actor->isAdmin() && $actor->id !== $id)) {
      return ApiResponse::error('Forbidden', 403);
    }

    $user = User::query()->findOrFail($id);
    $user->fill($request->validated());
    $user->save();
    $user->load(['role', 'departments', 'supervisorProfile']);

    return ApiResponse::success(new UserResource($user), 'User updated');
  }

  /** @param Request $request */
  public function patchStatus(PatchUserStatusRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $user = User::query()->findOrFail($id);
    $user->is_active = (bool) $request->validated('is_active');
    $user->save();

    return ApiResponse::success(new UserResource($user->load('role')), 'User status updated');
  }

  /** @param Request $request */
  public function departments(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $user = User::query()->findOrFail($id);
    $departments = Department::query()
      ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
      ->paginate(20);

    return ApiResponse::success(JsonResource::collection($departments), 'User departments fetched');
  }

  /** @param Request $request */
  public function supervisorProfile(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $profile = SupervisorProfile::query()->where('user_id', $id)->first();

    return ApiResponse::success($profile, 'Supervisor profile fetched');
  }

  /** @param Request $request */
  public function updateSupervisorProfile(UpdateSupervisorProfileRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $profile = SupervisorProfile::query()->firstOrCreate(
      ['user_id' => $id],
      ['max_projects' => 5],
    );

    $profile->max_projects = (int) $request->validated('max_projects');
    $profile->save();

    return ApiResponse::success($profile, 'Supervisor profile updated');
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
