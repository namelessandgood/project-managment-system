<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Project;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
  use HasFactory;

  protected $table = 'milestones';

  protected $fillable = [
    'project_id',
    'title',
    'description',
    'created_by',
  ];

  public $timestamps = false;

  // Project that owns this milestone.
  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'project_id');
  }

  // User who created this milestone.
  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  // Submissions uploaded for this milestone.
  public function submissions(): HasMany
  {
    return $this->hasMany(Submission::class, 'milestone_id');
  }
}
