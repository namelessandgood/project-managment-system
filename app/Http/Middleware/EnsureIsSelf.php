<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsSelf
{
  public function handle(Request $request, Closure $next): Response
  {
    $user = $request->user();
    $routeId = (int) $request->route('id');

    if (! $user || $user->id !== $routeId) {
      return response()->json(['message' => 'Forbidden'], 403);
    }

    return $next($request);
  }
}
