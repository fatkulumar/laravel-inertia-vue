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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Traits\EntityValidator;

class ProfileController extends Controller
{
    protected $directoryProfile = '/file/profile/';
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    // : Response
    {
        $userId = Auth::user()->id;
        $user = User::with(['profile.regional', 'profile.regencyRegional'])->where('id', $userId)->get();
        $regionals = Regional::all(['id', 'name']);
        $regencyRegional = RegencyRegional::where('id', $user[0]->profile->regency_regional_id ?? '')->get();
        $user->map(function ($us) {
            $us->profile->image = isset($us->image) ?  asset($this->directoryProfile . $us->profile->image) : // get path public
                null;
            return $us;
        });
        // return $regencyRegionals;
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'role' => Auth::user()->roles,
            'regionals' => $regionals,
            'regencyRegional' => $regencyRegional,
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    // : RedirectResponse
    {
        try {
            $validasiData = $this->updateValidator($request);
            if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();
            DB::transaction(function () use ($request) {
                $data = Profile::where('profileable_id', $request->user()->id)->first();

                $image = $request->file('image');
                if ($image) {
                    $filePath = public_path($this->directoryProfile . $data->image);
                    if (file_exists($filePath) && $data->image) {
                        unlink($filePath);
                    }
                    $fileName = "{$image->hashName()}-{$image->getClientOriginalName()}";
                    $image->move(public_path($this->directoryProfile), $fileName);
                    $uploadImage = $fileName;
                } else {
                    $uploadImage = $data->image;
                }

                $saveProfile = [
                    'hp' => $request->post('hp'),
                    'address' => $request->post('address'),
                    'gender' => $request->post('gender'),
                    'regional_id' => $request->post('regional_id'),
                    'regency_regional_id' => $request->post('regency_regional_id'),
                    'image' => $uploadImage,
                ];

                $saveUser = [
                    'name' => $request->post('name'),
                    'email' => $request->post('email'),
                    'image' => $uploadImage,
                ];

                $data->update($saveProfile);
                User::where('id', $request->user()->id)->update($saveUser);
                RegencyRegional::where('id', $request->post('regency_id'))->update([
                    'regency' => $request->post('regency_name'),
                ]);
            });

            return back()->with('success', 'Profile updated successfully!');
        } catch (\Exception $exception) {
            return back()->with('error', 'Profile failed to update!');
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function update in ProfileController', $errors);
        }
    }

    private function updateValidator(Request $request)
    {
        try {
            $rules = [
                'hp' => 'required|string|max:13',
                'address' => 'required|string|max:255',
                'gender' => 'required|string|10',
                'regional_id' => 'required|string|max:36',
                'regency_regional_id' => 'required|string|max:36',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'name' => 'required|string|max:100',
                'email' => 'required|string|max:225',
            ];
            $Validatedata = [
                'hp' => $request->post('hp'),
                'address' => $request->post('address'),
                'gender' => $request->post('gender'),
                'regional_id' => $request->post('regional_id'),
                'regency_regional_id' => $request->post('regency_regional_id'),
                'image' => $request->post('image'),
                'name' => $request->post('name'),
                'email' => $request->post('email'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function updateValidator in SubmissionController', $errors);
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

     /**
     * Get Regency Regional By Regional Id the user's account.
     */
    public function regencyRegional(Request $request, $regionalId)
    {
        try {
            $regencyRegionals = RegencyRegional::where('regional_id', $regionalId)->get();
            return response()->json($regencyRegionals);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function regencyRegional in ProfileController', $errors);
        }
    }
}
