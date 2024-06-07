<?php

    use App\Http\Controllers\Dashboard;
    use Illuminate\Support\Facades\Route;

    Route::middleware('auth')->group(function () {
        Route::get('/', [Dashboard\SubmissionController::class, 'index'])->name('index');
        Route::post('/store', [Dashboard\SubmissionController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [Dashboard\SubmissionController::class, 'delete'])->name('delete');
        Route::post('/destroy', [Dashboard\SubmissionController::class, 'destroy'])->name('destroy');
        Route::post('/reject-submission', [Dashboard\SubmissionController::class, 'rejectSubmission'])->name('rejectSubmission');
        Route::post('/approval-submission', [Dashboard\SubmissionController::class, 'approvalSubmission'])->name('approvalSubmission');
        Route::post('/graduation-submission', [Dashboard\SubmissionController::class, 'graduationSubmission'])->name('graduationSubmission');
        Route::post('/option-submission', [Dashboard\SubmissionController::class, 'optionSubmission'])->name('optionSubmission');
    });
