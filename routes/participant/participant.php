<?php

    use App\Http\Controllers\Participant;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:peserta'])->group(function () {
        Route::post('/register-class', [Participant\ParticipantController::class, 'RegisterClass'])->name('register.class');
        Route::get('/event-available', [Participant\ParticipantController::class, 'eventAvailable'])->name('event.available');
        Route::get('/waiting-approval', [Participant\ParticipantController::class, 'waitingApproval'])->name('waiting.approval');
        Route::get('/event-active', [Participant\ParticipantController::class, 'eventActive'])->name('event.active');
        Route::get('/history-class', [Participant\ParticipantController::class, 'historyClass'])->name('history.class');
        Route::get('/certificate/{credentialId}', [Participant\ParticipantController::class, 'certificate'])->name('certificate');
        Route::post('/change-image', [Participant\ParticipantController::class, 'changeImage'])->name('change.image');
        Route::get('/detail', [Participant\ParticipantController::class, 'show'])->name('detail');
    });
