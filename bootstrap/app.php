<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('dashboard/article')
                ->name('dashboard.article.')
                ->group(base_path('routes/dashboard/article.php'));

            //dashboard
            Route::middleware('web')
                ->prefix('dashboard/class-room')
                ->name('dashboard.classRoom.')
                ->group(base_path('routes/dashboard/class_room.php'));

            Route::middleware('web')
                ->prefix('dashboard/category')
                ->name('dashboard.category.')
                ->group(base_path('routes/dashboard/category.php'));

            Route::middleware('web')
                ->prefix('dashboard/regional')
                ->name('dashboard.regional.')
                ->group(base_path('routes/dashboard/regional.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
