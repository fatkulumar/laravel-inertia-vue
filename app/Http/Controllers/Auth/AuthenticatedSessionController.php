<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $email = $request->email;

        $user = User::with('roles')->where('email', $email)->first();

        $roles = $user->roles;
        foreach ($roles as $role) {
            if ($role->name == 'superadmin') {
                return $role = 'superadmin';
                break;
            } elseif ($role->name == 'admin') {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard.admin', absolute: false));
            } elseif ($role->name == 'panitia') {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard.committee', absolute: false));
            } else {
                //role == peserta
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard.participant', absolute: false));
            }
        }


    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
