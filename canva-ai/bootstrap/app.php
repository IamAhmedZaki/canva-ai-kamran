<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CanvaAuthMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'canva.auth' => \App\Http\Middleware\CanvaAuthMiddleware::class,
    ]);

    // Allow session on web routes (including your /canva/*)
    $middleware->web(append: [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Http\Middleware\HandleCors::class,
    ]);

    // This is the KEY: Make API routes stateful when coming from frontend
    // $middleware->statefulApi();

    $middleware->validateCsrfTokens(except: [
        'canva/*',
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();