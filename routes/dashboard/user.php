<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/', [Dashboard\UserController::class, 'index'])->name('index');
        Route::post('/store', [Dashboard\UserController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Dashboard\UserController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Dashboard\UserController::class, 'destroy'])->name('destroy');
    });
