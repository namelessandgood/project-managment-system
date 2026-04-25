<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\EvaluationDetail;
use App\Models\Rubric;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RubricCriterion extends Model
{
  use HasFactory;

  protected $table = 'rubric_criteria';

  protected $fillable = [
    'rubric_id',
    'title',
    'weight_percentage',
  ];

  protected $casts = [
    'weight_percentage' => 'float',
  ];

  public $timestamps = false;

  // Rubric that owns this criterion.
  public function rubric(): BelongsTo
  {
    return $this->belongsTo(Rubric::class, 'rubric_id');
  }

  // Evaluation detail rows scored for this criterion.
  public function evaluationDetails(): HasMany
  {
    return $this->hasMany(EvaluationDetail::class, 'criteria_id');
  }
}
