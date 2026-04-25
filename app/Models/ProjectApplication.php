<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Models\Group;
use App\Models\ProjectTopic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectApplication extends Model
{
  use HasFactory;

  protected $table = 'project_applications';

  protected $fillable = [
    'topic_id',
    'group_id',
    'status',
  ];

  protected $casts = [
    'status' => ApplicationStatus::class,
  ];

  public $timestamps = false;

  // Topic that this application targets.
  public function topic(): BelongsTo
  {
    return $this->belongsTo(ProjectTopic::class, 'topic_id');
  }

  // Group that submitted this application.
  public function group(): BelongsTo
  {
    return $this->belongsTo(Group::class, 'group_id');
  }
}
