<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FileType;
use App\Models\Feedback;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
  use HasFactory;

  protected $table = 'submissions';

  protected $fillable = [
    'project_id',
    'milestone_id',
    'version_number',
    'file_path',
    'file_type',
    'link_url',
    'submitted_by',
  ];

  protected $casts = [
    'file_type' => FileType::class,
  ];

  public $timestamps = false;

  // Project associated with this submission.
  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'project_id');
  }

  // Milestone associated with this submission.
  public function milestone(): BelongsTo
  {
    return $this->belongsTo(Milestone::class, 'milestone_id');
  }

  // User who submitted this delivery.
  public function submitter(): BelongsTo
  {
    return $this->belongsTo(User::class, 'submitted_by');
  }

  // Feedback entries attached to this submission.
  public function feedbackEntries(): HasMany
  {
    return $this->hasMany(Feedback::class, 'submission_id');
  }

  public function getDeliveryTypeAttribute(): string
  {
    return $this->file_path ? 'file' : 'link';
  }
}
