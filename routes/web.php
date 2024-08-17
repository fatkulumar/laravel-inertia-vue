<?php

use App\Http\Controllers\ProfileController;
use App\Models\Certificate;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if(isset(Auth::user()->roles)) {
        $role = Auth::user()->roles[0]->name;
    }else{
        $role = "tidak ada auth";
    }
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'role' => $role,
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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/profile/regency_regional/{regionalId}', [ProfileController::class, 'regencyRegional'])->name('profile.regency.regional');

Route::get('/certificate/{credentialId}', function ($credentialId) {
    $directoryCertificate = '/file/certificate/';
    $userProfile = '/user.png';
    $certificate = Certificate::with([
        'headOrganization',
        'user.submissions.schedule.category',
        'user.submissions.schedule.classRoom',
    ])
    ->where('credential_id', $credentialId)
    ->get();

    $certificate->map(function ($certif) {
        $certif->formatted_created_at = \Carbon\Carbon::now()->translatedFormat('l, d F Y');
        $certif->formatted_expired_at = \Carbon\Carbon::now()->translatedFormat('l, d F Y');
        return $certif;
    });

    $certificate->map(function ($certif) use ($directoryCertificate, $userProfile) {
        if($certif->image) {
            $certif->image = asset($directoryCertificate . $certif->image);
        }else{
            $certif->image = asset($userProfile);
        }
        return $certif;
    });

    // return $certificate;
    return Inertia::render('Certificate', [
        'certificate' => $certificate,
    ]);
});



require __DIR__.'/auth.php';
