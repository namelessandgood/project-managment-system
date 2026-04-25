<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LanguageCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
  use HasFactory;

  protected $table = 'translations';

  protected $fillable = [
    'key',
    'language',
    'value',
  ];

  protected $casts = [
    'language' => LanguageCode::class,
  ];

  public $timestamps = false;
}
