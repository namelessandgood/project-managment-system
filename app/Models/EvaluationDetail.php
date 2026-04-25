<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Evaluation;
use App\Models\RubricCriterion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationDetail extends Model
{
  use HasFactory;

  protected $table = 'evaluation_details';

  protected $fillable = [
    'evaluation_id',
    'criteria_id',
    'score',
  ];

  protected $casts = [
    'score' => 'float',
  ];

  public $timestamps = false;

  // Parent evaluation for this criterion score.
  public function evaluation(): BelongsTo
  {
    return $this->belongsTo(Evaluation::class, 'evaluation_id');
  }

  // Rubric criterion scored in this detail row.
  public function criterion(): BelongsTo
  {
    return $this->belongsTo(RubricCriterion::class, 'criteria_id');
  }
}
