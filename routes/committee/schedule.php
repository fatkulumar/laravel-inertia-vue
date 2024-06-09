<?php

    use App\Http\Controllers\Committee;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:panitia'])->group(function () {
        Route::get('/', [Committee\ScheduleController::class, 'index'])->name('index');
        Route::post('/store', [Committee\ScheduleController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Committee\ScheduleController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Committee\ScheduleController::class, 'destroy'])->name('destroy');
        Route::get('/detail/{id}', [Committee\ScheduleController::class, 'show'])->name('detail');
    });
