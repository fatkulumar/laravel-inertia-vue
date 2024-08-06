<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {   $role = $request->user()->roles->pluck('name')->implode(', ');
        if ($request->user()->hasVerifiedEmail()) {
            if($role == 'admin') {
                return redirect()->intended(route('dashboard.admin', absolute: false).'?verified=1');
            }elseif($role == 'panitia') {
                return redirect()->intended(route('dashboard.committee', absolute: false).'?verified=1');
            }elseif($role == 'peserta') {
                return redirect()->intended(route('dashboard.participant', absolute: false).'?verified=1');
            }
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }
        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
