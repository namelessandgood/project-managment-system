<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User;
use App\Models\UserDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
  use HasFactory;

  protected $table = 'departments';

  protected $fillable = [
    'name',
  ];

  public $timestamps = false;

  // User-department link rows that reference this department.
  public function userDepartments(): HasMany
  {
    return $this->hasMany(UserDepartment::class, 'department_id');
  }

  // Users belonging to this department.
  public function users(): BelongsToMany
  {
    return $this->belongsToMany(User::class, 'users_departments', 'department_id', 'user_id');
  }
}
