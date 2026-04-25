<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
  use HasFactory;

  protected $table = 'feedback';

  protected $fillable = [
    'submission_id',
    'user_id',
    'comment',
    'is_private',
  ];

  protected $casts = [
    'is_private' => 'bool',
  ];

  public $timestamps = false;

  // Submission that this feedback belongs to.
  public function submission(): BelongsTo
  {
    return $this->belongsTo(Submission::class, 'submission_id');
  }

  // User who wrote this feedback.
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}
