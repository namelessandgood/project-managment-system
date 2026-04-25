<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\SubmissionResourceCollection;
use App\Models\Milestone;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
  /** @param Request $request */
  public function indexByMilestone(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Supervisor, RoleName::Coordinator, RoleName::Evaluator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    Milestone::query()->findOrFail($id);

    $submissions = Submission::query()
      ->with(['project.group', 'milestone', 'submitter.role'])
      ->where('milestone_id', $id)
      ->orderByDesc('version_number')
      ->paginate(20);

    return ApiResponse::success(new SubmissionResourceCollection($submissions), 'Submissions fetched');
  }

  /** @param Request $request */
  public function storeByMilestone(StoreSubmissionRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Student])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $milestone = Milestone::query()->findOrFail($id);
    $projectId = (int) $request->validated('project_id');

    if ($milestone->project_id !== $projectId) {
      return ApiResponse::error('Milestone does not belong to the given project', 422);
    }

    // Auto-increment the version per (project_id, milestone_id) pair.
    $nextVersion = ((int) Submission::query()
      ->where('project_id', $projectId)
      ->where('milestone_id', $id)
      ->max('version_number')) + 1;

    $submission = Submission::query()->create([
      'project_id' => $projectId,
      'milestone_id' => $id,
      'version_number' => $nextVersion,
      'file_path' => $request->validated('file_path'),
      'file_type' => $request->validated('file_type'),
      'link_url' => $request->validated('link_url'),
      'submitted_by' => $request->user()->id,
    ]);

    return ApiResponse::success(new SubmissionResource($submission->load(['project', 'milestone', 'submitter.role'])), 'Submission created', 201);
  }

  /** @param Request $request */
  public function show(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Supervisor, RoleName::Evaluator, RoleName::Coordinator])) {
      return ApiResponse::error('Forbidden', 403);
    }

    $submission = Submission::query()
      ->with(['project.group', 'milestone', 'submitter.role', 'feedbackEntries.user.role'])
      ->findOrFail($id);

    return ApiResponse::success(new SubmissionResource($submission), 'Submission fetched');
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
