<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware('auth')->group(function () {
        Route::get('/', [Dashboard\ArticleController::class, 'index'])->name('index');
        Route::post('/store', [Dashboard\ArticleController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Dashboard\ArticleController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Dashboard\ArticleController::class, 'destroy'])->name('destroy');
    });
