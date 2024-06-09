<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/', [Dashboard\ClassRoomController::class, 'index'])->name('index');
        Route::post('/store', [Dashboard\ClassRoomController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Dashboard\ClassRoomController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Dashboard\ClassRoomController::class, 'destroy'])->name('destroy');
    });
