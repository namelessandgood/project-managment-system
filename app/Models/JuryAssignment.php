<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JuryAssignment extends Model
{
  use HasFactory;

  protected $table = 'jury_assignments';

  protected $fillable = [
    'project_id',
    'evaluator_id',
  ];

  public $timestamps = false;

  // Project associated with this jury assignment.
  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'project_id');
  }

  // Evaluator user assigned as juror.
  public function evaluator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'evaluator_id');
  }
}
