<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectTopic extends Model
{
  use HasFactory;

  protected $table = 'project_topics';

  protected $fillable = [
    'title',
    'description',
    'created_by',
  ];

  public $timestamps = false;

  // User who created this topic.
  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  // Projects that reference this topic.
  public function projects(): HasMany
  {
    return $this->hasMany(Project::class, 'topic_id');
  }

  // Group applications for this topic.
  public function projectApplications(): HasMany
  {
    return $this->hasMany(ProjectApplication::class, 'topic_id');
  }
}
