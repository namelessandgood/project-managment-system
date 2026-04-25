<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoleName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
  use HasFactory;

  protected $table = 'roles';

  protected $fillable = [
    'name',
  ];

  protected $casts = [
    'name' => RoleName::class,
  ];

  public $timestamps = false;

  public function users(): HasMany
  {
    return $this->hasMany(User::class, 'role_id');
  }
}
