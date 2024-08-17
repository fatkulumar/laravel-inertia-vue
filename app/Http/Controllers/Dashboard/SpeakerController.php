<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use App\Models\ClassRoom;
use App\Models\Province;
use App\Models\Regional;
use App\Models\Speaker;
use App\Traits\EntityValidator;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SpeakerController extends Controller
{
    use EntityValidator;
    use FileUpload;

    protected function fileSettings()
    {
        $this->settings = [
            'attributes'  => ['jpeg', 'jpg', 'png'],
            'path'        => 'file/speaker/',
            'softdelete'  => false
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $classRooms = ClassRoom::select('id', 'name')->get();
            $categories = Category::select('id', 'name')->get();
            $cities = City::select('id', 'name', 'code')->get();
            $provinces = Province::select('id', 'name', 'code')->get();
            $speakers = Speaker::orderBy('created_at', 'desc')
                ->when($request['search'], function ($query, $request) {
                    $query->where('name', 'like', '%' . $request . '%');
                })
                ->with(['classRoom:id,name', 'category:id,name', 'city:id,code,name', 'province:id,code,name'])
                ->select('class_room_id', 'category_id', 'city_code', 'province_code', 'id', 'name', 'image')
                ->paginate(5)
                ->withQueryString()
                ->appends(['search' => $request['search']]);

            $speakers->map(function ($speaker) {
                $this->fileSettings();
                if (isset($speaker['image'])) {
                    $speaker['image'] = $this->getFileAttribute($speaker['image']);
                } else {
                    $speaker['link_image'] = null;
                }
                return $speaker;
            });
            // return $speakers;
            return Inertia::render('Dashboard/Speaker', [
                'speakers' => $speakers,
                'classRooms' => $classRooms,
                'categories' => $categories,
                'provinces' => $provinces,
                'cities' => $cities,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function index in Dashboard/speakerController', $errors);
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
            $validasiData = $this->updateValidator($request);
            if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

            $id = $request->post('id');
            if ($id) {

                $speaker = Speaker::where('id', $id)->first();

                $image = $request->file('image');
                if ($image) {
                    $this->fileSettings();
                    $uploadImage = $this->uploadFile($image);
                } else {
                    $uploadImage = $speaker->image;
                }

                $saveData = [
                    'name' => $request->post('name'),
                    'image' => $uploadImage,
                    'province_code' => $request->post('province_code'),
                    'city_code' => $request->post('city_code'),
                    'category_id' => $request->post('category_id'),
                    'class_room_id' => $request->post('class_room_id'),
                ];
                $result = $speaker->update($saveData);
                if (!$result) return redirect()->back()->withErrors($result)->withInput();
            } else {

                $validasiData = $this->storeValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

                $image = $request->file('image');
                if ($image) {
                    $this->fileSettings();
                    $uploadImage = $this->uploadFile($image);
                } else {
                    $uploadImage = null;
                }

                $saveData = [
                    'name' => $request->post('name'),
                    'image' => $uploadImage,
                    'province_code' => $request->post('province_code'),
                    'city_code' => $request->post('city_code'),
                    'category_id' => $request->post('category_id'),
                    'class_room_id' => $request->post('class_room_id'),
                ];

                $result = Speaker::create($saveData);
                if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function store in speakerController', $errors);
        }
    }

    private function storeValidator(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:100',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'province_code' => 'required|integer',
                'city_code' => 'required|integer',
                'category_id' => 'required|string|max:36',
                'class_room_id' => 'required|string|max:36',
            ];
            $Validatedata = [
                'name' => $request->post('name'),
                'image' => $request->file('image'),
                'province_code' => $request->post('province_code'),
                'city_code' => $request->post('city_code'),
                'category_id' => $request->post('category_id'),
                'class_room_id' => $request->post('class_room_id'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function storeValidator in speakerController', $errors);
        }
    }

    private function updateValidator(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:100',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'province_code' => 'required|integer',
                'city_code' => 'required|integer',
                'category_id' => 'required|string|max:36',
                'class_room_id' => 'required|string|max:36',
            ];
            $Validatedata = [
                'name' => $request->post('name'),
                'image' => $request->file('image'),
                'province_code' => $request->post('province_code'),
                'city_code' => $request->post('city_code'),
                'category_id' => $request->post('category_id'),
                'class_room_id' => $request->post('class_room_id'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function updateValidator in speakerController', $errors);
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
            $speaker = Speaker::findOrFail($id);
            $this->fileSettings();
            if (isset($speaker['image'])) {
                $this->deleteFile($speaker['image']);
            }
            $speaker->delete();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in speakerController', $errors);
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
                $speaker = Speaker::findOrFail($id);
                $this->fileSettings();
                if (isset($speaker['image'])) {
                    $this->deleteFile($speaker['image']);
                }
                $speaker->delete();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in speakerController', $errors);
        }
    }

    public function city(Request $request, $provinceCode)
    {
        try {
            $speaker = City::where('province_code', $provinceCode)->get();
            return response()->json($speaker);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in speakerController', $errors);
        }
    }
}
