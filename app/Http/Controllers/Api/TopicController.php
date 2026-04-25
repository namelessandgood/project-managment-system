<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectTopicRequest;
use App\Http\Requests\UpdateProjectTopicRequest;
use App\Http\Resources\TopicResource;
use App\Http\Resources\TopicResourceCollection;
use App\Models\ProjectTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopicController extends Controller
{
  /** @param Request $request */
  public function index(Request $request): JsonResponse
  {
    $query = ProjectTopic::query()->with(['creator.role']);

    if ($request->filled('created_by')) {
      $query->where('created_by', (int) $request->input('created_by'));
    }

    $topics = $query->paginate(20);

    return ApiResponse::success(new TopicResourceCollection($topics), 'Topics fetched');
  }

  /** @param Request $request */
  public function store(StoreProjectTopicRequest $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Supervisor, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $topic = ProjectTopic::query()->create([
      ...$request->validated(),
      'created_by' => $request->user()->id,
    ]);

    return ApiResponse::success(new TopicResource($topic->load('creator.role')), 'Topic created', 201);
  }

  /** @param Request $request */
  public function show(Request $request, int $id): JsonResponse
  {
    $topic = ProjectTopic::query()->with(['creator.role'])->findOrFail($id);

    return ApiResponse::success(new TopicResource($topic), 'Topic fetched');
  }

  /** @param Request $request */
  public function update(UpdateProjectTopicRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Supervisor, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $topic = ProjectTopic::query()->findOrFail($id);
    $topic->fill($request->validated());
    $topic->save();

    return ApiResponse::success(new TopicResource($topic->load('creator.role')), 'Topic updated');
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
