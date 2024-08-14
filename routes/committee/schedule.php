<?php

    use App\Http\Controllers\Committee;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:panitia'])->group(function () {
        // schedule
        Route::get('/', [Committee\ScheduleController::class, 'index'])->name('index');
        Route::post('/store', [Committee\ScheduleController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Committee\ScheduleController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Committee\ScheduleController::class, 'destroy'])->name('destroy');
        Route::get('/detail/{id}', [Committee\ScheduleController::class, 'show'])->name('detail');
        // speaker
        Route::get('/speaker/{classRoomId}', [Committee\ScheduleController::class, 'speaker'])->name('speaker');
        Route::get('/speaker-list/{scheduleId}', [Committee\ScheduleController::class, 'speakerList'])->name('speaker.list');
        // letter
        Route::get('/letter/{scheduleId}', [Committee\ScheduleController::class, 'letter'])->name('letter');
        Route::post('/upload-letter/{scheduleId}', [Committee\ScheduleController::class, 'uploadLetter'])->name('upload.letter');
        Route::delete('/delete-letter/{scheduleId}', [Committee\ScheduleController::class, 'deleteLetter'])->name('delete.letter');
        //participant
        Route::get('/list-participant/{scheduleId}', [Committee\ScheduleController::class, 'participant'])->name('participant');
        // documentation
        Route::get('/documentation/{scheduleId}', [Committee\ScheduleController::class, 'documentation'])->name('documentation');
        Route::post('/documentation/store', [Committee\ScheduleController::class, 'documentationStore'])->name('documentation.store');
        Route::delete('/documentation/delete', [Committee\ScheduleController::class, 'documentationDelete'])->name('documentation.delete');
        // report
        Route::get('/report/{scheduleId}', [Committee\ScheduleController::class, 'report'])->name('report');

        Route::get('/download/report-total-participant-by-schedule-class/{scheduleId}', [Committee\ScheduleController::class, 'downloadReportTotalParticipantByScheduleClass'])->name('report.total.participant.by.schedule.class');
        Route::get('/download/report-total-male-participant-by-schedule-class/{scheduleId}', [Committee\ScheduleController::class, 'downloadReportTotalMaleParticipantByScheduleClass'])->name('report.total.male.participant.by.schedule.class');
        Route::get('/download/report-total-female-participant-by-schedule-class/{scheduleId}', [Committee\ScheduleController::class, 'downloadReportTotalFemaleParticipantByScheduleClass'])->name('report.total.female.participant.by.schedule.class');

        Route::get('/download/report-total-graduated-participant-by-schedule-class/{scheduleId}', [Committee\ScheduleController::class, 'downloadReportTotalGraduatedParticipantByScheduleClass'])->name('report.total.graduated.participant.by.schedule.class');
        Route::get('/download/report-total-male-graduated-participant-by-schedule-class/{scheduleId}', [Committee\ScheduleController::class, 'downloadReportTotalMaleGraduatedParticipantByScheduleClass'])->name('report.total.male.graduated.participant.by.schedule.class');
        Route::get('/download/report-total-female-graduated-participant-by-schedule-class/{scheduleId}', [Committee\ScheduleController::class, 'downloadReportTotalFemaleGraduatedParticipantByScheduleClass'])->name('report.total.female.graduated.participant.by.schedule.class');

        Route::get('/download/report-total-not-graduated-participant-by-schedule-class/{scheduleId}', [Committee\ScheduleController::class, 'downloadReportTotalNotGraduatedParticipantByScheduleClass'])->name('report.total.not.graduated.participant.by.schedule.class');
        Route::get('/download/report-total-male-not-graduated-participant-by-schedule-class/{scheduleId}', [Committee\ScheduleController::class, 'downloadReportTotalMaleNotGraduatedParticipantByScheduleClass'])->name('report.total.male.not.graduated.participant.by.schedule.class');
        Route::get('/download/report-total-female-not-graduated-participant-by-schedule-class/{scheduleId}', [Committee\ScheduleController::class, 'downloadReportTotalFemaleNotGraduatedParticipantByScheduleClass'])->name('report.total.female.not.graduated.participant.by.schedule.class');
        // upload berita acara
        Route::post('/upload-appointment-file', [Committee\ScheduleController::class, 'uploadAppointmentFile'])->name('upload.appintment.file');
        Route::post('/appointment-file', [Committee\ScheduleController::class, 'appointmentFile'])->name('appintment.file');

        Route::get('/committee/{userId}', [Committee\ScheduleController::class, 'committee'])->name('committee');

    });
