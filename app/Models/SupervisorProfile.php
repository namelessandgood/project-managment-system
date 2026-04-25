<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisorProfile extends Model
{
  use HasFactory;

  protected $table = 'supervisor_profiles';

  protected $fillable = [
    'user_id',
    'max_projects',
  ];

  public $timestamps = false;

  // Supervisor user that owns this profile.
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}
