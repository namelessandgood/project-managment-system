<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignDepartmentUserRequest;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use App\Models\UserDepartment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentController extends Controller
{
  /** @param Request $request */
  public function index(Request $request): JsonResponse
  {
    $departments = Department::query()->withCount('users')->paginate(20);

    return ApiResponse::success(JsonResource::collection($departments), 'Departments fetched');
  }

  /** @param Request $request */
  public function store(StoreDepartmentRequest $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $department = Department::query()->create($request->validated());

    return ApiResponse::success($department, 'Department created', 201);
  }

  /** @param Request $request */
  public function update(UpdateDepartmentRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $department = Department::query()->findOrFail($id);
    $department->name = (string) $request->validated('name');
    $department->save();

    return ApiResponse::success($department, 'Department updated');
  }

  /** @param Request $request */
  public function destroy(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $department = Department::query()->withCount('users')->findOrFail($id);

    // Guard against deleting departments currently linked to users.
    if ($department->users_count > 0) {
      return ApiResponse::error('Department has linked users and cannot be deleted', 422);
    }

    $department->delete();

    return ApiResponse::success(null, 'Department deleted');
  }

  /** @param Request $request */
  public function assignUser(AssignDepartmentUserRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    Department::query()->findOrFail($id);

    $link = UserDepartment::query()->firstOrCreate([
      'user_id' => (int) $request->validated('user_id'),
      'department_id' => $id,
    ]);

    return ApiResponse::success($link, 'User assigned to department');
  }

  /** @param Request $request */
  public function removeUser(Request $request, int $id, int $userId): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    UserDepartment::query()
      ->where('department_id', $id)
      ->where('user_id', $userId)
      ->delete();

    return ApiResponse::success(null, 'User removed from department');
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
