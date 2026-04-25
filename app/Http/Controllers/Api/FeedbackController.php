<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedbackRequest;
use App\Http\Requests\UpdateFeedbackRequest;
use App\Http\Resources\FeedbackResource;
use App\Http\Resources\FeedbackResourceCollection;
use App\Models\Feedback;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
  /** @param Request $request */
  public function indexBySubmission(Request $request, int $id): JsonResponse
  {
    Submission::query()->findOrFail($id);

    $query = Feedback::query()
      ->with(['user.role'])
      ->where('submission_id', $id)
      ->orderByDesc('created_at');

    if ($request->user()?->loadMissing('role')->role?->name === RoleName::Student) {
      // Students can only see non-private feedback entries.
      $query->where('is_private', false);
    }

    $feedback = $query->paginate(20);

    return ApiResponse::success(new FeedbackResourceCollection($feedback), 'Feedback fetched');
  }

  /** @param Request $request */
  public function storeBySubmission(StoreFeedbackRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Supervisor, RoleName::Evaluator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    Submission::query()->findOrFail($id);

    $feedback = Feedback::query()->create([
      'submission_id' => $id,
      'user_id' => $request->user()->id,
      'comment' => (string) $request->validated('comment'),
      'is_private' => (bool) ($request->validated('is_private') ?? false),
    ]);

    return ApiResponse::success(new FeedbackResource($feedback->load('user.role')), 'Feedback created', 201);
  }

  /** @param Request $request */
  public function update(UpdateFeedbackRequest $request, int $id): JsonResponse
  {
    $feedback = Feedback::query()->findOrFail($id);
    $actor = $request->user()?->loadMissing('role');

    $canEdit = $feedback->user_id === $actor?->id
      || $actor?->role?->name === RoleName::Supervisor
      || $actor?->role?->name === RoleName::Evaluator;

    if (! $canEdit) {
      return ApiResponse::error('Forbidden', 403);
    }

    $feedback->comment = (string) $request->validated('comment');
    $feedback->save();

    return ApiResponse::success(new FeedbackResource($feedback->load('user.role')), 'Feedback updated');
  }

  /** @param Request $request */
  public function destroy(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $feedback = Feedback::query()->findOrFail($id);
    $feedback->delete();

    return ApiResponse::success(null, 'Feedback deleted');
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
