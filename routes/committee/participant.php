<?php

    use App\Http\Controllers\Committee;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:panitia'])->group(function () {
        Route::post('/update', [Committee\ParticipantController::class, 'update'])->name('update');
        Route::get('/detail/{id}', [Committee\ParticipantController::class, 'show'])->name('detail');
        Route::get('/list-participant-class-room', [Committee\ParticipantController::class, 'participantClassRoom'])->name('participantClassRoom');
        Route::get('/history-class/{id}', [Committee\ParticipantController::class, 'historyClass'])->name('history.class');
    });
