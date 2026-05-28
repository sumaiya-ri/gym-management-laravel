<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);

        $middleware->alias([
            'admin'       => \App\Http\Middleware\AdminMiddleware::class,
            'trainer'     => \App\Http\Middleware\TrainerMiddleware::class,
            'member'      => \App\Http\Middleware\MemberMiddleware::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'gym_admin'   => \App\Http\Middleware\GymAdminMiddleware::class,
            'abilities'   => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability'     => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                \Illuminate\Support\Facades\Log::warning('Unauthorized access attempt: Failed ability check.', [
                    'user_id' => $request->user()?->id,
                    'email' => $request->user()?->email,
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                ]);
                return response()->json(['message' => 'Invalid abilities.'], 403);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                \Illuminate\Support\Facades\Log::warning('Unauthorized access attempt: Unauthenticated user.', [
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                ]);
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });
    })->create();
