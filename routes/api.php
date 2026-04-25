<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\MilestoneController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PlagiarismController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\RubricController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
  Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

  Route::middleware('auth:sanctum')->group(function (): void {
    // Auth
    Route::prefix('auth')->group(function (): void {
      Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
      Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
    });

    // Users
    Route::apiResource('users', UserController::class)
      ->only(['index', 'store', 'show', 'update'])
      ->parameters(['users' => 'id'])
      ->names([
        'index' => 'users.index',
        'store' => 'users.store',
        'show' => 'users.show',
        'update' => 'users.update',
      ]);

    Route::prefix('users')->group(function (): void {
      Route::patch('/{id}/status', [UserController::class, 'patchStatus'])->name('users.status.patch');
      Route::get('/{id}/departments', [UserController::class, 'departments'])->name('users.departments.index');
    });

    Route::prefix('supervisors')->group(function (): void {
      Route::get('/{id}/profile', [UserController::class, 'supervisorProfile'])->name('supervisors.profile.show');
      Route::put('/{id}/profile', [UserController::class, 'updateSupervisorProfile'])->name('supervisors.profile.update');
    });

    // Departments
    Route::apiResource('departments', DepartmentController::class)
      ->only(['index', 'store', 'update', 'destroy'])
      ->parameters(['departments' => 'id'])
      ->names([
        'index' => 'departments.index',
        'store' => 'departments.store',
        'update' => 'departments.update',
        'destroy' => 'departments.destroy',
      ]);

    Route::prefix('departments')->group(function (): void {
      Route::post('/{id}/users', [DepartmentController::class, 'assignUser'])->name('departments.users.assign');
      Route::delete('/{id}/users/{userId}', [DepartmentController::class, 'removeUser'])->name('departments.users.remove');
    });

    // Groups
    Route::apiResource('groups', GroupController::class)
      ->only(['index', 'store', 'show'])
      ->parameters(['groups' => 'id'])
      ->names([
        'index' => 'groups.index',
        'store' => 'groups.store',
        'show' => 'groups.show',
      ]);

    Route::prefix('groups')->group(function (): void {
      Route::patch('/{id}/status', [GroupController::class, 'patchStatus'])->name('groups.status.patch');
      Route::get('/{id}/members', [GroupController::class, 'members'])->name('groups.members.index');
      Route::post('/{id}/members', [GroupController::class, 'addMember'])->name('groups.members.store');
      Route::delete('/{id}/members/{userId}', [GroupController::class, 'removeMember'])->name('groups.members.destroy');
    });

    // Topics
    Route::apiResource('topics', TopicController::class)
      ->only(['index', 'store', 'show', 'update'])
      ->parameters(['topics' => 'id'])
      ->names([
        'index' => 'topics.index',
        'store' => 'topics.store',
        'show' => 'topics.show',
        'update' => 'topics.update',
      ]);

    // Projects
    Route::apiResource('projects', ProjectController::class)
      ->only(['index', 'store', 'show', 'update'])
      ->parameters(['projects' => 'id'])
      ->names([
        'index' => 'projects.index',
        'store' => 'projects.store',
        'show' => 'projects.show',
        'update' => 'projects.update',
      ]);

    Route::prefix('projects')->group(function (): void {
      Route::patch('/{id}/status', [ProjectController::class, 'patchStatus'])->name('projects.status.patch');
      Route::get('/{id}/supervisor', [AssignmentController::class, 'showSupervisor'])->name('projects.supervisor.show');
      Route::post('/{id}/supervisor', [AssignmentController::class, 'assignSupervisor'])->name('projects.supervisor.assign');
      Route::get('/{id}/supervisor/history', [AssignmentController::class, 'supervisorHistory'])->name('projects.supervisor.history');
      Route::get('/{id}/jury', [AssignmentController::class, 'juryIndex'])->name('projects.jury.index');
      Route::post('/{id}/jury', [AssignmentController::class, 'assignJury'])->name('projects.jury.assign');
      Route::delete('/{id}/jury/{evaluatorId}', [AssignmentController::class, 'removeJury'])->name('projects.jury.remove');
      Route::get('/{id}/milestones', [MilestoneController::class, 'indexByProject'])->name('projects.milestones.index');
      Route::post('/{id}/milestones', [MilestoneController::class, 'storeByProject'])->name('projects.milestones.store');
      Route::get('/{id}/plagiarism', [PlagiarismController::class, 'indexByProject'])->name('projects.plagiarism.index');
      Route::post('/{id}/plagiarism', [PlagiarismController::class, 'storeByProject'])->name('projects.plagiarism.store');
      Route::get('/{id}/evaluations', [EvaluationController::class, 'indexByProject'])->name('projects.evaluations.index');
      Route::post('/{id}/evaluations', [EvaluationController::class, 'storeByProject'])->name('projects.evaluations.store');
      Route::get('/{id}/grade', [GradeController::class, 'showByProject'])->name('projects.grade.show');
      Route::post('/{id}/grade/calculate', [GradeController::class, 'calculate'])->name('projects.grade.calculate');
    });

    // Applications
    Route::apiResource('applications', ApplicationController::class)
      ->only(['index', 'store', 'show'])
      ->parameters(['applications' => 'id'])
      ->names([
        'index' => 'applications.index',
        'store' => 'applications.store',
        'show' => 'applications.show',
      ]);

    Route::patch('/applications/{id}/status', [ApplicationController::class, 'patchStatus'])->name('applications.status.patch');

    // Milestones and submissions
    Route::prefix('milestones')->group(function (): void {
      Route::put('/{id}', [MilestoneController::class, 'update'])->name('milestones.update');
      Route::delete('/{id}', [MilestoneController::class, 'destroy'])->name('milestones.destroy');
      Route::get('/{id}/submissions', [SubmissionController::class, 'indexByMilestone'])->name('milestones.submissions.index');
      Route::post('/{id}/submissions', [SubmissionController::class, 'storeByMilestone'])->name('milestones.submissions.store');
    });

    Route::prefix('submissions')->group(function (): void {
      Route::get('/{id}', [SubmissionController::class, 'show'])->name('submissions.show');
      Route::get('/{id}/feedback', [FeedbackController::class, 'indexBySubmission'])->name('submissions.feedback.index');
      Route::post('/{id}/feedback', [FeedbackController::class, 'storeBySubmission'])->name('submissions.feedback.store');
    });

    // Feedback and plagiarism
    Route::prefix('feedback')->group(function (): void {
      Route::put('/{id}', [FeedbackController::class, 'update'])->name('feedback.update');
      Route::delete('/{id}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');
    });

    // Rubrics, evaluations, and grades
    Route::apiResource('rubrics', RubricController::class)
      ->only(['index', 'store', 'update'])
      ->parameters(['rubrics' => 'id'])
      ->names([
        'index' => 'rubrics.index',
        'store' => 'rubrics.store',
        'update' => 'rubrics.update',
      ]);

    Route::get('/evaluations/{id}', [EvaluationController::class, 'show'])->name('evaluations.show');

    // Notifications
    Route::prefix('notifications')->group(function (): void {
      Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
      Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read.patch');
      Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all.patch');
      Route::post('/', [NotificationController::class, 'store'])->name('notifications.store');
    });
  });
});
