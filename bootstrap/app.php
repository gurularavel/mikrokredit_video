<?php

use App\Http\Middleware\AdminAuthenticated;
use App\Http\Middleware\MerchantBasicAuth;
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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.auth'    => AdminAuthenticated::class,
            'merchant.auth' => MerchantBasicAuth::class,
        ]);

        // Video upload iframe içindən (merchant səhifəsi) də çağırıla bilir.
        // iOS Safari ITP cross-site iframe-də sessiya cookie-sini bloklayır —
        // bu da CSRF token uyğunsuzluğu (419) deməkdir. Bu route onsuz da
        // URL-dəki birdəfəlik token ilə qorunur, ona görə CSRF-dən çıxarılır.
        $middleware->validateCsrfTokens(except: [
            'record/*/upload',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => 0,
                    'message' => $e->getMessage(),
                    'errors'  => $e->errors(),
                ], 422);
            }
        });
    })->create();
