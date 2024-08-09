<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/', [Dashboard\HeadOrganizationController::class, 'index'])->name('index');
        Route::post('/store', [Dashboard\HeadOrganizationController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Dashboard\HeadOrganizationController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Dashboard\HeadOrganizationController::class, 'destroy'])->name('destroy');
    });
