<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Models\Evaluation;
use App\Models\FinalGrade;
use App\Models\Group;
use App\Models\JuryAssignment;
use App\Models\Milestone;
use App\Models\PlagiarismReport;
use App\Models\ProjectTopic;
use App\Models\Submission;
use App\Models\SupervisorAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
  use HasFactory;

  protected $table = 'projects';

  protected $fillable = [
    'group_id',
    'topic_id',
    'title',
    'abstract',
    'objectives',
    'tech_stack',
    'status',
  ];

  protected $casts = [
    'status' => ProjectStatus::class,
  ];

  // Group that owns this project.
  public function group(): BelongsTo
  {
    return $this->belongsTo(Group::class, 'group_id');
  }

  // Topic selected for this project.
  public function topic(): BelongsTo
  {
    return $this->belongsTo(ProjectTopic::class, 'topic_id');
  }

  // Supervisor assignment history for this project.
  public function supervisorAssignments(): HasMany
  {
    return $this->hasMany(SupervisorAssignment::class, 'project_id');
  }

  // Currently active supervisor assignment for this project.
  public function activeSupervisorAssignment(): HasOne
  {
    return $this->hasOne(SupervisorAssignment::class, 'project_id')->where('is_active', true);
  }

  // Supervisors assigned to this project.
  public function supervisors(): BelongsToMany
  {
    return $this->belongsToMany(User::class, 'supervisor_assignments', 'project_id', 'supervisor_id')
      ->withPivot(['is_active', 'assigned_at']);
  }

  // Jury assignment rows for this project.
  public function juryAssignments(): HasMany
  {
    return $this->hasMany(JuryAssignment::class, 'project_id');
  }

  // Evaluators assigned to this project jury.
  public function evaluators(): BelongsToMany
  {
    return $this->belongsToMany(User::class, 'jury_assignments', 'project_id', 'evaluator_id')
      ->withPivot(['assigned_at']);
  }

  // Milestones planned for this project.
  public function milestones(): HasMany
  {
    return $this->hasMany(Milestone::class, 'project_id');
  }

  // Submissions uploaded for this project.
  public function submissions(): HasMany
  {
    return $this->hasMany(Submission::class, 'project_id');
  }

  // Plagiarism reports attached to this project.
  public function plagiarismReports(): HasMany
  {
    return $this->hasMany(PlagiarismReport::class, 'project_id');
  }

  // Evaluations recorded for this project.
  public function evaluations(): HasMany
  {
    return $this->hasMany(Evaluation::class, 'project_id');
  }

  // Final grade computed for this project.
  public function finalGrade(): HasOne
  {
    return $this->hasOne(FinalGrade::class, 'project_id');
  }
}
