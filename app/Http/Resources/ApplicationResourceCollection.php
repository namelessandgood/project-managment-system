<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\ApplicationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApplicationResourceCollection extends ResourceCollection
{
  public $collects = ApplicationResource::class;

  public function toArray(Request $request): array
  {
    return parent::toArray($request);
  }
}
