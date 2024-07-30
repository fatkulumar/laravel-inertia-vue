<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:panitia'])->group(function () {
        Route::get('/', [Dashboard\GuideCadreController::class, 'index'])->name('index');
        // Route::post('/store', [Dashboard\GuideCadreController::class, 'store'])->name('store');
        // Route::delete('/delete/{id}', [Dashboard\GuideCadreController::class, 'delete'])->name('delete');
        // Route::post('/destroy', [Dashboard\GuideCadreController::class, 'destroy'])->name('destroy');
    });
