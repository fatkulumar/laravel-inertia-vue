<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/{regionalId}', [Dashboard\RegencyRegionalController::class, 'index'])->name('index');
        Route::post('/store', [Dashboard\RegencyRegionalController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Dashboard\RegencyRegionalController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Dashboard\RegencyRegionalController::class, 'destroy'])->name('destroy');
    });
