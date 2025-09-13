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
        $middleware->alias([
            'check.role' => \App\Http\Middleware\CheckRole::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'sanitize.input' => \App\Http\Middleware\SanitizeInput::class,
        ]);
        
        // Apply sanitization to API routes
        $middleware->group('api', [
            \App\Http\Middleware\SanitizeInput::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found',
                    'error' => 'The requested resource does not exist'
                ], 404);
            }
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'error' => 'Authentication token is required'
                ], 401);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($request->is('api/*')) {
                $statusCode = $e->getStatusCode();
                $message = match($statusCode) {
                    403 => 'Forbidden - Insufficient permissions',
                    404 => 'Not Found - Resource does not exist',
                    405 => 'Method Not Allowed',
                    429 => 'Too Many Requests - Rate limit exceeded',
                    default => 'An error occurred'
                };

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error' => $e->getMessage() ?: $message
                ], $statusCode);
            }
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*') && app()->environment('production')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Internal server error',
                    'error' => 'An unexpected error occurred'
                ], 500);
            }
        });
    })->create();
