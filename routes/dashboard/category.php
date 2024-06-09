<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/', [Dashboard\CategoryController::class, 'index'])->name('index');
        Route::post('/store', [Dashboard\CategoryController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Dashboard\CategoryController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Dashboard\CategoryController::class, 'destroy'])->name('destroy');
    });
