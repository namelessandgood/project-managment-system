<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GroupStatus;
use App\Models\GroupMember;
use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Group extends Model
{
  use HasFactory;

  protected $table = 'groups';

  protected $fillable = [
    'name',
    'created_by',
    'status',
  ];

  protected $casts = [
    'status' => GroupStatus::class,
  ];

  // User who created this group.
  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  // Membership rows belonging to this group.
  public function groupMembers(): HasMany
  {
    return $this->hasMany(GroupMember::class, 'group_id');
  }

  // Users who are members of this group.
  public function users(): BelongsToMany
  {
    return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id');
  }

  // Project attached to this group.
  public function project(): HasOne
  {
    return $this->hasOne(Project::class, 'group_id');
  }

  // Topic applications submitted by this group.
  public function projectApplications(): HasMany
  {
    return $this->hasMany(ProjectApplication::class, 'group_id');
  }
}
