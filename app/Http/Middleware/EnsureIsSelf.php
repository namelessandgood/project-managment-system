<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsSelf
{
  public function handle(Request $request, Closure $next): Response
  {
    $authId = Auth::id();
    $routeId = (int) $request->route('id');

    if (! $authId || $authId !== $routeId) {
      return response()->json(['message' => 'Forbidden'], 403);
    }

    return $next($request);
  }
}
