<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\GuideCadre;
use App\Models\TypeActivity;
use App\Traits\EntityValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GuideCadreController extends Controller
{
    use EntityValidator;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $guideCadres = GuideCadre::orderBy('created_at', 'desc')
                ->when($request['search'], function ($query, $request) {
                    $query->where('name', 'like', '%' . $request . '%');
                })
                ->with('typeActivity')
                ->paginate(5)
                ->withQueryString()
                ->appends(['search' => $request['search']]);
            // return $guideCadres;
            $typeActivities = TypeActivity::all('id', 'name');
            return Inertia::render('Dashboard/GuideCadre', [
                'guideCadres' => $guideCadres,
                'typeActivities' => $typeActivities,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function index in Dashboard/GuideCadreController', $errors);
        }
    }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     try {
    //         $validasiData = $this->storeValidator($request);
    //         if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

    //         $id = $request->post('id');
    //         if ($id) {

    //             $categories = GuideCadre::where('id', $id)->first();
    //             $saveData = [
    //                 'name' => $request->post('name'),
    //                 'type_activity_id' => $request->post('type_activity_id'),
    //                 'link' => $request->post('link'),
    //                 'information' => $request->post('information'),
    //             ];
    //             $result = $categories->update($saveData);
    //             if (!$result) return redirect()->back()->withErrors($result)->withInput();
    //         } else {

    //             $saveData = [
    //                 'name' => $request->post('name'),
    //                 'type_activity_id' => $request->post('type_activity_id'),
    //                 'link' => $request->post('link'),
    //                 'information' => $request->post('information'),
    //             ];

    //             $result = GuideCadre::create($saveData);
    //             if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
    //         }
    //     } catch (\Exception $exception) {
    //         $errors['message'] = $exception->getMessage();
    //         $errors['file'] = $exception->getFile();
    //         $errors['line'] = $exception->getLine();
    //         $errors['trace'] = $exception->getTrace();
    //         Log::channel('daily')->info('function store in GuideCadreController', $errors);
    //     }
    // }

    // private function storeValidator(Request $request)
    // {
    //     try {
    //         $rules = [
    //             'name' => 'required|string|max:100',
    //             'type_activity_id' => 'required|string|max:36',
    //             'link' => 'required|string|max:1000',
    //             'information' => 'required|string|max:100',
    //         ];
    //         $Validatedata = [
    //             'name' => $request->post('name'),
    //             'type_activity_id' => $request->post('type_activity_id'),
    //             'link' => $request->post('link'),
    //             'information' => $request->post('information'),
    //         ];
    //         $validator = EntityValidator::validate($Validatedata, $rules);
    //         if ($validator->fails()) return $validator->errors();
    //     } catch (\Exception $exception) {
    //         $errors['message'] = $exception->getMessage();
    //         $errors['file'] = $exception->getFile();
    //         $errors['line'] = $exception->getLine();
    //         $errors['trace'] = $exception->getTrace();
    //         Log::channel('daily')->info('function storeValidator in GuideCadreController', $errors);
    //     }
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function delete(string $id)
    // {
    //     try {
    //         GuideCadre::findOrFail($id)->delete();
    //     } catch (\Exception $exception) {
    //         $errors['message'] = $exception->getMessage();
    //         $errors['file'] = $exception->getFile();
    //         $errors['line'] = $exception->getLine();
    //         $errors['trace'] = $exception->getTrace();
    //         Log::channel('daily')->info('function delete in GuideCadreController', $errors);
    //     }
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Request $request)
    // {
    //     try {
    //         $ids = $request->post('id');
    //         foreach ($ids as $id) {
    //             GuideCadre::findOrFail($id)->delete();
    //         }
    //     } catch (\Exception $exception) {
    //         $errors['message'] = $exception->getMessage();
    //         $errors['file'] = $exception->getFile();
    //         $errors['line'] = $exception->getLine();
    //         $errors['trace'] = $exception->getTrace();
    //         Log::channel('daily')->info('function delete in GuideCadreController', $errors);
    //     }
    // }
}
