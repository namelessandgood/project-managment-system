<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\SubmissionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SubmissionResourceCollection extends ResourceCollection
{
  public $collects = SubmissionResource::class;

  public function toArray(Request $request): array
  {
    return parent::toArray($request);
  }
}
