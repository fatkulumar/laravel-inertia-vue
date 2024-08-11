<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Regional;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        $regionals = Regional::all();
        return Inertia::render('Auth/Register', [
            'regionals' => $regionals,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    // : RedirectResponse
    {
        // return $request;
        $request->validate([
            'regional_id' => 'required|string|max:36',
            'regency_regional_id' => 'required|string|max:36',
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user_id = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
        $user = User::create([
            'id' => $user_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $saveProfile = [
            'profileable_id' => $user_id,
            'profileable_type' => 'App\Models\User',
            'regional_id' => $request->regional_id,
            'regency_regional_id' => $request->regional_id,
        ];

        $user->profile()->create($saveProfile);

        $user->assignRole('peserta');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard.participant', absolute: false));
    }
}
