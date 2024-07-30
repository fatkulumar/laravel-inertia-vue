<?php

    use App\Http\Controllers\Committee;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:panitia'])->group(function () {
        Route::get('/', [Committee\RegencyRegionalController::class, 'index'])->name('index');
        Route::post('/store', [Committee\RegencyRegionalController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Committee\RegencyRegionalController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Committee\RegencyRegionalController::class, 'destroy'])->name('destroy');
    });
