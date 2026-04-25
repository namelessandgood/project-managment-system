<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\EvaluationDetail;
use App\Models\Project;
use App\Models\Rubric;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
  use HasFactory;

  protected $table = 'evaluations';

  protected $fillable = [
    'project_id',
    'evaluator_id',
    'rubric_id',
    'total_score',
    'notes',
  ];

  protected $casts = [
    'total_score' => 'float',
  ];

  public $timestamps = false;

  // Project being evaluated.
  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'project_id');
  }

  // Evaluator who submitted this evaluation.
  public function evaluator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'evaluator_id');
  }

  // Rubric used for scoring this evaluation.
  public function rubric(): BelongsTo
  {
    return $this->belongsTo(Rubric::class, 'rubric_id');
  }

  // Criterion-level scores under this evaluation.
  public function details(): HasMany
  {
    return $this->hasMany(EvaluationDetail::class, 'evaluation_id');
  }
}
