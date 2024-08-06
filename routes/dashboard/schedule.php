<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/', [Dashboard\ScheduleController::class, 'index'])->name('index');
        Route::post('/store', [Dashboard\ScheduleController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Dashboard\ScheduleController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Dashboard\ScheduleController::class, 'destroy'])->name('destroy');
        Route::post('/reject-schedule', [Dashboard\ScheduleController::class, 'rejectSchedule'])->name('rejects.schedule');
        Route::post('/approval-schedule', [Dashboard\ScheduleController::class, 'approvalSchedule'])->name('approval.schedule');
        Route::delete('/delete-schedule/{id}', [Dashboard\ScheduleController::class, 'deleteSchedule'])->name('delete.schedule');
        Route::post('/option-schedule', [Dashboard\ScheduleController::class, 'optionSchedule'])->name('option.schedule');
        Route::post('/overview-schedule', [Dashboard\ScheduleController::class, 'overviewSchedule'])->name('overview.schedule');
        Route::post('/received-schedule', [Dashboard\ScheduleController::class, 'receivedSchedule'])->name('received.schedule');
        Route::get('/report/{scheduleId}', [Dashboard\ScheduleController::class, 'report'])->name('report');

        Route::get('/download/report-total-participant-by-schedule-class/{scheduleId}', [Dashboard\ScheduleController::class, 'downloadReportTotalParticipantByScheduleClass'])->name('report.total.participant.by.schedule.class');
        Route::get('/download/report-total-male-participant-by-schedule-class/{scheduleId}', [Dashboard\ScheduleController::class, 'downloadReportTotalMaleParticipantByScheduleClass'])->name('report.total.male.participant.by.schedule.class');
        Route::get('/download/report-total-female-participant-by-schedule-class/{scheduleId}', [Dashboard\ScheduleController::class, 'downloadReportTotalFemaleParticipantByScheduleClass'])->name('report.total.female.participant.by.schedule.class');

        Route::get('/download/report-total-graduated-participant-by-schedule-class/{scheduleId}', [Dashboard\ScheduleController::class, 'downloadReportTotalGraduatedParticipantByScheduleClass'])->name('report.total.graduated.participant.by.schedule.class');
        Route::get('/download/report-total-male-graduated-participant-by-schedule-class/{scheduleId}', [Dashboard\ScheduleController::class, 'downloadReportTotalMaleGraduatedParticipantByScheduleClass'])->name('report.total.male.graduated.participant.by.schedule.class');
        Route::get('/download/report-total-female-graduated-participant-by-schedule-class/{scheduleId}', [Dashboard\ScheduleController::class, 'downloadReportTotalFemaleGraduatedParticipantByScheduleClass'])->name('report.total.female.graduated.participant.by.schedule.class');

        Route::get('/download/report-total-not-graduated-participant-by-schedule-class/{scheduleId}', [Dashboard\ScheduleController::class, 'downloadReportTotalNotGraduatedParticipantByScheduleClass'])->name('report.total.not.graduated.participant.by.schedule.class');
        Route::get('/download/report-total-male-not-graduated-participant-by-schedule-class/{scheduleId}', [Dashboard\ScheduleController::class, 'downloadReportTotalMaleNotGraduatedParticipantByScheduleClass'])->name('report.total.male.not.graduated.participant.by.schedule.class');
        Route::get('/download/report-total-female-not-graduated-participant-by-schedule-class/{scheduleId}', [Dashboard\ScheduleController::class, 'downloadReportTotalFemaleNotGraduatedParticipantByScheduleClass'])->name('report.total.female.not.graduated.participant.by.schedule.class');

        Route::post('/upload-appointment-file', [Dashboard\ScheduleController::class, 'uploadAppointmentFile'])->name('upload.appintment.file');
        Route::get('/download-appointment-file/{scheduleId}', [Dashboard\ScheduleController::class, 'downloadAppointmentFile'])->name('download.appintment.file');
    });
