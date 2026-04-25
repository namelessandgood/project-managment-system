<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  /** @param Request $request */
  public function login(LoginRequest $request): JsonResponse
  {
    $credentials = $request->validated();

    $user = \App\Models\User::query()
      ->with('role')
      ->where('email', $credentials['email'])
      ->first();

    if (! $user || ! Hash::check($credentials['password'], $user->password)) {
      return ApiResponse::error('Invalid credentials', 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return ApiResponse::success([
      'token' => $token,
      'user' => new UserResource($user),
    ], 'Authenticated successfully');
  }

  /** @param Request $request */
  public function logout(Request $request): JsonResponse
  {
    $request->user()?->currentAccessToken()?->delete();

    return ApiResponse::success(null, 'Logged out successfully');
  }

  /** @param Request $request */
  public function me(Request $request): JsonResponse
  {
    $user = $request->user()?->loadMissing(['role', 'departments', 'supervisorProfile']);

    return ApiResponse::success(new UserResource($user), 'Current user fetched');
  }
}
