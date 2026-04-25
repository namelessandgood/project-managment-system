<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\EvaluationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EvaluationResourceCollection extends ResourceCollection
{
  public $collects = EvaluationResource::class;

  public function toArray(Request $request): array
  {
    return parent::toArray($request);
  }
}
