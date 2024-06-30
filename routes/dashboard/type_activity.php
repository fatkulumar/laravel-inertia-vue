<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/', [Dashboard\TypeActivityController::class, 'index'])->name('index');
        Route::post('/store', [Dashboard\TypeActivityController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Dashboard\TypeActivityController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Dashboard\TypeActivityController::class, 'destroy'])->name('destroy');
    });
