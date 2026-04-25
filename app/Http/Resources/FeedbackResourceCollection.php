<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\FeedbackResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FeedbackResourceCollection extends ResourceCollection
{
  public $collects = FeedbackResource::class;

  public function toArray(Request $request): array
  {
    return parent::toArray($request);
  }
}
