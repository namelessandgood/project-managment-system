<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\GroupResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupResourceCollection extends ResourceCollection
{
  public $collects = GroupResource::class;

  public function toArray(Request $request): array
  {
    return parent::toArray($request);
  }
}
