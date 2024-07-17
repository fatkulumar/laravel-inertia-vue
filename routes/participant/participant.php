<?php

    use App\Http\Controllers\Participant;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:peserta'])->group(function () {
        Route::get('/event-available', [Participant\ParticipantController::class, 'eventAvailable'])->name('event.available');
        Route::post('/history', [Participant\ParticipantController::class, 'history'])->name('history');
    });
