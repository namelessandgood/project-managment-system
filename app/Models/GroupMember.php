<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMember extends Model
{
  use HasFactory;

  protected $table = 'group_members';

  protected $fillable = [
    'group_id',
    'user_id',
  ];

  public $timestamps = false;

  // Group associated with this membership.
  public function group(): BelongsTo
  {
    return $this->belongsTo(Group::class, 'group_id');
  }

  // User associated with this membership.
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}
