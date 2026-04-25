<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\GroupStatus;
use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddGroupMemberRequest;
use App\Http\Requests\PatchGroupStatusRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupResourceCollection;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupController extends Controller
{
  /** @param Request $request */
  public function index(Request $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Supervisor, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $query = Group::query()->with(['creator.role', 'project', 'users.role']);

    if ($request->filled('status')) {
      $query->where('status', $request->string('status'));
    }

    $groups = $query->paginate(20);

    return ApiResponse::success(new GroupResourceCollection($groups), 'Groups fetched');
  }

  /** @param Request $request */
  public function store(StoreGroupRequest $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Student])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $group = Group::query()->create([
      'name' => (string) $request->validated('name'),
      'created_by' => $request->user()->id,
      'status' => GroupStatus::Proposed->value,
    ]);

    GroupMember::query()->firstOrCreate([
      'group_id' => $group->id,
      'user_id' => $request->user()->id,
    ]);

    return ApiResponse::success(new GroupResource($group->load(['creator.role', 'users.role'])), 'Group created', 201);
  }

  /** @param Request $request */
  public function show(Request $request, int $id): JsonResponse
  {
    $group = Group::query()
      ->with(['creator.role', 'users.role', 'project.topic'])
      ->findOrFail($id);

    return ApiResponse::success(new GroupResource($group), 'Group fetched');
  }

  /** @param Request $request */
  public function patchStatus(PatchGroupStatusRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Supervisor, RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $group = Group::query()->findOrFail($id);
    $group->status = (string) $request->validated('status');
    $group->save();

    return ApiResponse::success(new GroupResource($group->load(['creator.role'])), 'Group status updated');
  }

  /** @param Request $request */
  public function members(Request $request, int $id): JsonResponse
  {
    Group::query()->findOrFail($id);

    $members = GroupMember::query()
      ->with(['user.role'])
      ->where('group_id', $id)
      ->paginate(20);

    return ApiResponse::success(JsonResource::collection($members), 'Group members fetched');
  }

  /** @param Request $request */
  public function addMember(AddGroupMemberRequest $request, int $id): JsonResponse
  {
    $user = $request->user();
    $requestedUserId = (int) $request->validated('user_id');

    if (! $this->hasAnyRole($request, [RoleName::Student, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    if ($user->role?->name === RoleName::Student && $user->id !== $requestedUserId) {
      return ApiResponse::error('Forbidden', 403);
    }

    Group::query()->findOrFail($id);

    $member = GroupMember::query()->firstOrCreate([
      'group_id' => $id,
      'user_id' => $requestedUserId,
    ]);

    return ApiResponse::success($member, 'Member added to group');
  }

  /** @param Request $request */
  public function removeMember(Request $request, int $id, int $userId): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    GroupMember::query()
      ->where('group_id', $id)
      ->where('user_id', $userId)
      ->delete();

    return ApiResponse::success(null, 'Member removed from group');
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
