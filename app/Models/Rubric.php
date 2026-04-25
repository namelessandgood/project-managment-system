<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Evaluation;
use App\Models\RubricCriterion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rubric extends Model
{
  use HasFactory;

  protected $table = 'rubrics';

  protected $fillable = [
    'name',
    'created_by',
  ];

  // User who created this rubric.
  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  // Criteria rows defined under this rubric.
  public function criteria(): HasMany
  {
    return $this->hasMany(RubricCriterion::class, 'rubric_id');
  }

  // Evaluations that used this rubric.
  public function evaluations(): HasMany
  {
    return $this->hasMany(Evaluation::class, 'rubric_id');
  }
}
