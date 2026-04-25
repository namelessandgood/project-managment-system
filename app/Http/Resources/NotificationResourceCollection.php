<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationResourceCollection extends ResourceCollection
{
  public $collects = NotificationResource::class;

  public function toArray(Request $request): array
  {
    return parent::toArray($request);
  }
}
