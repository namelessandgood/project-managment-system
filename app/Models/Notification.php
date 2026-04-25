<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
  use HasFactory;

  protected $table = 'notifications';

  protected $fillable = [
    'user_id',
    'title',
    'message',
    'is_read',
  ];

  protected $casts = [
    'is_read' => 'bool',
  ];

  public $timestamps = false;

  // User who received this notification.
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}
