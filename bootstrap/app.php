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

            Route::middleware('web')
                ->prefix('dashboard/user')
                ->name('dashboard.user.')
                ->group(base_path('routes/dashboard/user.php'));

            Route::middleware('web')
                ->prefix('dashboard/schedule')
                ->name('dashboard.schedule.')
                ->group(base_path('routes/dashboard/schedule.php'));

            Route::middleware('web')
                ->prefix('dashboard/type-activity')
                ->name('dashboard.typeActivity.')
                ->group(base_path('routes/dashboard/type_activity.php'));

            Route::middleware('web')
                ->prefix('dashboard/submission')
                ->name('dashboard.submission.')
                ->group(base_path('routes/dashboard/submission.php'));

            Route::middleware('web')
                ->prefix('dashboard/speaker')
                ->name('dashboard.speaker.')
                ->group(base_path('routes/dashboard/speaker.php'));

            Route::middleware('web')
                ->prefix('dashboard/guide-cadre')
                ->name('dashboard.guideCadre.')
                ->group(base_path('routes/dashboard/guide_cadre.php'));

            Route::middleware('web')
                ->prefix('dashboard/regency-regional')
                ->name('dashboard.regencyRegional.')
                ->group(base_path('routes/dashboard/regency_regional.php'));

            //committee
            Route::middleware('web')
                ->prefix('committee/schedule')
                ->name('committee.schedule.')
                ->group(base_path('routes/committee/schedule.php'));

            Route::middleware('web')
                ->prefix('committee/participant')
                ->name('committee.participant.')
                ->group(base_path('routes/committee/participant.php'));

            Route::middleware('web')
                ->prefix('committee/guide-cadre')
                ->name('committee.guideCadre.')
                ->group(base_path('routes/committee/guide_cadre.php'));

            Route::middleware('web')
                ->prefix('committee/regency-regional')
                ->name('committee.regencyRegional.')
                ->group(base_path('routes/committee/regency_regional.php'));


            //participant
            Route::middleware('web')
                ->prefix('participant/participant')
                ->name('participant.participant.')
                ->group(base_path('routes/participant/participant.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'Indonesia' => Laravolt\Indonesia\Facade::class,
            'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
