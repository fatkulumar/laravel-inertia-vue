<?php

use App\Http\Controllers\ProfileController;
use App\Mail\SendEmail;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})
->name('dashboard');

Route::get('/dashboard-admin', function () {
    return Inertia::render('DashboardAdmin');
})->middleware(['auth', 'verified', 'role:admin'])->name('dashboard.admin');

Route::get('/dashboard-panitia', function () {
    return Inertia::render('DashboardCommittee');
})
->middleware(['auth', 'verified', 'role:panitia'])
->name('dashboard.committee');

Route::get('/dashboard-peserta', function () {
    return Inertia::render('DashboardParticipant');
})
->middleware(['auth', 'verified', 'role:peserta'])
->name('dashboard.participant');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('certificate', function () {
    return view('pdf_template_certificate');
});

require __DIR__.'/auth.php';
