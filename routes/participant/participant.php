<?php

    use App\Http\Controllers\Participant;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:peserta'])->group(function () {
        Route::post('/register-class', [Participant\ParticipantController::class, 'RegisterClass'])->name('register.class');
        Route::get('/event-available', [Participant\ParticipantController::class, 'eventAvailable'])->name('event.available');
        Route::get('/event-active', [Participant\ParticipantController::class, 'eventActive'])->name('event.active');
        Route::get('/history', [Participant\ParticipantController::class, 'history'])->name('history');
    });
