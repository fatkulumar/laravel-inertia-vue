<?php

    use App\Http\Controllers\Committee;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:panitia'])->group(function () {
        Route::get('/', [Committee\GuideCadreController::class, 'index'])->name('index');
        // Route::post('/store', [Committee\GuideCadreController::class, 'store'])->name('store');
        // Route::delete('/delete/{id}', [Committee\GuideCadreController::class, 'delete'])->name('delete');
        // Route::post('/destroy', [Committee\GuideCadreController::class, 'destroy'])->name('destroy');
    });
