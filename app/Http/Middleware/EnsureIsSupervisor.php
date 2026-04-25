<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RoleName;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsSupervisor
{
  public function handle(Request $request, Closure $next): Response
  {
    $user = $request->user();

    if (! $user || $user->role?->name !== RoleName::Supervisor) {
      return response()->json(['message' => 'Forbidden'], 403);
    }

    return $next($request);
  }
}
