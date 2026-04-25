<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisorAssignment extends Model
{
  use HasFactory;

  protected $table = 'supervisor_assignments';

  protected $fillable = [
    'project_id',
    'supervisor_id',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'bool',
  ];

  public $timestamps = false;

  // Project associated with this supervisor assignment.
  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'project_id');
  }

  // Supervisor user assigned to the project.
  public function supervisor(): BelongsTo
  {
    return $this->belongsTo(User::class, 'supervisor_id');
  }

  public function scopeActive(Builder $query): Builder
  {
    return $query->where('is_active', true);
  }
}
