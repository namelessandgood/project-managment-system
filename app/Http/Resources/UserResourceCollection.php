<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResourceCollection extends ResourceCollection
{
  public $collects = UserResource::class;

  public function toArray(Request $request): array
  {
    return parent::toArray($request);
  }
}
