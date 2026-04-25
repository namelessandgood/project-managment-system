<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\GradeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GradeResourceCollection extends ResourceCollection
{
  public $collects = GradeResource::class;

  public function toArray(Request $request): array
  {
    return parent::toArray($request);
  }
}
