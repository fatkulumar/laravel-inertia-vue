<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Regional;
use App\Models\User;
use App\Traits\EntityValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use EntityValidator;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $users = User::orderBy('created_at', 'desc')
                ->with('roles','profile', 'profile.regional')
                ->when($request['search'], function ($query, $request) {
                    $query->where('name', 'like', '%' . $request . '%');
                })
                ->paginate(5)
                ->withQueryString()
                ->appends(['search' => $request['search']]);
            $regionals = Regional::select('id','name')->get();
            // return $users;
            return Inertia::render('Dashboard/User', [
                'users' => $users,
                'regionals' => $regionals,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function index in Dashboard/UserController', $errors);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $id = $request->post('id');
            if ($id) {
                $validasiData = $this->updateValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

                $users = User::where('id', $id)->first();
                $saveData = [
                    'name' => $request->post('name'),
                    'email' => $request->post('email'),
                    'password' => $request->post('password'),
                ];
                $result = $users->update($saveData);

                $role = $request->post('role');
                $users->roles()->detach();
                $users->assignRole($role);

                Profile::where('profileable_id', $id)->update([
                    'regional_id' => $request->post('regional_id'),
                    'gender' => $request->post('gender'),
                ]);

                if (!$result) return redirect()->back()->withErrors($result)->withInput();
            } else {

                $validasiData = $this->storeValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

                $userControler = new UserController();

                $saveData = [
                    'id' => $userControler->createUUID36(),
                    'name' => $request->post('name'),
                    'email' => $request->post('email'),
                    'password' => $request->post('password'),
                ];

                $result = User::create($saveData);
                $result->assignRole($request->post('role'));

                $saveProfile = [
                    'profileable_id' => $result->id,
                    'profileable_type' => 'App\Models\User',
                    'regional_id' => $request->post('regional_id'),
                    'gender' => $request->post('gender'),
                ];

                Profile::create($saveProfile);

                $result->assignRole($request->post('role'));
                if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function store in UserController', $errors);
        }
    }

    private function storeValidator(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string',
                'role' => 'required|string|max:36',
                'regional_id' => 'required|string|max:36',
                'gender' => 'required|string|max:10',
            ];
            $Validatedata = [
                'name' => $request->post('name'),
                'email' => $request->post('email'),
                'password' => $request->post('password'),
                'role' => $request->post('role'),
                'regional_id' => $request->post('regional_id'),
                'gender' => $request->post('gender'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function storeValidator in UserController', $errors);
        }
    }

    private function updateValidator(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:100',
                'email' => 'required|email',
                'password' => 'required|string',
                'role' => 'required|string|max:36',
                'regional_id' => 'required|string|max:36',
                'gender' => 'required|string|max:10',
            ];
            $Validatedata = [
                'name' => $request->post('name'),
                'email' => $request->post('email'),
                'password' => $request->post('password'),
                'role' => $request->post('role'),
                'regional_id' => $request->post('regional_id'),
                'gender' => $request->post('gender'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function storeValidator in UserController', $errors);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function delete(string $id)
    {
        try {
            User::findOrFail($id)->delete();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in UserController', $errors);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $ids = $request->post('id');
            foreach ($ids as $id) {
                User::findOrFail($id)->delete();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in UserController', $errors);
        }
    }
}
