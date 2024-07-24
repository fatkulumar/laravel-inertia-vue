<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/', [Dashboard\SpeakerController::class, 'index'])->name('index');
        Route::post('/store', [Dashboard\SpeakerController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Dashboard\SpeakerController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Dashboard\SpeakerController::class, 'destroy'])->name('destroy');
        Route::get('/city/{provinceCode}', [Dashboard\SpeakerController::class, 'city'])->name('city');
    });
