<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\RegencyRegional;
use App\Models\Regional;
use App\Models\TypeActivity;
use App\Traits\EntityValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class RegencyRegionalController extends Controller
{
    use EntityValidator;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $regencyRegionals = RegencyRegional::orderBy('created_at', 'desc')
                ->when($request['search'], function ($query, $request) {
                    $query->where('regency', 'like', '%' . $request . '%');
                })
                ->with('regional')
                ->paginate(5)
                ->withQueryString()
                ->appends(['search' => $request['search']]);
            $regional_id = Auth::user()->profile->regional->id;
            // return $regencyRegionals;
            return Inertia::render('Committee/RegencyRegional', [
                'regencyRegionals' => $regencyRegionals,
                'regional_id' => $regional_id,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function index in Committee/RegencyRegionalController', $errors);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validasiData = $this->storeValidator($request);
            if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

            $id = $request->post('id');
            if ($id) {

                $categories = RegencyRegional::where('id', $id)->first();
                $saveData = [
                    'regency' => $request->post('regency'),
                    'regional_id' => $request->post('regional_id'),
                ];
                $result = $categories->update($saveData);
                if (!$result) return redirect()->back()->withErrors($result)->withInput();
            } else {

                $saveData = [
                    'regency' => $request->post('regency'),
                    'regional_id' => $request->post('regional_id'),
                ];

                $result = RegencyRegional::create($saveData);
                if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function store in RegencyRegionalController', $errors);
        }
    }

    private function storeValidator(Request $request)
    {
        try {
            $rules = [
                'regency' => 'required|string|max:50',
                'regional_id' => 'required|string|max:36',
            ];
            $Validatedata = [
                'regency' => $request->post('regency'),
                'regional_id' => $request->post('regional_id'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function storeValidator in RegencyRegionalController', $errors);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function delete(string $id)
    {
        try {
            RegencyRegional::findOrFail($id)->delete();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in RegencyRegionalController', $errors);
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
                RegencyRegional::findOrFail($id)->delete();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in RegencyRegionalController', $errors);
        }
    }
}
