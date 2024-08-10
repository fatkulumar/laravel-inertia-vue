<?php

use App\Http\Controllers\ProfileController;
use App\Mail\SendEmail;
use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use setasign\Fpdi\Fpdi;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::get('certificate', function () {
//     return view('pdf_template_certificate');
// });

// Route::get('generate-certificate', function ($download = false) {
//     $nama = "Fatkul Umar";
//     $credential = "12345678";
//     // generate QRCode
//     $qrCode = QrCode::format('png')->size(500)->generate($credential);
//     $qrCodePath = public_path('qr/'. $credential . '.png');
//     file_put_contents($qrCodePath, $qrCode);

//     //create instance PDF
//     $pdf = new Fpdi();
//     $pathToTemplate = public_path('certificate.pdf');
//     $pdf->setSourceFile($pathToTemplate);
//     $template = $pdf->importPage(1);

//     $size = $pdf->getTemplateSize($template);

//     $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
//     $pdf->useTemplate($template, 0, 0, $size['width'], $size['height']);

//     $pdf->SetFont('Helvetica', '', 14);
//     $pdf->setFontSize(12);
//     $pdf->SetXY(100, 100);
//     $pdf->Write(0, $nama);

//     //credential
//     $pdf->SetFont('Helvetica', '', 14);
//     $pdf->setFontSize(12);
//     $pdf->SetXY(100, 100);
//     $pdf->Write(0, $nama);

//     // QrCode
//     $pdf->Image($qrCodePath, 200, 200, 50, 50);

//     $fileName = 'Certificate ' . $nama .  '.pdf';

//     if ($download) {
//         return response()->make($pdf->Output('D', $fileName), 200, [
//             'Content-Type' => 'application/pdf',
//             'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
//             'Content-Transfer-Encoding' => 'binary',
//         ]);
//     } else {
//         return response()->make($pdf->Output('I', $fileName), 200, [
//             'Content-Type' => 'application/pdf',
//             'Content-Disposition' => 'inline; filename="' . $fileName . '"',
//             'Content-Transfer-Encoding' => 'binary',
//         ]);
//     }
// });

Route::get('/certificate/{credentialId}', function ($credentialId) {
    // return $credentialId;
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

    // return $certificate;
    return Inertia::render('Certificate', [
        'certificate' => $certificate,
    ]);
});



require __DIR__.'/auth.php';
