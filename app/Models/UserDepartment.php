<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDepartment extends Model
{
  use HasFactory;

  protected $table = 'users_departments';

  protected $fillable = [
    'user_id',
    'department_id',
  ];

  public $timestamps = false;

  // User linked by this pivot row.
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  // Department linked by this pivot row.
  public function department(): BelongsTo
  {
    return $this->belongsTo(Department::class, 'department_id');
  }
}
