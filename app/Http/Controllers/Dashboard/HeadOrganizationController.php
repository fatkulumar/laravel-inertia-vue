<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HeadOrganization;
use App\Traits\EntityValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class HeadOrganizationController extends Controller
{
    use EntityValidator;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $HeadOrganizations = HeadOrganization::orderBy('created_at', 'desc')
                ->when($request['search'], function ($query, $request) {
                    $query->where('name', 'like', '%' . $request . '%');
                })
                ->paginate(5)
                ->withQueryString()
                ->appends(['search' => $request['search']]);
            return Inertia::render('Dashboard/HeadOrganization', [
                'HeadOrganizations' => $HeadOrganizations,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function index in Dashboard/HeadOrganizationController', $errors);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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

                $HeadOrganization = HeadOrganization::where('id', $id)->first();
                $saveData = [
                    'name' => $request->post('name'),
                    'status' => $request->post('status'),
                    'start_date' => $request->post('start_date'),
                    'end_date' => $request->post('end_date'),
                ];
                $result = $HeadOrganization->update($saveData);
                if (!$result) return redirect()->back()->withErrors($result)->withInput();
            } else {

                $saveData = [
                    'name' => $request->post('name'),
                    'status' => $request->post('status'),
                    'start_date' => $request->post('start_date'),
                    'end_date' => $request->post('end_date'),
                ];

                $result = HeadOrganization::create($saveData);
                if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function store in HeadOrganizationController', $errors);
        }
    }

    private function storeValidator(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'status' => 'required|string|max:50',
                'start_date' => 'required|string|max:10',
                'end_date' => 'required|string|max:10',
            ];
            $Validatedata = [
                'name' => $request->post('name'),
                'status' => $request->post('status'),
                'start_date' => $request->post('start_date'),
                'end_date' => $request->post('end_date'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function storeValidator in HeadOrganizationController', $errors);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function delete(string $id)
    {
        try {
            HeadOrganization::findOrFail($id)->delete();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in HeadOrganizationController', $errors);
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
                HeadOrganization::findOrFail($id)->delete();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in HeadOrganizationController', $errors);
        }
    }
}
