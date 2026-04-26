<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RoleName;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
  public function handle(Request $request, Closure $next): Response
  {
    $user = Auth::user();

    if (! $user || $user->role?->name !== RoleName::Admin) {
      return response()->json(['message' => 'Forbidden'], 403);
    }

    return $next($request);
  }
}
