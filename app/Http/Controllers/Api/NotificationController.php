<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\NotificationResourceCollection;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
  /** @param Request $request */
  public function index(Request $request): JsonResponse
  {
    $query = Notification::query()
      ->with('user.role')
      ->where('user_id', $request->user()->id)
      ->orderByDesc('created_at');

    if ($request->filled('is_read')) {
      $query->where('is_read', filter_var($request->input('is_read'), FILTER_VALIDATE_BOOLEAN));
    }

    $notifications = $query->paginate(20);

    return ApiResponse::success(new NotificationResourceCollection($notifications), 'Notifications fetched');
  }

  /** @param Request $request */
  public function markAsRead(Request $request, int $id): JsonResponse
  {
    $notification = Notification::query()
      ->where('id', $id)
      ->where('user_id', $request->user()->id)
      ->firstOrFail();

    $notification->is_read = true;
    $notification->save();

    return ApiResponse::success(new NotificationResource($notification), 'Notification marked as read');
  }

  /** @param Request $request */
  public function markAllAsRead(Request $request): JsonResponse
  {
    Notification::query()
      ->where('user_id', $request->user()->id)
      ->where('is_read', false)
      ->update(['is_read' => true]);

    return ApiResponse::success(null, 'All notifications marked as read');
  }

  /** @param Request $request */
  public function store(SendNotificationRequest $request): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Admin, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $validated = $request->validated();
    $recipientIds = collect($validated['user_ids'] ?? [])
      ->when(isset($validated['user_id']), fn($c) => $c->push((int) $validated['user_id']))
      ->unique()
      ->values();

    if ($recipientIds->isEmpty()) {
      return ApiResponse::error('No recipients provided', 422);
    }

    $created = collect();
    foreach ($recipientIds as $recipientId) {
      $created->push(Notification::query()->create([
        'user_id' => $recipientId,
        'title' => (string) $validated['title'],
        'message' => (string) $validated['message'],
        'is_read' => false,
      ]));
    }

    return ApiResponse::success(NotificationResource::collection($created), 'Notifications sent', 201);
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
