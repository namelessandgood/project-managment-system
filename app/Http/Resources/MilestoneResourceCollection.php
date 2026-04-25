<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\MilestoneResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MilestoneResourceCollection extends ResourceCollection
{
  public $collects = MilestoneResource::class;

  public function toArray(Request $request): array
  {
    return parent::toArray($request);
  }
}
