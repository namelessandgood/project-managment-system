<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalGrade extends Model
{
  use HasFactory;

  protected $table = 'final_grades';

  protected $fillable = [
    'project_id',
    'supervisor_score',
    'jury_average_score',
    'final_score',
  ];

  protected $casts = [
    'supervisor_score' => 'float',
    'jury_average_score' => 'float',
    'final_score' => 'float',
  ];

  public $timestamps = false;

  // Project that owns this final grade.
  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'project_id');
  }
}
