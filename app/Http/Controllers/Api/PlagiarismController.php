<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\RoleName;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlagiarismReportRequest;
use App\Models\PlagiarismReport;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlagiarismController extends Controller
{
  /** @param Request $request */
  public function indexByProject(Request $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin, RoleName::Supervisor])) {
      return ApiResponse::error('Forbidden', 403);
    }

    Project::query()->findOrFail($id);

    $reports = PlagiarismReport::query()
      ->with(['creator.role'])
      ->where('project_id', $id)
      ->orderByDesc('created_at')
      ->paginate(20);

    return ApiResponse::success(JsonResource::collection($reports), 'Plagiarism reports fetched');
  }

  /** @param Request $request */
  public function storeByProject(StorePlagiarismReportRequest $request, int $id): JsonResponse
  {
    if (! $this->hasAnyRole($request, [RoleName::Coordinator, RoleName::Admin])) {
      return ApiResponse::error('Forbidden', 403);
    }

    Project::query()->findOrFail($id);

    $path = $request->file('report_file')?->store('plagiarism-reports');
    if (! $path) {
      return ApiResponse::error('Failed to store report file', 422);
    }

    $report = PlagiarismReport::query()->create([
      'project_id' => $id,
      'report_file' => $path,
      'similarity_score' => $request->validated('similarity_score'),
      'notes' => $request->validated('notes'),
      'created_by' => $request->user()->id,
    ]);

    return ApiResponse::success($report->load('creator.role'), 'Plagiarism report uploaded', 201);
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
