<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use App\Models\RegencyRegional;
use App\Models\Regional;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    protected $directoryProfile = '/file/profile/';
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $userId = Auth::user()->id;
        $user = User::with(['profile.regional.regencyRegional'])->where('id', $userId)->get();
        $regionals = Regional::all(['id', 'name']);
        $regencyRegionals = RegencyRegional::all(['id', 'regency']);
        $user->map(function ($us) {
            $us->profile->image = isset($us->image) ?  asset($this->directoryProfile . $us->profile->image) : // get path public
                null;
            return $us;
        });
        // return $user;
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'role' => Auth::user()->roles,
            'regionals' => $regionals,
            'regencyRegionals' => $regencyRegionals,
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    // : RedirectResponse
    {
        $updateData = DB::transaction(function () use ($request) {
            $data = Profile::where('profileable_id', $request->user()->id)->first();

            $image = $request->file('image');
            if ($image) {
                $filePath = public_path($this->directoryProfile . $data->image);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $fileName = "{$image->hashName()}-{$image->getClientOriginalName()}";
                $image->move(public_path($this->directoryProfile), $fileName);
                $uploadImage = $fileName;
            } else {
                $uploadImage = "Foto Tidak Ada";
            }

            $saveProfile = [
                'hp' => $request->post('hp'),
                'address' => $request->post('address'),
                'gender' => $request->post('gender'),
                'regional_id' => $request->post('regional_id'),
                'image' => $uploadImage,
            ];

            $saveUser = [
                'name' => $request->post('name'),
                'email' => $request->post('email'),
            ];

            $data->update($saveProfile);
            User::where('id', $request->user()->id)->update($saveUser);
        });

        if($updateData) {
            return Redirect::route('profile.edit')->with('success', 'Profile updated successfully!');
        }else{
            return Redirect::route('profile.edit')->with('error', 'Profile failed to update!');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
