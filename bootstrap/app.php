<?php

use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\EnsureIsCoordinator;
use App\Http\Middleware\EnsureIsEvaluator;
use App\Http\Middleware\EnsureIsSelf;
use App\Http\Middleware\EnsureIsStudent;
use App\Http\Middleware\EnsureIsSupervisor;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'admin' => EnsureIsAdmin::class,
            'coordinator' => EnsureIsCoordinator::class,
            'supervisor' => EnsureIsSupervisor::class,
            'evaluator' => EnsureIsEvaluator::class,
            'student' => EnsureIsStudent::class,
            'self' => EnsureIsSelf::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
