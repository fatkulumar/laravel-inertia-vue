<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/', [Dashboard\RegionalController::class, 'index'])->name('index');
        Route::post('/store', [Dashboard\RegionalController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Dashboard\RegionalController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Dashboard\RegionalController::class, 'destroy'])->name('destroy');
    });
