<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlagiarismReport extends Model
{
  use HasFactory;

  protected $table = 'plagiarism_reports';

  protected $fillable = [
    'project_id',
    'report_file',
    'similarity_score',
    'notes',
    'created_by',
  ];

  protected $casts = [
    'similarity_score' => 'float',
  ];

  public $timestamps = false;

  // Project analyzed by this report.
  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'project_id');
  }

  // User who created this report.
  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }
}
