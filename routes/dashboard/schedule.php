<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/', [Dashboard\ScheduleController::class, 'index'])->name('index');
        Route::post('/store', [Dashboard\ScheduleController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Dashboard\ScheduleController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Dashboard\ScheduleController::class, 'destroy'])->name('destroy');
        Route::post('/reject-schedule', [Dashboard\ScheduleController::class, 'rejectSchedule'])->name('rejectSchedule');
        Route::post('/approval-schedule', [Dashboard\ScheduleController::class, 'approvalSchedule'])->name('approvalSchedule');
        Route::delete('/delete-schedule/{id}', [Dashboard\ScheduleController::class, 'deleteSchedule'])->name('deleteSchedule');
        Route::post('/option-schedule', [Dashboard\ScheduleController::class, 'optionSchedule'])->name('optionSchedule');
        Route::post('/overview-schedule', [Dashboard\ScheduleController::class, 'overviewSchedule'])->name('overviewSchedule');
        Route::post('/received-schedule', [Dashboard\ScheduleController::class, 'receivedSchedule'])->name('receivedSchedule');
    });
