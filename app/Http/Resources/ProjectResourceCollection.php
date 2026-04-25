<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectResourceCollection extends ResourceCollection
{
  public $collects = ProjectResource::class;

  public function toArray(Request $request): array
  {
    return parent::toArray($request);
  }
}
