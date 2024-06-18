<?php

    use App\Http\Controllers\Committee;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:panitia'])->group(function () {
        Route::get('/{idSubmission}', [Committee\ParticipantController::class, 'index'])->name('index');
        Route::post('/store', [Committee\ParticipantController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Committee\ParticipantController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Committee\ParticipantController::class, 'destroy'])->name('destroy');
        Route::get('/detail/{id}', [Committee\ParticipantController::class, 'show'])->name('detail');
    });
